<?php
require_once __DIR__ . '/_init.php';

$id     = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$course = null;

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $course = $stmt->fetch();
    if (!$course) {
        set_flash('danger', 'That course was not found.');
        redirect('admin/courses.php');
    }
}
$isEdit = $course !== null;

$levels = ['Beginner', 'Intermediate', 'Advanced'];

$data = [
    'title'       => $course['title']       ?? '',
    'description' => $course['description'] ?? '',
    'duration'    => $course['duration']    ?? '',
    'level'       => $course['level']       ?? 'Beginner',
    'price'       => $course['price']       ?? '',
];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Session expired. Please try again.';
    } else {
        foreach (array_keys($data) as $field) {
            $data[$field] = trim($_POST[$field] ?? '');
        }

        if ($data['title'] === '' || mb_strlen($data['title']) > 150) {
            $errors[] = 'Title is required (150 characters maximum).';
        }
        if ($data['description'] === '') {
            $errors[] = 'Description is required.';
        }
        if (!in_array($data['level'], $levels, true)) {
            $errors[] = 'Please choose a valid level.';
        }
        if (mb_strlen($data['duration']) > 50 || mb_strlen($data['price']) > 50) {
            $errors[] = 'Duration and price must be 50 characters or fewer.';
        }

        if (!$errors) {
            try {
                $params = [
                    ':title' => $data['title'], ':description' => $data['description'],
                    ':duration' => $data['duration'] ?: null, ':level' => $data['level'],
                    ':price' => $data['price'] ?: null,
                ];
                if ($isEdit) {
                    $stmt = $pdo->prepare(
                        "UPDATE courses SET title=:title, description=:description, duration=:duration,
                         level=:level, price=:price WHERE id=:id"
                    );
                    $stmt->execute($params + [':id' => $id]);
                    set_flash('success', 'Course updated.');
                } else {
                    $stmt = $pdo->prepare(
                        "INSERT INTO courses (title, description, duration, level, price)
                         VALUES (:title, :description, :duration, :level, :price)"
                    );
                    $stmt->execute($params);
                    set_flash('success', 'Course added.');
                }
                redirect('admin/courses.php');
            } catch (PDOException $e) {
                error_log('Course save failed: ' . $e->getMessage());
                $errors[] = 'Could not save the course. Please try again.';
            }
        }
    }
}

$adminTitle  = $isEdit ? 'Edit course' : 'Add course';
$adminActive = 'courses';
require_once __DIR__ . '/_header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <h1 class="h3 fw-bold mb-0"><?= e($adminTitle) ?></h1>
    <a href="<?= url('admin/courses.php') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
</div>

<?php if ($errors): ?>
    <div class="alert alert-danger">
        <ul class="mb-0"><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<form method="post" class="admin-card p-4" novalidate>
    <?= csrf_field() ?>
    <div class="row g-3">
        <div class="col-12">
            <label class="form-label" for="title">Course title *</label>
            <input class="form-control" id="title" name="title" maxlength="150" required value="<?= e($data['title']) ?>">
        </div>

        <div class="col-12">
            <label class="form-label" for="description">Description *</label>
            <textarea class="form-control" id="description" name="description" rows="4" required><?= e($data['description']) ?></textarea>
        </div>

        <div class="col-md-4">
            <label class="form-label" for="level">Level *</label>
            <select class="form-select" id="level" name="level">
                <?php foreach ($levels as $lvl): ?>
                    <option value="<?= e($lvl) ?>" <?= $data['level'] === $lvl ? 'selected' : '' ?>><?= e($lvl) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label" for="duration">Duration</label>
            <input class="form-control" id="duration" name="duration" maxlength="50" placeholder="e.g. 4 weeks" value="<?= e($data['duration']) ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label" for="price">Price</label>
            <input class="form-control" id="price" name="price" maxlength="50" placeholder="e.g. ₦20,000 or Contact me" value="<?= e($data['price']) ?>">
        </div>

        <div class="col-12 pt-2">
            <button class="btn btn-brand px-4 py-2" type="submit">
                <i class="bi bi-check-lg me-1"></i> <?= $isEdit ? 'Save changes' : 'Add course' ?>
            </button>
            <a href="<?= url('admin/courses.php') ?>" class="btn btn-outline-secondary px-4 py-2 ms-1">Cancel</a>
        </div>
    </div>
</form>

<?php require_once __DIR__ . '/_footer.php'; ?>