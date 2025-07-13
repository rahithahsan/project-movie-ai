<?php
/* router.php – only for the PHP dev‐server
   Send static files directly, everything else → public/index.php */

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . '/public' . $path;

if ($path !== '/' && is_file($file)) {
    return false;                 // = “let the server serve the file”
}

require __DIR__ . '/public/index.php';