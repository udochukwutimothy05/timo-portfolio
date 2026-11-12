<?php
require_once __DIR__ . '/_init.php';

$count = fn(string $table): int => (int) $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();

$totalProjects = $count('projects');
$totalCourses  = $count('courses');
$totalServices = $count('services');
$totalMessages = $count('messages');
$totalUnread   = (int) $pdo->query("SELECT COUNT(*) FROM messages WHERE is_read = 0")->fetchColumn();

$recent = $pdo->query(
    "SELECT id, name, subject, is_read, created_at FROM messages ORDER BY created_at DESC LIMIT 5"
)->fetchAll();

$adminTitle  = 'Dashboard';
$adminActive = 'dashboard';
require_once __DIR__ . '/_header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h1 class="h3 fw-bold mb-0">Welcome, <?= e($_SESSION['admin_name']) ?></h1>
        <p class="text-secondary mb-0">Here is what is happening on your website.</p>
    </div>
    <a href="<?= url('admin/project_form.php') ?>" class="btn btn-brand px-4"><i class="bi bi-plus-lg me-1"></i> Add project</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="admin-card stat-card">
            <div class="icon-box"><i class="bi bi-folder2-open"></i></div>
            <div><div class="num"><?= $totalProjects ?></div><div class="text-secondary small">Projects</div></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="admin-card stat-card">
            <div class="icon-box"><i class="bi bi-envelope"></i></div>
            <div>
                <div class="num"><?= $totalMessages ?></div>
                <div class="text-secondary small">Messages (<?= $totalUnread ?> unread)</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="admin-card stat-card">
            <div class="icon-box"><i class="bi bi-mortarboard"></i></div>
            <div><div class="num"><?= $totalCourses ?></div><div class="text-secondary small">Courses</div></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="admin-card stat-card">
            <div class="icon-box"><i class="bi bi-tools"></i></div>
            <div><div class="num"><?= $totalServices ?></div><div class="text-secondary small">Services</div></div>
        </div>
    </div>
</div>

<div class="admin-card">
    <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
        <h2 class="h5 fw-semibold mb-0">Recent messages</h2>
        <a href="<?= url('admin/messages.php') ?>" class="link-brand small fw-medium">View all</a>
    </div>

    <?php if (empty($recent)): ?>
        <p class="text-secondary p-4 mb-0">No messages yet. They will appear here when someone uses your contact form.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table mb-0">
                <tbody>
                <?php foreach ($recent as $m): ?>
                    <tr class="<?= $m['is_read'] ? '' : 'msg-unread' ?>">
                        <td style="width:30px"><span class="dot<?= $m['is_read'] ? ' read' : '' ?>"></span></td>
                        <td><?= e($m['name']) ?></td>
                        <td class="text-secondary"><?= e($m['subject'] ?: '(no subject)') ?></td>
                        <td class="text-secondary small"><?= e(date('d M Y, g:i A', strtotime($m['created_at']))) ?></td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-brand" href="<?= url('admin/message_view.php?id=' . (int) $m['id']) ?>">Open</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/_footer.php'; ?>