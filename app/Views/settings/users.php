<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manajemen User — Toko Kayu Kontan Jaya</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-amber-50 min-h-screen font-sans">
<nav class="bg-amber-900 text-white px-6 py-3 flex items-center justify-between shadow-lg">
  <div class="flex items-center gap-3">
    <svg class="w-8 h-8" viewBox="0 0 32 32" fill="none">
      <rect width="32" height="32" rx="6" fill="#d97706"/>
      <path d="M8 22 L16 8 L24 22 Z" fill="white" opacity="0.9"/>
      <rect x="10" y="18" width="12" height="2" rx="1" fill="#78350f"/>
    </svg>
    <span class="font-bold text-lg">Toko Kayu Kontan Jaya</span>
  </div>
  <div class="flex items-center gap-4 text-sm">
    <a href="<?= base_url('dashboard') ?>"  class="hover:text-amber-200">Dashboard</a>
    <a href="<?= base_url('products') ?>"   class="hover:text-amber-200">Produk</a>
    <a href="<?= base_url('inventori') ?>"  class="hover:text-amber-200">Inventori</a>
    <a href="<?= base_url('pos') ?>"        class="hover:text-amber-200">Kasir</a>
    <a href="<?= base_url('reports') ?>"    class="hover:text-amber-200">Laporan</a>
    <a href="<?= base_url('settings') ?>"   class="bg-amber-700 px-3 py-1 rounded-full font-semibold">⚙️ Pengaturan</a>
    <a href="<?= base_url('logout') ?>"     class="hover:text-amber-200">Logout</a>
  </div>
</nav>

<div class="max-w-4xl mx-auto px-6 py-8">
  <h1 class="text-3xl font-bold text-amber-900 mb-8">Pengaturan</h1>

  <?php if (session('success')): ?>
  <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
    ✅ <?= esc(session('success')) ?>
  </div>
  <?php endif; ?>
  <?php if (session('errors')): ?>
  <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
    <?php foreach (session('errors') as $e): ?>
    <div>❌ <?= esc($e) ?></div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <!-- Tabs -->
  <div class="flex gap-2 mb-6">
    <a href="<?= base_url('settings') ?>"
       class="px-4 py-2 rounded-xl text-sm font-semibold bg-white text-gray-600 border border-gray-200 hover:bg-amber-50">
      🏪 Profil Toko
    </a>
    <a href="<?= base_url('settings/users') ?>"
       class="px-4 py-2 rounded-xl text-sm font-semibold bg-amber-700 text-white">
      👥 Manajemen User
    </a>
  </div>

  <!-- Daftar User -->
  <div class="bg-white rounded-2xl shadow-sm border border-amber-100 overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-amber-100">
      <h2 class="font-bold text-gray-800">Daftar User</h2>
    </div>
    <table class="w-full text-sm">
      <thead class="bg-amber-50 text-gray-500 text-xs uppercase">
        <tr>
          <th class="px-6 py-3 text-left">Nama</th>
          <th class="px-6 py-3 text-left">Email</th>
          <th class="px-6 py-3 text-center">Role</th>
          <th class="px-6 py-3 text-center">Status</th>
          <th class="px-6 py-3 text-center">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        <?php foreach ($users as $u): ?>
        <tr class="hover:bg-amber-50">
          <td class="px-6 py-3 font-medium text-gray-800"><?= esc($u['name']) ?></td>
          <td class="px-6 py-3 text-gray-500"><?= esc($u['email']) ?></td>
          <td class="px-6 py-3 text-center">
            <span class="px-2 py-0.5 rounded-full text-xs font-semibold
              <?= $u['role'] === 'owner' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' ?>">
              <?= $u['role'] === 'owner' ? 'Owner' : 'Kasir' ?>
            </span>
          </td>
          <td class="px-6 py-3 text-center">
            <span class="px-2 py-0.5 rounded-full text-xs
              <?= $u['is_active'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' ?>">
              <?= $u['is_active'] ? 'Aktif' : 'Nonaktif' ?>
            </span>
          </td>
          <td class="px-6 py-3 text-center">
            <?php if ($u['id'] !== session('user_id')): ?>
            <form method="POST" action="<?= base_url('settings/users/' . $u['id'] . '/toggle') ?>" style="display:inline">
              <?= csrf_field() ?>
              <button type="submit" class="text-xs px-3 py-1 rounded-full
                <?= $u['is_active'] ? 'bg-red-100 text-red-600 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' ?>">
                <?= $u['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>
              </button>
            </form>
            <?php else: ?>
            <span class="text-xs text-gray-400">(Anda)</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Form Tambah User -->
  <div class="bg-white rounded-2xl shadow-sm border border-amber-100 p-8">
    <h2 class="font-bold text-lg text-gray-800 mb-6">Tambah User Baru</h2>
    <form method="POST" action="<?= base_url('settings/users/store') ?>">
      <?= csrf_field() ?>
      <div class="grid grid-cols-2 gap-6">
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
          <input type="text" name="name" value="<?= esc(old('name')) ?>"
            class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-amber-500 focus:outline-none"
            placeholder="Nama user" required/>
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
          <input type="email" name="email" value="<?= esc(old('email')) ?>"
            class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-amber-500 focus:outline-none"
            placeholder="email@contoh.com" required/>
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
          <input type="password" name="password"
            class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-amber-500 focus:outline-none"
            placeholder="Min. 6 karakter" required/>
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Role</label>
          <select name="role" class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-amber-500 focus:outline-none">
            <option value="cashier">Kasir</option>
            <option value="owner">Owner</option>
          </select>
        </div>
      </div>
      <div class="mt-6">
        <button type="submit"
          class="px-8 py-3 bg-amber-700 hover:bg-amber-800 text-white font-bold rounded-xl transition-colors">
          + Tambah User
        </button>
      </div>
    </form>
  </div>
</div>
</body>
</html>