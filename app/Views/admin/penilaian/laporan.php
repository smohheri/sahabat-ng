<?php
$months = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$currentYear = (int) date('Y');
$years = range($currentYear, $currentYear - 5);

$trend = $trend ?? [];
$summary = $summary ?? [];
$rekap = $rekap ?? $summary;

$dates = [];
foreach ($trend as $row) {
    $dates[$row['assessment_date']] = true;
}
$labels = array_keys($dates);
sort($labels);

$datasets = [];
$palette = ['#6366f1', '#0ea5e9', '#10b981', '#f59e0b', '#f43f5e', '#8b5cf6', '#14b8a6', '#eab308'];
$byAspect = [];
foreach ($trend as $row) {
    $byAspect[$row['aspect_name']][$row['assessment_date']] = (float) $row['avg_score'];
}
$colorIndex = 0;
foreach ($byAspect as $aspectName => $values) {
    $data = [];
    foreach ($labels as $date) {
        $data[] = $values[$date] ?? null;
    }
    $datasets[] = [
        'label' => $aspectName,
        'data' => $data,
        'borderColor' => $palette[$colorIndex % count($palette)],
        'backgroundColor' => $palette[$colorIndex % count($palette)],
        'tension' => 0.35,
        'spanGaps' => true,
        'pointRadius' => 3,
    ];
    $colorIndex++;
}

$chartLabels = array_map(static fn ($date) => date('d/m', strtotime($date)), $labels);

$filterQuery = http_build_query(array_filter([
    'id_anak'       => $filters['id_anak'] ?? '',
    'assessor_type' => $filters['assessor_type'] ?? '',
    'month'         => $filters['month'] ?? '',
    'year'          => $filters['year'] ?? '',
]));
?>

