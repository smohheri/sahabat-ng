<?php
$progress = $progress ?? [];
$recent   = $recent ?? [];
$avgAll   = 0;

if ($progress !== []) {
    $weighted = 0;
    $count    = 0;
    foreach ($progress as $row) {
        $weighted += (float) ($row['avg_score'] ?? 0) * (int) ($row['total_assessment'] ?? 0);
        $count    += (int) ($row['total_assessment'] ?? 0);
    }
    $avgAll = $count > 0 ? $weighted / $count : 0;
}

$stats = [
    ['Anak Terpantau', number_format((int) $totalAnak, 0, ',', '.'), 'children', 'indigo'],
    ['Rombel Diampu', number_format((int) $totalRombel, 0, ',', '.'), 'users-rectangle', 'sky'],
    ['Penilaian Terakhir', number_format((int) $totalAssess, 0, ',', '.'), 'clipboard-check', 'emerald'],
    ['Rata-rata Karakter', number_format($avgAll, 2) . ' / 4', 'star', 'amber'],
];
?>

<div class="space-y-6">
    <div class="relative overflow-hidden rounded-3xl bg-indigo-600 p-6 sm:p-8">
        <div class="absolute -right-16 -top-16 h-56 w-56 rounded-full bg-white/10 blur-2xl"></div>
        <div class="absolute -bottom-20 left-1/3 h-48 w-48 rounded-full bg-sky-300/20 blur-2xl"></div>
        <div class="relative flex flex-col justify-between gap-5 lg:flex-row lg:items-center">
            <div class="max-w-xl">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-200">Panel Pengajar</p>
                <h1 class="mt-2 text-2xl font-extrabold text-white sm:text-3xl">Assalamu&rsquo;alaikum, <?= esc($guru['nama_guru'] ?? ($currentUser['nama'] ?? 'Pengajar')) ?></h1>
                <p class="mt-2 text-sm leading-relaxed text-indigo-100">
                    Pantau perkembangan karakter anak asuh, catat penilaian, dan kelola absensi mata pelajaran dalam satu tempat.
                </p>
                <div class="mt-5 flex flex-wrap gap-2">
                    <a href="<?= site_url('guru/penilaian-karakter') ?>" class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-xs font-bold text-indigo-700 shadow-lg shadow-indigo-950/20 transition hover:bg-indigo-50"><i class="fa-solid fa-star"></i> Nilai Karakter</a>
                    <a href="<?= site_url('guru/akademik/absensi') ?>" class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2.5 text-xs font-bold text-white ring-1 ring-inset ring-white/30 transition hover:bg-white/20"><i class="fa-solid fa-calendar-check"></i> Isi Absensi</a>
                </div>
            </div>
            <div class="flex items-center gap-4 rounded-2xl bg-white/10 p-4 ring-1 ring-inset ring-white/20">
                <?php if (! empty($guru['foto'])): ?>
                    <img src="<?= upload_url('foto_guru', $guru['foto']) ?>" alt="<?= esc($guru['nama_guru'] ?? '') ?>" class="h-16 w-16 rounded-2xl object-cover ring-2 ring-white/60">
                <?php else: ?>
                    <span class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white/20 text-xl font-extrabold text-white"><?= esc(strtoupper(mb_substr((string) ($guru['nama_guru'] ?? $currentUser['nama'] ?? 'P'), 0, 1))) ?></span>
                <?php endif; ?>
                <div>
                    <p class="text-sm font-bold text-white"><?= esc($guru['jabatan'] ?: 'Pengajar') ?></p>
                    <p class="text-xs text-indigo-100"><?= esc($guru['nip'] ? 'NIP ' . $guru['nip'] : 'NIP belum diatur') ?></p>
                    <a href="<?= site_url('guru/profil-pengajar') ?>" class="mt-1 inline-flex items-center gap-1 text-[11px] font-semibold text-indigo-200 hover:text-white">Lihat profil <i class="fa-solid fa-arrow-right text-[9px]"></i></a>
                </div>
            </div>
        </div>
    </div>

    <?php if (! $guru): ?>
        <div class="flex flex-col gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="flex items-center gap-2 text-sm text-amber-800"><i class="fa-solid fa-triangle-exclamation"></i> Profil pengajar Anda belum lengkap.</p>
            <a href="<?= site_url('guru/profil-pengajar/edit') ?>" class="inline-flex items-center gap-2 self-start rounded-xl bg-amber-600 px-4 py-2 text-xs font-bold text-white transition hover:bg-amber-700">Lengkapi Profil</a>
        </div>
    <?php endif; ?>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <?php foreach ($stats as [$label, $value, $icon, $tone]): ?>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400"><?= $label ?></p>
                        <p class="mt-1 text-2xl font-extrabold text-slate-900"><?= $value ?></p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-<?= $tone ?>-50 text-<?= $tone ?>-600"><i class="fa-solid fa-<?= $icon ?>"></i></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="grid gap-5 lg:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Penilaian Terbaru</h3>
                    <p class="text-xs text-slate-500">8 penilaian terakhir yang Anda simpan.</p>
                </div>
                <a href="<?= site_url('guru/penilaian-karakter') ?>" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">Buka <i class="fa-solid fa-arrow-right text-[9px]"></i></a>
            </div>

            <?php if ($recent === []): ?>
                <div class="px-5 py-12 text-center">
                    <i class="fa-solid fa-clipboard-list mb-3 text-3xl text-slate-300"></i>
                    <p class="text-sm text-slate-500">Belum ada penilaian yang Anda buat.</p>
                    <a href="<?= site_url('guru/penilaian-karakter') ?>" class="mt-3 inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-xs font-bold text-white transition hover:bg-indigo-700"><i class="fa-solid fa-plus"></i> Mulai Menilai</a>
                </div>
            <?php else: ?>
                <div class="divide-y divide-slate-100">
                    <?php foreach ($recent as $item): ?>
                        <?php $avg = (float) ($item['avg_score'] ?? 0); ?>
                        <div class="flex items-center gap-3 px-5 py-3.5">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-xs font-bold text-indigo-600"><?= esc(strtoupper(mb_substr((string) ($item['nama_anak'] ?? '-'), 0, 1))) ?></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800"><?= esc($item['nama_anak'] ?: '-') ?></p>
                                <p class="text-[11px] text-slate-400"><?= tanggal_id($item['assessment_date'] ?? null) ?></p>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="h-2 w-16 overflow-hidden rounded-full bg-slate-100">
                                    <div class="h-full rounded-full <?= $avg >= 3 ? 'bg-emerald-500' : ($avg >= 2 ? 'bg-amber-500' : 'bg-rose-500') ?>" style="width: <?= min(100, ($avg / 4) * 100) ?>%"></div>
                                </div>
                                <span class="w-9 text-right text-xs font-bold text-slate-700"><?= number_format($avg, 2) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4">
                <h3 class="text-sm font-bold text-slate-900">Progres per Anak</h3>
                <p class="text-xs text-slate-500">Rata-rata nilai yang Anda berikan.</p>
            </div>

            <?php if ($progress === []): ?>
                <div class="px-5 py-12 text-center">
                    <i class="fa-solid fa-chart-line mb-3 text-3xl text-slate-300"></i>
                    <p class="text-sm text-slate-500">Belum ada data progres.</p>
                </div>
            <?php else: ?>
                <div class="max-h-[22rem] space-y-3 overflow-y-auto p-5">
                    <?php foreach (array_slice($progress, 0, 10) as $row): ?>
                        <?php $score = (float) ($row['avg_score'] ?? 0); ?>
                        <div>
                            <div class="flex items-center justify-between gap-2 text-xs">
                                <a href="<?= site_url('guru/perkembangan-anak/detail/' . $row['id_anak']) ?>" class="truncate font-medium text-slate-700 hover:text-indigo-600"><?= esc($row['nama_anak']) ?></a>
                                <span class="shrink-0 font-bold text-slate-800"><?= number_format($score, 2) ?></span>
                            </div>
                            <div class="mt-1.5 h-2 overflow-hidden rounded-full bg-slate-100">
                                <div class="h-full rounded-full <?= $score >= 3 ? 'bg-emerald-500' : ($score >= 2 ? 'bg-amber-500' : 'bg-rose-500') ?>" style="width: <?= min(100, ($score / 4) * 100) ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
