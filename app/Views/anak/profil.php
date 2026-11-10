<?php
$identity = [
    ['Nama Lengkap', $anak['nama_anak'] ?? null, 'user'],
    ['No. Registrasi', $anak['no_registrasi'] ?? null, 'hashtag'],
    ['NIK', $anak['nik'] ?? null, 'id-card'],
    ['No. KK', $anak['no_kk'] ?? null, 'id-card-clip'],
    ['Jenis Kelamin', ($anak['jenis_kelamin'] ?? '') === 'L' ? 'Laki-laki' : 'Perempuan', 'venus-mars'],
    ['Tempat, Tanggal Lahir', trim(($anak['tempat_lahir'] ?? '') . ', ' . ($anak['tanggal_lahir'] ? tanggal_id($anak['tanggal_lahir']) : ''), ', '), 'cake-candles'],
    ['Agama', $anak['agama'] ?? null, 'hands-praying'],
    ['Kewarganegaraan', $anak['kewarganegaraan'] ?? null, 'flag'],
    ['Pendidikan', $anak['pendidikan'] ?? null, 'graduation-cap'],
    ['Status', $anak['status_anak'] ?? null, 'circle-info'],
    ['Status Tinggal', $anak['status_tinggal'] ?? null, 'house'],
    ['Tanggal Masuk', $anak['tanggal_masuk'] ? tanggal_id($anak['tanggal_masuk']) : null, 'calendar-check'],
];

$family = [
    ['Nama Ayah Kandung', $anak['nama_ayah_kandung'] ?? null],
    ['Nama Ibu Kandung', $anak['nama_ibu_kandung'] ?? null],
    ['Pekerjaan Ayah', $anak['pekerjaan_ayah'] ?? null],
    ['Pekerjaan Ibu', $anak['pekerjaan_ibu'] ?? null],
    ['Nama Wali', $anak['nama_wali'] ?? null],
    ['No. HP Orang Tua / Wali', $anak['no_telp_orang_tua'] ?: ($anak['no_telp_wali'] ?? null)],
    ['Alamat Orang Tua', $anak['alamat_orang_tua'] ?: ($anak['alamat_wali'] ?? null)],
];

$school = [
    ['Sekolah', $anak['nama_sekolah'] ?? null, 'school'],
    ['Kelas', $anak['kelas'] ?? null, 'chalkboard'],
    ['Alamat Sekolah', $anak['alamat_sekolah'] ?? null, 'location-dot'],
    ['No. Telp Sekolah', $anak['no_telp_sekolah'] ?? null, 'phone'],
    ['Wilayah', $anak['nama_kabupaten'] ?: ($anak['nama_provinsi'] ?? null), 'map-location-dot'],
];

$documents = [
    ['Kartu Keluarga', $anak['file_kk'] ?? null, 'file_kk', 'file-lines'],
    ['Akta Lahir', $anak['file_akta'] ?? null, 'file_akta', 'file-contract'],
    ['Dokumen Pendukung', $anak['file_pendukung'] ?? null, 'file_pendukung', 'file-circle-plus'],
];
?>

<div class="space-y-5">
    <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
        <div>
            <h2 class="text-lg font-extrabold text-slate-900">Profil Saya</h2>
            <p class="text-sm text-slate-500">Data diri, keluarga, sekolah, dan dokumen Anda.</p>
        </div>
        <a href="<?= site_url('anak') ?>" class="inline-flex items-center gap-2 self-start rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
    </div>

    <div class="grid gap-5 lg:grid-cols-3">
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="h-24 bg-indigo-600"></div>
            <div class="-mt-12 px-5 pb-5 text-center">
                <?php if (! empty($anak['foto'])): ?>
                    <img src="<?= upload_url('foto_anak', $anak['foto']) ?>" alt="<?= esc($anak['nama_anak']) ?>" class="mx-auto h-24 w-24 rounded-2xl object-cover ring-4 ring-white">
                <?php else: ?>
                    <span class="mx-auto flex h-24 w-24 items-center justify-center rounded-2xl bg-indigo-600 text-3xl font-extrabold text-white ring-4 ring-white"><?= esc(strtoupper(mb_substr((string) $anak['nama_anak'], 0, 1))) ?></span>
                <?php endif; ?>
                <p class="mt-3 text-base font-extrabold text-slate-900"><?= esc($anak['nama_anak']) ?></p>
                <p class="text-xs text-slate-500"><?= esc($anak['no_registrasi'] ?: 'Tanpa No. Registrasi') ?></p>
                <span class="mt-2 inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold ring-1 ring-inset <?= status_badge($anak['status_anak'] ?? '') ?>"><?= esc($anak['status_anak'] ?: '-') ?></span>

                <?php if (! empty($anak['nama_sekolah'])): ?>
                    <div class="mt-4 rounded-xl bg-slate-50 p-3 text-left">
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Sekolah</p>
                        <p class="text-xs font-semibold text-slate-700"><?= esc($anak['nama_sekolah']) ?></p>
                        <p class="text-[11px] text-slate-400"><?= esc($anak['kelas'] ?: '-') ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="space-y-5 lg:col-span-2">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="flex items-center gap-2 text-sm font-bold text-slate-900"><i class="fa-solid fa-address-card text-indigo-500"></i> Identitas</h3>
                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <?php foreach ($identity as [$label, $value, $icon]): ?>
                        <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-3.5">
                            <p class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400"><i class="fa-solid fa-<?= $icon ?> text-indigo-400"></i> <?= $label ?></p>
                            <p class="mt-1 break-words text-sm font-semibold text-slate-800"><?= esc($value ?: '-') ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-5 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="flex items-center gap-2 text-sm font-bold text-slate-900"><i class="fa-solid fa-people-roof text-emerald-500"></i> Keluarga</h3>
            <div class="mt-4 space-y-3">
                <?php foreach ($family as [$label, $value]): ?>
                    <div class="flex flex-col gap-0.5 rounded-xl bg-slate-50 px-4 py-3 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400"><?= $label ?></p>
                        <p class="break-words text-sm font-semibold text-slate-800 sm:text-right"><?= esc($value ?: '-') ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="space-y-5">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="flex items-center gap-2 text-sm font-bold text-slate-900"><i class="fa-solid fa-school text-sky-500"></i> Sekolah</h3>
                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <?php foreach ($school as [$label, $value, $icon]): ?>
                        <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-3">
                            <p class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400"><i class="fa-solid fa-<?= $icon ?> text-sky-400"></i> <?= $label ?></p>
                            <p class="mt-1 break-words text-xs font-semibold text-slate-800"><?= esc($value ?: '-') ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="flex items-center gap-2 text-sm font-bold text-slate-900"><i class="fa-solid fa-folder-open text-amber-500"></i> Dokumen</h3>
                <div class="mt-4 space-y-2">
                    <?php foreach ($documents as [$label, $file, $folder, $icon]): ?>
                        <?php if (! empty($file)): ?>
                            <a href="<?= upload_url($folder, $file) ?>" target="_blank" class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3 transition hover:bg-slate-50">
                                <span class="flex items-center gap-2 text-xs font-semibold text-slate-700"><i class="fa-solid fa-<?= $icon ?> text-indigo-500"></i> <?= $label ?></span>
                                <span class="text-[11px] font-semibold text-indigo-600">Lihat <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i></span>
                            </a>
                        <?php else: ?>
                            <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3">
                                <span class="flex items-center gap-2 text-xs font-semibold text-slate-400"><i class="fa-solid fa-<?= $icon ?>"></i> <?= $label ?></span>
                                <span class="text-[11px] text-slate-400">Belum ada</span>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
