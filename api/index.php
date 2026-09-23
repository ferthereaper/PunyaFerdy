<?php
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$rootDir = realpath(__DIR__ . '/..');
$filePath = $rootDir . $uri;

// 1. Tampilkan index.html di root jika membuka "/"
if ($uri === '/' || $uri === '') {
    $indexHtml = $rootDir . '/index.html';
    if (file_exists($indexHtml)) {
        header("Content-Type: text/html; charset=utf-8");
        readfile($indexHtml);
        exit;
    }
}

// 2. Jika mengakses folder (seperti /WebUMKM atau /Jobsheet-01)
if (is_dir($filePath)) {
    $dirIndexPhp = rtrim($filePath, '/') . '/index.php';
    $dirIndexHtml = rtrim($filePath, '/') . '/index.html';

    if (file_exists($dirIndexPhp)) {
        chdir(dirname($dirIndexPhp));
        header("Content-Type: text/html; charset=utf-8");
        require $dirIndexPhp;
        exit;
    } elseif (file_exists($dirIndexHtml)) {
        header("Content-Type: text/html; charset=utf-8");
        readfile($dirIndexHtml);
        exit;
    }
}

// 3. Jika mengakses file langsung (.php, .css, .js, .png, dll)
if (file_exists($filePath)) {
    if (str_ends_with($filePath, '.php')) {
        chdir(dirname($filePath));
        header("Content-Type: text/html; charset=utf-8");
        require $filePath;
        exit;
    } else {
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mimeTypes = [
            'css'  => 'text/css; charset=utf-8',
            'js'   => 'application/javascript; charset=utf-8',
            'png'  => 'image/png',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'svg'  => 'image/svg+xml',
            'json' => 'application/json'
        ];
        $mime = $mimeTypes[$ext] ?? mime_content_type($filePath);
        header("Content-Type: $mime");
        readfile($filePath);
        exit;
    }
}

http_response_code(404);
echo "404 Not Found";