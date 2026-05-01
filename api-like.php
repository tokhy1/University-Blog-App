<?php
require_once ROOT_PATH . '/classes/Auth.php';
require_once ROOT_PATH . '/classes/Like.php';

Auth::requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$userId = Auth::id();
$postId = intval($_POST['post_id'] ?? 0);

if ($postId === 0) {
    http_response_code(400);
    exit;
}

$likeModel = new Like();
$liked = $likeModel->toggle($postId, $userId);
$count = $likeModel->getCount($postId);

header('Content-Type: application/json');
echo json_encode(['liked' => $liked, 'count' => $count]);
