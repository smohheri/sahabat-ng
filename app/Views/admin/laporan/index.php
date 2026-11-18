<?php
$labels = [
    'data_anak' => ['Data Anak', 'children'],
    'pengurus'  => ['Pengurus', 'user-tie'],
    'dokumen'   => ['Kelengkapan Dokumen', 'folder-open'],
    'keuangan'  => ['Keuangan / SPP', 'wallet'],
    'statistik' => ['Statistik', 'chart-pie'],
    'eksternal' => ['Laporan Eksternal', 'file-export'],
];

$query = http_build_query(array_filter([
    'q'             => $filters['search'] ?? '',
    'status_anak'   => $filters['status_anak'] ?? '',
    'jenis_kelamin' => $filters['jenis_kelamin'] ?? '',
    'pendidikan'    => $filters['pendidikan'] ?? '',
]));

$exportPdf = match ($jenis) {
    'pengurus'  => site_url('admin/export_pdf_pengurus'),
    'dokumen'   => site_url('admin/export_pdf_dokumen'),
    'statistik' => site_url('admin/generate_pdf_statistik'),
    'eksternal' => site_url('admin/export_pdf_eksternal') . ($query ? '?' . $query : ''),
    default     => site_url('admin/export_pdf_anak') . ($query ? '?' . $query : ''),
};
$exportExcel = match ($jenis) {
    'pengurus'  => site_url('admin/export_excel_pengurus'),
    'dokumen'   => site_url('admin/export_excel_dokumen'),
    'eksternal' => site_url('admin/export_excel_eksternal') . ($query ? '?' . $query : ''),
    default     => site_url('admin/export_excel_anak') . ($query ? '?' . $query : ''),
};
?>

