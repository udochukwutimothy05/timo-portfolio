<?php
require_once __DIR__ . '/_init.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM messages WHERE id = :id");
$stmt->execute([':id' => $id]);
$m = $stmt->fetch();

if (!$m) {
    set_flash('danger', 'That message was not found.');
    redirect('admin/messages.php');
}

// Opening a message marks it as read
if (!$m['is_read']) {
    $pdo->prepare("UPDATE messages SET is_read = 1 WHERE id = :id")->execute([':id' => $id]);
}

$replySubject = 'Re: ' . ($m['subject'] ?: 'Your message');
$mailto = 'mailto:' . $m['email'] . '?subject=' . rawurlencode($replySubject);

$adminTitle  = 'Message';
$adminActive = 'messages';
require_once __DIR__ . '/_header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <h1 class="h3 fw-bold mb-0">Message</h1>
    <a href="<?= url('admin/messages.php') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back to messages</a>
</div>

<div class="admin-card p-4">
    <div class="row g-3 mb-4 pb-3 border-bottom">
        <div class="col-md-4">
            <div class="text-secondary small">From</div>
            <div class="fw-semibold"><?= e($m['name']) ?></div>
        </div>
        <div class="col-md-4">
            <div class="text-secondary small">Email</div>
            <a href="<?= e($mailto) ?>" class="link-brand"><?= e($m['email']) ?></a>
        </div>
        <div class="col-md-4">
            <div class="text-secondary small">Received</div>
            <div><?= e(date('d M Y, g:i A', strtotime($m['created_at']))) ?></div>
        </div>
        <div class="col-12">
            <div class="text-secondary small">Subject</div>
            <div class="fw-medium"><?= e($m['subject'] ?: '(no subject)') ?></div>
        </div>
    </div>

    <div class="message-body mb-4"><?= e($m['message']) ?></div>

    <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-brand px-4" href="<?= e($mailto) ?>"><i class="bi bi-reply me-1"></i> Reply by email</a>

        <form method="post" action="<?= url('admin/messages.php') ?>" class="m-0">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="mark_unread">
            <input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
            <button class="btn btn-outline-secondary" type="submit">Mark as unread</button>
        </form>

        <form method="post" action="<?= url('admin/messages.php') ?>" class="m-0"
              onsubmit="return confirm('Delete this message?');">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
            <button class="btn btn-outline-danger" type="submit"><i class="bi bi-trash me-1"></i> Delete</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/_footer.php'; ?>