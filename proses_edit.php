<?php
require_once 'config/koneksi.php';

// Auth check
if (!isset($_SESSION['username'])) {
    header('Location: index.php?error=2');
    exit;
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

// Get & sanitize inputs
$id           = (int)($_POST['id'] ?? 0);
$sampul_lama  = trim($_POST['sampul_lama'] ?? '');
$judul        = mysqli_real_escape_string($conn, trim($_POST['judul'] ?? ''));
$pengarang    = mysqli_real_escape_string($conn, trim($_POST['pengarang'] ?? ''));
$penerbit     = mysqli_real_escape_string($conn, trim($_POST['penerbit'] ?? ''));
$tahun_terbit = (int)($_POST['tahun_terbit'] ?? date('Y'));
$genre        = mysqli_real_escape_string($conn, trim($_POST['genre'] ?? ''));
$stok         = (int)($_POST['stok'] ?? 0);
$deskripsi    = mysqli_real_escape_string($conn, trim($_POST['deskripsi'] ?? ''));

if ($id <= 0 || empty($judul) || empty($pengarang) || empty($penerbit) || empty($genre)) {
    header("Location: edit_buku.php?id=$id&error=empty");
    exit;
}

// Handle new file upload
$sampul_baru = $sampul_lama; // keep old by default

if (isset($_FILES['sampul']) && $_FILES['sampul']['error'] === UPLOAD_ERR_OK) {
    // Validasi ukuran file (maks. 2MB sesuai ketentuan tugas)
    if ($_FILES['sampul']['size'] > MAX_FILE_SIZE) {
        header("Location: edit_buku.php?id=$id&error=size");
        exit;
    }

    // Validasi ekstensi dan MIME type file
    $ext          = strtolower(pathinfo($_FILES['sampul']['name'], PATHINFO_EXTENSION));
    $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($ext, $allowed_exts)) {
        header("Location: edit_buku.php?id=$id&error=upload");
        exit;
    }

    $file_type = mime_content_type($_FILES['sampul']['tmp_name']);
    if (!in_array($file_type, ALLOWED_IMAGE_TYPES)) {
        header("Location: edit_buku.php?id=$id&error=upload");
        exit;
    }

    // Generate nama file unik
    $sampul_baru = uniqid('buku_', true) . '.' . $ext;

    // Pastikan direktori upload ada
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }

    if (!move_uploaded_file($_FILES['sampul']['tmp_name'], UPLOAD_DIR . $sampul_baru)) {
        header("Location: edit_buku.php?id=$id&error=upload");
        exit;
    }

    // Hapus file gambar lama jika ada dan berbeda
    if (!empty($sampul_lama) && $sampul_lama !== $sampul_baru && file_exists(UPLOAD_DIR . $sampul_lama)) {
        unlink(UPLOAD_DIR . $sampul_lama);
    }
}

$sampul_baru_esc = mysqli_real_escape_string($conn, $sampul_baru);

// UPDATE query
$sql = "UPDATE buku SET
    judul        = '$judul',
    pengarang    = '$pengarang',
    penerbit     = '$penerbit',
    tahun_terbit = $tahun_terbit,
    genre        = '$genre',
    stok         = $stok,
    sampul       = '$sampul_baru_esc',
    deskripsi    = '$deskripsi'
  WHERE id = $id";

if (mysqli_query($conn, $sql)) {
    header('Location: dashboard.php?status=edit_berhasil');
    exit;
} else {
    header("Location: edit_buku.php?id=$id&error=db");
    exit;
}
