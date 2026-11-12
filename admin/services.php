<?php
require_once __DIR__ . '/_init.php';

// ---- Handle delete ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        set_flash('danger', 'Session expired. Please try again.');
        redirect('admin/services.php');
    }

    $id = (int) ($_POST['id'] ?? 0);
    if (($_POST['action'] ?? '') === 'delete' && $id) {
        $pdo->prepare("DELETE FROM services WHERE id = :id")->execute([':id' => $id]);
        set_flash('success', 'Service deleted.');
    }
    redirect('admin/services.php');
}

$services = $pdo->query("SELECT * FROM services ORDER BY sort_order ASC, id ASC")->fetchAll();

$adminTitle  = 'Services';
$adminActive = 'services';
require_once __DIR__ . '/_header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <h1 class="h3 fw-bold mb-0">Services</h1>
    <a href="<?= url('admin/service_form.php') ?>" class="btn btn-brand px-4"><i class="bi bi-plus-lg me-1"></i> Add service</a>
</div>

<div class="admin-card">
    <?php if (empty($services)): ?>
        <p class="text-secondary p-4 mb-0">No services yet. Click "Add service" to create one.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr class="text-secondary small">
                        <th style="width:60px">Icon</th><th>Title</th><th>Description</th><th>Order</th><th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($services as $s): ?>
                    <tr>
                        <td><span class="icon-box" style="width:42px;height:42px;font-size:1.2rem"><i class="bi <?= e($s['icon']) ?>"></i></span></td>
                        <td class="fw-medium"><?= e($s['title']) ?></td>
                        <td class="text-secondary small"><?= e(mb_strimwidth($s['description'], 0, 90, '...')) ?></td>
                        <td><?= (int) $s['sort_order'] ?></td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a class="btn btn-sm btn-outline-brand" href="<?= url('admin/service_form.php?id=' . (int) $s['id']) ?>" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="post" class="m-0" onsubmit="return confirm('Delete this service?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= (int) $s['id'] ?>">
                                    <button class="btn btn-sm btn-outline-danger" type="submit" title="Delete"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<p class="text-secondary small mt-3">Services appear on the Home page (first 6) and on the Services page, in the order shown here.</p>

<?php require_once __DIR__ . '/_footer.php'; ?>