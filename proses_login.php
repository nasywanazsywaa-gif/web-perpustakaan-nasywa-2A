<?php
require_once 'config/koneksi.php';

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');
$remember = isset($_POST['remember']) ? (int)$_POST['remember'] : 0;

if (empty($username) || empty($password)) {
    header('Location: index.php?error=1');
    exit;
}

// Sanitize inputs
$username_esc = mysqli_real_escape_string($conn, $username);

// Query user berdasarkan username
$sql    = "SELECT id, username, password FROM user WHERE username = '$username_esc' LIMIT 1";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) === 1) {
    $user     = mysqli_fetch_assoc($result);
    $is_valid = false;

    // Verifikasi dengan password_verify() (Bcrypt) atau MD5 (fallback kompatibilitas)
    if (password_verify($password, $user['password'])) {
        $is_valid = true;
    } elseif ($user['password'] === md5($password)) {
        $is_valid = true;
        // Otomatis upgrade password hash lama (MD5) ke Bcrypt
        $new_hash = password_hash($password, PASSWORD_BCRYPT);
        $user_id  = (int)$user['id'];
        mysqli_query($conn, "UPDATE user SET password = '$new_hash' WHERE id = $user_id");
    }

    if ($is_valid) {
        // Set session
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_id']  = $user['id'];

        // Set cookie jika remember me dicentang (30 hari)
        if ($remember) {
            setcookie('remember_username', $user['username'], time() + (30 * 24 * 60 * 60), '/');
        }

        header('Location: dashboard.php');
        exit;
    }
}

header('Location: index.php?error=1');
exit;
