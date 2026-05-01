<?php

require_once __DIR__ . '/config/config.php';

$pageTitle = 'Home';
require_once ROOT_PATH . '/classes/Post.php';
require_once ROOT_PATH . '/classes/Category.php';
require_once ROOT_PATH . '/classes/Auth.php';
require_once ROOT_PATH . '/includes/functions.php';
require_once ROOT_PATH . '/includes/header.php';

$page = max(1, intval($_GET['page'] ?? 1));
$postModel = new Post();
$posts = $postModel->getPublished($page, 9);
$total = $postModel->getTotalPublished();
?>

<section class="hero">
    <div class="container">
        <h1>Stories, ideas, and insights</h1>
        <p>A place to read, write, and share what matters.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if (empty($posts)): ?>
            <div class="empty-state">
                <p>No posts yet. Be the first to write something!</p>
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

            <?= paginate($page, $total, 9, BASE_URL . '/index.php?page=') ?>
        <?php endif; ?>
    </div>
</section>

<?php require_once ROOT_PATH . '/includes/footer.php'; ?>