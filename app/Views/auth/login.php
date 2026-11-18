<?php
$logo = $settings['logo'] ?? null;
?>
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <title>Masuk · SAHABAT-NG</title>
    <link rel="icon" href="<?= favicon_url() ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { sans: ['Plus Jakarta Sans','ui-sans-serif','system-ui','sans-serif'] } } } }
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.2/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none !important}</style>
</head>
<body class="min-h-full bg-stone-100 font-sans text-stone-800 antialiased">
    <div class="flex min-h-screen">
        <div class="hidden w-1/2 flex-col justify-between bg-stone-900 p-10 lg:flex">
            <a href="<?= site_url('/') ?>" class="flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-600 text-lg text-white">
                    <i class="fa-solid fa-hands-holding-child"></i>
                </span>
                <span class="text-lg font-extrabold text-white">SAHABAT-NG</span>
            </a>
            <div class="max-w-md">
                <h1 class="text-3xl font-extrabold leading-tight text-white">Sistem pendamping pengasuhan dan pembinaan anak</h1>
                <p class="mt-4 text-sm leading-relaxed text-stone-300">
                    Kelola data anak asuh, penilaian karakter, akademik, dan laporan dalam satu tempat yang rapi dan mudah digunakan.
                </p>
                <ul class="mt-8 space-y-3 text-sm text-stone-300">
                    <li class="flex items-center gap-3"><i class="fa-solid fa-check text-emerald-400"></i> Penilaian karakter terstruktur</li>
                    <li class="flex items-center gap-3"><i class="fa-solid fa-check text-emerald-400"></i> Pencatatan akademik dan absensi</li>
                    <li class="flex items-center gap-3"><i class="fa-solid fa-check text-emerald-400"></i> Laporan perkembangan berkala</li>
                </ul>
            </div>
            <p class="text-xs text-stone-500">&copy; <?= date('Y') ?> <?= esc($settings['nama_lksa'] ?? 'SAHABAT-NG') ?></p>
        </div>

        <div class="flex w-full items-center justify-center px-5 py-10 lg:w-1/2">
            <div class="w-full max-w-md">
                <div class="mb-8 text-center lg:hidden">
                    <a href="<?= site_url('/') ?>" class="inline-flex items-center gap-2 text-emerald-700">
                        <i class="fa-solid fa-hands-holding-child text-xl"></i>
                        <span class="text-lg font-extrabold">SAHABAT-NG</span>
                    </a>
                </div>

                <div class="card p-7 sm:p-8">
                    <div class="mb-6 text-center">
                        <?php if ($logo): ?>
                            <img src="<?= base_url('uploads/' . $logo) ?>" alt="Logo" class="mx-auto mb-4 h-14 w-auto object-contain">
                        <?php else: ?>
                            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                                <i class="fa-solid fa-right-to-bracket text-lg"></i>
                            </div>
                        <?php endif; ?>
                        <h2 class="text-xl font-extrabold text-stone-900">Masuk ke akun</h2>
                        <p class="mt-1 text-sm text-stone-500">Gunakan akun yang terdaftar di sistem.</p>
                    </div>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="mb-5 flex items-start gap-2 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                            <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                            <span><?= esc(session()->getFlashdata('error')) ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="mb-5 flex items-start gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                            <i class="fa-solid fa-circle-check mt-0.5"></i>
                            <span><?= esc(session()->getFlashdata('success')) ?></span>
                        </div>
                    <?php endif; ?>

                    <form action="<?= site_url('auth/login') ?>" method="post" class="space-y-4" x-data="{ show: false }">
                        <?= csrf_field() ?>

                        <div>
                            <label for="username" class="mb-1 block text-sm font-medium text-stone-600">Nama pengguna</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-stone-400"><i class="fa-solid fa-user text-sm"></i></span>
                                <input type="text" id="username" name="username" value="<?= old('username') ?>" required autocomplete="username"
                                    class="w-full rounded-xl border border-stone-200 bg-stone-50 py-2.5 pl-10 pr-4 text-sm text-stone-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20"
                                    placeholder="Masukkan nama pengguna">
                            </div>
                        </div>

                        <div>
                            <label for="password" class="mb-1 block text-sm font-medium text-stone-600">Kata sandi</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-stone-400"><i class="fa-solid fa-lock text-sm"></i></span>
                                <input :type="show ? 'text' : 'password'" id="password" name="password" required autocomplete="current-password"
                                    class="w-full rounded-xl border border-stone-200 bg-stone-50 py-2.5 pl-10 pr-11 text-sm text-stone-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20"
                                    placeholder="Masukkan kata sandi">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-3 text-stone-400 hover:text-stone-600" :aria-label="show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'">
                                    <i class="fa-solid" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label for="akses" class="mb-1 block text-sm font-medium text-stone-600">Masuk sebagai</label>
                            <select id="akses" name="akses" class="input">
                                <?php foreach (['admin' => 'Admin / Pengurus', 'guru' => 'Guru / Pengajar', 'anak' => 'Anak Asuh'] as $value => $label): ?>
                                    <option value="<?= esc($value) ?>" <?= old('akses') === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" class="mt-2 flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-700 px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                            <i class="fa-solid fa-right-to-bracket"></i> Masuk
                        </button>
                    </form>
                </div>

                <p class="mt-6 text-center text-sm text-stone-500">
                    <a href="<?= site_url('/') ?>" class="font-semibold text-emerald-700 hover:text-emerald-800"><i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke beranda</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
