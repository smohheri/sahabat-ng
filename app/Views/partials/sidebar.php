<?php
$role          = strtolower((string) ($currentUser['role'] ?? ''));
$isTeacher     = in_array($role, ['guru', 'pengajar'], true);
$isChild       = $role === 'anak';
$isStrictAdmin = $role === 'admin';
$path          = trim(service('request')->getPath(), '/');
$dataAnakActive = in_array($path, ['admin/anak_aktif', 'admin/anak_nonaktif', 'admin/anak'], true)
    || str_starts_with($path, 'admin/anak/');

$itemClass = static function (string $target, bool $exact = false) use ($path): string {
    $target = trim($target, '/');
    $active = $exact
        ? ($path === $target)
        : (($path === $target) || str_starts_with($path, $target . '/'));

    return $active
        ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-950/30'
        : 'text-slate-300 hover:bg-slate-800/70 hover:text-white';
};

$icon = static fn (string $name): string => '<i class="fa-solid fa-' . $name . ' w-5 text-center text-[13px]"></i>';
?>

<aside class="flex h-full w-72 shrink-0 flex-col border-r border-slate-800 bg-slate-900">
    <div class="flex h-16 items-center gap-3 border-b border-slate-800 px-5">
        <?php $logo = $settings['logo'] ?? null; ?>
        <?php if (! empty($logo)): ?>
            <img src="<?= upload_url('logos', $logo) ?>" alt="Logo" class="h-9 w-9 rounded-lg object-cover ring-1 ring-slate-700">
        <?php else: ?>
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-600 text-sm font-bold text-white">SA</div>
        <?php endif; ?>
        <div class="min-w-0">
            <p class="truncate text-sm font-semibold text-white"><?= esc($settings['nama_lksa'] ?? 'SAHABAT-NG') ?></p>
            <p class="text-[11px] uppercase tracking-wider text-slate-400">Sistem Informasi</p>
        </div>
    </div>

    <nav class="sidebar-nav flex-1 space-y-1 overflow-y-auto scrollbar-hidden px-3 py-4 text-sm">
        <?php if ($isChild): ?>
            <p class="px-2 pb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Panel Anak</p>
            <a href="<?= site_url('anak') ?>" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('anak', true) ?>"><?= $icon('house') ?> Dashboard</a>
            <a href="<?= site_url('anak/asesmen-mandiri') ?>" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('anak/asesmen-mandiri') ?>"><?= $icon('clipboard-check') ?> Asesmen Mandiri</a>
            <a href="<?= site_url('anak/profil') ?>" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('anak/profil') ?>"><?= $icon('user') ?> Profil Saya</a>
        <?php elseif ($isTeacher): ?>
            <p class="px-2 pb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Panel Pengajar</p>
            <a href="<?= site_url('guru') ?>" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('guru', true) ?>"><?= $icon('house') ?> Dashboard</a>
            <a href="<?= site_url('guru/anak') ?>" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('guru/anak') ?>"><?= $icon('children') ?> Data Anak</a>
            <a href="<?= site_url('guru/penilaian-karakter') ?>" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('guru/penilaian-karakter') ?>"><?= $icon('star') ?> Penilaian Karakter</a>
            <a href="<?= site_url('guru/perkembangan-anak') ?>" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('guru/perkembangan-anak') ?>"><?= $icon('chart-line') ?> Perkembangan Anak</a>

            <p class="px-2 pb-2 pt-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Akademik</p>
            <a href="<?= site_url('guru/akademik/absensi') ?>" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('guru/akademik/absensi') ?>"><?= $icon('calendar-check') ?> Absensi Mapel</a>
            <a href="<?= site_url('guru/akademik/mapel') ?>" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('guru/akademik/mapel') ?>"><?= $icon('book') ?> Mata Pelajaran</a>
            <a href="<?= site_url('guru/akademik/rombel') ?>" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('guru/akademik/rombel') ?>"><?= $icon('users-rectangle') ?> Rombongan Belajar</a>

            <p class="px-2 pb-2 pt-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Akun</p>
            <a href="<?= site_url('guru/profil-pengajar') ?>" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('guru/profil-pengajar') ?>"><?= $icon('id-card') ?> Profil Pengajar</a>
        <?php else: ?>
            <p class="px-2 pb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Utama</p>
            <a href="<?= site_url('admin') ?>" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('admin', true) ?>"><?= $icon('chart-pie') ?> Dashboard</a>

            <p class="px-2 pb-2 pt-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Data Master</p>
            <div x-data="{ open: true }">
                <button type="button" @click="open = !open" class="flex w-full items-center justify-between rounded-xl px-3 py-2.5 font-medium transition <?= $dataAnakActive ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-950/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' ?>">
                    <span class="flex items-center gap-3"><?= $icon('children') ?> Data Anak</span>
                    <i class="fa-solid fa-chevron-down text-[10px] transition-transform" :class="open ? '' : '-rotate-90'"></i>
                </button>
                <div x-show="open" class="mt-1 space-y-1 border-l border-slate-700/60 pl-3 ml-3">
                    <a href="<?= site_url('admin/anak_aktif') ?>" class="flex items-center gap-3 rounded-lg px-3 py-2 text-[13px] font-medium transition <?= $itemClass('admin/anak_aktif') ?>"><?= $icon('child-reaching') ?> Anak Aktif</a>
                    <a href="<?= site_url('admin/anak_nonaktif') ?>" class="flex items-center gap-3 rounded-lg px-3 py-2 text-[13px] font-medium transition <?= $itemClass('admin/anak_nonaktif') ?>"><?= $icon('user-slash') ?> Anak Nonaktif</a>
                </div>
            </div>
            <a href="<?= site_url('admin/pengurus') ?>" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('admin/pengurus') ?>"><?= $icon('user-tie') ?> Pengurus</a>
            <?php if ($isStrictAdmin): ?>
                <a href="<?= site_url('admin/user') ?>" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('admin/user') ?>"><?= $icon('users-gear') ?> Manajemen User</a>
            <?php endif; ?>

            <p class="px-2 pb-2 pt-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Akademik</p>
            <a href="<?= site_url('admin/akademik/mapel') ?>" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('admin/akademik/mapel') ?>"><?= $icon('book') ?> Mata Pelajaran</a>
            <a href="<?= site_url('admin/akademik/rombel') ?>" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('admin/akademik/rombel') ?>"><?= $icon('users-rectangle') ?> Rombongan Belajar</a>
            <a href="<?= site_url('admin/akademik/absensi') ?>" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('admin/akademik/absensi') ?>"><?= $icon('calendar-check') ?> Absensi Mapel</a>

            <p class="px-2 pb-2 pt-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Penilaian Karakter</p>
            <a href="<?= site_url('admin/penilaian-karakter/master') ?>" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('admin/penilaian-karakter/master') ?>"><?= $icon('sliders') ?> Master Penilaian</a>
            <a href="<?= site_url('admin/penilaian-karakter/data-penilaian') ?>" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('admin/penilaian-karakter/data-penilaian') ?>"><?= $icon('clipboard-list') ?> Data Penilaian</a>
            <a href="<?= site_url('admin/penilaian-karakter/catatan-kualitatif') ?>" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('admin/penilaian-karakter/catatan-kualitatif') ?>"><?= $icon('comment-dots') ?> Catatan Kualitatif</a>
            <a href="<?= site_url('admin/penilaian-karakter/ringkasan-mingguan') ?>" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('admin/penilaian-karakter/ringkasan-mingguan') ?>"><?= $icon('calendar-week') ?> Ringkasan Mingguan</a>
            <a href="<?= site_url('admin/penilaian-karakter/ringkasan-bulanan') ?>" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('admin/penilaian-karakter/ringkasan-bulanan') ?>"><?= $icon('calendar-days') ?> Ringkasan Bulanan</a>
            <a href="<?= site_url('admin/penilaian-karakter/laporan') ?>" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('admin/penilaian-karakter/laporan') ?>"><?= $icon('chart-column') ?> Laporan Karakter</a>

            <p class="px-2 pb-2 pt-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Laporan &amp; Konten</p>
            <a href="<?= site_url('admin/laporan') ?>" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('admin/laporan') ?>"><?= $icon('file-lines') ?> Laporan</a>
            <a href="<?= site_url('admin/carousel') ?>" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('admin/carousel') ?>"><?= $icon('images') ?> Carousel</a>
            <a href="<?= site_url('admin/hero') ?>" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('admin/hero') ?>"><?= $icon('panorama') ?> Hero</a>
            <a href="<?= site_url('admin/facilities') ?>" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('admin/facilities') ?>"><?= $icon('building') ?> Fasilitas</a>
            <?php if ($isStrictAdmin): ?>
                <a href="<?= site_url('admin/pengaturan') ?>" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('admin/pengaturan') ?>"><?= $icon('gear') ?> Pengaturan</a>
            <?php endif; ?>

            <p class="px-2 pb-2 pt-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Sistem</p>
            <a href="<?= site_url('admin/logs') ?>" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('admin/logs') ?>"><?= $icon('clock-rotate-left') ?> Log Aktivitas</a>
            <?php if ($isStrictAdmin): ?>
                <a href="<?= site_url('admin/backup') ?>" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('admin/backup') ?>"><?= $icon('database') ?> Backup &amp; Restore</a>
            <?php endif; ?>
            <a href="<?= site_url('admin/dukung_kami') ?>" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('admin/dukung_kami') ?>"><?= $icon('hand-holding-heart') ?> Dukung Kami</a>
            <a href="<?= site_url('admin/kontak') ?>" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('admin/kontak') ?>"><?= $icon('address-book') ?> Kontak</a>
            <a href="<?= site_url('admin/changelog') ?>" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('admin/changelog') ?>"><?= $icon('code-branch') ?> Changelog</a>
        <?php endif; ?>
    </nav>

    <div class="border-t border-slate-800 p-3">
        <a href="<?= site_url('logout') ?>" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-rose-300 transition hover:bg-rose-500/10 hover:text-rose-200">
            <?= $icon('right-from-bracket') ?> Keluar
        </a>
    </div>
</aside>
