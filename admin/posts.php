<?php
$pageTitle = 'Posts';
$activePage = 'posts';
require_once ROOT_PATH . '/classes/Auth.php';
require_once ROOT_PATH . '/classes/Post.php';
require_once ROOT_PATH . '/includes/functions.php';
require_once ROOT_PATH . '/includes/admin-header.php';

$postModel = new Post();
$page = max(1, intval($_GET['page'] ?? 1));
$posts = $postModel->getAll($page, 20);
$total = $postModel->getTotal();
?>

<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>Category</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($posts as $post): ?>
                <tr>
                    <td><a href="<?= BASE_URL ?>/post.php?id=<?= $post['id'] ?>" class="table-link" target="_blank"><?= e($post['title']) ?></a></td>
                    <td><?= e($post['author_name']) ?></td>
                    <td><?= e($post['category_name']) ?></td>
                    <td><span class="status-badge status-<?= $post['status'] ?>"><?= ucfirst($post['status']) ?></span></td>
                    <td><?= date('M d, Y', strtotime($post['created_at'])) ?></td>
                    <td class="table-actions">
                        <a href="<?= BASE_URL ?>/admin/post-edit.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-outline">Edit</a>
                        <form method="POST" action="<?= BASE_URL ?>/delete-post.php" style="display:inline"
                            onsubmit="return confirm('Delete this post permanently?')">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= $post['id'] ?>">
                            <input type="hidden" name="from" value="admin">
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($posts)): ?>
                <tr>
                    <td colspan="6" class="text-muted" style="text-align:center;padding:32px">No posts found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?= paginate($page, $total, 20, BASE_URL . '/admin/posts.php?page=') ?>

<?php require_once ROOT_PATH . '/includes/admin-footer.php'; ?>