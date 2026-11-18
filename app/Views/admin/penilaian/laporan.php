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
$palette = ['#059669', '#047857', '#065f46', '#064e3b', '#10b981', '#f59e0b', '#ecfdf5', '#d1fae5'];
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
    <div class="page-header">
        <div>
            <h2 class="page-title">Laporan Penilaian Karakter</h2>
            <p class="page-subtitle">Pantau perkembangan karakter anak secara keseluruhan.</p>
        </div>
        <div class="page-actions">
            <a href="<?= site_url('admin/penilaian-karakter/data-penilaian') ?>" class="btn-secondary"><i class="fa-solid fa-clipboard-list text-emerald-600"></i> Data Penilaian</a>
            <a href="<?= site_url('admin/penilaian-karakter/ringkasan-bulanan') ?>" class="btn-primary"><i class="fa-solid fa-calendar-days"></i> Ringkasan</a>
        </div>
    </div>

    <div class="card p-4">
        <form method="get" action="<?= site_url('admin/penilaian-karakter/laporan') ?>" class="grid gap-3 md:grid-cols-2 xl:grid-cols-5">
            <div class="xl:col-span-2">
                <label class="label">Anak</label>
                <select name="id_anak" class="input">
                    <option value="">Semua anak</option>
                    <?php foreach ($anakList as $anak): ?>
                        <option value="<?= (int) $anak['id_anak'] ?>" <?= (int) ($filters['id_anak'] ?? 0) === (int) $anak['id_anak'] ? 'selected' : '' ?>><?= esc($anak['nama_anak']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="label">Penilai</label>
                <select name="assessor_type" class="input">
                    <option value="">Semua</option>
                    <option value="guru" <?= ($filters['assessor_type'] ?? '') === 'guru' ? 'selected' : '' ?>>Guru</option>
                    <option value="anak_asuh" <?= ($filters['assessor_type'] ?? '') === 'anak_asuh' ? 'selected' : '' ?>>Anak Asuh</option>
                </select>
            </div>
            <div>
                <label class="label">Bulan</label>
                <select name="month" class="input">
                    <option value="">Semua</option>
                    <?php foreach ($months as $num => $label): ?>
                        <option value="<?= $num ?>" <?= (int) ($filters['month'] ?? 0) === $num ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <label class="label">Tahun</label>
                    <select name="year" class="input">
                        <option value="">Semua</option>
                        <?php foreach ($years as $year): ?>
                            <option value="<?= $year ?>" <?= (int) ($filters['year'] ?? 0) === $year ? 'selected' : '' ?>><?= $year ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn-dark"><i class="fa-solid fa-filter"></i></button>
                <a href="<?= site_url('admin/penilaian-karakter/laporan') ?>" class="btn-secondary"><i class="fa-solid fa-rotate-left"></i></a>
            </div>
        </form>
    </div>

    <div class="grid gap-5 lg:grid-cols-3">
        <div class="card p-5 lg:col-span-2">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="section-title">Tren Rata-rata Skor per Aspek</h3>
                    <p class="text-xs text-stone-500">Berdasarkan tanggal penilaian</p>
                </div>
                <span class="rounded-lg bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700"><?= count($dates) ?> tanggal</span>
            </div>
            <div class="mt-4 h-72">
                <?php if ($datasets === []): ?>
                    <div class="flex h-full items-center justify-center rounded-xl bg-stone-50">
                        <p class="text-sm text-stone-400">Belum ada data tren.</p>
                    </div>
                <?php else: ?>
                    <canvas id="chartTrend"></canvas>
                <?php endif; ?>
            </div>
        </div>

        <div class="card p-5">
            <h3 class="section-title">Ringkasan Cepat</h3>
            <div class="mt-4 space-y-3">
                <?php
                $totalAssessment = array_sum(array_map(static fn ($row) => (int) $row['total_assessment'], $summary));
                $overall = $summary === [] ? 0 : array_sum(array_map(static fn ($row) => (float) $row['avg_score'] * (int) $row['total_assessment'], $summary)) / max(1, $totalAssessment);
                $top = $summary === [] ? null : $summary[array_search(max(array_column($summary, 'avg_score')), array_column($summary, 'avg_score'), true)];
                ?>
                <div class="rounded-xl bg-emerald-50 p-4">
                    <p class="text-xs font-semibold text-emerald-600">Rata-rata Keseluruhan</p>
                    <p class="mt-1 text-2xl font-extrabold text-emerald-700"><?= number_format($overall, 2) ?><span class="text-sm font-semibold text-emerald-400">/4</span></p>
                </div>
                <div class="rounded-xl bg-emerald-50 p-4">
                    <p class="text-xs font-semibold   text-emerald-500">Total Anak Dinilai</p>
                    <p class="mt-1 text-2xl font-extrabold text-emerald-700"><?= count($summary) ?></p>
                </div>
                <div class="rounded-xl bg-stone-50 p-4">
                    <p class="text-xs font-semibold text-stone-500">Total Penilaian</p>
                    <p class="mt-1 text-2xl font-extrabold text-stone-700"><?= $totalAssessment ?></p>
                </div>
                <?php if ($top): ?>
                    <div class="rounded-xl bg-amber-50 p-4">
                        <p class="text-xs font-semibold   text-amber-600">Skor Tertinggi</p>
                        <p class="mt-1 truncate text-sm font-bold text-amber-800"><?= esc($top['nama_anak']) ?></p>
                        <p class="text-xs font-semibold text-amber-600"><?= number_format((float) $top['avg_score'], 2) ?> / 4</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="card-header flex items-center justify-between">
            <h3 class="section-title">Rekap per Anak</h3>
            <span class="rounded-lg bg-stone-100 px-2.5 py-1 text-xs font-semibold text-stone-600"><?= number_format((int) ($total ?? count($summary)), 0, ',', '.') ?> anak</span>
        </div>
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th class="px-4 py-3 font-semibold">No</th>
                        <th class="px-4 py-3 font-semibold">Nama Anak</th>
                        <th class="px-4 py-3 font-semibold">Jumlah Penilaian</th>
                        <th class="px-4 py-3 font-semibold">Rata-rata</th>
                        <th class="px-4 py-3 font-semibold">Visual</th>
                        <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    <?php if ($rekap === []): ?>
                        <tr><td colspan="6" class="px-4 py-12 text-center text-sm text-stone-400"><i class="fa-solid fa-chart-column mb-2 block text-3xl text-stone-300"></i>Belum ada data penilaian.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($rekap as $index => $row): ?>
                        <?php $avg = (float) $row['avg_score']; ?>
                        <tr class="transition hover:bg-stone-50/70">
                            <td class="px-4 py-3 text-stone-500"><?= $index + 1 ?></td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-50 text-xs font-bold text-emerald-700"><?= esc(strtoupper(mb_substr((string) $row['nama_anak'], 0, 1))) ?></span>
                                    <p class="font-semibold text-stone-800"><?= esc($row['nama_anak']) ?></p>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-stone-600"><?= (int) $row['total_assessment'] ?></td>
                            <td class="px-4 py-3 font-bold text-stone-800"><?= number_format($avg, 2) ?></td>
                            <td class="px-4 py-3">
                                <div class="flex gap-1">
                                    <?php for ($i = 1; $i <= 4; $i++): ?>
                                        <span class="h-2 w-8 rounded-full <?= $i <= round($avg) ? 'bg-emerald-500' : 'bg-stone-100' ?>"></span>
                                    <?php endfor; ?>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="<?= site_url('admin/penilaian-karakter/laporan/detail/' . $row['id_anak'] . ($filterQuery ? '?' . $filterQuery : '')) ?>" class="inline-flex items-center gap-1.5 rounded-lg border border-stone-200 px-3 py-2 text-xs font-semibold text-stone-600 transition hover:bg-emerald-50 hover:text-emerald-700">
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
