<?php
require_once __DIR__ . '/config/database.php';

require_once ROOT_PATH . '/classes/Auth.php';
require_once ROOT_PATH . '/classes/Favorite.php';
require_once ROOT_PATH . '/includes/functions.php';

Auth::requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_POST['csrf_token'] ?? '';
if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
    http_response_code(403);
    exit;
}

$userId = Auth::id();
$postId = intval($_POST['post_id'] ?? 0);

if ($postId === 0) {
    http_response_code(400);
    exit;
}

$favoriteModel = new Favorite();
$favorited = $favoriteModel->toggle($postId, $userId);

header('Content-Type: application/json');
echo json_encode(['favorited' => $favorited]);
