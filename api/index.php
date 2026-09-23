<?php
$request = $_SERVER['REQUEST_URI'];
$filePath = __DIR__ . '/..' . strtok($request, '?');

if (file_exists($filePath) && !is_dir($filePath)) {
    $mime = mime_content_type($filePath);
    if (str_ends_with($filePath, '.css')) $mime = 'text/css';
    if (str_ends_with($filePath, '.js')) $mime = 'application/javascript';
    
    if (str_ends_with($filePath, '.php')) {
        require $filePath;
    } else {
        header("Content-Type: $mime");
        readfile($filePath);
    }
} else {
    $indexPath = rtrim($filePath, '/') . '/index.php';
    if (file_exists($indexPath)) {
        require $indexPath;
    } else {
        http_response_code(404);
        echo "404 Not Found";
    }
}