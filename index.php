<?php
require_once 'config/koneksi.php';

// If already logged in, redirect to dashboard
if (isset($_SESSION['username'])) {
    header('Location: dashboard.php');
    exit;
}

// Ambil username dari cookie jika Remember Me pernah dicentang
$remembered_username = $_COOKIE['remember_username'] ?? '';

$error = isset($_GET['error']) ? (int)$_GET['error'] : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login &mdash; Perpustakaan</title>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-[Outfit] bg-gray-50 min-h-screen">
  <div class="min-h-screen flex">

    <!-- Left: Login Form -->
    <div class="flex-1 flex items-center justify-center px-6 py-12 bg-white">
      <div class="w-full max-w-md">

        <!-- Logo -->
        <div class="mb-8">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-[#465fff] rounded-xl flex items-center justify-center">
              <i class="fa-solid fa-book-open text-white text-lg"></i>
            </div>
            <span class="text-xl font-bold text-gray-900">Perpustakaan</span>
          </div>
          <h1 class="text-2xl font-bold text-gray-900 mb-1">Selamat Datang!</h1>
          <p class="text-gray-500 text-sm">Masuk ke sistem manajemen perpustakaan</p>
        </div>

        <!-- Error Alert -->
        <?php if ($error === 1): ?>
        <div class="mb-5 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center gap-2 text-sm">
          <i class="fa-solid fa-circle-exclamation"></i>
          <span>Username atau password salah. Silakan coba lagi.</span>
        </div>
        <?php elseif ($error === 2): ?>
        <div class="mb-5 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center gap-2 text-sm">
          <i class="fa-solid fa-circle-exclamation"></i>
          <span>Sesi telah berakhir. Silakan login kembali.</span>
        </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form method="POST" action="proses_login.php" class="space-y-5">

          <!-- Username -->
          <div>
            <label for="username" class="block text-sm font-medium text-gray-700 mb-1">
              Username
            </label>
            <div class="relative">
              <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                <i class="fa-solid fa-user text-sm"></i>
              </span>
              <input
                type="text"
                id="username"
                name="username"
                value="<?= htmlspecialchars($remembered_username) ?>"
                required
                autocomplete="username"
                placeholder="Masukkan username"
                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#465fff]/20 focus:border-[#465fff] transition-colors"
              >
            </div>
          </div>

          <!-- Password -->
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
              Password
            </label>
            <div class="relative">
              <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                <i class="fa-solid fa-lock text-sm"></i>
              </span>
              <input
                type="password"
                id="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="Masukkan password"
                class="w-full pl-10 pr-10 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#465fff]/20 focus:border-[#465fff] transition-colors"
              >
              <button
                type="button"
                onclick="togglePassword()"
                class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600"
                tabindex="-1"
              >
                <i id="eye-icon" class="fa-solid fa-eye text-sm"></i>
              </button>
            </div>
          </div>

          <!-- Remember Me -->
          <div class="flex items-center gap-2">
            <input
              type="checkbox"
              id="remember"
              name="remember"
              value="1"
              <?= !empty($remembered_username) ? 'checked' : '' ?>
              class="w-4 h-4 rounded border-gray-300 text-[#465fff] focus:ring-[#465fff]/20 cursor-pointer"
            >
            <label for="remember" class="text-sm text-gray-600 cursor-pointer select-none">
              Ingat saya selama 30 hari
            </label>
          </div>

          <!-- Submit -->
          <button
            type="submit"
            class="w-full bg-[#465fff] hover:bg-[#3641f5] text-white py-2.5 rounded-lg font-medium transition-colors flex items-center justify-center gap-2"
          >
            <i class="fa-solid fa-right-to-bracket"></i>
            Masuk
          </button>

        </form>

        <!-- Footer hint -->
        <p class="mt-6 text-center text-xs text-gray-400">
          Default: admin / admin123
        </p>

      </div>
    </div>

    <!-- Right: Branding Panel -->
    <div class="hidden lg:flex flex-1 bg-gradient-to-br from-[#465fff] to-[#2a31d8] items-center justify-center p-12 relative overflow-hidden">
      <!-- Decorative circles -->
      <div class="absolute top-[-80px] right-[-80px] w-64 h-64 bg-white/10 rounded-full"></div>
      <div class="absolute bottom-[-60px] left-[-60px] w-48 h-48 bg-white/10 rounded-full"></div>
      <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-white/5 rounded-full"></div>

      <div class="relative z-10 text-center text-white max-w-sm">
        <div class="w-24 h-24 bg-white/20 rounded-3xl flex items-center justify-center mx-auto mb-6 backdrop-blur-sm">
          <i class="fa-solid fa-book-open text-5xl text-white"></i>
        </div>
        <h2 class="text-3xl font-bold mb-4">Sistem Manajemen<br>Perpustakaan</h2>
        <p class="text-blue-200 text-sm leading-relaxed">
          Kelola koleksi buku perpustakaan dengan mudah dan efisien.
          Tambah, ubah, dan hapus data buku dalam satu platform terintegrasi.
        </p>

        <div class="mt-8 grid grid-cols-3 gap-4 text-center">
          <div class="bg-white/10 rounded-xl p-3 backdrop-blur-sm">
            <div class="text-2xl font-bold">📚</div>
            <div class="text-xs text-blue-200 mt-1">Koleksi Buku</div>
          </div>
          <div class="bg-white/10 rounded-xl p-3 backdrop-blur-sm">
            <div class="text-2xl font-bold">🔍</div>
            <div class="text-xs text-blue-200 mt-1">Pencarian</div>
          </div>
          <div class="bg-white/10 rounded-xl p-3 backdrop-blur-sm">
            <div class="text-2xl font-bold">📊</div>
            <div class="text-xs text-blue-200 mt-1">Statistik</div>
          </div>
        </div>
      </div>
    </div>

  </div>

  <script>
    function togglePassword() {
      const pwd = document.getElementById('password');
      const icon = document.getElementById('eye-icon');
      if (pwd.type === 'password') {
        pwd.type = 'text';
        icon.className = 'fa-solid fa-eye-slash text-sm';
      } else {
        pwd.type = 'password';
        icon.className = 'fa-solid fa-eye text-sm';
      }
    }
  </script>
</body>
</html>
