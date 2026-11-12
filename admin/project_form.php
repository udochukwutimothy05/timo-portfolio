<?php
require_once __DIR__ . '/_init.php';

$id      = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$project = null;

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $project = $stmt->fetch();
    if (!$project) {
        set_flash('danger', 'That project was not found.');
        redirect('admin/projects.php');
    }
}
$isEdit = $project !== null;

$data = [
    'title'       => $project['title']       ?? '',
    'category'    => $project['category']    ?? '',
    'tech_stack'  => $project['tech_stack']  ?? '',
    'live_url'    => $project['live_url']    ?? '',
    'github_url'  => $project['github_url']  ?? '',
    'description' => $project['description'] ?? '',
    'featured'    => (int) ($project['featured'] ?? 0),
];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Session expired or the upload was too large. Please try again.';
    } else {
        foreach (['title', 'category', 'tech_stack', 'live_url', 'github_url', 'description'] as $field) {
            $data[$field] = trim($_POST[$field] ?? '');
        }
        $data['featured'] = isset($_POST['featured']) ? 1 : 0;

        if ($data['title'] === '' || mb_strlen($data['title']) > 150) {
            $errors[] = 'Title is required (150 characters maximum).';
        }
        if ($data['category'] === '' || mb_strlen($data['category']) > 50) {
            $errors[] = 'Category is required (50 characters maximum).';
        }
        if ($data['description'] === '') {
            $errors[] = 'Description is required.';
        }
        if (mb_strlen($data['tech_stack']) > 255) {
            $errors[] = 'Technologies list is too long (255 characters maximum).';
        }
        if (!is_valid_http_url($data['live_url']) || !is_valid_http_url($data['github_url'])) {
            $errors[] = 'Links must start with http:// or https://';
        }

        // Image upload (optional)
        $newImage = null;
        if (!$errors) {
            [$ok, $result] = handle_image_upload($_FILES['image'] ?? []);
            if (!$ok) {
                $errors[] = $result;
            } else {
                $newImage = $result;
            }
        }

        if (!$errors) {
            $image    = $project['image'] ?? null;
            $oldImage = null;                       // an old file to delete after saving

            if ($newImage) {
                $oldImage = $image;
                $image    = $newImage;
            } elseif ($isEdit && isset($_POST['remove_image'])) {
                $oldImage = $image;
                $image    = null;
            }

            try {
                if ($isEdit) {
                    $stmt = $pdo->prepare(
                        "UPDATE projects SET title=:title, description=:description, category=:category,
                         tech_stack=:tech, image=:image, live_url=:live, github_url=:github, featured=:featured
                         WHERE id=:id"
                    );
                    $stmt->execute([
                        ':title' => $data['title'], ':description' => $data['description'],
                        ':category' => $data['category'], ':tech' => $data['tech_stack'] ?: null,
                        ':image' => $image, ':live' => $data['live_url'] ?: null,
                        ':github' => $data['github_url'] ?: null, ':featured' => $data['featured'],
                        ':id' => $id,
                    ]);
                    $message = 'Project updated.';
                } else {
                    $stmt = $pdo->prepare(
                        "INSERT INTO projects (title, slug, description, category, tech_stack, image, live_url, github_url, featured)
                         VALUES (:title, :slug, :description, :category, :tech, :image, :live, :github, :featured)"
                    );
                    $stmt->execute([
                        ':title' => $data['title'], ':slug' => unique_slug($pdo, $data['title']),
                        ':description' => $data['description'], ':category' => $data['category'],
                        ':tech' => $data['tech_stack'] ?: null, ':image' => $image,
                        ':live' => $data['live_url'] ?: null, ':github' => $data['github_url'] ?: null,
                        ':featured' => $data['featured'],
                    ]);
                    $message = 'Project added.';
                }

                if ($oldImage) {
                    delete_upload($oldImage);
                }
                set_flash('success', $message);
                redirect('admin/projects.php');

            } catch (PDOException $e) {
                error_log('Project save failed: ' . $e->getMessage());
                $errors[] = 'Could not save the project. Please try again.';
                if ($newImage) {
                    delete_upload($newImage);       // do not leave an orphan file
                }
            }
        }
    }
}

