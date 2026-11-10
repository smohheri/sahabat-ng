<?php
$history    = $history ?? [];
$indicators = $indicators ?? [];
$notes      = $notes ?? [];
$months     = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

$notes = array_values(array_filter($notes, static fn ($note) => (int) ($note['id_anak'] ?? 0) === (int) $anak['id_anak']));

$grouped = [];
foreach ($indicators as $row) {
    $grouped[$row['aspect_name']][] = $row;
}

$totalIndicator = 0;
$sumIndicator   = 0.0;
foreach ($indicators as $row) {
    $totalIndicator += (int) $row['total'];
    $sumIndicator   += (float) $row['avg_score'] * (int) $row['total'];
}
$overall = $totalIndicator > 0 ? $sumIndicator / $totalIndicator : 0;

$avgColor = static fn (float $score): string => $score >= 3 ? 'bg-emerald-500' : ($score >= 2 ? 'bg-amber-500' : 'bg-rose-500');
?>

<div class="space-y-5">
    <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
        <div>
            <h2 class="text-lg font-extrabold text-slate-900">Detail Perkembangan Anak</h2>
            <p class="text-sm text-slate-500"><?= esc($anak['nama_anak']) ?> &middot; <?= esc($anak['no_registrasi'] ?: 'Tanpa No. Registrasi') ?></p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="<?= site_url('guru/perkembangan-anak') ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
            <a href="<?= site_url('guru/perkembangan-anak/detail/' . $anak['id_anak'] . '/export-pdf') ?>" target="_blank" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-bold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700"><i class="fa-solid fa-file-pdf"></i> Unduh PDF</a>
        </div>
    </div>

    <div class="grid gap-5 lg:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-4">
                <?php if (! empty($anak['foto'])): ?>
                    <img src="<?= upload_url('foto_anak', $anak['foto']) ?>" alt="<?= esc($anak['nama_anak']) ?>" class="h-16 w-16 rounded-2xl object-cover ring-1 ring-slate-200">
                <?php else: ?>
                    <span class="flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-600 text-xl font-extrabold text-white"><?= esc(strtoupper(mb_substr((string) $anak['nama_anak'], 0, 1))) ?></span>
                <?php endif; ?>
                <div class="min-w-0">
                    <p class="truncate text-base font-extrabold text-slate-900"><?= esc($anak['nama_anak']) ?></p>
                    <p class="text-xs text-slate-500"><?= ($anak['jenis_kelamin'] ?? '') === 'L' ? 'Laki-laki' : 'Perempuan' ?> &middot; <?= esc($anak['pendidikan'] ?: '-') ?></p>
                    <span class="mt-1 inline-flex rounded-full px-2.5 py-0.5 text-[10px] font-semibold ring-1 ring-inset <?= status_badge($anak['status_anak'] ?? '') ?>"><?= esc($anak['status_anak'] ?: '-') ?></span>
                </div>
            </div>

            <div class="mt-4 grid grid-cols-2 gap-3">
                <div class="rounded-xl bg-slate-50 p-3">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Total Penilaian</p>
                    <p class="mt-1 text-lg font-extrabold text-slate-800"><?= count($history) ?></p>
                </div>
                <div class="rounded-xl bg-slate-50 p-3">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Rata-rata</p>
                    <p class="mt-1 text-lg font-extrabold text-slate-800"><?= number_format($overall, 2) ?></p>
                </div>
            </div>

            <div class="mt-4 space-y-2 text-xs">
                <div class="flex items-center justify-between"><span class="text-slate-400">Sekolah</span><span class="font-semibold text-slate-700"><?= esc($anak['nama_sekolah'] ?: '-') ?></span></div>
                <div class="flex items-center justify-between"><span class="text-slate-400">Wilayah</span><span class="font-semibold text-slate-700"><?= esc($anak['nama_kabupaten'] ?: ($anak['nama_provinsi'] ?: '-')) ?></span></div>
                <div class="flex items-center justify-between"><span class="text-slate-400">Masuk</span><span class="font-semibold text-slate-700"><?= tanggal_id($anak['tanggal_masuk'] ?? null) ?></span></div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Indikator Bulan Ini</h3>
                    <p class="text-xs text-slate-500"><?= $months[(int) date('n')] ?> <?= date('Y') ?> &middot; <?= count($indicators) ?> indikator</p>
                </div>
                <span class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-50 px-3 py-2 text-xs font-bold text-indigo-600"><i class="fa-solid fa-star"></i> <?= number_format($overall, 2) ?> / 4</span>
            </div>

            <?php if ($grouped === []): ?>
                <div class="mt-5 rounded-xl bg-slate-50 px-4 py-10 text-center">
                    <i class="fa-solid fa-clipboard mb-2 block text-2xl text-slate-300"></i>
                    <p class="text-sm text-slate-400">Belum ada penilaian pada bulan ini.</p>
                </div>
            <?php else: ?>
                <div class="mt-4 max-h-80 space-y-4 overflow-y-auto pr-1">
                    <?php foreach ($grouped as $aspectName => $rows): ?>
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400"><?= esc($aspectName) ?></p>
                            <div class="mt-2 space-y-2">
                                <?php foreach ($rows as $row): ?>
                                    <?php $avg = (float) $row['avg_score']; ?>
                                    <div>
                                        <div class="flex items-center justify-between gap-2 text-xs">
                                            <span class="truncate text-slate-600"><?= esc($row['indicator_name']) ?></span>
                                            <span class="shrink-0 font-bold text-slate-800"><?= number_format($avg, 2) ?></span>
                                        </div>
                                        <div class="mt-1 h-1.5 overflow-hidden rounded-full bg-slate-100">
                                            <div class="h-full rounded-full <?= $avgColor($avg) ?>" style="width: <?= min(100, ($avg / 4) * 100) ?>%"></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid gap-5 lg:grid-cols-2">
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <h3 class="text-sm font-bold text-slate-900">Riwayat Penilaian</h3>
                <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-600"><?= count($history) ?> data</span>
            </div>
            <?php if ($history === []): ?>
                <div class="px-5 py-10 text-center text-sm text-slate-400">Belum ada riwayat penilaian.</div>
            <?php else: ?>
                <div class="max-h-96 divide-y divide-slate-100 overflow-y-auto">
                    <?php foreach ($history as $item): ?>
                        <?php $avg = (float) ($item['avg_score'] ?? 0); ?>
                        <div class="flex items-center justify-between gap-3 px-5 py-3">
                            <div class="min-w-0">
                                <p class="text-xs font-semibold text-slate-800"><?= tanggal_id($item['assessment_date'] ?? null) ?></p>
                                <p class="text-[11px] text-slate-400"><?= esc($item['assessor_name'] ?: '-') ?> &middot; <?= ($item['assessor_type'] ?? '') === 'anak_asuh' ? 'Anak Asuh' : 'Guru' ?> &middot; <?= (int) ($item['indicator_count'] ?? 0) ?> indikator</p>
                                <?php if (! empty($item['notes'])): ?><p class="mt-1 line-clamp-2 text-[11px] italic text-slate-500"><?= esc($item['notes']) ?></p><?php endif; ?>
                            </div>
                            <span class="shrink-0 rounded-lg px-2.5 py-1 text-xs font-extrabold <?= $avg >= 3 ? 'bg-emerald-100 text-emerald-700' : ($avg >= 2 ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') ?>"><?= number_format($avg, 2) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <h3 class="text-sm font-bold text-slate-900">Catatan Kualitatif</h3>
                <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-600"><?= count($notes) ?> catatan</span>
            </div>
            <?php if ($notes === []): ?>
                <div class="px-5 py-10 text-center text-sm text-slate-400">Belum ada catatan kualitatif.</div>
            <?php else: ?>
                <div class="max-h-96 space-y-3 overflow-y-auto p-5">
                    <?php foreach ($notes as $note): ?>
                        <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-3">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-[11px] font-bold text-slate-700"><?= tanggal_id($note['assessment_date'] ?? null) ?></p>
                                <p class="text-[10px] text-slate-400"><?= esc($note['assessor_name'] ?: '-') ?></p>
                            </div>
                            <div class="mt-2 space-y-1.5 text-[11px] leading-relaxed text-slate-600">
                                <?php foreach ([
                                    ['Kekuatan', $note['strengths'] ?? null, 'emerald'],
                                    ['Perkembangan', $note['development_observed'] ?? null, 'sky'],
                                    ['Perlu Dukungan', $note['areas_to_support'] ?? null, 'amber'],
                                    ['Strategi', $note['support_strategy'] ?? null, 'indigo'],
                                ] as [$label, $value, $tone]): ?>
                                    <?php if (! empty($value)): ?>
                                        <p><span class="font-semibold text-<?= $tone ?>-600"><?= $label ?>:</span> <?= esc($value) ?></p>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
