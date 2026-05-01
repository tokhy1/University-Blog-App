<?php
require_once ROOT_PATH . '/classes/Auth.php';
require_once ROOT_PATH . '/classes/Post.php';
require_once ROOT_PATH . '/includes/functions.php';

Auth::requireLogin();
verify_csrf();

$id = intval($_POST['id'] ?? 0);
$postModel = new Post();
$post = $postModel->getById($id);

if (!$post) {
    set_flash('error', 'Post not found.');
    redirect(BASE_URL . '/my-posts.php');
}

// Allow own posts, or admin can delete any
if ($post['user_id'] !== Auth::id() && !Auth::isAdmin()) {
    set_flash('error', 'You do not have permission.');
    redirect(BASE_URL . '/my-posts.php');
}

$postModel->delete($id);
set_flash('success', 'Post deleted.');

if (Auth::isAdmin() && isset($_POST['from']) && $_POST['from'] === 'admin') {
    redirect(BASE_URL . '/admin/posts.php');
}

redirect(BASE_URL . '/my-posts.php');
