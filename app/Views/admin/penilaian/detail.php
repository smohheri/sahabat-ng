<?php
$grouped = [];

foreach ($details as $detail) {
    $grouped[$detail['aspect_name']][] = $detail;
}

$scores = array_map(static fn ($d) => (float) $d['score'], $details);
$average = $scores === [] ? 0 : array_sum($scores) / count($scores);

$scoreTone = static function (float $score): string {
    if ($score >= 3.5) {
        return 'bg-emerald-100 text-emerald-700';
    }
    if ($score >= 2.5) {
        return 'bg-sky-100 text-sky-700';
    }
    if ($score >= 1.5) {
        return 'bg-amber-100 text-amber-700';
    }

    return 'bg-rose-100 text-rose-700';
};

$months = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
?>

<div class="space-y-5">
    <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
        <div>
            <h2 class="text-lg font-extrabold text-slate-900">Detail Penilaian Karakter</h2>
            <p class="text-sm text-slate-500">Rincian skor setiap indikator beserta catatan kualitatif.</p>
        </div>
        <a href="<?= site_url('admin/penilaian-karakter/data-penilaian') ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="grid gap-4 lg:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-600 text-lg font-extrabold text-white"><?= esc(strtoupper(mb_substr((string) ($assessment['nama_anak'] ?? 'A'), 0, 1))) ?></span>
                    <div>
                        <p class="text-base font-extrabold text-slate-900"><?= esc($assessment['nama_anak'] ?: '-') ?></p>
                        <p class="text-xs text-slate-500">Penilai: <?= esc($assessment['assessor_name'] ?: '-') ?> &middot; <?= ($assessment['assessor_type'] ?? '') === 'anak_asuh' ? 'Anak Asuh' : 'Guru' ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold ring-1 ring-inset <?= status_badge($assessment['status'] ?? '') ?>"><?= esc(ucfirst($assessment['status'] ?? '-')) ?></span>
                    <span class="inline-flex items-center gap-2 rounded-xl bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700"><i class="fa-solid fa-star"></i> <?= number_format($average, 2) ?> / 4</span>
                </div>
            </div>

            <div class="mt-5 grid gap-3 sm:grid-cols-4">
                <div class="rounded-xl bg-slate-50 p-3">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Tanggal</p>
                    <p class="mt-1 text-xs font-semibold text-slate-800"><?= tanggal_id($assessment['assessment_date'] ?? null) ?></p>
                </div>
                <div class="rounded-xl bg-slate-50 p-3">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Minggu</p>
                    <p class="mt-1 text-xs font-semibold text-slate-800">Ke-<?= (int) ($assessment['week_number'] ?? 0) ?></p>
                </div>
                <div class="rounded-xl bg-slate-50 p-3">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Bulan</p>
                    <p class="mt-1 text-xs font-semibold text-slate-800"><?= esc($months[(int) ($assessment['month'] ?? 0)] ?? '-') ?></p>
                </div>
                <div class="rounded-xl bg-slate-50 p-3">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Tahun</p>
                    <p class="mt-1 text-xs font-semibold text-slate-800"><?= (int) ($assessment['year'] ?? 0) ?></p>
                </div>
            </div>

            <?php if (! empty($assessment['notes'])): ?>
                <div class="mt-4 rounded-xl border border-slate-100 bg-slate-50 p-4">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Catatan Penilaian</p>
                    <p class="mt-1 text-sm leading-relaxed text-slate-600"><?= nl2br(esc($assessment['notes'])) ?></p>
                </div>
            <?php endif; ?>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="flex items-center gap-2 text-sm font-bold text-slate-900"><i class="fa-solid fa-comment-dots text-indigo-500"></i> Catatan Kualitatif</h3>
            <?php if ($note): ?>
                <div class="mt-4 space-y-4">
                    <?php
                    $noteFields = [
                        ['Kekuatan', $note['strengths'] ?? null, 'emerald', 'thumbs-up'],
                        ['Perkembangan Teramati', $note['development_observed'] ?? null, 'sky', 'arrow-trend-up'],
                        ['Area Pendampingan', $note['areas_to_support'] ?? null, 'amber', 'triangle-exclamation'],
                        ['Strategi Dukungan', $note['support_strategy'] ?? null, 'indigo', 'lightbulb'],
                    ];
                    ?>
                    <?php foreach ($noteFields as [$label, $value, $tone, $icon]): ?>
                        <div class="rounded-xl border border-<?= $tone ?>-100 bg-<?= $tone ?>-50/60 p-3">
                            <p class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-<?= $tone ?>-700"><i class="fa-solid fa-<?= $icon ?>"></i> <?= $label ?></p>
                            <p class="mt-1 text-xs leading-relaxed text-slate-600"><?= esc($value ?: '-') ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="mt-4 rounded-xl bg-slate-50 px-3 py-4 text-center text-xs text-slate-400">Tidak ada catatan kualitatif.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($grouped === []): ?>
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center">
            <i class="fa-solid fa-clipboard mb-3 text-3xl text-slate-300"></i>
            <p class="text-sm text-slate-500">Tidak ada rincian indikator pada penilaian ini.</p>
        </div>
    <?php endif; ?>

    <?php foreach ($grouped as $aspectName => $rows): ?>
        <?php $aspectAvg = array_sum(array_map(static fn ($r) => (float) $r['score'], $rows)) / max(1, count($rows)); ?>
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/60 px-5 py-3.5">
                <div class="flex items-center gap-2">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600"><i class="fa-solid fa-layer-group text-xs"></i></span>
                    <p class="text-sm font-bold text-slate-900"><?= esc($aspectName) ?></p>
                    <span class="rounded-full bg-white px-2 py-0.5 text-[10px] font-semibold text-slate-500 ring-1 ring-slate-200"><?= count($rows) ?> indikator</span>
                </div>
                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-bold <?= $scoreTone($aspectAvg) ?>">Rata-rata <?= number_format($aspectAvg, 2) ?></span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-white text-[11px] uppercase tracking-wider text-slate-400">
                        <tr>
                            <th class="px-4 py-2.5 font-semibold">Kode</th>
                            <th class="px-4 py-2.5 font-semibold">Indikator</th>
                            <th class="px-4 py-2.5 font-semibold">Skor</th>
                            <th class="px-4 py-2.5 font-semibold">Visual</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($rows as $row): ?>
                            <tr class="transition hover:bg-slate-50/70">
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-md bg-slate-100 px-2 py-1 font-mono text-[10px] font-semibold text-slate-600"><?= esc($row['indicator_code'] ?: '-') ?></span>
                                </td>
                                <td class="px-4 py-3 font-medium text-slate-800"><?= esc($row['indicator_name']) ?></td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex h-7 min-w-[2rem] items-center justify-center rounded-lg px-2 text-xs font-extrabold <?= $scoreTone((float) $row['score']) ?>"><?= (int) $row['score'] ?></span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-1">
                                        <?php for ($i = 1; $i <= 4; $i++): ?>
                                            <span class="h-2 w-8 rounded-full <?= $i <= (int) $row['score'] ? 'bg-indigo-500' : 'bg-slate-100' ?>"></span>
                                        <?php endfor; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endforeach; ?>
</div>
