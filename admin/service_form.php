<?php
require_once __DIR__ . '/_init.php';

$id      = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$service = null;

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM services WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $service = $stmt->fetch();
    if (!$service) {
        set_flash('danger', 'That service was not found.');
        redirect('admin/services.php');
    }
}
$isEdit = $service !== null;

$nextOrder = (int) $pdo->query("SELECT COALESCE(MAX(sort_order), 0) + 1 FROM services")->fetchColumn();

$data = [
    'title'       => $service['title']       ?? '',
    'description' => $service['description'] ?? '',
    'icon'        => $service['icon']        ?? 'bi-code-slash',
    'sort_order'  => (int) ($service['sort_order'] ?? $nextOrder),
];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Session expired. Please try again.';
    } else {
        $data['title']       = trim($_POST['title'] ?? '');
        $data['description'] = trim($_POST['description'] ?? '');
        $data['icon']        = trim($_POST['icon'] ?? '');
        $data['sort_order']  = (int) ($_POST['sort_order'] ?? 0);

        if ($data['title'] === '' || mb_strlen($data['title']) > 100) {
            $errors[] = 'Title is required (100 characters maximum).';
        }
        if ($data['description'] === '') {
            $errors[] = 'Description is required.';
        }
        if (!preg_match('/^bi-[a-z0-9-]+$/', $data['icon']) || strlen($data['icon']) > 50) {
            $errors[] = 'Icon must look like bi-code-slash (see the icon link below the field).';
        }

        if (!$errors) {
            try {
                if ($isEdit) {
                    $stmt = $pdo->prepare(
                        "UPDATE services SET title=:title, description=:description, icon=:icon, sort_order=:sort WHERE id=:id"
                    );
                    $stmt->execute([
                        ':title' => $data['title'], ':description' => $data['description'],
                        ':icon' => $data['icon'], ':sort' => $data['sort_order'], ':id' => $id,
                    ]);
                    set_flash('success', 'Service updated.');
                } else {
                    $stmt = $pdo->prepare(
                        "INSERT INTO services (title, description, icon, sort_order) VALUES (:title, :description, :icon, :sort)"
                    );
                    $stmt->execute([
                        ':title' => $data['title'], ':description' => $data['description'],
                        ':icon' => $data['icon'], ':sort' => $data['sort_order'],
                    ]);
                    set_flash('success', 'Service added.');
                }
                redirect('admin/services.php');
            } catch (PDOException $e) {
                error_log('Service save failed: ' . $e->getMessage());
                $errors[] = 'Could not save the service. Please try again.';
            }
        }
    }
}

$iconSuggestions = [
    'bi-code-slash', 'bi-tools', 'bi-mortarboard', 'bi-laptop', 'bi-phone', 'bi-palette', 'bi-brush',
    'bi-database', 'bi-shield-check', 'bi-speedometer2', 'bi-globe', 'bi-cart', 'bi-envelope',
    'bi-chat-dots', 'bi-rocket-takeoff', 'bi-lightning-charge', 'bi-gear', 'bi-search', 'bi-bug', 'bi-bar-chart',
];

$adminTitle  = $isEdit ? 'Edit service' : 'Add service';
$adminActive = 'services';
require_once __DIR__ . '/_header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <h1 class="h3 fw-bold mb-0"><?= e($adminTitle) ?></h1>
    <a href="<?= url('admin/services.php') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
</div>

<?php if ($errors): ?>
    <div class="alert alert-danger">
        <ul class="mb-0"><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<form method="post" class="admin-card p-4" novalidate>
    <?= csrf_field() ?>
    <div class="row g-3">
        <div class="col-md-8">
            <label class="form-label" for="title">Service title *</label>
            <input class="form-control" id="title" name="title" maxlength="100" required value="<?= e($data['title']) ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label" for="sort_order">Display order</label>
            <input class="form-control" type="number" id="sort_order" name="sort_order" value="<?= (int) $data['sort_order'] ?>">
            <div class="form-text">Smaller numbers come first.</div>
        </div>

        <div class="col-12">
            <label class="form-label" for="description">Description *</label>
            <textarea class="form-control" id="description" name="description" rows="4" required><?= e($data['description']) ?></textarea>
        </div>

        <div class="col-md-8">
            <label class="form-label" for="icon">Icon *</label>
            <div class="d-flex align-items-center gap-3">
                <span class="icon-box"><i id="iconPreview" class="bi <?= e($data['icon']) ?>"></i></span>
                <input class="form-control" id="icon" name="icon" maxlength="50" list="iconList" required value="<?= e($data['icon']) ?>">
            </div>
            <datalist id="iconList">
                <?php foreach ($iconSuggestions as $ic): ?><option value="<?= e($ic) ?>"><?php endforeach; ?>
            </datalist>
            <div class="form-text">
                Pick from the suggestions or find more at
                <a href="https://icons.getbootstrap.com/" target="_blank" rel="noopener">icons.getbootstrap.com</a>
                (use the name starting with <code>bi-</code>).
            </div>
        </div>

        <div class="col-12 pt-2">
            <button class="btn btn-brand px-4 py-2" type="submit">
                <i class="bi bi-check-lg me-1"></i> <?= $isEdit ? 'Save changes' : 'Add service' ?>
            </button>
            <a href="<?= url('admin/services.php') ?>" class="btn btn-outline-secondary px-4 py-2 ms-1">Cancel</a>
        </div>
    </div>
</form>

<script>
    // Live icon preview
    document.getElementById('icon').addEventListener('input', function () {
        var v = this.value.trim();
        if (/^bi-[a-z0-9-]+$/.test(v)) {
            document.getElementById('iconPreview').className = 'bi ' + v;
        }
    });
</script>

<?php require_once __DIR__ . '/_footer.php'; ?>