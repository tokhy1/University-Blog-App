<?php
require_once __DIR__ . '/config/database.php';

require_once ROOT_PATH . '/classes/Auth.php';
require_once ROOT_PATH . '/includes/functions.php';

$auth = new Auth();
$auth->logout();
set_flash('success', 'You have been logged out.');
redirect(BASE_URL . '/login.php');
