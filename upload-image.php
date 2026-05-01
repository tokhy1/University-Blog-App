<?php
require_once __DIR__ . '/config/database.php';

require_once ROOT_PATH . '/classes/Auth.php';
require_once ROOT_PATH . '/classes/Upload.php';
require_once ROOT_PATH . '/classes/Post.php';
require_once ROOT_PATH . '/includes/functions.php';

Auth::requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

$upload = new Upload();
$result = $upload->handle($_FILES['image'], UPLOAD_PATH . 'posts/');

if (!$result['success']) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['error' => $result['message']]);
    exit;
}

// If a post_id is sent, link the image to that post
$postId = intval($_POST['post_id'] ?? 0);
if ($postId > 0) {
    $postModel = new Post();
    $postModel->addImage($postId, $result['path']);
}

$url = str_replace(ROOT_PATH, BASE_URL, $result['path']);
header('Content-Type: application/json');
echo json_encode(['url' => $url]);
