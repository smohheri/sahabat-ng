<?php
$months = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
$chartLabels = [];
$chartValues = [];

for ($i = 1; $i <= 12; $i++) {
    $chartLabels[] = $months[$i];
    $chartValues[] = (int) ($monthlyAssessments[$i] ?? 0);
}

$cards = [
    ['label' => 'Total Anak', 'value' => (int) ($stats['total'] ?? 0), 'icon' => 'children', 'tone' => 'emerald', 'hint' => (int) ($stats['aktif'] ?? 0) . ' aktif'],
    ['label' => 'Anak Aktif', 'value' => (int) ($stats['aktif'] ?? 0), 'icon' => 'child-reaching', 'tone' => 'emerald', 'hint' => 'Terdaftar & dibina'],
    ['label' => 'Pengurus', 'value' => (int) $totalPengurus, 'icon' => 'user-tie', 'tone' => 'sky', 'hint' => 'Pengelola LKSA'],
    ['label' => 'Pengajar', 'value' => (int) $totalGuru, 'icon' => 'chalkboard-user', 'tone' => 'amber', 'hint' => 'Tenaga pendidik'],
];

$logColors = [
    'success' => 'bg-emerald-100 text-emerald-600',
    'warning' => 'bg-amber-100 text-amber-600',
    'danger'  => 'bg-rose-100 text-rose-600',
    'primary' => 'bg-emerald-100 text-emerald-700',
    'info'    => 'bg-sky-100 text-sky-600',
];
?>

