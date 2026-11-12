<?php
require_once __DIR__ . '/_init.php';

// ---- Handle delete ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        set_flash('danger', 'Session expired. Please try again.');
        redirect('admin/courses.php');
    }

    $id = (int) ($_POST['id'] ?? 0);
    if (($_POST['action'] ?? '') === 'delete' && $id) {
        $pdo->prepare("DELETE FROM courses WHERE id = :id")->execute([':id' => $id]);
        set_flash('success', 'Course deleted.');
    }
    redirect('admin/courses.php');
}

$courses = $pdo->query("SELECT * FROM courses ORDER BY id ASC")->fetchAll();

$adminTitle  = 'Courses';
$adminActive = 'courses';
require_once __DIR__ . '/_header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <h1 class="h3 fw-bold mb-0">Courses</h1>
    <a href="<?= url('admin/course_form.php') ?>" class="btn btn-brand px-4"><i class="bi bi-plus-lg me-1"></i> Add course</a>
</div>

<div class="admin-card">
    <?php if (empty($courses)): ?>
        <p class="text-secondary p-4 mb-0">No courses yet. Click "Add course" to create one.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr class="text-secondary small">
                        <th>Title</th><th>Level</th><th>Duration</th><th>Price</th><th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($courses as $c): ?>
                    <tr>
                        <td>
                            <div class="fw-medium"><?= e($c['title']) ?></div>
                            <div class="text-secondary small"><?= e(mb_strimwidth($c['description'], 0, 80, '...')) ?></div>
                        </td>
                        <td><span class="badge badge-soft"><?= e($c['level']) ?></span></td>
                        <td><?= e($c['duration'] ?: '-') ?></td>
                        <td><?= e($c['price'] ?: '-') ?></td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a class="btn btn-sm btn-outline-brand" href="<?= url('admin/course_form.php?id=' . (int) $c['id']) ?>" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="post" class="m-0" onsubmit="return confirm('Delete this course?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= (int) $c['id'] ?>">
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

<?php require_once __DIR__ . '/_footer.php'; ?>