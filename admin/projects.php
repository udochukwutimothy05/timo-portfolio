<?php
require_once __DIR__ . '/_init.php';

// ---- Handle delete / featured toggle ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        set_flash('danger', 'Session expired. Please try again.');
        redirect('admin/projects.php');
    }

    $id     = (int) ($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($action === 'delete' && $id) {
        $stmt = $pdo->prepare("SELECT image FROM projects WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $image = $stmt->fetchColumn();

        $pdo->prepare("DELETE FROM projects WHERE id = :id")->execute([':id' => $id]);
        if ($image) {
            delete_upload($image);
        }
        set_flash('success', 'Project deleted.');
    } elseif ($action === 'toggle_featured' && $id) {
        $pdo->prepare("UPDATE projects SET featured = 1 - featured WHERE id = :id")->execute([':id' => $id]);
        set_flash('success', 'Featured status updated.');
    }
    redirect('admin/projects.php');
}

$projects = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC")->fetchAll();

$adminTitle  = 'Projects';
$adminActive = 'projects';
require_once __DIR__ . '/_header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <h1 class="h3 fw-bold mb-0">Projects</h1>
    <a href="<?= url('admin/project_form.php') ?>" class="btn btn-brand px-4"><i class="bi bi-plus-lg me-1"></i> Add project</a>
</div>

<div class="admin-card">
    <?php if (empty($projects)): ?>
        <p class="text-secondary p-4 mb-0">No projects yet. Click "Add project" to create your first one.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr class="text-secondary small">
                        <th>Image</th><th>Title</th><th>Category</th><th>Featured</th><th>Added</th><th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($projects as $p): ?>
                    <?php $hasImage = !empty($p['image']) && file_exists(__DIR__ . '/../uploads/' . $p['image']); ?>
                    <tr>
                        <td>
                            <?php if ($hasImage): ?>
                                <img class="admin-thumb" src="<?= url('uploads/' . $p['image']) ?>" alt="">
                            <?php else: ?>
                                <span class="admin-thumb"><i class="bi bi-image"></i></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="fw-medium"><?= e($p['title']) ?></div>
                            <div class="text-secondary small"><?= e($p['slug']) ?></div>
                        </td>
                        <td><span class="badge badge-soft"><?= e($p['category']) ?></span></td>
                        <td>
                            <form method="post" class="m-0">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="toggle_featured">
                                <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                                <button type="submit" class="btn btn-sm <?= $p['featured'] ? 'btn-warning' : 'btn-outline-secondary' ?>"
                                        title="Show on Home page">
                                    <i class="bi bi-star<?= $p['featured'] ? '-fill' : '' ?>"></i>
                                </button>
                            </form>
                        </td>
                        <td class="text-secondary small"><?= e(date('d M Y', strtotime($p['created_at']))) ?></td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a class="btn btn-sm btn-outline-secondary" target="_blank" rel="noopener"
                                   href="<?= url('project.php?slug=' . urlencode($p['slug'])) ?>" title="View on site">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a class="btn btn-sm btn-outline-brand" href="<?= url('admin/project_form.php?id=' . (int) $p['id']) ?>" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="post" class="m-0"
                                      onsubmit="return confirm('Delete this project? This cannot be undone.');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
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

<?php require_once __DIR__ . '/_footer.php'; ?>