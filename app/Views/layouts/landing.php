<?php
$currentPath = trim(service('request')->getPath(), '/');
$navLink = static function (string $path) use ($currentPath): string {
    $active = $currentPath === $path;

    return $active ? 'text-emerald-600' : 'text-slate-600 hover:text-emerald-600';
};

$namaLksa  = esc($settings['nama_lksa'] ?? 'LKSA Harapan Bangsa');
$alamat    = ! empty($settings['alamat']) ? esc($settings['alamat']) : '';
$logo      = $settings['logo'] ?? null;

// Sosial media dari tabel pengaturan.
$socials = [];
foreach (['facebook', 'twitter', 'instagram', 'youtube', 'linkedin'] as $key) {
    if (! empty($settings[$key])) {
        $socials[$key] = $settings[$key];
    }
}

$wa = nomor_wa((string) ($settings['whatsapp'] ?? $settings['no_telp'] ?? ''));
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="description" content="<?= esc($settings['nama_lksa'] ?? 'LKSA Harapan Bangsa') ?> - Lembaga Kesejahteraan Sosial Anak">
    <title><?= esc($title ?? 'Beranda') ?></title>
    <link rel="icon" href="<?= favicon_url() ?>">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.2/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
                },
            },
        };
    </script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
    <?= $this->renderSection('head') ?>
