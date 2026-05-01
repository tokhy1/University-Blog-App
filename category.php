<?php
require_once __DIR__ . '/config/database.php';

require_once ROOT_PATH . '/classes/Post.php';
require_once ROOT_PATH . '/classes/Category.php';
require_once ROOT_PATH . '/includes/functions.php';

$id = intval($_GET['id'] ?? 0);
$catModel = new Category();
$category = $catModel->getById($id);

if (!$category) {
    http_response_code(404);
    $pageTitle = 'Not Found';
    require_once ROOT_PATH . '/includes/header.php';
    echo '<div class="container" style="padding:80px 20px;text-align:center"><h1>Category not found</h1><a href="' . BASE_URL . '" class="btn btn-primary" style="margin-top:16px">Go Home</a></div>';
    require_once ROOT_PATH . '/includes/footer.php';
    exit;
}

$pageTitle = $category['name'];
require_once ROOT_PATH . '/includes/header.php';

$page = max(1, intval($_GET['page'] ?? 1));
$postModel = new Post();
$posts = $postModel->getByCategory($id, $page, 9);
$total = $postModel->countByCategory($id);
?>

<section class="section">
    <div class="container">
        <div class="category-header">
            <h1><?= e($category['name']) ?></h1>
            <p><?= $total ?> post<?= $total !== 1 ? 's' : '' ?></p>
        </div>

        <?php if (empty($posts)): ?>
            <div class="empty-state">
                <p>No posts in this category yet.</p>
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
            <?= paginate($page, $total, 9, BASE_URL . "/category.php?id=$id&page=") ?>
        <?php endif; ?>
    </div>
</section>

<?php require_once ROOT_PATH . '/includes/footer.php'; ?>