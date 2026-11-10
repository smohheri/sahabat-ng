<?php
$logo = $settings['logo'] ?? null;
?>
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <title><?= esc($title ?? 'Login') ?></title>
    <link rel="icon" href="<?= favicon_url() ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: { sans: ['Plus Jakarta Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'] } } },
        };
    </script>
</head>
<body class="h-full bg-slate-950 font-sans antialiased">
    <div class="grid min-h-full lg:grid-cols-2">
        <div class="relative hidden overflow-hidden bg-slate-900 lg:block">
            <div class="absolute inset-0 bg-indigo-600/20"></div>
            <div class="absolute -left-24 top-1/3 h-72 w-72 rounded-full bg-indigo-600/30 blur-3xl"></div>
            <div class="absolute -bottom-24 right-0 h-80 w-80 rounded-full bg-sky-500/20 blur-3xl"></div>

            <div class="relative flex h-full flex-col justify-between p-12 xl:p-16">
                <a href="<?= site_url('/') ?>" class="flex items-center gap-3">
                    <?php if (! empty($logo)): ?>
                        <img src="<?= upload_url('logos', $logo) ?>" alt="Logo" class="h-12 w-12 rounded-xl object-cover ring-1 ring-white/20">
                    <?php else: ?>
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/10 text-sm font-bold text-white ring-1 ring-white/20">SA</div>
                    <?php endif; ?>
                    <span class="leading-tight text-white">
                        <span class="block text-base font-extrabold"><?= esc($settings['nama_lksa'] ?? 'LKSA Harapan Bangsa') ?></span>
                        <span class="block text-[11px] font-semibold uppercase tracking-[0.2em] text-indigo-300">SAHABAT-NG</span>
                    </span>
                </a>

                <div class="max-w-md">
                    <p class="mb-4 inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-indigo-200 ring-1 ring-white/10">
                        <i class="fa-solid fa-shield-halved"></i> Sistem Administrasi Terpadu
                    </p>
                    <h1 class="text-4xl font-extrabold leading-tight text-white xl:text-5xl">
                        Satu panel untuk<br>semua kebutuhan<br>
                        <span class="text-indigo-400">LKSA Anda.</span>
                    </h1>
                    <p class="mt-5 text-sm leading-relaxed text-slate-300">
                        Kelola data anak, pengurus, akademik, penilaian karakter, hingga laporan dalam satu sistem yang rapi dan aman.
                    </p>

                    <div class="mt-8 grid grid-cols-2 gap-3 text-sm">
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                            <i class="fa-solid fa-children mb-2 text-indigo-400"></i>
                            <p class="font-semibold text-white">Data Terpadu</p>
                            <p class="mt-1 text-xs text-slate-400">Anak, pengurus, dan akun terhubung.</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                            <i class="fa-solid fa-chart-line mb-2 text-sky-400"></i>
                            <p class="font-semibold text-white">Penilaian Karakter</p>
                            <p class="mt-1 text-xs text-slate-400">Pantau perkembangan secara berkala.</p>
                        </div>
                    </div>
                </div>

                <p class="text-xs text-slate-400">&copy; <?= date('Y') ?> <?= esc($settings['nama_lksa'] ?? 'LKSA Harapan Bangsa') ?></p>
            </div>
        </div>

        <div class="flex items-center justify-center bg-slate-50 p-6 sm:p-10">
            <div class="w-full max-w-md">
                <div class="mb-8 flex items-center gap-3 lg:hidden">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-600 text-sm font-bold text-white">SA</div>
                    <div>
                        <p class="text-sm font-extrabold text-slate-900"><?= esc($settings['nama_lksa'] ?? 'LKSA Harapan Bangsa') ?></p>
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-indigo-600">SAHABAT-NG</p>
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-7 shadow-xl shadow-slate-200/60 sm:p-9">
                    <h2 class="text-2xl font-extrabold text-slate-900">Selamat datang</h2>
                    <p class="mt-1 text-sm text-slate-500">Masuk untuk melanjutkan ke panel Anda.</p>

                    <?= view('partials/flash') ?>

                    <form method="post" action="<?= site_url('login') ?>" class="mt-6 space-y-4" x-data="{ showPassword: false }">
                        <?= csrf_field() ?>

                        <div>
                            <label for="username" class="mb-1.5 block text-sm font-semibold text-slate-700">Username</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400"><i class="fa-solid fa-user"></i></span>
                                <input id="username" name="username" type="text" value="<?= esc(old('username')) ?>" required autofocus autocomplete="username"
                                       class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-3 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20"
                                       placeholder="Masukkan username">
                            </div>
                        </div>

                        <div>
                            <label for="password" class="mb-1.5 block text-sm font-semibold text-slate-700">Password</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400"><i class="fa-solid fa-lock"></i></span>
                                <input id="password" name="password" type="password" :type="showPassword ? 'text' : 'password'" required autocomplete="current-password"
                                       class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-10 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20"
                                       placeholder="Masukkan password">
                                <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600" tabindex="-1">
                                    <i class="fa-solid" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label for="akses" class="mb-1.5 block text-sm font-semibold text-slate-700">Masuk sebagai</label>
                            <select id="akses" name="akses" required
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20">
                                <?php foreach ($aksesOptions as $value => $label): ?>
                                    <option value="<?= esc($value) ?>" <?= old('akses') === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" class="mt-2 flex w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-600/25 transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            <i class="fa-solid fa-right-to-bracket"></i> Masuk
                        </button>
                    </form>
                </div>

                <p class="mt-6 text-center text-sm text-slate-500">
                    <a href="<?= site_url('/') ?>" class="font-semibold text-indigo-600 hover:text-indigo-700"><i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke beranda</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
