<?php
$role           = strtolower((string) ($currentUser['role'] ?? ''));
$isTeacher      = in_array($role, ['guru', 'pengajar'], true);
$isChild        = $role === 'anak';
$isStrictAdmin  = $role === 'admin';
$path           = trim(service('request')->getPath(), '/');
$dataAnakActive = in_array($path, ['admin/anak_aktif', 'admin/anak_nonaktif', 'admin/anak'], true)
    || str_starts_with($path, 'admin/anak/');

$isActive = static function (string $target, bool $exact = false) use ($path): bool {
    $target = trim($target, '/');
    return $exact
        ? ($path === $target)
        : (($path === $target) || str_starts_with($path, $target . '/'));
};

$itemClass = static function (string $target, bool $exact = false) use ($isActive): string {
    $active = $isActive($target, $exact);
    return $active
        ? 'bg-emerald-600 text-white'
        : 'text-stone-300 hover:bg-stone-800 hover:text-white';
};

$dataAttr = static function (string $target, bool $exact = false) use ($isActive): string {
    return $isActive($target, $exact) ? ' data-active="1"' : '';
};

$icon = static fn (string $name): string => '<i class="fa-solid fa-' . $name . ' w-5 text-center text-[13px]"></i>';
$section = 'mb-2 mt-5 px-3 text-xs font-semibold text-stone-400';
?>

