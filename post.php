<?php
require_once __DIR__ . '/config/database.php';

require_once ROOT_PATH . '/classes/Post.php';
require_once ROOT_PATH . '/classes/Comment.php';
require_once ROOT_PATH . '/classes/Like.php';
require_once ROOT_PATH . '/classes/Favorite.php';
require_once ROOT_PATH . '/classes/Auth.php';
require_once ROOT_PATH . '/includes/functions.php';

$id = intval($_GET['id'] ?? 0);
$postModel = new Post();
$post = $postModel->getById($id);

if (!$post || $post['status'] !== 'published') {
    http_response_code(404);
    $pageTitle = 'Not Found';
    require_once ROOT_PATH . '/includes/header.php';
    echo '<div class="container" style="padding:80px 20px;text-align:center"><h1>Post not found</h1><p>This post may not exist or has been removed.</p><a href="' . BASE_URL . '" class="btn btn-primary" style="margin-top:16px">Go Home</a></div>';
    require_once ROOT_PATH . '/includes/footer.php';
    exit;
}

$pageTitle = $post['title'];
require_once ROOT_PATH . '/includes/header.php';

$tags = $postModel->getTags($id);
$commentModel = new Comment();
$comments = $commentModel->getByPost($id);
$likeModel = new Like();
$favoriteModel = new Favorite();
$userId = Auth::id();
$isLiked = $likeModel->isLiked($id, $userId);
$isFavorited = $favoriteModel->isFavorited($id, $userId);
$likeCount = $likeModel->getCount($id);

$commentErrors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'comment') {
    Auth::requireLogin();
    verify_csrf();
    $content = trim($_POST['content'] ?? '');
    if ($content === '') {
        $commentErrors[] = 'Comment cannot be empty.';
    } elseif (strlen($content) > 1000) {
        $commentErrors[] = 'Comment is too long.';
    } else {
        $commentModel->create($id, $userId, $content);
        set_flash('success', 'Comment added.');
        redirect(BASE_URL . "/post.php?id=$id");
    }
}
?>

<article class="single-post">
    <div class="container">
        <header class="single-post-header">
            <a href="<?= BASE_URL ?>/category.php?id=<?= $post['category_id'] ?>" class="category-badge"><?= e($post['category_name']) ?></a>
            <h1><?= e($post['title']) ?></h1>
            <div class="single-post-meta">
                <div class="author-info">
                    <?php if ($post['author_image']): ?>
                        <img src="<?= e($post['author_image']) ?>" alt="" class="avatar-sm">
                    <?php else: ?>
                        <div class="avatar-sm avatar-fallback"><?= get_initials($post['author_name']) ?></div>
                    <?php endif; ?>
                    <div>
                        <span class="post-author"><?= e($post['author_name']) ?></span>
                        <span class="post-date"><?= time_ago($post['created_at']) ?></span>
                    </div>
                </div>
            </div>
        </header>

        <?php if ($post['thumbnail']): ?>
            <img src="<?= e($post['thumbnail']) ?>" alt="<?= e($post['title']) ?>" class="single-post-thumbnail">
        <?php endif; ?>

        <div class="single-post-content">
            <?= parse_markdown($post['content']) ?>
        </div>

        <?php if ($tags): ?>
            <div class="post-tags">
                <?php foreach ($tags as $tag): ?>
                    <span class="tag-badge">#<?= e($tag['name']) ?></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="post-actions">
            <button class="action-btn <?= $isLiked ? 'liked' : '' ?>" onclick="toggleLike(<?= $id ?>)">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="<?= $isLiked ? 'currentColor' : 'none' ?>" stroke="currentColor" stroke-width="2">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                </svg>
                <span id="like-count-<?= $id ?>"><?= $likeCount ?></span>
            </button>
            <?php if ($userId): ?>
                <button class="action-btn <?= $isFavorited ? 'favorited' : '' ?>" onclick="toggleFavorite(<?= $id ?>)">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="<?= $isFavorited ? 'currentColor' : 'none' ?>" stroke="currentColor" stroke-width="2">
                        <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z" />
                    </svg>
                    <span><?= $isFavorited ? 'Saved' : 'Save' ?></span>
                </button>
            <?php endif; ?>
        </div>

        <section class="comments-section">
            <h3>Comments (<?= count($comments) ?>)</h3>

            <?php if ($userId): ?>
                <?php if (!empty($commentErrors)): ?>
                    <div class="form-errors" style="margin-bottom:16px">
                        <?php foreach ($commentErrors as $err): ?>
                            <p><?= e($err) ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <form method="POST" action="" class="comment-form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="comment">
                    <textarea name="content" class="form-textarea" rows="3" placeholder="Write a comment..."></textarea>
                    <button type="submit" class="btn btn-primary btn-sm">Post Comment</button>
                </form>
            <?php else: ?>
                <p class="comment-login-prompt"><a href="<?= BASE_URL ?>/login.php">Sign in</a> to leave a comment.</p>
            <?php endif; ?>

            <div class="comments-list">
                <?php foreach ($comments as $comment): ?>
                    <div class="comment-item">
                        <div class="comment-avatar">
                            <?php if ($comment['author_image']): ?>
                                <img src="<?= e($comment['author_image']) ?>" alt="">
                            <?php else: ?>
                                <div class="avatar-sm avatar-fallback"><?= get_initials($comment['author_name']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="comment-body">
                            <div class="comment-header">
                                <strong><?= e($comment['author_name']) ?></strong>
                                <span class="post-date"><?= time_ago($comment['created_at']) ?></span>
                            </div>
                            <p><?= nl2br(e($comment['content'])) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($comments)): ?>
                    <p class="no-comments">No comments yet. Be the first to share your thoughts.</p>
                <?php endif; ?>
            </div>
        </section>
    </div>
</article>

<?php require_once ROOT_PATH . '/includes/footer.php'; ?>