</head>
<body class="bg-white font-sans text-slate-700 antialiased" x-data="{ mobileMenu: false }">

    <header class="sticky top-0 z-40 border-b border-emerald-100 bg-white/90 backdrop-blur">
        <div class="mx-auto flex h-20 w-full max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
            <a href="<?= site_url('/') ?>" class="flex items-center gap-3">
                <?php if (! empty($logo)): ?>
                    <img src="<?= upload_url('logos', $logo) ?>" alt="Logo" class="h-11 w-11 rounded-xl object-cover ring-1 ring-emerald-100">
                <?php else: ?>
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-600 text-sm font-bold text-white">SA</div>
                <?php endif; ?>
                <span class="leading-tight">
                    <span class="block text-base font-extrabold text-slate-900"><?= $namaLksa ?></span>
                    <span class="block text-[11px] font-medium uppercase tracking-[0.18em] text-emerald-600">SAHABAT-NG</span>
                </span>
            </a>

            <nav class="hidden items-center gap-7 text-sm font-semibold md:flex">
                <a href="<?= site_url('/') ?>#beranda" class="text-slate-600 transition hover:text-emerald-600">Beranda</a>
                <a href="<?= site_url('/') ?>#tentang" class="text-slate-600 transition hover:text-emerald-600">Tentang</a>
                <a href="<?= site_url('/') ?>#program" class="text-slate-600 transition hover:text-emerald-600">Program</a>
                <a href="<?= site_url('/') ?>#fasilitas" class="text-slate-600 transition hover:text-emerald-600">Fasilitas</a>
                <a href="<?= site_url('/') ?>#galeri" class="text-slate-600 transition hover:text-emerald-600">Galeri</a>
                <a href="<?= site_url('lisensi') ?>" class="<?= $navLink('lisensi') ?> transition">Lisensi</a>
                <a href="<?= site_url('donasi') ?>" class="<?= $navLink('donasi') ?> transition">Donasi</a>
            </nav>

            <div class="flex items-center gap-2">
                <?php if (session()->get('is_logged_in')): ?>
                    <a href="<?= role_home((string) session()->get('role')) ?>" class="hidden rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-emerald-600/20 transition hover:bg-emerald-700 sm:inline-flex">Buka Panel</a>
                <?php else: ?>
                    <a href="<?= site_url('login') ?>" class="hidden rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-emerald-600/20 transition hover:bg-emerald-700 sm:inline-flex">
                        <i class="fa-solid fa-right-to-bracket mr-1.5"></i> Masuk
                    </a>
                <?php endif; ?>
                <button type="button" @click="mobileMenu = !mobileMenu" class="rounded-xl border border-emerald-100 p-2.5 text-slate-600 md:hidden">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
        </div>

        <div x-cloak x-show="mobileMenu" class="border-t border-emerald-100 bg-white px-4 py-3 md:hidden">
            <nav class="flex flex-col gap-1 text-sm font-semibold">
                <a href="<?= site_url('/') ?>#beranda" class="rounded-lg px-3 py-2 hover:bg-emerald-50">Beranda</a>
                <a href="<?= site_url('/') ?>#tentang" class="rounded-lg px-3 py-2 hover:bg-emerald-50">Tentang</a>
                <a href="<?= site_url('/') ?>#program" class="rounded-lg px-3 py-2 hover:bg-emerald-50">Program</a>
                <a href="<?= site_url('/') ?>#fasilitas" class="rounded-lg px-3 py-2 hover:bg-emerald-50">Fasilitas</a>
                <a href="<?= site_url('/') ?>#galeri" class="rounded-lg px-3 py-2 hover:bg-emerald-50">Galeri</a>
                <a href="<?= site_url('lisensi') ?>" class="rounded-lg px-3 py-2 hover:bg-emerald-50">Lisensi</a>
                <a href="<?= site_url('donasi') ?>" class="rounded-lg px-3 py-2 hover:bg-emerald-50">Donasi</a>
                <a href="<?= session()->get('is_logged_in') ? role_home((string) session()->get('role')) : site_url('login') ?>" class="mt-1 rounded-lg bg-emerald-600 px-3 py-2 text-center text-white"><?= session()->get('is_logged_in') ? 'Buka Panel' : 'Masuk' ?></a>
            </nav>
        </div>
    </header>

    <?= $content ?>

    <footer class="border-t border-emerald-100 bg-emerald-950">
        <div class="mx-auto grid w-full max-w-7xl gap-10 px-4 py-14 sm:px-6 md:grid-cols-2 lg:grid-cols-4 lg:px-8">
            <div class="lg:col-span-2">
                <div class="flex items-center gap-3">
                    <?php if (! empty($logo)): ?>
                        <img src="<?= upload_url('logos', $logo) ?>" alt="Logo" class="h-11 w-11 rounded-xl object-cover ring-1 ring-white/10">
                    <?php else: ?>
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-500 text-sm font-bold text-emerald-950">SA</div>
                    <?php endif; ?>
                    <span class="text-base font-extrabold text-white"><?= $namaLksa ?></span>
                </div>
                <p class="mt-4 max-w-md text-sm leading-relaxed text-emerald-100/60">
                    <?= $alamat !== '' ? $alamat : 'Lembaga Kesejahteraan Sosial Anak yang berfokus pada pendidikan, pengasuhan, dan pengembangan karakter anak.' ?>
                </p>

                <?php if ($socials !== []): ?>
                    <div class="mt-6 flex flex-wrap gap-2.5">
                        <?php foreach ($socials as $key => $url): ?>
                            <a href="<?= esc($url) ?>" target="_blank" rel="noopener" aria-label="<?= esc(ucfirst($key)) ?>" class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/5 text-emerald-100/80 ring-1 ring-white/10 transition hover:bg-emerald-500 hover:text-emerald-950">
                                <i class="fa-brands fa-<?= esc($key) ?>"></i>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div>
                <p class="text-sm font-bold text-white">Navigasi</p>
                <ul class="mt-4 space-y-2.5 text-sm text-emerald-100/60">
                    <li><a href="<?= site_url('/') ?>#beranda" class="transition hover:text-white">Beranda</a></li>
                    <li><a href="<?= site_url('/') ?>#tentang" class="transition hover:text-white">Tentang Kami</a></li>
                    <li><a href="<?= site_url('/') ?>#fasilitas" class="transition hover:text-white">Fasilitas</a></li>
                    <li><a href="<?= site_url('lisensi') ?>" class="transition hover:text-white">Lisensi</a></li>
                    <li><a href="<?= site_url('donasi') ?>" class="transition hover:text-white">Donasi</a></li>
                </ul>
            </div>

            <div>
                <p class="text-sm font-bold text-white">Kontak</p>
                <ul class="mt-4 space-y-3 text-sm text-emerald-100/60">
                    <?php if ($alamat !== ''): ?>
                        <li class="flex gap-3"><i class="fa-solid fa-location-dot mt-1 text-emerald-400"></i><span><?= $alamat ?></span></li>
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
        <div class="border-t border-white/10 py-5 text-center text-xs text-emerald-100/50">
            &copy; <?= date('Y') ?> <?= $namaLksa ?>. Dibangun dengan <i class="fa-solid fa-leaf text-emerald-400/70"></i> CodeIgniter 4, Tailwind CSS &amp; Alpine.js.
        </div>
    </footer>

    <?php if ($wa !== ''): ?>
        <a href="https://wa.me/<?= esc($wa) ?>" target="_blank" rel="noopener" aria-label="Hubungi via WhatsApp" class="fixed bottom-6 right-6 z-50 flex h-14 w-14 items-center justify-center rounded-full bg-emerald-500 text-2xl text-white shadow-xl shadow-emerald-600/30 transition hover:scale-105 hover:bg-emerald-600">
            <i class="fa-brands fa-whatsapp"></i>
        </a>
    <?php endif; ?>

    <?= $this->renderSection('scripts') ?>
</body>
</html>