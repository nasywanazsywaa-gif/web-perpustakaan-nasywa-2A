<?php
require_once 'config/koneksi.php';

// Destroy session
$_SESSION = [];
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}
session_destroy();

// Delete remember me cookie
if (isset($_COOKIE['remember_username'])) {
    setcookie('remember_username', '', time() - 3600, '/');
}

header('Location: index.php');
exit;
