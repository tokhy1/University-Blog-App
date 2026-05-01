<?php
require_once ROOT_PATH . '/includes/functions.php';

$auth = new Auth();
if (!$auth->isLoggedIn()) {
    $auth->checkRememberMe();
}

$flash = get_flash();
$categories = (new Category())->getAll();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php e($pageTitle ?? 'Inkwell'); ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <meta name="csrf-token" content="<?= $_SESSION['csrf_token'] ?>">
    <script>
        var BASE_URL = '<?= BASE_URL ?>';
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
</head>

<body>

    <?php if ($flash): ?>
        <div class="flash flash-<?= $flash['type'] ?>">
            <span><?= $flash['type'] === 'success' ? '&#10003;' : '&#10007;' ?> <?= e($flash['message']) ?></span>
            <button class="flash-close" onclick="this.parentElement.remove()">&times;</button>
        </div>
    <?php endif; ?>

    <header class="site-header">
        <div class="container header-inner">
            <a href="<?= BASE_URL ?>" class="logo">Inkwell</a>

            <nav class="main-nav" id="mainNav">
                <a href="<?= BASE_URL ?>">Home</a>
                <?php foreach (array_slice($categories, 0, 5) as $cat): ?>
                    <a href="<?= BASE_URL ?>/category.php?id=<?= $cat['id'] ?>"><?= e($cat['name']) ?></a>
                <?php endforeach; ?>
            </nav>

            <div class="header-actions">
                <?php if ($auth->isLoggedIn()): ?>
                    <?php if ($auth->isAdmin()): ?>
                        <a href="<?= BASE_URL ?>/admin/" class="btn btn-sm btn-dark">Dashboard</a>
                    <?php endif; ?>
                    <a href="<?= BASE_URL ?>/create-post.php" class="btn btn-sm btn-primary">Write</a>
                    <div class="user-menu" id="userMenu">
                        <?php if (!empty($_SESSION['user_image'])): ?>
                            <img src="<?= e($_SESSION['user_image']) ?>" alt="" class="avatar-sm">
                        <?php else: ?>
                            <div class="avatar-sm avatar-fallback"><?= get_initials($_SESSION['user_name'] ?? 'U') ?></div>
                        <?php endif; ?>
                        <div class="user-dropdown" id="userDropdown">
                            <div class="dropdown-header">
                                <strong><?= e($_SESSION['user_name'] ?? '') ?></strong>
                                <span class="text-muted"><?= ucfirst($_SESSION['user_role'] ?? 'user') ?></span>
                            </div>
                            <hr>
                            <a href="<?= BASE_URL ?>/profile.php">Profile</a>
                            <a href="<?= BASE_URL ?>/my-posts.php">My Posts</a>
                            <a href="<?= BASE_URL ?>/favorites.php">Favorites</a>
                            <hr>
                            <a href="<?= BASE_URL ?>/logout.php" class="text-danger">Sign Out</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/login.php" class="btn btn-sm btn-ghost">Sign In</a>
                    <a href="<?= BASE_URL ?>/register.php" class="btn btn-sm btn-primary">Get Started</a>
                <?php endif; ?>
            </div>

            <button class="mobile-toggle" id="mobileToggle" aria-label="Menu">
                <span></span><span></span><span></span>
            </button>
        </div>
    </header>

    <main>