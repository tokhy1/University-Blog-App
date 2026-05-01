<?php
require_once ROOT_PATH . '/includes/functions.php';
Auth::requireAdmin();

$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Admin') ?> - Admin</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>

<body class="admin-body">

    <?php if ($flash): ?>
        <div class="flash flash-<?= $flash['type'] ?>">
            <?= e($flash['message']) ?>
            <button class="flash-close" onclick="this.parentElement.remove()">&times;</button>
        </div>
    <?php endif; ?>

    <aside class="admin-sidebar">
        <div class="sidebar-brand">
            <a href="<?= BASE_URL ?>/admin/">Inkwell</a>
            <span class="sidebar-badge">Admin</span>
        </div>

        <nav class="sidebar-nav">
            <a href="<?= BASE_URL ?>/admin/" class="sidebar-link <?= ($activePage ?? '') === 'dashboard' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7" rx="1" />
                    <rect x="14" y="3" width="7" height="7" rx="1" />
                    <rect x="3" y="14" width="7" height="7" rx="1" />
                    <rect x="14" y="14" width="7" height="7" rx="1" />
                </svg>
                Dashboard
            </a>
            <a href="<?= BASE_URL ?>/admin/posts.php" class="sidebar-link <?= ($activePage ?? '') === 'posts' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                </svg>
                Posts
            </a>
            <a href="<?= BASE_URL ?>/admin/categories.php" class="sidebar-link <?= ($activePage ?? '') === 'categories' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z" />
                </svg>
                Categories
            </a>
            <a href="<?= BASE_URL ?>/admin/users.php" class="sidebar-link <?= ($activePage ?? '') === 'users' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
                Users
            </a>
            <a href="<?= BASE_URL ?>/admin/comments.php" class="sidebar-link <?= ($activePage ?? '') === 'comments' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                </svg>
                Comments
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="<?= BASE_URL ?>" class="sidebar-link">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                    <polyline points="10 17 15 12 10 7" />
                    <line x1="15" y1="12" x2="3" y2="12" />
                </svg>
                Back to Site
            </a>
            <a href="<?= BASE_URL ?>/logout.php" class="sidebar-link">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                    <polyline points="16 17 21 12 16 7" />
                    <line x1="21" y1="12" x2="9" y2="12" />
                </svg>
                Logout
            </a>
        </div>
    </aside>

    <div class="admin-main">
        <div class="admin-topbar">
            <h1 class="admin-page-title"><?= e($pageTitle ?? '') ?></h1>
            <div class="admin-user">
                <?= e($_SESSION['user_name'] ?? '') ?>
            </div>
        </div>
        <div class="admin-content">