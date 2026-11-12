<?php
require_once __DIR__ . '/_init.php';

// ---- Handle actions (delete, mark read/unread) ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        set_flash('danger', 'Session expired. Please try again.');
        redirect('admin/messages.php');
    }

    $id     = (int) ($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($action === 'delete' && $id) {
        $pdo->prepare("DELETE FROM messages WHERE id = :id")->execute([':id' => $id]);
        set_flash('success', 'Message deleted.');
    } elseif ($action === 'mark_unread' && $id) {
        $pdo->prepare("UPDATE messages SET is_read = 0 WHERE id = :id")->execute([':id' => $id]);
        set_flash('success', 'Marked as unread.');
    } elseif ($action === 'mark_all_read') {
        $pdo->exec("UPDATE messages SET is_read = 1");
        set_flash('success', 'All messages marked as read.');
    }
    redirect('admin/messages.php');
}

// ---- List with filter and pagination ----
$filter  = ($_GET['filter'] ?? '') === 'unread' ? 'unread' : 'all';
$where   = $filter === 'unread' ? 'WHERE is_read = 0' : '';
$perPage = 15;
$page    = max(1, (int) ($_GET['page'] ?? 1));

$total      = (int) $pdo->query("SELECT COUNT(*) FROM messages $where")->fetchColumn();
$totalPages = max(1, (int) ceil($total / $perPage));
$page       = min($page, $totalPages);
$offset     = ($page - 1) * $perPage;

$stmt = $pdo->prepare("SELECT * FROM messages $where ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$messages = $stmt->fetchAll();

$adminTitle  = 'Messages';
$adminActive = 'messages';
require_once __DIR__ . '/_header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <h1 class="h3 fw-bold mb-0">Messages</h1>
    <div class="d-flex gap-2">
        <div class="btn-group" role="group" aria-label="Filter messages">
            <a href="<?= url('admin/messages.php') ?>" class="btn btn-sm <?= $filter === 'all' ? 'btn-brand' : 'btn-outline-secondary' ?>">All</a>
            <a href="<?= url('admin/messages.php?filter=unread') ?>" class="btn btn-sm <?= $filter === 'unread' ? 'btn-brand' : 'btn-outline-secondary' ?>">Unread</a>
        </div>
        <form method="post" class="m-0">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="mark_all_read">
            <button class="btn btn-sm btn-outline-secondary" type="submit">Mark all read</button>
        </form>
    </div>
</div>

<div class="admin-card">
    <?php if (empty($messages)): ?>
        <p class="text-secondary p-4 mb-0">
            <?= $filter === 'unread' ? 'No unread messages.' : 'No messages yet.' ?>
        </p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr class="text-secondary small">
                        <th style="width:30px"></th><th>From</th><th>Subject</th><th>Received</th><th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($messages as $m): ?>
                    <tr class="<?= $m['is_read'] ? '' : 'msg-unread' ?>">
                        <td><span class="dot<?= $m['is_read'] ? ' read' : '' ?>"></span></td>
                        <td>
                            <a class="text-decoration-none" style="color:inherit"
                               href="<?= url('admin/message_view.php?id=' . (int) $m['id']) ?>"><?= e($m['name']) ?></a>
                            <div class="text-secondary small fw-normal"><?= e($m['email']) ?></div>
                        </td>
                        <td><?= e($m['subject'] ?: '(no subject)') ?></td>
                        <td class="text-secondary small"><?= e(date('d M Y, g:i A', strtotime($m['created_at']))) ?></td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a class="btn btn-sm btn-outline-brand" href="<?= url('admin/message_view.php?id=' . (int) $m['id']) ?>">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <form method="post" class="m-0" onsubmit="return confirm('Delete this message?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
                                    <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i></button>
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

<?php if ($totalPages > 1): ?>
    <nav class="mt-4" aria-label="Message pages">
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item<?= $i === $page ? ' active' : '' ?>">
                    <a class="page-link" href="<?= url('admin/messages.php?' . http_build_query(['filter' => $filter, 'page' => $i])) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
<?php endif; ?>

<?php require_once __DIR__ . '/_footer.php'; ?>