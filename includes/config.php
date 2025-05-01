<?php
session_start();
define('SITE_NAME', 'Pendataan Kehadiran RemaCo');
date_default_timezone_set('Asia/Jakarta');

// Set folder permission warning
if (!is_writable(__DIR__ . '/../data')) {
    @mkdir(__DIR__ . '/data', 0777, true);
}

if (!is_writable(__DIR__ . '/data')) {
    die('<div class="alert alert-danger">Folder "data" tidak bisa ditulisi. Mohon set permission folder menjadi 0777.</div>');
}
