<?php
require_once dirname(__DIR__) . '/config/database.php';$pageTitle = 'Comments';
$activePage = 'comments';
require_once ROOT_PATH . '/classes/Auth.php';
require_once ROOT_PATH . '/classes/Comment.php';
require_once ROOT_PATH . '/includes/functions.php';
require_once ROOT_PATH . '/includes/admin-header.php';

$commentModel = new Comment();

if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    verify_csrf();
    $commentModel->delete(intval($_POST['id'] ?? 0));
    set_flash('success', 'Comment deleted.');
    redirect(BASE_URL . '/admin/comments.php');
}

$page = max(1, intval($_GET['page'] ?? 1));
$comments = $commentModel->getAll($page, 20);
$total = $commentModel->getTotal();
?>

<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>Comment</th>
                <th>Author</th>
                <th>Post</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($comments as $c): ?>
                <tr>
                    <td style="max-width:300px"><?= e(mb_strimwidth($c['content'], 0, 80, '...')) ?></td>
                    <td><?= e($c['author_name']) ?></td>
                    <td><a href="<?= BASE_URL ?>/post.php?id=<?= $c['post_id'] ?>" class="table-link" target="_blank"><?= e(mb_strimwidth($c['post_title'], 0, 35, '...')) ?></a></td>
                    <td><?= time_ago($c['created_at']) ?></td>
                    <td class="table-actions">
                        <form method="POST" action="" style="display:inline"
                            onsubmit="return confirm('Delete this comment?')">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $c['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($comments)): ?>
                <tr>
                    <td colspan="5" class="text-muted" style="text-align:center;padding:32px">No comments yet.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?= paginate($page, $total, 20, BASE_URL . '/admin/comments.php?page=') ?>

<?php require_once ROOT_PATH . '/includes/admin-footer.php'; ?>