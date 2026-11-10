<?php
$months = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$currentYear = (int) date('Y');
$years = range($currentYear, $currentYear - 5);
?>

<div class="space-y-5">
    <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
        <div>
            <h2 class="text-lg font-extrabold text-slate-900">Data Penilaian Karakter</h2>
            <p class="text-sm text-slate-500">Total <?= number_format((int) $total, 0, ',', '.') ?> penilaian tersimpan.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="<?= site_url('admin/penilaian-karakter/ringkasan-mingguan') ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50"><i class="fa-solid fa-calendar-week text-indigo-500"></i> Mingguan</a>
            <a href="<?= site_url('admin/penilaian-karakter/ringkasan-bulanan') ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50"><i class="fa-solid fa-calendar-days text-sky-500"></i> Bulanan</a>
            <a href="<?= site_url('admin/penilaian-karakter/laporan') ?>" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-bold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700"><i class="fa-solid fa-chart-column"></i> Laporan</a>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <form method="get" action="<?= site_url('admin/penilaian-karakter/data-penilaian') ?>" class="grid gap-3 md:grid-cols-2 xl:grid-cols-5">
            <div class="xl:col-span-2">
                <label class="mb-1 block text-[11px] font-semibold uppercase tracking-wider text-slate-400">Anak</label>
                <select name="id_anak" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20">
                    <option value="">Semua anak</option>
                    <?php foreach ($anakList as $anak): ?>
                        <option value="<?= (int) $anak['id_anak'] ?>" <?= (int) ($filters['id_anak'] ?? 0) === (int) $anak['id_anak'] ? 'selected' : '' ?>><?= esc($anak['nama_anak']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="mb-1 block text-[11px] font-semibold uppercase tracking-wider text-slate-400">Penilai</label>
                <select name="assessor_type" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20">
                    <option value="">Semua</option>
                    <option value="guru" <?= ($filters['assessor_type'] ?? '') === 'guru' ? 'selected' : '' ?>>Guru</option>
                    <option value="anak_asuh" <?= ($filters['assessor_type'] ?? '') === 'anak_asuh' ? 'selected' : '' ?>>Anak Asuh</option>
                </select>
            </div>
            <div>
                <label class="mb-1 block text-[11px] font-semibold uppercase tracking-wider text-slate-400">Bulan</label>
                <select name="month" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20">
                    <option value="">Semua</option>
                    <?php foreach ($months as $num => $label): ?>
                        <option value="<?= $num ?>" <?= (int) ($filters['month'] ?? 0) === $num ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="mb-1 block text-[11px] font-semibold uppercase tracking-wider text-slate-400">Tahun</label>
                <select name="year" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20">
                    <option value="">Semua</option>
                    <?php foreach ($years as $year): ?>
                        <option value="<?= $year ?>" <?= (int) ($filters['year'] ?? 0) === $year ? 'selected' : '' ?>><?= $year ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex items-end gap-2 md:col-span-2 xl:col-span-5">
                <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-slate-800"><i class="fa-solid fa-filter mr-1"></i> Filter</button>
                <a href="<?= site_url('admin/penilaian-karakter/data-penilaian') ?>" class="rounded-xl border border-slate-200 px-3 py-2.5 text-xs font-semibold text-slate-500 transition hover:bg-slate-50"><i class="fa-solid fa-rotate-left mr-1"></i> Reset</a>
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-4 py-3 font-semibold">No</th>
                        <th class="px-4 py-3 font-semibold">Anak</th>
                        <th class="px-4 py-3 font-semibold">Tanggal</th>
                        <th class="px-4 py-3 font-semibold">Periode</th>
                        <th class="px-4 py-3 font-semibold">Penilai</th>
                        <th class="px-4 py-3 font-semibold">Rata-rata</th>
                        <th class="px-4 py-3 font-semibold">Indikator</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if ($items === []): ?>
                        <tr><td colspan="9" class="px-4 py-12 text-center text-sm text-slate-400"><i class="fa-solid fa-clipboard-list mb-2 block text-3xl text-slate-300"></i>Tidak ada data penilaian.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($items as $index => $item): ?>
                        <?php $avg = (float) ($item['avg_score'] ?? 0); ?>
                        <tr class="transition hover:bg-slate-50/70">
                            <td class="px-4 py-3 text-slate-500"><?= (($page - 1) * $perPage) + $index + 1 ?></td>
                            <td class="px-4 py-3">
                                <p class="font-semibold text-slate-800"><?= esc($item['nama_anak'] ?: '-') ?></p>
                                <p class="text-[11px] text-slate-400">Oleh: <?= esc($item['assessor_name'] ?: '-') ?></p>
                            </td>
                            <td class="px-4 py-3 text-slate-600"><?= tanggal_id($item['assessment_date'] ?? null) ?></td>
                            <td class="px-4 py-3 text-[11px] text-slate-500">
                                <p>Minggu <?= (int) ($item['week_number'] ?? 0) ?></p>
                                <p><?= esc($months[(int) ($item['month'] ?? 0)] ?? '-') ?> <?= (int) ($item['year'] ?? 0) ?></p>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold <?= ($item['assessor_type'] ?? '') === 'anak_asuh' ? 'bg-violet-100 text-violet-700' : 'bg-sky-100 text-sky-700' ?>">
                                    <?= ($item['assessor_type'] ?? '') === 'anak_asuh' ? 'Anak Asuh' : 'Guru' ?>
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="h-2 w-20 overflow-hidden rounded-full bg-slate-100">
                                        <div class="h-full rounded-full <?= $avg >= 3 ? 'bg-emerald-500' : ($avg >= 2 ? 'bg-amber-500' : 'bg-rose-500') ?>" style="width: <?= min(100, ($avg / 4) * 100) ?>%"></div>
                                    </div>
                                    <span class="text-xs font-bold text-slate-700"><?= number_format($avg, 2) ?></span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-slate-600"><?= (int) ($item['indicator_count'] ?? 0) ?></td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold ring-1 ring-inset <?= status_badge($item['status'] ?? '') ?>"><?= esc(ucfirst($item['status'] ?? '-')) ?></span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="<?= site_url('admin/penilaian-karakter/detail-penilaian/' . $item['id_assessment']) ?>" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 py-2 text-[11px] font-semibold text-slate-600 transition hover:bg-indigo-50 hover:text-indigo-600">
                                    <i class="fa-solid fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="px-4 pb-4">
            <?= view('partials/pagination', ['page' => $page, 'perPage' => $perPage, 'total' => $total]) ?>
        </div>
    </div>
</div>
