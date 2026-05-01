<?php
$pageTitle = 'My Favorites';
require_once ROOT_PATH . '/classes/Auth.php';
require_once ROOT_PATH . '/classes/Favorite.php';
require_once ROOT_PATH . '/includes/functions.php';

Auth::requireLogin();
require_once ROOT_PATH . '/includes/header.php';

$favoriteModel = new Favorite();
$posts = $favoriteModel->getByUser(Auth::id());
?>

<section class="section">
    <div class="container">
        <div class="page-header">
            <h1>Saved Posts</h1>
            <a href="<?= BASE_URL ?>" class="btn btn-outline">Browse Posts</a>
        </div>

        <?php if (empty($posts)): ?>
            <div class="empty-state">
                <p>You haven't saved any posts yet.</p>
                <p class="text-muted">Click the bookmark icon on any post to save it here.</p>
            </div>
        <?php else: ?>
            <div class="post-grid">
                <?php foreach ($posts as $post): ?>
                    <article class="post-card card">
                        <?php if ($post['thumbnail']): ?>
                            <a href="<?= BASE_URL ?>/post.php?id=<?= $post['id'] ?>" class="post-card-img">
                                <img src="<?= e($post['thumbnail']) ?>" alt="<?= e($post['title']) ?>">
                            </a>
                        <?php else: ?>
                            <a href="<?= BASE_URL ?>/post.php?id=<?= $post['id'] ?>" class="post-card-img post-card-placeholder">
                                <span><?= get_initials($post['title']) ?></span>
                            </a>
                        <?php endif; ?>
                        <div class="post-card-body">
                            <div class="post-card-meta">
                                <a href="<?= BASE_URL ?>/category.php?id=<?= $post['category_id'] ?>" class="category-badge"><?= e($post['category_name']) ?></a>
                                <span class="post-date"><?= time_ago($post['created_at']) ?></span>
                            </div>
                            <h2 class="post-card-title">
                                <a href="<?= BASE_URL ?>/post.php?id=<?= $post['id'] ?>"><?= e($post['title']) ?></a>
                            </h2>
                            <p class="post-card-excerpt"><?= e(excerpt($post['content'])) ?></p>
                            <div class="post-card-footer">
                                <span class="post-author">by <?= e($post['author_name']) ?></span>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once ROOT_PATH . '/includes/footer.php'; ?>