<?php
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$rootDir = realpath(__DIR__ . '/..');
$filePath = $rootDir . $uri;

// Jika mengakses root "/", tampilkan index.html dari root
if ($uri === '/' || $uri === '') {
    $indexHtml = $rootDir . '/index.html';
    if (file_exists($indexHtml)) {
        header("Content-Type: text/html");
        readfile($indexHtml);
        exit;
    }
}

// Jika mengakses file langsung
if (file_exists($filePath) && !is_dir($filePath)) {
    if (str_ends_with($filePath, '.php')) {
        chdir(dirname($filePath));
        require $filePath;
        exit;
    } else {
        $mime = mime_content_type($filePath);
        if (str_ends_with($filePath, '.css')) $mime = 'text/css';
        if (str_ends_with($filePath, '.js')) $mime = 'application/javascript';
        header("Content-Type: $mime");
        readfile($filePath);
        exit;
    }
}

// Jika mengakses direktori (misal /WebUMKM atau /Jobsheet-01), cari index.php/index.html
if (is_dir($filePath)) {
    $dirIndexPhp = rtrim($filePath, '/') . '/index.php';
    $dirIndexHtml = rtrim($filePath, '/') . '/index.html';

    if (file_exists($dirIndexPhp)) {
        chdir(dirname($dirIndexPhp));
        require $dirIndexPhp;
        exit;
    } elseif (file_exists($dirIndexHtml)) {
        header("Content-Type: text/html");
        readfile($dirIndexHtml);
        exit;
    }
}

http_response_code(404);
echo "404 Not Found";