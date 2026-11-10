<?php
$months = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$aspectAvg = $aspectAvg ?? [];
$indicators = $indicators ?? [];
$history = $history ?? [];

$grouped = [];
foreach ($indicators as $row) {
    $grouped[$row['aspect_name']][] = $row;
}

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

$avgColor = static function (float $score): string {
    return $score >= 3 ? 'bg-emerald-500' : ($score >= 2 ? 'bg-amber-500' : 'bg-rose-500');
};
?>

<div class="space-y-5">
    <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
        <div>
            <h2 class="text-lg font-extrabold text-slate-900">Detail Laporan Karakter</h2>
            <p class="text-sm text-slate-500"><?= esc($anak['nama_anak']) ?> &middot; <?= esc($anak['no_registrasi'] ?: 'Tanpa No. Registrasi') ?></p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="<?= site_url('admin/penilaian-karakter/laporan') ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
            <a href="<?= site_url('admin/penilaian-karakter/laporan/detail/' . $anak['id_anak'] . '/export-pdf') ?>" target="_blank" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-bold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700"><i class="fa-solid fa-file-pdf"></i> Unduh PDF</a>
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
                    <p class="text-xs text-slate-500"><?= esc($anak['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan') ?> &middot; <?= esc($anak['pendidikan'] ?: '-') ?></p>
                    <span class="mt-1 inline-flex rounded-full px-2.5 py-0.5 text-[10px] font-semibold ring-1 ring-inset <?= status_badge($anak['status_anak'] ?? '') ?>"><?= esc($anak['status_anak'] ?: '-') ?></span>
                </div>
            </div>

            <div class="mt-4 grid grid-cols-2 gap-3">
                <div class="rounded-xl bg-slate-50 p-3">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Total Penilaian</p>
                    <p class="mt-1 text-lg font-extrabold text-slate-800"><?= count($history) ?></p>
                </div>
                <div class="rounded-xl bg-slate-50 p-3">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Indikator</p>
                    <p class="mt-1 text-lg font-extrabold text-slate-800"><?= count($indicators) ?></p>
                </div>
            </div>

            <div class="mt-4 space-y-2 text-xs">
                <div class="flex items-center justify-between"><span class="text-slate-400">Asal</span><span class="font-semibold text-slate-700"><?= esc($anak['nama_kabupaten'] ?: ($anak['nama_provinsi'] ?: '-')) ?></span></div>
                <div class="flex items-center justify-between"><span class="text-slate-400">Sekolah</span><span class="font-semibold text-slate-700"><?= esc($anak['nama_sekolah'] ?: '-') ?></span></div>
                <div class="flex items-center justify-between"><span class="text-slate-400">Masuk</span><span class="font-semibold text-slate-700"><?= tanggal_id($anak['tanggal_masuk'] ?? null) ?></span></div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900">Rata-rata per Aspek</h3>
                <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-600"><?= count($aspectAvg) ?> aspek</span>
            </div>
            <?php if ($aspectAvg === []): ?>
                <p class="mt-6 rounded-xl bg-slate-50 px-4 py-8 text-center text-sm text-slate-400">Belum ada data indikator.</p>
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
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <h3 class="text-sm font-bold text-slate-900">Riwayat Penilaian</h3>
            <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-600"><?= count($history) ?> data</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-4 py-3 font-semibold">No</th>
                        <th class="px-4 py-3 font-semibold">Tanggal</th>
                        <th class="px-4 py-3 font-semibold">Penilai</th>
                        <th class="px-4 py-3 font-semibold">Tipe</th>
                        <th class="px-4 py-3 font-semibold">Rata-rata</th>
                        <th class="px-4 py-3 font-semibold">Indikator</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if ($history === []): ?>
                        <tr><td colspan="8" class="px-4 py-10 text-center text-sm text-slate-400">Belum ada riwayat penilaian.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($history as $index => $item): ?>
                        <tr class="transition hover:bg-slate-50/70">
                            <td class="px-4 py-3 text-slate-500"><?= $index + 1 ?></td>
                            <td class="px-4 py-3 text-slate-600"><?= tanggal_id($item['assessment_date'] ?? null) ?></td>
                            <td class="px-4 py-3 text-slate-600"><?= esc($item['assessor_name'] ?: '-') ?></td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold <?= ($item['assessor_type'] ?? '') === 'anak_asuh' ? 'bg-violet-100 text-violet-700' : 'bg-sky-100 text-sky-700' ?>">
                                    <?= ($item['assessor_type'] ?? '') === 'anak_asuh' ? 'Anak Asuh' : 'Guru' ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 font-bold text-slate-800"><?= number_format((float) ($item['avg_score'] ?? 0), 2) ?></td>
                            <td class="px-4 py-3 text-slate-600"><?= (int) ($item['indicator_count'] ?? 0) ?></td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold ring-1 ring-inset <?= status_badge($item['status'] ?? '') ?>"><?= esc(ucfirst($item['status'] ?? '-')) ?></span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="<?= site_url('admin/penilaian-karakter/detail-penilaian/' . $item['id_assessment']) ?>" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 py-2 text-[11px] font-semibold text-slate-600 transition hover:bg-indigo-50 hover:text-indigo-600"><i class="fa-solid fa-eye"></i> Detail</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if ($grouped !== []): ?>
        <div class="space-y-4">
            <h3 class="text-sm font-bold text-slate-900">Rincian Skor Indikator</h3>
            <?php foreach ($grouped as $aspectName => $rows): ?>
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/60 px-5 py-3">
                        <div class="flex items-center gap-2">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600"><i class="fa-solid fa-layer-group text-xs"></i></span>
                            <p class="text-sm font-bold text-slate-900"><?= esc($aspectName) ?></p>
                        </div>
                        <span class="rounded-full bg-white px-2.5 py-1 text-[10px] font-semibold text-slate-500 ring-1 ring-slate-200"><?= count($rows) ?> indikator</span>
                    </div>
                    <div class="divide-y divide-slate-100">
                        <?php foreach ($rows as $row): ?>
                            <?php $avg = (float) $row['avg_score']; ?>
                            <div class="flex flex-col gap-2 px-5 py-3 sm:flex-row sm:items-center sm:justify-between">
                                <div class="min-w-0">
                                    <p class="text-xs font-semibold text-slate-700"><?= esc($row['indicator_name']) ?></p>
                                    <p class="text-[10px] text-slate-400"><?= esc($row['indicator_code'] ?: '-') ?> &middot; <?= (int) $row['total'] ?> penilaian</p>
                                </div>
                                <div class="flex shrink-0 items-center gap-3">
                                    <div class="h-2 w-24 overflow-hidden rounded-full bg-slate-100">
                                        <div class="h-full rounded-full <?= $avgColor($avg) ?>" style="width: <?= min(100, ($avg / 4) * 100) ?>%"></div>
                                    </div>
                                    <span class="inline-flex min-w-[3rem] justify-center rounded-lg px-2 py-1 text-xs font-extrabold <?= $scoreTone($avg) ?>"><?= number_format($avg, 2) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