// Existing categories for the suggestion list
$categories = $pdo->query("SELECT DISTINCT category FROM projects ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);
foreach (['Website', 'Redesign', 'Web App'] as $default) {
    if (!in_array($default, $categories, true)) {
        $categories[] = $default;
    }
}

$currentImage = $project['image'] ?? null;
$hasImage     = $currentImage && file_exists(__DIR__ . '/../uploads/' . $currentImage);

$adminTitle  = $isEdit ? 'Edit project' : 'Add project';
$adminActive = 'projects';
require_once __DIR__ . '/_header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <h1 class="h3 fw-bold mb-0"><?= e($adminTitle) ?></h1>
    <a href="<?= url('admin/projects.php') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
</div>

<?php if ($errors): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" class="admin-card p-4" novalidate>
    <?= csrf_field() ?>

    <div class="row g-3">
        <div class="col-md-8">
            <label class="form-label" for="title">Project title *</label>
            <input class="form-control" id="title" name="title" maxlength="150" required value="<?= e($data['title']) ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label" for="category">Category *</label>
            <input class="form-control" id="category" name="category" maxlength="50" list="categoryList" required
                   value="<?= e($data['category']) ?>">
            <datalist id="categoryList">
                <?php foreach ($categories as $cat): ?><option value="<?= e($cat) ?>"><?php endforeach; ?>
            </datalist>
        </div>

        <div class="col-12">
            <label class="form-label" for="description">Description *</label>
            <textarea class="form-control" id="description" name="description" rows="6" required><?= e($data['description']) ?></textarea>
            <div class="form-text">What you built, what the client needed, and the result. Line breaks are kept.</div>
        </div>

        <div class="col-12">
            <label class="form-label" for="tech_stack">Technologies</label>
            <input class="form-control" id="tech_stack" name="tech_stack" maxlength="255"
                   placeholder="HTML, CSS, Bootstrap 5, JavaScript" value="<?= e($data['tech_stack']) ?>">
            <div class="form-text">Separate with commas.</div>
        </div>

        <div class="col-md-6">
            <label class="form-label" for="live_url">Live website link</label>
            <input class="form-control" id="live_url" name="live_url" placeholder="https://example.com" value="<?= e($data['live_url']) ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label" for="github_url">GitHub link</label>
            <input class="form-control" id="github_url" name="github_url" placeholder="https://github.com/you/project" value="<?= e($data['github_url']) ?>">
        </div>

        <div class="col-12">
            <label class="form-label" for="image">Project image</label>
            <?php if ($hasImage): ?>
                <div class="mb-2">
                    <img src="<?= url('uploads/' . $currentImage) ?>" alt="" style="max-width:220px;border-radius:10px;" class="border">
                </div>
            <?php endif; ?>
            <input class="form-control" type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp">
            <div class="form-text">JPG, PNG or WebP, up to 2 MB. A wide screenshot (about 1200 x 700) looks best.
                <?= $hasImage ? 'Choosing a new file replaces the current one.' : '' ?></div>
            <?php if ($hasImage): ?>
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image">
                    <label class="form-check-label" for="remove_image">Remove the current image</label>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-12">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="featured" name="featured"
                       <?= $data['featured'] ? 'checked' : '' ?>>
                <label class="form-check-label" for="featured">Feature on the Home page</label>
            </div>
        </div>

        <div class="col-12 pt-2">
            <button class="btn btn-brand px-4 py-2" type="submit">
                <i class="bi bi-check-lg me-1"></i> <?= $isEdit ? 'Save changes' : 'Add project' ?>
            </button>
            <a href="<?= url('admin/projects.php') ?>" class="btn btn-outline-secondary px-4 py-2 ms-1">Cancel</a>
        </div>
    </div>
</form>

<?php require_once __DIR__ . '/_footer.php'; ?>