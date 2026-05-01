<?php
$pageTitle = 'Dashboard';
$activePage = 'dashboard';
require_once ROOT_PATH . '/classes/Auth.php';
require_once ROOT_PATH . '/classes/Post.php';
require_once ROOT_PATH . '/classes/User.php';
require_once ROOT_PATH . '/classes/Comment.php';
require_once ROOT_PATH . '/classes/Category.php';
require_once ROOT_PATH . '/includes/functions.php';
require_once ROOT_PATH . '/includes/admin-header.php';

$postModel = new Post();
$userModel = new User();
$commentModel = new Comment();
$catModel = new Category();

$totalPosts = $postModel->getTotal();
$totalUsers = $userModel->getTotal();
$totalComments = $commentModel->getTotal();
$totalCategories = count($catModel->getAll());

// Get published vs draft counts
$published = (int) Database::getInstance()->getConnection()
    ->query("SELECT COUNT(*) FROM posts WHERE status = 'published'")->fetchColumn();
$drafts = $totalPosts - $published;

$recentPosts = $postModel->getRecent(5);
$recentComments = $commentModel->getRecent(5);
?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value"><?= $totalPosts ?></div>
        <div class="stat-label">Total Posts</div>
        <div class="stat-sub"><?= $published ?> published, <?= $drafts ?> drafts</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= $totalUsers ?></div>
        <div class="stat-label">Users</div>
        <div class="stat-sub">Registered members</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= $totalComments ?></div>
        <div class="stat-label">Comments</div>
        <div class="stat-sub">Across all posts</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= $totalCategories ?></div>
        <div class="stat-label">Categories</div>
        <div class="stat-sub">Content categories</div>
    </div>
</div>

<div class="admin-grid">
    <div class="admin-card">
        <div class="admin-card-header">
            <h3>Recent Posts</h3>
            <a href="<?= BASE_URL ?>/admin/posts.php" class="link">View all</a>
        </div>
        <?php if (empty($recentPosts)): ?>
            <p class="text-muted" style="padding:16px">No posts yet.</p>
        <?php else: ?>
            <div class="admin-list">
                <?php foreach ($recentPosts as $p): ?>
                    <div class="admin-list-item">
                        <div>
                            <div class="admin-list-title"><?= e($p['title']) ?></div>
                            <div class="admin-list-sub">by <?= e($p['author_name']) ?> &middot; <?= time_ago($p['created_at']) ?></div>
                        </div>
                        <span class="status-badge status-<?= $p['status'] ?>"><?= ucfirst($p['status']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <h3>Recent Comments</h3>
            <a href="<?= BASE_URL ?>/admin/comments.php" class="link">View all</a>
        </div>
        <?php if (empty($recentComments)): ?>
            <p class="text-muted" style="padding:16px">No comments yet.</p>
        <?php else: ?>
            <div class="admin-list">
                <?php foreach ($recentComments as $c): ?>
                    <div class="admin-list-item">
                        <div>
                            <div class="admin-list-title"><?= e(mb_strimwidth($c['content'], 0, 50, '...')) ?></div>
                            <div class="admin-list-sub"><?= e($c['author_name']) ?> on <?= e(mb_strimwidth($c['post_title'], 0, 30, '...')) ?></div>
                        </div>
                        <span class="post-date"><?= time_ago($c['created_at']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once ROOT_PATH . '/includes/admin-footer.php'; ?>