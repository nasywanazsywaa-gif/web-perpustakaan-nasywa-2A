<?php
/*
 * =============================================
 * SQL SETUP (run database.sql in phpMyAdmin):
 * =============================================
 *
 * CREATE DATABASE IF NOT EXISTS perpustakaan;
 * USE perpustakaan;
 *
 * CREATE TABLE IF NOT EXISTS buku (
 *   id INT AUTO_INCREMENT PRIMARY KEY,
 *   judul VARCHAR(255) NOT NULL,
 *   pengarang VARCHAR(255) NOT NULL,
 *   penerbit VARCHAR(255) NOT NULL,
 *   tahun_terbit YEAR NOT NULL,
 *   genre VARCHAR(100) NOT NULL,
 *   stok INT NOT NULL DEFAULT 0,
 *   sampul VARCHAR(255) DEFAULT NULL,
 *   deskripsi TEXT DEFAULT NULL,
 *   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
 * );
 *
 * CREATE TABLE IF NOT EXISTS user (
 *   id INT AUTO_INCREMENT PRIMARY KEY,
 *   username VARCHAR(50) UNIQUE NOT NULL,
 *   password VARCHAR(255) NOT NULL
 * );
 *
 * -- Default admin user (password: admin123)
 * INSERT INTO user (username, password)
 * VALUES ('admin', MD5('admin123'))
 * ON DUPLICATE KEY UPDATE username = username;
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Konfigurasi Database
$host     = 'localhost';
$user     = 'root';
$password = '';
$database = 'db_perpustakaan';

// Koneksi ke database (auto-create database jika belum ada)
try {
    $conn = @mysqli_connect($host, $user, $password, $database);
} catch (mysqli_sql_exception $e) {
    $conn = false;
}

if (!$conn) {
    // Coba koneksi ke server MySQL tanpa memilih database
    $conn_server = @mysqli_connect($host, $user, $password);
    if ($conn_server) {
        mysqli_query($conn_server, "CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        mysqli_close($conn_server);
        try {
            $conn = @mysqli_connect($host, $user, $password, $database);
        } catch (mysqli_sql_exception $e) {
            $conn = false;
        }
    }
}

if (!$conn) {
    die('<div style="font-family:sans-serif;padding:40px;color:#dc2626;background:#fef2f2;border:1px solid #fca5a5;border-radius:8px;margin:40px auto;max-width:500px;">
        <strong>Koneksi database gagal!</strong><br>
        Error: ' . mysqli_connect_error() . '<br><br>
        Pastikan:<br>
        &bull; XAMPP (Apache & MySQL) sudah berjalan<br>
        &bull; Database <strong>' . htmlspecialchars($database) . '</strong> sudah dibuat<br>
        &bull; Jalankan file <strong>db_perpustakaan.sql</strong> di phpMyAdmin
    </div>');
}

mysqli_set_charset($conn, 'utf8mb4');

// Inisialisasi Otomatis Tabel & Data Awal (jika tabel belum ada)
$check_user_table = mysqli_query($conn, "SHOW TABLES LIKE 'user'");
if ($check_user_table && mysqli_num_rows($check_user_table) === 0) {
    // 1. Buat Tabel buku
    mysqli_query($conn, "CREATE TABLE IF NOT EXISTS buku (
        id INT AUTO_INCREMENT PRIMARY KEY,
        judul VARCHAR(255) NOT NULL,
        pengarang VARCHAR(255) NOT NULL,
        penerbit VARCHAR(255) NOT NULL,
        tahun_terbit YEAR NOT NULL,
        genre VARCHAR(100) NOT NULL,
        stok INT NOT NULL DEFAULT 0,
        sampul VARCHAR(255) DEFAULT NULL,
        deskripsi TEXT DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // 2. Buat Tabel user
    mysqli_query($conn, "CREATE TABLE IF NOT EXISTS user (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // 3. Masukkan default user admin (password: admin123)
    $default_admin_pass = md5('admin123');
    mysqli_query($conn, "INSERT INTO user (username, password) VALUES ('admin', '$default_admin_pass') ON DUPLICATE KEY UPDATE username=username");

    // 4. Masukkan sampel data buku
    mysqli_query($conn, "INSERT INTO buku (judul, pengarang, penerbit, tahun_terbit, genre, stok, deskripsi) VALUES
        ('Laskar Pelangi', 'Andrea Hirata', 'Bentang Pustaka', 2005, 'Fiksi', 10, 'Novel tentang perjuangan anak-anak Belitung meraih mimpi.'),
        ('Bumi Manusia', 'Pramoedya Ananta Toer', 'Lentera Dipantara', 2005, 'Sejarah', 7, 'Kisah Minke di era kolonial Belanda yang penuh perjuangan.'),
        ('Atomic Habits', 'James Clear', 'Gramedia', 2019, 'Non-Fiksi', 15, 'Panduan membangun kebiasaan baik dan menghilangkan kebiasaan buruk.')");
}

// Konstanta Aplikasi
if (!defined('UPLOAD_DIR')) {
    define('UPLOAD_DIR', 'uploads/');
}
if (!defined('MAX_FILE_SIZE')) {
    define('MAX_FILE_SIZE', 2 * 1024 * 1024); // 2 MB (Sesuai ketentuan tugas)
}
if (!defined('ALLOWED_IMAGE_TYPES')) {
    define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
}
