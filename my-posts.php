<?php
require_once __DIR__ . '/config/database.php';

$pageTitle = 'My Posts';
require_once ROOT_PATH . '/classes/Auth.php';
require_once ROOT_PATH . '/classes/Post.php';
require_once ROOT_PATH . '/includes/functions.php';

Auth::requireLogin();
require_once ROOT_PATH . '/includes/header.php';

$postModel = new Post();
$posts = $postModel->getByUser(Auth::id());
?>

<section class="section">
    <div class="container">
        <div class="page-header">
            <h1>My Posts</h1>
            <a href="<?= BASE_URL ?>/create-post.php" class="btn btn-primary">Write New Post</a>
        </div>

        <?php if (empty($posts)): ?>
            <div class="empty-state">
                <p>You haven't written any posts yet.</p>
                <a href="<?= BASE_URL ?>/create-post.php" class="btn btn-primary" style="margin-top:12px">Start Writing</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($posts as $post): ?>
                            <tr>
                                <td><a href="<?= BASE_URL ?>/post.php?id=<?= $post['id'] ?>" class="table-link"><?= e($post['title']) ?></a></td>
                                <td><?= e($post['category_name']) ?></td>
                                <td><span class="status-badge status-<?= $post['status'] ?>"><?= ucfirst($post['status']) ?></span></td>
                                <td><?= date('M d, Y', strtotime($post['created_at'])) ?></td>
                                <td class="table-actions">
                                    <a href="<?= BASE_URL ?>/edit-post.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-outline">Edit</a>
                                    <form method="POST" action="<?= BASE_URL ?>/delete-post.php" style="display:inline"
                                        onsubmit="return confirm('Delete this post? This cannot be undone.')">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= $post['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once ROOT_PATH . '/includes/footer.php'; ?>