<div class="space-y-5">
    <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
        <div>
            <h2 class="text-lg font-extrabold text-slate-900">Laporan Penilaian Karakter</h2>
            <p class="text-sm text-slate-500">Pantau perkembangan karakter anak secara keseluruhan.</p>
        </div>
        <div class="flex gap-2">
            <a href="<?= site_url('admin/penilaian-karakter/data-penilaian') ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50"><i class="fa-solid fa-clipboard-list text-indigo-500"></i> Data Penilaian</a>
            <a href="<?= site_url('admin/penilaian-karakter/ringkasan-bulanan') ?>" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-bold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700"><i class="fa-solid fa-calendar-days"></i> Ringkasan</a>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <form method="get" action="<?= site_url('admin/penilaian-karakter/laporan') ?>" class="grid gap-3 md:grid-cols-2 xl:grid-cols-5">
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
            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <label class="mb-1 block text-[11px] font-semibold uppercase tracking-wider text-slate-400">Tahun</label>
                    <select name="year" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20">
                        <option value="">Semua</option>
                        <?php foreach ($years as $year): ?>
                            <option value="<?= $year ?>" <?= (int) ($filters['year'] ?? 0) === $year ? 'selected' : '' ?>><?= $year ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-slate-800"><i class="fa-solid fa-filter"></i></button>
                <a href="<?= site_url('admin/penilaian-karakter/laporan') ?>" class="rounded-xl border border-slate-200 px-3 py-2.5 text-xs font-semibold text-slate-500 transition hover:bg-slate-50"><i class="fa-solid fa-rotate-left"></i></a>
            </div>
        </form>
    </div>

    <div class="grid gap-5 lg:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Tren Rata-rata Skor per Aspek</h3>
                    <p class="text-xs text-slate-500">Berdasarkan tanggal penilaian</p>
                </div>
                <span class="rounded-lg bg-indigo-50 px-2.5 py-1 text-[11px] font-semibold text-indigo-600"><?= count($dates) ?> tanggal</span>
            </div>
            <div class="mt-4 h-72">
                <?php if ($datasets === []): ?>
                    <div class="flex h-full items-center justify-center rounded-xl bg-slate-50">
                        <p class="text-sm text-slate-400">Belum ada data tren.</p>
                    </div>
                <?php else: ?>
                    <canvas id="chartTrend"></canvas>
                <?php endif; ?>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-bold text-slate-900">Ringkasan Cepat</h3>
            <div class="mt-4 space-y-3">
                <?php
                $totalAssessment = array_sum(array_map(static fn ($row) => (int) $row['total_assessment'], $summary));
                $overall = $summary === [] ? 0 : array_sum(array_map(static fn ($row) => (float) $row['avg_score'] * (int) $row['total_assessment'], $summary)) / max(1, $totalAssessment);
                $top = $summary === [] ? null : $summary[array_search(max(array_column($summary, 'avg_score')), array_column($summary, 'avg_score'), true)];
                ?>
                <div class="rounded-xl bg-indigo-50 p-4">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-indigo-500">Rata-rata Keseluruhan</p>
                    <p class="mt-1 text-2xl font-extrabold text-indigo-700"><?= number_format($overall, 2) ?><span class="text-sm font-semibold text-indigo-400">/4</span></p>
                </div>
                <div class="rounded-xl bg-emerald-50 p-4">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-emerald-500">Total Anak Dinilai</p>
                    <p class="mt-1 text-2xl font-extrabold text-emerald-700"><?= count($summary) ?></p>
                </div>
                <div class="rounded-xl bg-slate-50 p-4">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Total Penilaian</p>
                    <p class="mt-1 text-2xl font-extrabold text-slate-700"><?= $totalAssessment ?></p>
                </div>
                <?php if ($top): ?>
                    <div class="rounded-xl bg-amber-50 p-4">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-amber-600">Skor Tertinggi</p>
                        <p class="mt-1 truncate text-sm font-bold text-amber-800"><?= esc($top['nama_anak']) ?></p>
                        <p class="text-xs font-semibold text-amber-600"><?= number_format((float) $top['avg_score'], 2) ?> / 4</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <h3 class="text-sm font-bold text-slate-900">Rekap per Anak</h3>
            <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-600"><?= number_format((int) ($total ?? count($summary)), 0, ',', '.') ?> anak</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-4 py-3 font-semibold">No</th>
                        <th class="px-4 py-3 font-semibold">Nama Anak</th>
                        <th class="px-4 py-3 font-semibold">Jumlah Penilaian</th>
                        <th class="px-4 py-3 font-semibold">Rata-rata</th>
                        <th class="px-4 py-3 font-semibold">Visual</th>
                        <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if ($rekap === []): ?>
                        <tr><td colspan="6" class="px-4 py-12 text-center text-sm text-slate-400"><i class="fa-solid fa-chart-column mb-2 block text-3xl text-slate-300"></i>Belum ada data penilaian.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($rekap as $index => $row): ?>
                        <?php $avg = (float) $row['avg_score']; ?>
                        <tr class="transition hover:bg-slate-50/70">
                            <td class="px-4 py-3 text-slate-500"><?= $index + 1 ?></td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-50 text-xs font-bold text-indigo-600"><?= esc(strtoupper(mb_substr((string) $row['nama_anak'], 0, 1))) ?></span>
                                    <p class="font-semibold text-slate-800"><?= esc($row['nama_anak']) ?></p>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-slate-600"><?= (int) $row['total_assessment'] ?></td>
                            <td class="px-4 py-3 font-bold text-slate-800"><?= number_format($avg, 2) ?></td>
                            <td class="px-4 py-3">
                                <div class="flex gap-1">
                                    <?php for ($i = 1; $i <= 4; $i++): ?>
                                        <span class="h-2 w-8 rounded-full <?= $i <= round($avg) ? 'bg-indigo-500' : 'bg-slate-100' ?>"></span>
                                    <?php endfor; ?>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="<?= site_url('admin/penilaian-karakter/laporan/detail/' . $row['id_anak'] . ($filterQuery ? '?' . $filterQuery : '')) ?>" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 py-2 text-[11px] font-semibold text-slate-600 transition hover:bg-indigo-50 hover:text-indigo-600">
                                    <i class="fa-solid fa-chart-line"></i> Detail
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="px-5 pb-4">
            <?= view('partials/pagination', ['page' => $page ?? 1, 'perPage' => $perPage ?? 15, 'total' => $total ?? 0]) ?>
        </div>
    </div>
</div>

<?php if ($datasets !== []): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('chartTrend');
    if (!canvas) return;

    new Chart(canvas, {
        type: 'line',
        data: {
            labels: <?= json_encode($chartLabels) ?>,
            datasets: <?= json_encode($datasets) ?>,
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true, font: { size: 10 } } } },
            scales: {
                y: { beginAtZero: true, max: 4, ticks: { stepSize: 1 }, grid: { color: '#f1f5f9' } },
                x: { grid: { display: false }, ticks: { font: { size: 10 } } },
            },
        },
    });
});
</script>
<?php endif; ?>
