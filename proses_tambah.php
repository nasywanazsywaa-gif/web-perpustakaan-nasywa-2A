<?php
require_once 'config/koneksi.php';

// Auth check
if (!isset($_SESSION['username'])) {
    header('Location: index.php?error=2');
    exit;
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: tambah_buku.php');
    exit;
}

// Get & sanitize inputs
$judul        = mysqli_real_escape_string($conn, trim($_POST['judul'] ?? ''));
$pengarang    = mysqli_real_escape_string($conn, trim($_POST['pengarang'] ?? ''));
$penerbit     = mysqli_real_escape_string($conn, trim($_POST['penerbit'] ?? ''));
$tahun_terbit = (int)($_POST['tahun_terbit'] ?? date('Y'));
$genre        = mysqli_real_escape_string($conn, trim($_POST['genre'] ?? ''));
$stok         = (int)($_POST['stok'] ?? 0);
$deskripsi    = mysqli_real_escape_string($conn, trim($_POST['deskripsi'] ?? ''));

if (empty($judul) || empty($pengarang) || empty($penerbit) || empty($genre)) {
    header('Location: tambah_buku.php?error=empty');
    exit;
}

// Handle file upload
$sampul_filename = '';
if (isset($_FILES['sampul']) && $_FILES['sampul']['error'] === UPLOAD_ERR_OK) {
    // Validasi ukuran file (maks. 2MB sesuai ketentuan tugas)
    if ($_FILES['sampul']['size'] > MAX_FILE_SIZE) {
        header('Location: tambah_buku.php?error=size');
        exit;
    }

    // Validasi ekstensi dan MIME type file
    $ext          = strtolower(pathinfo($_FILES['sampul']['name'], PATHINFO_EXTENSION));
    $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($ext, $allowed_exts)) {
        header('Location: tambah_buku.php?error=upload');
        exit;
    }

    $file_type = mime_content_type($_FILES['sampul']['tmp_name']);
    if (!in_array($file_type, ALLOWED_IMAGE_TYPES)) {
        header('Location: tambah_buku.php?error=upload');
        exit;
    }

    // Generate nama file unik
    $sampul_filename = uniqid('buku_', true) . '.' . $ext;

    // Pastikan direktori upload ada
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }

    if (!move_uploaded_file($_FILES['sampul']['tmp_name'], UPLOAD_DIR . $sampul_filename)) {
        header('Location: tambah_buku.php?error=upload');
        exit;
    }
}

// INSERT into database
$sql = "INSERT INTO buku (judul, pengarang, penerbit, tahun_terbit, genre, stok, sampul, deskripsi)
        VALUES ('$judul', '$pengarang', '$penerbit', $tahun_terbit, '$genre', $stok, '$sampul_filename', '$deskripsi')";

if (mysqli_query($conn, $sql)) {
    header('Location: dashboard.php?status=tambah_berhasil');
    exit;
} else {
    // Jika query gagal, hapus file gambar yang sempat diupload
    if (!empty($sampul_filename) && file_exists(UPLOAD_DIR . $sampul_filename)) {
        unlink(UPLOAD_DIR . $sampul_filename);
    }
    header('Location: tambah_buku.php?error=db');
    exit;
}
