<?php
require_once 'config/koneksi.php';

// Auth check
if (!isset($_SESSION['username'])) {
    header('Location: index.php?error=2');
    exit;
}

// Get book ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: dashboard.php');
    exit;
}

// Fetch book to get cover filename
$sql    = "SELECT sampul FROM buku WHERE id = $id LIMIT 1";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) === 0) {
    header('Location: dashboard.php?status=error');
    exit;
}

$buku = mysqli_fetch_assoc($result);

// Delete cover image file if exists
if (!empty($buku['sampul']) && file_exists(UPLOAD_DIR . $buku['sampul'])) {
    unlink(UPLOAD_DIR . $buku['sampul']);
}

// DELETE from database
$sql_del = "DELETE FROM buku WHERE id = $id";

if (mysqli_query($conn, $sql_del)) {
    header('Location: dashboard.php?status=hapus_berhasil');
    exit;
} else {
    header('Location: dashboard.php?status=error');
    exit;
}
