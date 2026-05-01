<?php
require_once dirname(__DIR__) . '/config/database.php';
require_once ROOT_PATH . '/classes/Auth.php';
require_once ROOT_PATH . '/classes/User.php';
require_once ROOT_PATH . '/includes/functions.php';

Auth::requireAdmin();
verify_csrf();

$id = intval($_POST['id'] ?? 0);
$userModel = new User();
$user = $userModel->getById($id);

if (!$user) {
    set_flash('error', 'User not found.');
    redirect(BASE_URL . '/admin/users.php');
}

if ($user['role'] === 'admin') {
    set_flash('error', 'Cannot delete admin users.');
    redirect(BASE_URL . '/admin/users.php');
}

if ($id === Auth::id()) {
    set_flash('error', 'You cannot delete your own account.');
    redirect(BASE_URL . '/admin/users.php');
}

$userModel->delete($id);
set_flash('success', 'User deleted.');
redirect(BASE_URL . '/admin/users.php');