<div class="space-y-6">
    <div class="relative overflow-hidden rounded-2xl bg-emerald-700 p-6 sm:p-8">
        <div class="relative flex flex-col justify-between gap-6 lg:flex-row lg:items-center">
            <div>
                <p class="text-sm font-medium text-emerald-100">Selamat datang kembali</p>
                <h2 class="mt-2 text-2xl font-extrabold text-white sm:text-3xl"><?= esc($currentUser['nama'] ?? 'Administrator') ?></h2>
                <p class="mt-2 max-w-xl text-sm leading-relaxed text-emerald-100">
                    Ringkasan data <?= esc($settings['nama_lksa'] ?? 'LKSA') ?> per <?= tanggal_id(date('Y-m-d')) ?>. Kelola data anak, akademik, dan penilaian karakter dari satu tempat.
                </p>
            </div>
            <div class="page-actions">
                <a href="<?= site_url('admin/anak/tambah') ?>" class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-xs font-bold text-emerald-700 transition hover:bg-emerald-50">
                    <i class="fa-solid fa-plus"></i> Tambah Anak
                </a>
                <a href="<?= site_url('admin/laporan') ?>" class="inline-flex items-center gap-2 rounded-xl border border-white/30 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-white/10">
                    <i class="fa-solid fa-file-lines"></i> Laporan
                </a>
            </div>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <?php foreach ($cards as $card): ?>
            <div class="card p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold   text-stone-400"><?= esc($card['label']) ?></p>
                        <p class="mt-2 text-3xl font-extrabold text-stone-900"><?= number_format($card['value'], 0, ',', '.') ?></p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-<?= $card['tone'] ?>-50 text-<?= $card['tone'] ?>-600">
                        <i class="fa-solid fa-<?= $card['icon'] ?>"></i>
                    </div>
                </div>
                <p class="mt-3 text-xs font-medium text-stone-500"><?= esc($card['hint']) ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="card p-5 xl:col-span-2">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="section-title">Penilaian Karakter <?= date('Y') ?></h3>
                    <p class="text-xs text-stone-500">Jumlah penilaian per bulan</p>
                </div>
                <span class="rounded-lg bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700"><?= array_sum($chartValues) ?> penilaian</span>
            </div>
            <div class="mt-4 h-64">
                <canvas id="chartAssessments"></canvas>
            </div>
        </div>

        <div class="card p-5">
            <h3 class="section-title">Komposisi Anak</h3>
            <p class="text-xs text-stone-500">Berdasarkan jenis kelamin</p>
            <div class="mx-auto mt-4 h-48 max-w-[220px]">
                <canvas id="chartGender"></canvas>
            </div>
            <div class="mt-4 space-y-2">
                <div class="flex items-center justify-between rounded-xl bg-stone-50 px-3 py-2 text-xs">
                    <span class="flex items-center gap-2 text-stone-600"><span class="h-2 w-2 rounded-full bg-emerald-500"></span> Laki-laki</span>
                    <span class="font-bold text-stone-800"><?= (int) ($stats['laki'] ?? 0) ?></span>
                </div>
                <div class="flex items-center justify-between rounded-xl bg-stone-50 px-3 py-2 text-xs">
                    <span class="flex items-center gap-2 text-stone-600"><span class="h-2 w-2 rounded-full bg-rose-400"></span> Perempuan</span>
                    <span class="font-bold text-stone-800"><?= (int) ($stats['perempuan'] ?? 0) ?></span>
                </div>
                <div class="flex items-center justify-between rounded-xl bg-stone-50 px-3 py-2 text-xs">
                    <span class="flex items-center gap-2 text-stone-600"><span class="h-2 w-2 rounded-full bg-emerald-500"></span> Alumni</span>
                    <span class="font-bold text-stone-800"><?= (int) ($stats['alumni'] ?? 0) ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="card p-4">
            <p class="text-xs font-semibold text-stone-500">Total User</p>
            <p class="mt-1 text-xl font-extrabold text-stone-900"><?= number_format((int) $totalUsers, 0, ',', '.') ?></p>
        </div>
        <div class="card p-4">
            <p class="text-xs font-semibold text-stone-500">Total Penilaian</p>
            <p class="mt-1 text-xl font-extrabold text-stone-900"><?= number_format((int) $totalAssessments, 0, ',', '.') ?></p>
        </div>
        <div class="card p-4">
            <p class="text-xs font-semibold text-stone-500">Anak Nonaktif</p>
            <p class="mt-1 text-xl font-extrabold text-stone-900"><?= number_format((int) ($stats['nonaktif'] ?? 0), 0, ',', '.') ?></p>
        </div>
        <div class="card p-4">
            <p class="text-xs font-semibold text-stone-500">Tahun Masuk Terbaru</p>
            <p class="mt-1 text-xl font-extrabold text-stone-900"><?= $stats['tahun_masuk'] !== [] ? esc((string) array_key_last($stats['tahun_masuk'])) : '-' ?></p>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <div class=card overflow-hidden xl:col-span-2">
            <div class="card-header flex items-center justify-between">
                <h3 class="section-title">Anak Terbaru</h3>
                <a href="<?= site_url('admin/anak_aktif') ?>" class="text-xs font-semibold text-emerald-700 hover:text-emerald-800">Lihat semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="px-5 py-3 font-semibold">Nama</th>
                            <th class="px-5 py-3 font-semibold">Kategori</th>
                            <th class="px-5 py-3 font-semibold">Pendidikan</th>
                            <th class="px-5 py-3 font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        <?php if ($recentAnak === []): ?>
                            <tr><td colspan="4" class="px-5 py-8 text-center text-sm text-stone-400">Belum ada data anak.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($recentAnak as $anak): ?>
                            <tr class="transition hover:bg-stone-50/70">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-xs font-bold text-emerald-700">
                                            <?= esc(strtoupper(mb_substr((string) $anak['nama_anak'], 0, 1))) ?>
                                        </span>
                                        <div>
                                            <a href="<?= site_url('admin/anak/edit/' . $anak['id_anak']) ?>" class="font-semibold text-stone-800 hover:text-emerald-700"><?= esc($anak['nama_anak']) ?></a>
                                            <p class="text-xs text-stone-400"><?= esc($anak['no_registrasi'] ?: '-') ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-stone-600"><?= esc($anak['kategori'] ?: '-') ?></td>
                                <td class="px-5 py-3 text-stone-600"><?= esc($anak['pendidikan'] ?: '-') ?></td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset <?= status_badge($anak['status_anak'] ?? '') ?>"><?= esc($anak['status_anak'] ?: '-') ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header flex items-center justify-between">
                <h3 class="section-title">Aktivitas Terbaru</h3>
                <a href="<?= site_url('admin/logs') ?>" class="text-xs font-semibold text-emerald-700 hover:text-emerald-800">Semua</a>
            </div>
            <div class="max-h-[420px] divide-y divide-stone-100 overflow-y-auto">
                <?php if ($recentLogs === []): ?>
                    <p class="px-5 py-8 text-center text-sm text-stone-400">Belum ada aktivitas.</p>
                <?php endif; ?>
                <?php foreach ($recentLogs as $log): ?>
                    <div class="flex gap-3 px-5 py-3">
                        <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg <?= $logColors[activity_color($log['activity'])] ?? 'bg-stone-100 text-stone-500' ?>">
                            <i class="fa-solid fa-<?= activity_icon($log['activity']) ?> text-xs"></i>
                        </span>
                        <div class="min-w-0">
                            <p class="truncate text-xs font-semibold text-stone-700"><?= esc($log['nama'] ?? 'Sistem') ?></p>
                            <p class="truncate text-xs text-stone-500"><?= esc($log['description'] ?: $log['activity']) ?></p>
                            <p class="mt-0.5 text-[10px] text-stone-400"><?= tanggal_id($log['created_at'], true) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const assessmentCanvas = document.getElementById('chartAssessments');
    if (assessmentCanvas) {
        new Chart(assessmentCanvas, {
            type: 'bar',
            data: {
                labels: <?= json_encode($chartLabels) ?>,
                datasets: [{
                    label: 'Penilaian',
                    data: <?= json_encode($chartValues) ?>,
                    backgroundColor: 'rgba(79, 70, 229, 0.75)',
                    borderRadius: 6,
                    maxBarThickness: 28,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#f1f5f9' } },
                    x: { grid: { display: false } },
                },
            },
        });
    }

    const genderCanvas = document.getElementById('chartGender');
    if (genderCanvas) {
        new Chart(genderCanvas, {
            type: 'doughnut',
            data: {
                labels: ['Laki-laki', 'Perempuan'],
                datasets: [{
                    data: [<?= (int) ($stats['laki'] ?? 0) ?>, <?= (int) ($stats['perempuan'] ?? 0) ?>],
                    backgroundColor: ['#6366f1', '#fb7185'],
                    borderWidth: 0,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: { legend: { display: false } },
            },
        });
    }
});
</script>
