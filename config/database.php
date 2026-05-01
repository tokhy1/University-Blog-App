<?php
session_start();

define('BASE_URL', '/blog-system');
define('ROOT_PATH', dirname(__DIR__));
define('UPLOAD_PATH', ROOT_PATH . '/assets/uploads/');
define('UPLOAD_URL', BASE_URL . '/assets/uploads/');
