<?php
require_once 'config/koneksi.php';

// Auth check
if (!isset($_SESSION['username'])) {
    header('Location: index.php?error=2');
    exit;
}

$username = htmlspecialchars($_SESSION['username']);

$genres = ['Fiksi', 'Non-Fiksi', 'Ilmu Pengetahuan', 'Sejarah', 'Teknologi', 'Lainnya'];
$error  = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Buku &mdash; Perpustakaan</title>
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
         class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-50 hover:text-gray-900 font-medium text-sm transition-colors">
        <i class="fa-solid fa-gauge-high w-4 text-center"></i>
        Dashboard
      </a>

      <a href="tambah_buku.php"
         class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-[#465fff]/10 text-[#465fff] font-medium text-sm transition-colors">
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
        <div class="flex items-center gap-3">
          <a href="dashboard.php" class="text-gray-400 hover:text-gray-600 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
          </a>
          <div>
            <h1 class="text-lg font-semibold text-gray-900">Tambah Buku</h1>
            <p class="text-sm text-gray-500">Isi formulir untuk menambahkan buku baru</p>
          </div>
        </div>
      </div>
    </header>

    <main class="p-6">

      <?php if ($error === 'upload'): ?>
      <div class="mb-5 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center gap-2 text-sm">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span>Format file sampul tidak valid. Gunakan JPG, PNG, GIF, atau WebP.</span>
      </div>
      <?php elseif ($error === 'size'): ?>
      <div class="mb-5 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center gap-2 text-sm">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span>Ukuran file terlalu besar. Maksimal ukuran gambar adalah 2 MB.</span>
      </div>
      <?php elseif ($error === 'empty'): ?>
      <div class="mb-5 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center gap-2 text-sm">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span>Semua kolom bertanda bintang (*) wajib diisi.</span>
      </div>
      <?php elseif ($error === 'db'): ?>
      <div class="mb-5 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center gap-2 text-sm">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span>Gagal menyimpan data ke database. Silakan coba lagi.</span>
      </div>
      <?php endif; ?>

      <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 max-w-3xl">
        <form method="POST" action="proses_tambah.php" enctype="multipart/form-data" class="space-y-5">

          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- Judul -->
            <div class="md:col-span-2">
              <label for="judul" class="block text-sm font-medium text-gray-700 mb-1">Judul Buku <span class="text-red-500">*</span></label>
              <input type="text" id="judul" name="judul" required placeholder="Masukkan judul buku"
                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#465fff]/20 focus:border-[#465fff] transition-colors">
            </div>

            <!-- Pengarang / Penulis -->
            <div>
              <label for="pengarang" class="block text-sm font-medium text-gray-700 mb-1">Penulis / Pengarang <span class="text-red-500">*</span></label>
              <input type="text" id="pengarang" name="pengarang" required placeholder="Nama penulis/pengarang"
                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#465fff]/20 focus:border-[#465fff] transition-colors">
            </div>

            <!-- Penerbit -->
            <div>
              <label for="penerbit" class="block text-sm font-medium text-gray-700 mb-1">Penerbit <span class="text-red-500">*</span></label>
              <input type="text" id="penerbit" name="penerbit" required placeholder="Nama penerbit"
                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#465fff]/20 focus:border-[#465fff] transition-colors">
            </div>

            <!-- Tahun Terbit -->
            <div>
              <label for="tahun_terbit" class="block text-sm font-medium text-gray-700 mb-1">Tahun Terbit <span class="text-red-500">*</span></label>
              <input type="number" id="tahun_terbit" name="tahun_terbit" required
                min="1900" max="<?= date('Y') ?>" placeholder="<?= date('Y') ?>"
                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#465fff]/20 focus:border-[#465fff] transition-colors">
            </div>

            <!-- Genre / Kategori -->
            <div>
              <label for="genre" class="block text-sm font-medium text-gray-700 mb-1">Kategori / Genre <span class="text-red-500">*</span></label>
              <select id="genre" name="genre" required
                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#465fff]/20 focus:border-[#465fff] transition-colors bg-white">
                <option value="">-- Pilih Kategori --</option>
                <?php foreach ($genres as $g): ?>
                <option value="<?= $g ?>"><?= $g ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <!-- Stok & Status Ketersediaan -->
            <div>
              <label for="stok" class="block text-sm font-medium text-gray-700 mb-1">Stok (Ketersediaan) <span class="text-red-500">*</span></label>
              <input type="number" id="stok" name="stok" required min="0" value="0" placeholder="0"
                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#465fff]/20 focus:border-[#465fff] transition-colors">
              <p class="text-xs text-gray-400 mt-1">Stok &gt; 0 = Tersedia, Stok 0 = Dipinjam</p>
            </div>

            <!-- Sampul -->
            <div>
              <label for="sampul" class="block text-sm font-medium text-gray-700 mb-1">Sampul Buku (.jpg / .png)</label>
              <div class="flex items-start gap-4">
                <div id="preview-wrapper" class="hidden">
                  <img id="preview-img" src="#" alt="Preview" class="w-16 h-20 object-cover rounded-lg border border-gray-200">
                </div>
                <div class="flex-1">
                  <input type="file" id="sampul" name="sampul" accept="image/jpeg,image/png,image/gif,image/webp"
                    onchange="previewImage(this)"
                    class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#465fff]/20 focus:border-[#465fff] transition-colors file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-[#465fff]/10 file:text-[#465fff] hover:file:bg-[#465fff]/20">
                  <p class="text-xs text-gray-400 mt-1">JPG, PNG, GIF, WebP (maks. 2MB)</p>
                </div>
              </div>
            </div>

            <!-- Deskripsi -->
            <div class="md:col-span-2">
              <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
              <textarea id="deskripsi" name="deskripsi" rows="4" placeholder="Deskripsi singkat tentang buku..."
                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#465fff]/20 focus:border-[#465fff] transition-colors resize-none"></textarea>
            </div>
          </div>

          <!-- Buttons -->
          <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
            <button type="submit"
              class="inline-flex items-center gap-2 bg-[#465fff] hover:bg-[#3641f5] text-white px-5 py-2 rounded-lg font-medium text-sm transition-colors">
              <i class="fa-solid fa-floppy-disk"></i>
              Simpan Buku
            </button>
            <a href="dashboard.php"
              class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2 rounded-lg font-medium text-sm transition-colors">
              <i class="fa-solid fa-xmark"></i>
              Batal
            </a>
          </div>

        </form>
      </div>
    </main>
  </div>
</div>

<script>
  function previewImage(input) {
    const wrapper = document.getElementById('preview-wrapper');
    const img = document.getElementById('preview-img');
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = e => {
        img.src = e.target.result;
        wrapper.classList.remove('hidden');
      };
      reader.readAsDataURL(input.files[0]);
    } else {
      wrapper.classList.add('hidden');
    }
  }
</script>
</body>
</html>
