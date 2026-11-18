<?php
$currentPath = trim(service('request')->getPath(), '/');
$navLink = static function (string $path) use ($currentPath): string {
    $active = $currentPath === $path;
    return $active ? 'text-emerald-700 font-semibold' : 'text-stone-600 hover:text-emerald-700';
};
?>
<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= esc($settings['deskripsi_singkat'] ?? 'SAHABAT-NG - Sistem pendamping pengasuhan dan pembinaan anak.') ?>">
    <title><?= esc($title ?? 'SAHABAT-NG') ?></title>
    <link rel="icon" href="<?= favicon_url() ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'] }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.2/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none !important}</style>
    <?= $this->renderSection('head') ?>
</head>
<body class="min-h-full bg-stone-50 font-sans text-stone-800 antialiased">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[60] focus:rounded-lg focus:bg-emerald-700 focus:px-4 focus:py-2 focus:text-white">Lewati ke konten</a>

    <header x-data="{ open: false, scrolled: false }" @scroll.window="scrolled = window.scrollY > 8" :class="scrolled ? 'border-stone-200 bg-white shadow-sm' : 'border-transparent bg-white'" class="sticky top-0 z-50 border-b transition">
        <div class="mx-auto flex w-full max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
            <a href="<?= site_url('/') ?>" class="flex items-center gap-3">
                <?php if (! empty($settings['logo'])): ?>
                    <img src="<?= base_url('uploads/' . $settings['logo']) ?>" alt="Logo" class="h-10 w-10 rounded-xl object-cover">
                <?php else: ?>
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-700 text-white"><i class="fa-solid fa-hands-holding-child"></i></span>
                <?php endif; ?>
                <span class="text-sm font-extrabold text-stone-900 sm:text-base"><?= esc($settings['nama_lksa'] ?? 'SAHABAT-NG') ?></span>
            </a>

            <nav class="hidden items-center gap-6 text-sm font-medium md:flex">
                <a href="<?= site_url('/') ?>" class="<?= $navLink('') ?>">Beranda</a>
                <a href="<?= site_url('donasi') ?>" class="<?= $navLink('donasi') ?>">Donasi</a>
                <a href="<?= site_url('license') ?>" class="<?= $navLink('license') ?>">Lisensi</a>
            </nav>

            <div class="hidden items-center gap-2 md:flex">
                <a href="<?= site_url('login') ?>" class="btn-primary">
                    <i class="fa-solid fa-right-to-bracket text-xs"></i> Masuk
                </a>
            </div>

            <button type="button" @click="open = !open" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-stone-200 text-stone-600 hover:bg-stone-50 md:hidden" :aria-expanded="open.toString()" aria-label="Menu">
                <i class="fa-solid" :class="open ? 'fa-xmark' : 'fa-bars'"></i>
            </button>
        </div>

        <div x-cloak x-show="open" @click.outside="open = false" class="border-t border-stone-100 bg-white md:hidden">
            <nav class="mx-auto flex max-w-7xl flex-col gap-1 px-4 py-3 text-sm font-medium sm:px-6">
                <a href="<?= site_url('/') ?>" class="rounded-lg px-3 py-2 <?= $navLink('') ?>">Beranda</a>
                <a href="<?= site_url('donasi') ?>" class="rounded-lg px-3 py-2 <?= $navLink('donasi') ?>">Donasi</a>
                <a href="<?= site_url('license') ?>" class="rounded-lg px-3 py-2 <?= $navLink('license') ?>">Lisensi</a>
                <a href="<?= site_url('login') ?>" class="btn-primary mt-1 w-full">Masuk</a>
            </nav>
        </div>
    </header>

    <main id="main-content">
        <?= $content ?>
    </main>

    <footer class="border-t border-stone-200 bg-stone-900 text-stone-300">
        <div class="mx-auto grid w-full max-w-7xl gap-10 px-4 py-12 sm:px-6 lg:grid-cols-3 lg:px-8">
            <div>
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-700 text-white"><i class="fa-solid fa-hands-holding-child"></i></span>
                    <span class="text-base font-extrabold text-white"><?= esc($settings['nama_lksa'] ?? 'SAHABAT-NG') ?></span>
                </div>
                <p class="mt-4 max-w-sm text-sm leading-relaxed text-stone-400"><?= esc($settings['deskripsi_singkat'] ?? 'Sistem pendamping pengasuhan dan pembinaan anak asuh.') ?></p>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-white">Navigasi</h3>
                <ul class="mt-4 space-y-2 text-sm">
                    <li><a href="<?= site_url('/') ?>" class="hover:text-white">Beranda</a></li>
                    <li><a href="<?= site_url('donasi') ?>" class="hover:text-white">Donasi</a></li>
                    <li><a href="<?= site_url('license') ?>" class="hover:text-white">Lisensi</a></li>
                    <li><a href="<?= site_url('login') ?>" class="hover:text-white">Masuk</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-white">Kontak</h3>
                <ul class="mt-4 space-y-3 text-sm text-stone-400">
                    <?php if (! empty($settings['alamat'])): ?>
                        <li class="flex gap-3"><i class="fa-solid fa-location-dot mt-1 text-emerald-400"></i><span><?= esc($settings['alamat']) ?></span></li>
                    <?php endif; ?>
                    <?php if (! empty($settings['no_telp'])): ?>
                        <li class="flex gap-3"><i class="fa-solid fa-phone mt-1 text-emerald-400"></i><span><?= esc($settings['no_telp']) ?></span></li>
                    <?php endif; ?>
                    <?php if (! empty($settings['email'])): ?>
                        <li class="flex gap-3"><i class="fa-solid fa-envelope mt-1 text-emerald-400"></i><span><?= esc($settings['email']) ?></span></li>
                    <?php endif; ?>
                    <?php if (! empty($settings['tahun_berdiri'])): ?>
                        <li class="flex gap-3"><i class="fa-solid fa-calendar mt-1 text-emerald-400"></i><span>Berdiri sejak <?= esc($settings['tahun_berdiri']) ?></span></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <div class="border-t border-stone-800">
            <div class="mx-auto flex w-full max-w-7xl flex-col items-center justify-between gap-2 px-4 py-4 text-xs text-stone-500 sm:flex-row sm:px-6 lg:px-8">
                <p>&copy; <?= date('Y') ?> <?= esc($settings['nama_lksa'] ?? 'SAHABAT-NG') ?>. Seluruh hak cipta dilindungi.</p>
                <p>Dikelola dengan SAHABAT-NG</p>
            </div>
        </div>
    </footer>

    <?php if (! empty($settings['whatsapp'])): ?>
        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $settings['whatsapp']) ?>" target="_blank" rel="noopener" class="fixed bottom-5 right-5 z-40 flex h-12 w-12 items-center justify-center rounded-full bg-emerald-600 text-xl text-white shadow-lg transition hover:bg-emerald-700" aria-label="Hubungi via WhatsApp">
            <i class="fa-brands fa-whatsapp"></i>
        </a>
    <?php endif; ?>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
