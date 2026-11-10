<?php
$history     = $history ?? [];
$indicators  = $indicators ?? [];
$aspectAvg   = $aspectAvg ?? [];
$latestNote  = $latestNote ?? null;
$months      = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

$avgColor = static fn (float $score): string => $score >= 3 ? 'bg-emerald-500' : ($score >= 2 ? 'bg-amber-500' : 'bg-rose-500');
$overall  = $aspectAvg !== [] ? array_sum($aspectAvg) / count($aspectAvg) : 0;
$best     = $aspectAvg !== [] ? array_key_first($aspectAvg) : null;
$worst    = $aspectAvg !== [] ? array_key_last($aspectAvg) : null;
?>

<div class="space-y-6">
    <div class="relative overflow-hidden rounded-3xl bg-indigo-600 p-6 sm:p-8">
        <div class="absolute -right-14 -top-14 h-52 w-52 rounded-full bg-white/10 blur-2xl"></div>
        <div class="relative flex flex-col justify-between gap-5 sm:flex-row sm:items-center">
            <div class="flex items-center gap-4">
                <?php if (! empty($anak['foto'])): ?>
                    <img src="<?= upload_url('foto_anak', $anak['foto']) ?>" alt="<?= esc($anak['nama_anak']) ?>" class="h-16 w-16 rounded-2xl object-cover ring-2 ring-white/60">
                <?php else: ?>
                    <span class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white/20 text-2xl font-extrabold text-white"><?= esc(strtoupper(mb_substr((string) $anak['nama_anak'], 0, 1))) ?></span>
                <?php endif; ?>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-200">Panel Anak Asuh</p>
                    <h1 class="mt-1 text-2xl font-extrabold text-white">Halo, <?= esc($anak['nama_anak']) ?>!</h1>
                    <p class="text-xs text-indigo-100"><?= esc($anak['no_registrasi'] ?: 'Tanpa No. Registrasi') ?> &middot; <?= esc($anak['pendidikan'] ?: '-') ?></p>
                </div>
            </div>
            <div class="flex items-center gap-3 rounded-2xl bg-white/10 px-4 py-3 ring-1 ring-inset ring-white/20">
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-indigo-200">Rata-rata Bulan Ini</p>
                    <p class="text-2xl font-extrabold text-white"><?= number_format($overall, 2) ?><span class="text-sm font-semibold text-indigo-200"> / 4</span></p>
                </div>
                <i class="fa-solid fa-star text-2xl text-amber-300"></i>
            </div>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Aspek Terkuat</p>
            <p class="mt-1 truncate text-base font-extrabold text-emerald-600"><?= esc($best ?: '-') ?></p>
            <p class="text-xs text-slate-400"><?= $best ? 'Skor ' . number_format((float) $aspectAvg[$best], 2) : 'Belum ada data' ?></p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Perlu Perhatian</p>
            <p class="mt-1 truncate text-base font-extrabold text-amber-600"><?= esc($worst ?: '-') ?></p>
            <p class="text-xs text-slate-400"><?= $worst ? 'Skor ' . number_format((float) $aspectAvg[$worst], 2) : 'Belum ada data' ?></p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Asesmen Mandiri</p>
            <?php if ($alreadySubmit): ?>
                <p class="mt-1 inline-flex items-center gap-1.5 text-sm font-extrabold text-emerald-600"><i class="fa-solid fa-circle-check"></i> Sudah dikirim</p>
                <p class="text-xs text-slate-400"><?= $months[(int) $month] ?> <?= (int) $year ?></p>
            <?php else: ?>
                <a href="<?= site_url('anak/asesmen-mandiri') ?>" class="mt-1 inline-flex items-center gap-1.5 text-sm font-extrabold text-indigo-600 hover:text-indigo-700">Isi sekarang <i class="fa-solid fa-arrow-right text-[10px]"></i></a>
                <p class="text-xs text-slate-400">Penilaian diri bulan ini</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid gap-5 lg:grid-cols-3">
        <div class="rounded-2xl border border-indigo-100 bg-indigo-50/60 p-5 lg:col-span-2">
            <h3 class="flex items-center gap-2 text-sm font-bold text-slate-900"><i class="fa-solid fa-wand-magic-sparkles text-indigo-500"></i> Ringkasan Perkembangan</h3>
            <p class="mt-2 text-sm leading-relaxed text-slate-600"><?= esc($summary) ?></p>
        </div>
        <div class="rounded-2xl border border-amber-100 bg-amber-50/60 p-5">
            <h3 class="flex items-center gap-2 text-sm font-bold text-slate-900"><i class="fa-solid fa-lightbulb text-amber-500"></i> Rencana Perbaikan</h3>
            <p class="mt-2 text-sm leading-relaxed text-slate-600"><?= esc($improvement) ?></p>
        </div>
    </div>

    <div class="grid gap-5 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Skor per Aspek</h3>
                    <p class="text-xs text-slate-500"><?= $months[(int) $month] ?> <?= (int) $year ?></p>
                </div>
                <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-600"><?= count($aspectAvg) ?> aspek</span>
            </div>

            <?php if ($aspectAvg === []): ?>
                <div class="mt-5 rounded-xl bg-slate-50 px-4 py-10 text-center">
                    <i class="fa-solid fa-chart-simple mb-2 block text-2xl text-slate-300"></i>
                    <p class="text-sm text-slate-400">Belum ada penilaian pada bulan ini.</p>
                </div>
            <?php else: ?>
                <div class="mt-4 space-y-3.5">
                    <?php foreach ($aspectAvg as $name => $score): ?>
                        <div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-semibold text-slate-700"><?= esc($name) ?></span>
                                <span class="font-bold text-slate-800"><?= number_format((float) $score, 2) ?> / 4</span>
                            </div>
                            <div class="mt-1.5 h-2.5 overflow-hidden rounded-full bg-slate-100">
                                <div class="h-full rounded-full <?= $avgColor((float) $score) ?>" style="width: <?= min(100, ((float) $score / 4) * 100) ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Catatan Terbaru</h3>
                    <p class="text-xs text-slate-500">Dari penilai terakhir</p>
                </div>
                <?php if ($latestNote): ?>
                    <span class="text-[11px] text-slate-400"><?= tanggal_id($latestNote['assessment_date'] ?? null) ?></span>
                <?php endif; ?>
            </div>

            <?php if (! $latestNote): ?>
                <div class="mt-5 rounded-xl bg-slate-50 px-4 py-10 text-center">
                    <i class="fa-solid fa-comment-slash mb-2 block text-2xl text-slate-300"></i>
                    <p class="text-sm text-slate-400">Belum ada catatan dari guru.</p>
                </div>
            <?php else: ?>
                <div class="mt-4 space-y-2.5">
                    <?php foreach ([
                        ['Kekuatan', $latestNote['strengths'] ?? null, 'emerald', 'thumbs-up'],
                        ['Perkembangan', $latestNote['development_observed'] ?? null, 'sky', 'arrow-trend-up'],
                        ['Perlu Dukungan', $latestNote['areas_to_support'] ?? null, 'amber', 'triangle-exclamation'],
                        ['Strategi', $latestNote['support_strategy'] ?? null, 'indigo', 'lightbulb'],
                    ] as [$label, $value, $tone, $icon]): ?>
                        <?php if (! empty($value)): ?>
                            <div class="rounded-xl border border-<?= $tone ?>-100 bg-<?= $tone ?>-50/60 p-3">
                                <p class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-<?= $tone ?>-700"><i class="fa-solid fa-<?= $icon ?>"></i> <?= $label ?></p>
                                <p class="mt-1 text-xs leading-relaxed text-slate-600"><?= esc($value) ?></p>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <?php if (empty($latestNote['strengths']) && empty($latestNote['development_observed']) && empty($latestNote['areas_to_support']) && empty($latestNote['support_strategy'])): ?>
                        <p class="rounded-xl bg-slate-50 px-3 py-4 text-center text-xs text-slate-400">Catatan belum diisi.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <h3 class="text-sm font-bold text-slate-900">Riwayat Penilaian Saya</h3>
            <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-600"><?= count($history) ?> data</span>
        </div>
        <?php if ($history === []): ?>
            <div class="px-5 py-10 text-center text-sm text-slate-400">Belum ada riwayat penilaian.</div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-4 py-3 font-semibold">No</th>
                            <th class="px-4 py-3 font-semibold">Tanggal</th>
                            <th class="px-4 py-3 font-semibold">Penilai</th>
                            <th class="px-4 py-3 font-semibold">Tipe</th>
                            <th class="px-4 py-3 font-semibold">Rata-rata</th>
                            <th class="px-4 py-3 font-semibold">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($history as $index => $item): ?>
                            <?php $avg = (float) ($item['avg_score'] ?? 0); ?>
                            <tr class="transition hover:bg-slate-50/70">
                                <td class="px-4 py-3 text-slate-500"><?= $index + 1 ?></td>
                                <td class="px-4 py-3 text-slate-600"><?= tanggal_id($item['assessment_date'] ?? null) ?></td>
                                <td class="px-4 py-3 text-slate-600"><?= esc($item['assessor_name'] ?: '-') ?></td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold <?= ($item['assessor_type'] ?? '') === 'anak_asuh' ? 'bg-violet-100 text-violet-700' : 'bg-sky-100 text-sky-700' ?>">
                                        <?= ($item['assessor_type'] ?? '') === 'anak_asuh' ? 'Mandiri' : 'Guru' ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-bold text-slate-800"><?= number_format($avg, 2) ?></td>
                                <td class="px-4 py-3 text-[11px] text-slate-500"><?= esc($item['notes'] ?: '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
