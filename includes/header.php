<?php
require_once ROOT_PATH . '/includes/functions.php';

$auth = new Auth();
if (!$auth->isLoggedIn()) {
    $auth->checkRememberMe();
}

$flash = get_flash();
$categories = (new Category())->getAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php e($pageTitle ?? 'Blog'); ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>

<body>

    <?php if ($flash): ?>
        <div class="flash flash-<?= $flash['type'] ?>">
            <?= e($flash['message']) ?>
            <button class="flash-close" onclick="this.parentElement.remove()">&times;</button>
        </div>
    <?php endif; ?>

    <header class="site-header">
        <div class="container header-inner">
            <a href="<?= BASE_URL ?>" class="logo">Inkwell</a>

            <nav class="main-nav">
                <a href="<?= BASE_URL ?>">Home</a>
                <?php foreach ($categories as $cat): ?>
                    <a href="<?= BASE_URL ?>/category.php?id=<?= $cat['id'] ?>"><?= e($cat['name']) ?></a>
                <?php endforeach; ?>
            </nav>

            <div class="header-actions">
                <?php if ($auth->isLoggedIn()): ?>
                    <?php if ($auth->isAdmin()): ?>
                        <a href="<?= BASE_URL ?>/admin/" class="btn btn-sm btn-outline">Dashboard</a>
                    <?php endif; ?>
                    <a href="<?= BASE_URL ?>/my-posts.php" class="btn btn-sm btn-outline">My Posts</a>
                    <a href="<?= BASE_URL ?>/favorites.php" class="btn btn-sm btn-outline">Favorites</a>
                    <div class="user-menu">
                        <?php if (!empty($_SESSION['user_image'])): ?>
                            <img src="<?= e($_SESSION['user_image']) ?>" alt="" class="avatar-sm">
                        <?php else: ?>
                            <div class="avatar-sm avatar-fallback"><?= get_initials($_SESSION['user_name'] ?? 'U') ?></div>
                        <?php endif; ?>
                        <div class="user-dropdown">
                            <a href="<?= BASE_URL ?>/profile.php">Profile</a>
                            <a href="<?= BASE_URL ?>/create-post.php">New Post</a>
                            <hr>
                            <a href="<?= BASE_URL ?>/logout.php" class="text-danger">Logout</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/login.php" class="btn btn-sm btn-outline">Login</a>
                    <a href="<?= BASE_URL ?>/register.php" class="btn btn-sm btn-primary">Sign Up</a>
                <?php endif; ?>
            </div>

            <button class="mobile-toggle" onclick="document.querySelector('.main-nav').classList.toggle('open')">
                <span></span><span></span><span></span>
            </button>
        </div>
    </header>

    <main>