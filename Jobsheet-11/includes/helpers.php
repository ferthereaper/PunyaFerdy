<?php
// Fungsi bantu umum. e() = escape output HTML untuk mencegah XSS.
// Gunakan setiap kali mencetak data dari database / $_GET / $_POST / $_SESSION ke HTML.

function e($value)
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}