<aside class="flex h-full w-72 shrink-0 flex-col border-r border-stone-800 bg-stone-900">
    <div class="flex items-center gap-3 border-b border-stone-800 px-5 py-5">
        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-600 text-lg font-extrabold text-white">
            <i class="fa-solid fa-hands-holding-child"></i>
        </div>
        <div class="min-w-0">
            <p class="truncate text-sm font-extrabold text-white"><?= esc($settings['nama_lksa'] ?? 'SAHABAT-NG') ?></p>
            <p class="truncate text-xs text-stone-400">Panel <?= $isTeacher ? 'Guru' : ($isChild ? 'Anak' : 'Admin') ?></p>
        </div>
    </div>

    <nav class="sidebar-nav flex-1 space-y-1 overflow-y-auto px-3 py-4 text-sm">
        <p class="<?= $section ?> mt-0">Dashboard</p>

        <?php if ($isChild): ?>
            <a href="<?= site_url('anak') ?>"<?= $dataAttr('anak', true) ?> class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('anak', true) ?>"><?= $icon('house') ?> Dashboard</a>
            <a href="<?= site_url('anak') ?>"<?= $dataAttr('anak/penilaian') ?> class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('anak/penilaian') ?>"><?= $icon('star') ?> Penilaian Saya</a>
            <a href="<?= site_url('anak/asesmen-mandiri') ?>"<?= $dataAttr('anak/asesmen-mandiri') ?> class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('anak/asesmen-mandiri') ?>"><?= $icon('clipboard-check') ?> Asesmen Mandiri</a>
            <a href="<?= site_url('anak/profil') ?>"<?= $dataAttr('anak/profil') ?> class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('anak/profil') ?>"><?= $icon('id-card') ?> Profil Saya</a>

        <?php elseif ($isTeacher): ?>
            <a href="<?= site_url('guru') ?>"<?= $dataAttr('guru', true) ?> class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('guru', true) ?>"><?= $icon('house') ?> Dashboard</a>
            <a href="<?= site_url('guru/profil-pengajar') ?>"<?= $dataAttr('guru/profil-pengajar') ?> class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('guru/profil-pengajar') ?>"><?= $icon('id-card') ?> Profil Pengajar</a>
            <a href="<?= site_url('guru/anak') ?>"<?= $dataAttr('guru/anak') ?> class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('guru/anak') ?>"><?= $icon('children') ?> Data Anak</a>
            <a href="<?= site_url('guru/penilaian-karakter') ?>"<?= $dataAttr('guru/penilaian-karakter') ?> class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('guru/penilaian-karakter') ?>"><?= $icon('clipboard-check') ?> Penilaian Karakter</a>
            <a href="<?= site_url('guru/perkembangan-anak') ?>"<?= $dataAttr('guru/perkembangan-anak') ?> class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('guru/perkembangan-anak') ?>"><?= $icon('chart-line') ?> Perkembangan</a>

            <p class="<?= $section ?>">Akademik</p>
            <a href="<?= site_url('guru/akademik/mapel') ?>"<?= $dataAttr('guru/akademik/mapel') ?> class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('guru/akademik/mapel') ?>"><?= $icon('book') ?> Mata Pelajaran</a>
            <a href="<?= site_url('guru/akademik/rombel') ?>"<?= $dataAttr('guru/akademik/rombel') ?> class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('guru/akademik/rombel') ?>"><?= $icon('users-rectangle') ?> Rombel</a>
            <a href="<?= site_url('guru/akademik/absensi') ?>"<?= $dataAttr('guru/akademik/absensi') ?> class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('guru/akademik/absensi') ?>"><?= $icon('calendar-check') ?> Absensi</a>

        <?php else: ?>
            <a href="<?= site_url('admin') ?>"<?= $dataAttr('admin', true) ?> class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('admin', true) ?>"><?= $icon('house') ?> Dashboard</a>

            <p class="<?= $section ?>">Data</p>
            <a href="<?= site_url('admin/pengurus') ?>"<?= $dataAttr('admin/pengurus') ?> class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('admin/pengurus') ?>"><?= $icon('user-tie') ?> Pengurus</a>
            <a href="<?= site_url('admin/anak') ?>"<?= $dataAnakActive ? ' data-active="1"' : '' ?> class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $dataAnakActive ? 'bg-emerald-600 text-white' : 'text-stone-300 hover:bg-stone-800 hover:text-white' ?>"><?= $icon('children') ?> Anak Asuh</a>

            <p class="<?= $section ?>">Akademik</p>
            <a href="<?= site_url('admin/akademik/mapel') ?>"<?= $dataAttr('admin/akademik/mapel') ?> class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('admin/akademik/mapel') ?>"><?= $icon('book') ?> Mata Pelajaran</a>
            <a href="<?= site_url('admin/akademik/rombel') ?>"<?= $dataAttr('admin/akademik/rombel') ?> class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('admin/akademik/rombel') ?>"><?= $icon('users-rectangle') ?> Rombel</a>
            <a href="<?= site_url('admin/akademik/absensi') ?>"<?= $dataAttr('admin/akademik/absensi') ?> class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('admin/akademik/absensi') ?>"><?= $icon('calendar-check') ?> Absensi</a>

            <p class="<?= $section ?>">Penilaian</p>
            <a href="<?= site_url('admin/penilaian-karakter/data-penilaian') ?>"<?= $dataAttr('admin/penilaian-karakter/data-penilaian') ?> class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('admin/penilaian-karakter/data-penilaian') ?>"><?= $icon('clipboard-check') ?> Data Penilaian</a>
            <a href="<?= site_url('admin/penilaian-karakter/master') ?>"<?= $dataAttr('admin/penilaian-karakter/master') ?> class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('admin/penilaian-karakter/master') ?>"><?= $icon('layer-group') ?> Aspek & Indikator</a>
            <a href="<?= site_url('admin/penilaian-karakter/ringkasan-bulanan') ?>"<?= $dataAttr('admin/penilaian-karakter/ringkasan-bulanan') ?> class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('admin/penilaian-karakter/ringkasan-bulanan') ?>"><?= $icon('chart-column') ?> Ringkasan</a>
            <a href="<?= site_url('admin/penilaian-karakter/ringkasan-mingguan') ?>"<?= $dataAttr('admin/penilaian-karakter/ringkasan-mingguan') ?> class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('admin/penilaian-karakter/ringkasan-mingguan') ?>"><?= $icon('calendar-week') ?> Ringkasan Mingguan</a>
            <a href="<?= site_url('admin/penilaian-karakter/catatan-kualitatif') ?>"<?= $dataAttr('admin/penilaian-karakter/catatan-kualitatif') ?> class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('admin/penilaian-karakter/catatan-kualitatif') ?>"><?= $icon('note-sticky') ?> Catatan</a>
            <a href="<?= site_url('admin/laporan') ?>"<?= $dataAttr('admin/laporan') ?> class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('admin/laporan') ?>"><?= $icon('file-lines') ?> Laporan</a>

            <p class="<?= $section ?>">Pengaturan</p>
            <a href="<?= site_url('admin/user') ?>"<?= $dataAttr('admin/user') ?> class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('admin/user') ?>"><?= $icon('users-gear') ?> Pengguna</a>
            <a href="<?= site_url('admin/hero') ?>"<?= $dataAttr('admin/hero') ?> class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('admin/hero') ?>"><?= $icon('image') ?> Hero</a>
            <a href="<?= site_url('admin/facilities') ?>"<?= $dataAttr('admin/facilities') ?> class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('admin/facilities') ?>"><?= $icon('building') ?> Fasilitas</a>
            <a href="<?= site_url('admin/carousel') ?>"<?= $dataAttr('admin/carousel') ?> class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('admin/carousel') ?>"><?= $icon('images') ?> Carousel</a>
            <?php if ($isStrictAdmin): ?>
                <a href="<?= site_url('admin/pengaturan') ?>"<?= $dataAttr('admin/pengaturan') ?> class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('admin/pengaturan') ?>"><?= $icon('gear') ?> Pengaturan</a>
                <a href="<?= site_url('admin/backup') ?>"<?= $dataAttr('admin/backup') ?> class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('admin/backup') ?>"><?= $icon('database') ?> Backup</a>
                <a href="<?= site_url('admin/logs') ?>"<?= $dataAttr('admin/logs') ?> class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('admin/logs') ?>"><?= $icon('clock-rotate-left') ?> Log Aktivitas</a>
            <?php endif; ?>
            <a href="<?= site_url('admin/dukung_kami') ?>"<?= $dataAttr('admin/dukung_kami') ?> class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('admin/dukung_kami') ?>"><?= $icon('hand-holding-heart') ?> Dukung Kami</a>
            <a href="<?= site_url('admin/kontak') ?>"<?= $dataAttr('admin/kontak') ?> class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('admin/kontak') ?>"><?= $icon('address-book') ?> Kontak</a>
            <a href="<?= site_url('admin/changelog') ?>"<?= $dataAttr('admin/changelog') ?> class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition <?= $itemClass('admin/changelog') ?>"><?= $icon('code-branch') ?> Changelog</a>
        <?php endif; ?>
    </nav>

    <div class="border-t border-stone-800 p-3">
        <a href="<?= site_url('logout') ?>" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-rose-300 transition hover:bg-rose-500/10 hover:text-rose-200">
            <?= $icon('right-from-bracket') ?> Keluar
        </a>
    </div>
</aside>
