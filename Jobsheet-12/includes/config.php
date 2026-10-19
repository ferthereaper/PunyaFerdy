<?php
// Awalan URL aplikasi, dihitung dari nama folder aplikasi ini.
//   Vercel : /Jobsheet-12/api/index.php            -> BASE_URL = "/Jobsheet-12"
//   XAMPP  : /PunyaFerdy/Jobsheet-12/api/index.php -> BASE_URL = "/PunyaFerdy/Jobsheet-12"
//   Root   : /api/index.php (folder ini jadi root)  -> BASE_URL = ""
// Dengan begini link tidak patah ketika folder dipindah / diganti namanya.
if (!defined('BASE_URL')) {
    $appDir = basename(dirname(__DIR__));
    $path   = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
    $pos    = strpos($path, '/' . $appDir . '/');
    $base   = $pos === false ? '' : substr($path, 0, $pos + strlen($appDir) + 1);
    // Hanya karakter aman untuk URL (mencegah injeksi lewat REQUEST_URI).
    define('BASE_URL', preg_replace('#[^\w./~-]#', '', $base));
    unset($appDir, $path, $pos, $base);
}
