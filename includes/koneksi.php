<?php
$host = "db.rmvsvouottsiokychbok.supabase.co";
$port = "5432";
$db   = "postgres";
$user = "postgres";
$pass = "@Ferdy270105";

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}
