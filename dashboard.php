<?php
require_once 'config/koneksi.php';

// Auth check
if (!isset($_SESSION['username'])) {
    header('Location: index.php?error=2');
    exit;
}

$username = htmlspecialchars($_SESSION['username']);

// Fetch all books
$sql_books  = "SELECT * FROM buku ORDER BY created_at DESC";
$result_books = mysqli_query($conn, $sql_books);
$books      = [];
while ($row = mysqli_fetch_assoc($result_books)) {
    $books[] = $row;
}

// Stats
$total_buku = count($books);
$total_stok = array_sum(array_column($books, 'stok'));
$total_genre = count(array_unique(array_column($books, 'genre')));

// Flash messages
$status = $_GET['status'] ?? '';
$flash  = '';
if ($status === 'tambah_berhasil') {
    $flash = ['type' => 'success', 'msg' => '<i class="fa-solid fa-circle-check"></i> Buku berhasil ditambahkan!'];
} elseif ($status === 'edit_berhasil') {
    $flash = ['type' => 'success', 'msg' => '<i class="fa-solid fa-circle-check"></i> Buku berhasil diperbarui!'];
} elseif ($status === 'hapus_berhasil') {
    $flash = ['type' => 'success', 'msg' => '<i class="fa-solid fa-circle-check"></i> Buku berhasil dihapus!'];
} elseif ($status === 'error') {
    $flash = ['type' => 'error', 'msg' => '<i class="fa-solid fa-circle-exclamation"></i> Terjadi kesalahan. Silakan coba lagi.'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard &mdash; Perpustakaan</title>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-[Outfit] bg-gray-50">
<div class="flex h-screen bg-gray-50">

  <!-- Sidebar -->
  <aside class="w-64 bg-white border-r border-gray-200 flex flex-col fixed h-full z-40">
    <!-- Logo -->
    <div class="p-6 border-b border-gray-100">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 bg-[#465fff] rounded-xl flex items-center justify-center shrink-0">
          <i class="fa-solid fa-book-open text-white text-sm"></i>
        </div>
        <div>
          <span class="font-bold text-gray-900 text-sm block leading-tight">Perpustakaan</span>
          <span class="text-xs text-gray-400">Management System</span>
        </div>
      </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 p-4 space-y-1">
      <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 mb-2">Menu Utama</p>

      <a href="dashboard.php"
         class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-[#465fff]/10 text-[#465fff] font-medium text-sm transition-colors">
        <i class="fa-solid fa-gauge-high w-4 text-center"></i>
        Dashboard
      </a>

      <a href="tambah_buku.php"
         class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-50 hover:text-gray-900 font-medium text-sm transition-colors">
        <i class="fa-solid fa-plus-circle w-4 text-center"></i>
        Tambah Buku
      </a>
    </nav>

    <!-- User Info + Logout -->
    <div class="p-4 border-t border-gray-100">
      <div class="flex items-center gap-3 px-3 py-2 mb-2">
        <div class="w-8 h-8 bg-[#465fff] rounded-full flex items-center justify-center shrink-0">
          <span class="text-white text-xs font-bold"><?= strtoupper(substr($username, 0, 1)) ?></span>
        </div>
        <div class="min-w-0">
          <p class="text-sm font-medium text-gray-900 truncate"><?= $username ?></p>
          <p class="text-xs text-gray-400">Administrator</p>
        </div>
      </div>
      <a href="logout.php"
         class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-red-500 hover:bg-red-50 font-medium text-sm transition-colors w-full">
        <i class="fa-solid fa-right-from-bracket w-4 text-center"></i>
        Logout
      </a>
    </div>
  </aside>

  <!-- Main Content -->
  <div class="flex-1 ml-64 overflow-auto">

    <!-- Header -->
    <header class="bg-white border-b border-gray-200 px-6 py-4 sticky top-0 z-30">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-lg font-semibold text-gray-900">Dashboard</h1>
          <p class="text-sm text-gray-500">Selamat datang, <?= $username ?>!</p>
        </div>
        <a href="tambah_buku.php"
           class="inline-flex items-center gap-2 bg-[#465fff] hover:bg-[#3641f5] text-white px-4 py-2 rounded-lg font-medium text-sm transition-colors">
          <i class="fa-solid fa-plus"></i>
          Tambah Buku
        </a>
      </div>
    </header>

    <main class="p-6 space-y-6">

      <!-- Flash Message -->
      <?php if ($flash): ?>
      <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
           class="<?= $flash['type'] === 'success' ? 'bg-green-50 border border-green-200 text-green-700' : 'bg-red-50 border border-red-200 text-red-700' ?> px-4 py-3 rounded-lg flex items-center justify-between gap-2 text-sm">
        <span class="flex items-center gap-2"><?= $flash['msg'] ?></span>
        <button @click="show = false" class="text-current opacity-60 hover:opacity-100">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>
      <?php endif; ?>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
          <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
              <i class="fa-solid fa-book text-[#465fff]"></i>
            </div>
            <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-full">Total</span>
          </div>
          <p class="text-3xl font-bold text-gray-900"><?= $total_buku ?></p>
          <p class="text-sm text-gray-500 mt-1">Judul Buku</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
          <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center">
              <i class="fa-solid fa-layer-group text-purple-500"></i>
            </div>
            <span class="text-xs font-medium text-purple-600 bg-purple-50 px-2 py-1 rounded-full">Stok</span>
          </div>
          <p class="text-3xl font-bold text-gray-900"><?= $total_stok ?></p>
          <p class="text-sm text-gray-500 mt-1">Total Stok Buku</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
          <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center">
              <i class="fa-solid fa-tags text-amber-500"></i>
            </div>
            <span class="text-xs font-medium text-amber-600 bg-amber-50 px-2 py-1 rounded-full">Genre</span>
          </div>
          <p class="text-3xl font-bold text-gray-900"><?= $total_genre ?></p>
          <p class="text-sm text-gray-500 mt-1">Kategori Genre</p>
        </div>
      </div>

      <!-- Book Table -->
      <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
          <div>
            <h2 class="text-base font-semibold text-gray-900">Daftar Buku</h2>
            <p class="text-sm text-gray-500 mt-0.5"><?= $total_buku ?> buku tersedia</p>
          </div>
        </div>

        <div class="overflow-x-auto">
          <?php if (empty($books)): ?>
          <div class="text-center py-16 text-gray-400">
            <i class="fa-solid fa-book-open text-5xl mb-4 opacity-30"></i>
            <p class="font-medium">Belum ada buku</p>
            <p class="text-sm mt-1">Klik "Tambah Buku" untuk menambahkan buku pertama</p>
          </div>
          <?php else: ?>
          <table class="w-full text-sm">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider text-xs w-10">No</th>
                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider text-xs w-16">Sampul</th>
                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider text-xs">Judul</th>
                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider text-xs">Pengarang</th>
                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider text-xs">Penerbit</th>
                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider text-xs">Tahun</th>
                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider text-xs">Genre</th>
                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider text-xs">Stok</th>
                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider text-xs">Status</th>
                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider text-xs">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <?php foreach ($books as $i => $buku): 
                // Logika Bisnis & Validasi: Percabangan if-else penentuan label status ketersediaan (sesuai ketentuan tugas)
                $stok_buku = (int)$buku['stok'];
                if ($stok_buku > 0) {
                    $status_label = 'Tersedia';
                    $status_class = 'bg-green-50 text-green-700 border border-green-200';
                    $status_icon  = 'fa-circle-check';
                } else {
                    $status_label = 'Dipinjam';
                    $status_class = 'bg-red-50 text-red-700 border border-red-200';
                    $status_icon  = 'fa-circle-xmark';
                }
              ?>
              <tr class="hover:bg-gray-50/50 transition-colors">
                <td class="px-4 py-4 text-gray-500"><?= $i + 1 ?></td>
                <td class="px-4 py-4">
                  <?php if (!empty($buku['sampul']) && file_exists(UPLOAD_DIR . $buku['sampul'])): ?>
                    <img src="<?= UPLOAD_DIR . htmlspecialchars($buku['sampul']) ?>"
                         alt="<?= htmlspecialchars($buku['judul']) ?>"
                         class="w-10 h-14 object-cover rounded-md border border-gray-100">
                  <?php else: ?>
                    <div class="w-10 h-14 bg-gray-100 rounded-md flex items-center justify-center border border-gray-200">
                      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                      </svg>
                    </div>
                  <?php endif; ?>
                </td>
                <td class="px-4 py-4">
                  <p class="font-medium text-gray-900 max-w-[200px] truncate"><?= htmlspecialchars($buku['judul']) ?></p>
                </td>
                <td class="px-4 py-4 text-gray-700"><?= htmlspecialchars($buku['pengarang']) ?></td>
                <td class="px-4 py-4 text-gray-700"><?= htmlspecialchars($buku['penerbit']) ?></td>
                <td class="px-4 py-4 text-gray-700"><?= htmlspecialchars($buku['tahun_terbit']) ?></td>
                <td class="px-4 py-4">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                    <?= htmlspecialchars($buku['genre']) ?>
                  </span>
                </td>
                <td class="px-4 py-4 font-medium text-gray-700">
                  <?= $stok_buku ?>
                </td>
                <td class="px-4 py-4">
                  <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold <?= $status_class ?>">
                    <i class="fa-solid <?= $status_icon ?> text-[10px]"></i>
                    <?= $status_label ?>
                  </span>
                </td>
                <td class="px-4 py-4">
                  <div class="flex items-center gap-2">
                    <a href="edit_buku.php?id=<?= (int)$buku['id'] ?>"
                       class="inline-flex items-center gap-1 bg-amber-50 hover:bg-amber-100 text-amber-700 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors">
                      <i class="fa-solid fa-pen text-xs"></i> Edit
                    </a>
                    <a href="hapus_buku.php?id=<?= (int)$buku['id'] ?>"
                       onclick="return confirm('Yakin ingin menghapus buku ini?')"
                       class="inline-flex items-center gap-1 bg-red-50 hover:bg-red-100 text-red-600 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors">
                      <i class="fa-solid fa-trash text-xs"></i> Hapus
                    </a>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <?php endif; ?>
        </div>
      </div>

    </main>
  </div>
</div>
</body>
</html>
