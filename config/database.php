<?php

session_start();

define('ROOT_PATH', realpath(__DIR__ . '/..'));

define('BASE_URL', '/blog-system');
define('UPLOAD_PATH', ROOT_PATH . '/assets/uploads/');
define('UPLOAD_URL', BASE_URL . '/assets/uploads/');