<div class="space-y-5">
    <div class="page-header">
        <div>
            <h2 class="page-title">Pusat Laporan</h2>
            <p class="page-subtitle">Cetak atau unduh data lembaga dalam format PDF dan Excel.</p>
        </div>
        <div class="page-actions">
            <?php if ($jenis !== 'keuangan'): ?>
                <a href="<?= $exportExcel ?>" class="btn-secondary">
                    <i class="fa-solid fa-file-excel text-emerald-600"></i> Excel
                </a>
            <?php endif; ?>
            <a href="<?= $exportPdf ?>" target="_blank" class="btn-primary">
                <i class="fa-solid fa-file-pdf"></i> Unduh PDF
            </a>
        </div>
    </div>

    <div class="page-actions">
        <?php foreach ($jenisList as $key): ?>
            <?php $active = $key === $jenis; ?>
            <a href="<?= site_url('admin/laporan/' . $key) ?>"
               class="inline-flex items-center gap-2 rounded-xl border px-4 py-2.5 text-xs font-semibold transition <?= $active ? 'border-emerald-600 bg-emerald-700 text-white' : 'border-stone-200 bg-white text-stone-600 hover:bg-stone-50' ?>">
                <i class="fa-solid fa-<?= $labels[$key][1] ?>"></i> <?= esc($labels[$key][0]) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if (in_array($jenis, ['data_anak', 'dokumen', 'keuangan', 'eksternal'], true)): ?>
        <div class="card p-4">
            <form method="get" action="<?= site_url('admin/laporan/' . $jenis) ?>" class="grid gap-3 md:grid-cols-2 xl:grid-cols-5">
                <div class="xl:col-span-2">
                    <label class="label">Cari</label>
                    <input type="text" name="q" value="<?= esc($filters['search'] ?? '') ?>" placeholder="Nama, NIK, sekolah..."
                           class="input">
                </div>
                <div>
                    <label class="label">Status</label>
                    <select name="status_anak" class="input">
                        <option value="">Semua</option>
                        <?php foreach (['Aktif', 'Nonaktif', 'Alumni'] as $status): ?>
                            <option value="<?= $status ?>" <?= ($filters['status_anak'] ?? '') === $status ? 'selected' : '' ?>><?= $status ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="label">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="input">
                        <option value="">Semua</option>
                        <option value="L" <?= ($filters['jenis_kelamin'] ?? '') === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="P" <?= ($filters['jenis_kelamin'] ?? '') === 'P' ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="btn-dark flex-1"><i class="fa-solid fa-filter mr-1"></i> Filter</button>
                    <a href="<?= site_url('admin/laporan/' . $jenis) ?>" class="btn-secondary"><i class="fa-solid fa-rotate-left"></i></a>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <?php if ($jenis === 'statistik'): ?>
        <?php $stats = $stats ?? []; ?>
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <?php
            $cards = [
                ['Total Anak', $stats['total'] ?? 0, 'children', 'emerald'],
                ['Aktif', $stats['aktif'] ?? 0, 'child-reaching', 'emerald'],
                ['Alumni', $stats['alumni'] ?? 0, 'graduation-cap', 'amber'],
                ['Nonaktif', $stats['nonaktif'] ?? 0, 'user-slash', 'rose'],
            ];
            ?>
            <?php foreach ($cards as [$label, $value, $icon, $tone]): ?>
                <div class="card p-5">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-semibold text-stone-500"><?= $label ?></p>
                            <p class="mt-1 text-3xl font-extrabold text-stone-900"><?= number_format((int) $value, 0, ',', '.') ?></p>
                        </div>
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-<?= $tone ?>-50 text-<?= $tone ?>-600"><i class="fa-solid fa-<?= $icon ?>"></i></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="grid gap-5 lg:grid-cols-2">
            <div class="card p-5">
                <h3 class="section-title">Berdasarkan Pendidikan</h3>
                <div class="mt-4 space-y-3">
                    <?php
                    $pendidikan = [
                        'TK / PAUD' => $stats['tk'] ?? 0,
                        'SD / MI'   => $stats['sd'] ?? 0,
                        'SMP / MTs' => $stats['smp'] ?? 0,
                        'SMA / SMK' => $stats['sma'] ?? 0,
                        'Perguruan Tinggi' => $stats['pt'] ?? 0,
                    ];
                    $maxPend = max(1, max($pendidikan));
                    ?>
                    <?php foreach ($pendidikan as $label => $value): ?>
                        <div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-medium text-stone-600"><?= $label ?></span>
                                <span class="font-bold text-stone-800"><?= (int) $value ?></span>
                            </div>
                            <div class="mt-1.5 h-2 overflow-hidden rounded-full bg-stone-100">
                                <div class="h-full rounded-full bg-emerald-500" style="width: <?= round(($value / $maxPend) * 100) ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="card p-5">
                <h3 class="section-title">Kelompok Usia</h3>
                <div class="mt-4 space-y-3">
                    <?php
                    $usia = [
                        '0 - 5 tahun' => $stats['usia_0_5'] ?? 0,
                        '6 - 12 tahun' => $stats['usia_5_12'] ?? 0,
                        '13 - 17 tahun' => $stats['usia_13_17'] ?? 0,
                        '> 17 tahun' => $stats['usia_17'] ?? 0,
                    ];
                    $maxUsia = max(1, max($usia));
                    ?>
                    <?php foreach ($usia as $label => $value): ?>
                        <div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-medium text-stone-600"><?= $label ?></span>
                                <span class="font-bold text-stone-800"><?= (int) $value ?></span>
                            </div>
                            <div class="mt-1.5 h-2 overflow-hidden rounded-full bg-stone-100">
                                <div class="h-full rounded-full bg-sky-500" style="width: <?= round(($value / $maxUsia) * 100) ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="card overflow-hidden">
            <div class="card-header flex items-center justify-between">
                <h3 class="section-title"><?= esc($labels[$jenis][0]) ?></h3>
                <span class="rounded-lg bg-stone-100 px-2.5 py-1 text-xs font-semibold text-stone-600"><?= number_format((int) ($total ?? count($items ?? [])), 0, ',', '.') ?> baris</span>
            </div>

            <?php if ($jenis === 'keuangan'): ?>
                <?php $ringkasan = $keuangan ?? ['total' => 0, 'per_pendidikan' => [], 'jumlah_anak' => 0]; ?>
                <div class="grid gap-4 border-b border-stone-100 p-5 sm:grid-cols-3">
                    <div class="rounded-2xl bg-emerald-50 p-4">
                        <p class="text-xs font-semibold text-emerald-600">Total SPP</p>
                        <p class="mt-1 text-2xl font-extrabold text-emerald-700"><?= rupiah($ringkasan['total'] ?? 0) ?></p>
                    </div>
                    <div class="rounded-2xl bg-emerald-50 p-4">
                        <p class="text-xs font-semibold   text-emerald-500">Jumlah Anak</p>
                        <p class="mt-1 text-2xl font-extrabold text-emerald-700"><?= number_format((int) ($ringkasan['jumlah_anak'] ?? 0), 0, ',', '.') ?></p>
                    </div>
                    <div class="rounded-2xl bg-stone-50 p-4">
                        <p class="text-xs font-semibold text-stone-500">Rata-rata SPP</p>
                        <p class="mt-1 text-2xl font-extrabold text-stone-700">
                            <?= rupiah(((float) ($ringkasan['total'] ?? 0)) / max(1, (int) ($ringkasan['jumlah_anak'] ?? 1))) ?>
                        </p>
                    </div>
                </div>
                <div class="p-5">
                    <h4 class="text-xs font-bold   text-stone-500">Rincian per Jenjang</h4>
                    <div class="mt-3 space-y-2">
                        <?php foreach (($ringkasan['per_pendidikan'] ?? []) as $jenjang => $nominal): ?>
                            <div class="flex items-center justify-between rounded-xl bg-stone-50 px-4 py-2.5 text-sm">
                                <span class="font-medium text-stone-600"><?= esc($jenjang) ?></span>
                                <span class="font-bold text-stone-800"><?= rupiah($nominal) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <?php if (in_array($jenis, ['data_anak', 'eksternal', 'keuangan'], true)): ?>
                            <tr>
                                <th class="px-4 py-3 font-semibold">No</th>
                                <th class="px-4 py-3 font-semibold">No. Registrasi</th>
                                <th class="px-4 py-3 font-semibold">Nama Anak</th>
                                <th class="px-4 py-3 font-semibold">Jenis Kelamin</th>
                                <th class="px-4 py-3 font-semibold">Tempat / Tgl Lahir</th>
                                <th class="px-4 py-3 font-semibold">Pendidikan</th>
                                <th class="px-4 py-3 font-semibold">Status</th>
                                <th class="px-4 py-3 font-semibold">Wilayah</th>
                            </tr>
                        <?php elseif ($jenis === 'pengurus'): ?>
                            <tr>
                                <th class="px-4 py-3 font-semibold">No</th>
                                <th class="px-4 py-3 font-semibold">Nama</th>
                                <th class="px-4 py-3 font-semibold">Jabatan</th>
                                <th class="px-4 py-3 font-semibold">No. HP</th>
                                <th class="px-4 py-3 font-semibold">Email</th>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <th class="px-4 py-3 font-semibold">No</th>
                                <th class="px-4 py-3 font-semibold">Nama Anak</th>
                                <th class="px-4 py-3 font-semibold">No. KK</th>
                                <th class="px-4 py-3 text-center font-semibold">Kartu Keluarga</th>
                                <th class="px-4 py-3 text-center font-semibold">Akta Lahir</th>
                                <th class="px-4 py-3 text-center font-semibold">Dokumen Pendukung</th>
                            </tr>
                        <?php endif; ?>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        <?php if (($items ?? []) === []): ?>
                            <tr><td colspan="8" class="px-4 py-12 text-center text-sm text-stone-400"><i class="fa-solid fa-inbox mb-2 block text-3xl text-stone-300"></i>Tidak ada data.</td></tr>
                        <?php endif; ?>

                        <?php foreach (($items ?? []) as $index => $row): ?>
                            <tr class="transition hover:bg-stone-50/70">
                                <?php if ($jenis === 'dokumen'): ?>
                                    <?php
                                    // rows are numeric arrays: [no, nama, no_kk, kk, akta, pendukung]
                                    $docCheck = static fn ($cell): string => $cell === 'Lengkap'
                                        ? '<i class="fa-solid fa-circle-check text-xl text-emerald-500"></i>'
                                        : '<i class="fa-solid fa-circle-xmark text-xl text-rose-400"></i>';
                                    ?>
                                    <td class="px-4 py-3 text-stone-500"><?= esc((string) ($row[0] ?? $index + 1)) ?></td>
                                    <td class="px-4 py-3 font-semibold text-stone-800"><?= esc((string) ($row[1] ?? '-')) ?></td>
                                    <td class="px-4 py-3 text-stone-600"><?= esc((string) ($row[2] ?? '-')) ?></td>
                                    <td class="px-4 py-3 text-center"><?= $docCheck($row[3] ?? '') ?></td>
                                    <td class="px-4 py-3 text-center"><?= $docCheck($row[4] ?? '') ?></td>
                                    <td class="px-4 py-3 text-center"><?= $docCheck($row[5] ?? '') ?></td>
                                <?php elseif ($jenis === 'pengurus'): ?>
                                    <td class="px-4 py-3 text-stone-500"><?= $index + 1 ?></td>
                                    <td class="px-4 py-3 font-semibold text-stone-800"><?= esc($row['nama_pengurus'] ?? '-') ?></td>
                                    <td class="px-4 py-3 text-stone-600"><?= esc($row['jabatan'] ?: '-') ?></td>
                                    <td class="px-4 py-3 text-stone-600"><?= esc($row['no_hp'] ?: '-') ?></td>
                                    <td class="px-4 py-3 text-stone-600"><?= esc($row['email'] ?: '-') ?></td>
                                <?php elseif (in_array($jenis, ['data_anak', 'eksternal', 'keuangan'], true)): ?>
                                    <?php
                                    // numeric rows: [0]=no, [1]=no_reg, [2]=nama, [3]=jk, [4]=ttl, [5]=umur, [6]=kategori, [7]=pendidikan, [8]=status, [9]=wilayah
                                    $no       = $row[0] ?? ($index + 1);
                                    $noReg    = $row[1] ?? '-';
                                    $nama     = $row[2] ?? '-';
                                    $jk       = $row[3] ?? '-';
                                    $ttl      = $row[4] ?? '-';
                                    $pendidikan = $row[7] ?? '-';
                                    $status   = $row[8] ?? '-';
                                    $wilayah  = $row[9] ?? '-';
                                    ?>
                                    <td class="px-4 py-3 text-stone-500"><?= esc((string) $no) ?></td>
                                    <td class="px-4 py-3 text-stone-600"><?= esc((string) $noReg) ?></td>
                                    <td class="px-4 py-3">
                                        <p class="font-semibold text-stone-800"><?= esc((string) $nama) ?></p>
                                    </td>
                                    <td class="px-4 py-3 text-stone-600"><?= esc((string) $jk) ?></td>
                                    <td class="px-4 py-3 text-stone-600"><?= esc((string) $ttl) ?></td>
                                    <td class="px-4 py-3 text-stone-600"><?= esc((string) $pendidikan) ?></td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset <?= status_badge((string) $status) ?>"><?= esc((string) $status) ?></span>
                                    </td>
                                    <td class="px-4 py-3 text-stone-600"><?= esc((string) $wilayah) ?></td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="px-5 pb-4">
                <?= view('partials/pagination', ['page' => $page ?? 1, 'perPage' => $perPage ?? 15, 'total' => $total ?? 0]) ?>
            </div>
        </div>
    <?php endif; ?>
</div>
