<?php
$fields = [
    ['Jabatan', $guru['jabatan'] ?? null, 'user-tie', 'indigo'],
    ['NIP', $guru['nip'] ?? null, 'id-badge', 'sky'],
    ['Pendidikan Terakhir', $guru['pendidikan_terakhir'] ?? null, 'graduation-cap', 'emerald'],
    ['Bidang Keahlian', $guru['bidang_keahlian'] ?? null, 'lightbulb', 'amber'],
    ['Sertifikasi', $guru['sertifikasi'] ?? null, 'certificate', 'violet'],
    ['Pengalaman Mengajar', isset($guru['pengalaman_tahun']) && $guru['pengalaman_tahun'] !== null ? $guru['pengalaman_tahun'] . ' tahun' : null, 'clock', 'rose'],
    ['Status Kepegawaian', $guru['status_kepegawaian'] ?? null, 'briefcase', 'slate'],
    ['No. HP', $guru['no_hp'] ?? null, 'phone', 'sky'],
    ['Email', $guru['email'] ?? null, 'envelope', 'emerald'],
    ['Alamat', $guru['alamat'] ?? null, 'location-dot', 'indigo'],
];
?>

<div class="space-y-5">
    <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
        <div>
            <h2 class="text-lg font-extrabold text-slate-900">Profil Pengajar</h2>
            <p class="text-sm text-slate-500">Data kepegawaian dan kontak pengajar.</p>
        </div>
        <a href="<?= site_url('guru/profil-pengajar/edit') ?>" class="inline-flex items-center gap-2 self-start rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-bold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700">
            <i class="fa-solid fa-pen-to-square"></i> Edit Profil
        </a>
    </div>

    <?php if (! $guru): ?>
        <div class="rounded-2xl border border-amber-200 bg-amber-50 px-6 py-14 text-center">
            <i class="fa-solid fa-id-card mb-3 text-4xl text-amber-400"></i>
            <p class="text-sm font-semibold text-amber-800">Profil pengajar belum tersedia</p>
            <p class="mt-1 text-xs text-amber-700">Lengkapi data kepegawaian Anda agar tampil pada halaman ini.</p>
            <a href="<?= site_url('guru/profil-pengajar/edit') ?>" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-amber-600 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-amber-700"><i class="fa-solid fa-plus"></i> Buat Profil</a>
        </div>
    <?php else: ?>
        <div class="grid gap-5 lg:grid-cols-3">
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="h-24 bg-indigo-600"></div>
                <div class="-mt-12 px-5 pb-5 text-center">
                    <?php if (! empty($guru['foto'])): ?>
                        <img src="<?= upload_url('foto_guru', $guru['foto']) ?>" alt="<?= esc($guru['nama_guru']) ?>" class="mx-auto h-24 w-24 rounded-2xl object-cover ring-4 ring-white">
                    <?php else: ?>
                        <span class="mx-auto flex h-24 w-24 items-center justify-center rounded-2xl bg-indigo-600 text-3xl font-extrabold text-white ring-4 ring-white"><?= esc(strtoupper(mb_substr((string) $guru['nama_guru'], 0, 1))) ?></span>
                    <?php endif; ?>
                    <p class="mt-3 text-base font-extrabold text-slate-900"><?= esc($guru['nama_guru']) ?></p>
                    <p class="text-xs text-slate-500"><?= esc($guru['jabatan'] ?: 'Pengajar') ?></p>
                    <span class="mt-2 inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold <?= (int) ($guru['is_active'] ?? 0) === 1 ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' ?>">
                        <i class="fa-solid fa-circle text-[6px]"></i> <?= (int) ($guru['is_active'] ?? 0) === 1 ? 'Aktif' : 'Nonaktif' ?>
                    </span>

                    <?php if (! empty($guru['file_ijazah_terakhir'])): ?>
                        <a href="<?= upload_url('file_ijazah', $guru['file_ijazah_terakhir']) ?>" target="_blank" class="mt-4 flex items-center justify-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-50">
                            <i class="fa-solid fa-file-pdf text-rose-500"></i> Lihat Ijazah Terakhir
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2">
                <h3 class="text-sm font-bold text-slate-900">Detail Data</h3>
                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <?php foreach ($fields as [$label, $value, $icon, $tone]): ?>
                        <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-3.5">
                            <p class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400"><i class="fa-solid fa-<?= $icon ?> text-<?= $tone ?>-400"></i> <?= $label ?></p>
                            <p class="mt-1 break-words text-sm font-semibold text-slate-800"><?= esc($value ?: '-') ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
