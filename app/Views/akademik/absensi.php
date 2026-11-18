<?php
$inputClass = 'input';
$labelClass = 'label';
$statuses   = ['Hadir' => 'emerald', 'Izin' => 'sky', 'Sakit' => 'amber', 'Alpha' => 'rose'];
$summary    = $summary ?? ['Hadir' => 0, 'Izin' => 0, 'Sakit' => 0, 'Alpha' => 0, 'total' => 0];
$hasFilter  = (int) ($filters['id_rombel'] ?? 0) > 0 && (int) ($filters['id_mata_pelajaran'] ?? 0) > 0;
$exportQuery = http_build_query(array_filter([
    'id_rombel'         => $filters['id_rombel'] ?? '',
    'id_mata_pelajaran' => $filters['id_mata_pelajaran'] ?? '',
    'tahun_ajaran'      => $filters['tahun_ajaran'] ?? '',
    'semester'          => $filters['semester'] ?? '',
]));
?>

<div class="space-y-5">
    <div class="page-header">
        <div>
            <h2 class="page-title">Absensi Mata Pelajaran</h2>
            <p class="page-subtitle">Catat kehadiran anak per sesi mata pelajaran.</p>
        </div>
        <div class="page-actions">
            <a href="<?= site_url($prefix . '/export/absensi/excel') . ($exportQuery ? '?' . $exportQuery : '') ?>" class="btn-secondary"><i class="fa-solid fa-file-excel text-emerald-600"></i> Excel</a>
            <a href="<?= site_url($prefix . '/export/absensi/pdf') . ($exportQuery ? '?' . $exportQuery : '') ?>" target="_blank" class="btn-secondary"><i class="fa-solid fa-file-pdf text-rose-500"></i> PDF</a>
        </div>
    </div>

    <div class="card p-4">
        <form method="get" action="<?= site_url($prefix . '/absensi') ?>" class="grid gap-3 md:grid-cols-2 xl:grid-cols-6">
            <div class="xl:col-span-2">
                <label class="<?= $labelClass ?>">Rombel</label>
                <select name="id_rombel" class="<?= $inputClass ?>">
                    <option value="">Pilih rombel</option>
                    <?php foreach ($rombelOptions as $rombel): ?>
                        <option value="<?= (int) $rombel['id_rombel'] ?>" <?= (int) ($filters['id_rombel'] ?? 0) === (int) $rombel['id_rombel'] ? 'selected' : '' ?>>
                            <?= esc($rombel['kode_rombel'] . ' - ' . $rombel['nama_rombel']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="xl:col-span-2">
                <label class="<?= $labelClass ?>">Mata Pelajaran</label>
                <select name="id_mata_pelajaran" class="<?= $inputClass ?>">
                    <option value="">Pilih mata pelajaran</option>
                    <?php foreach ($mapelOptions as $mapel): ?>
                        <option value="<?= (int) $mapel['id_mata_pelajaran'] ?>" <?= (int) ($filters['id_mata_pelajaran'] ?? 0) === (int) $mapel['id_mata_pelajaran'] ? 'selected' : '' ?>>
                            <?= esc($mapel['kode_mapel'] . ' - ' . $mapel['nama_mapel']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="<?= $labelClass ?>">Tanggal</label>
                <input type="date" name="tanggal" value="<?= esc($filters['tanggal'] ?? date('Y-m-d')) ?>" class="<?= $inputClass ?>">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="btn-dark flex-1"><i class="fa-solid fa-filter mr-1"></i> Tampilkan</button>
                <a href="<?= site_url($prefix . '/absensi') ?>" class="btn-secondary"><i class="fa-solid fa-rotate-left"></i></a>
            </div>
            <?php if ($rombelOptions === [] || $mapelOptions === []): ?>
                <p class="text-xs text-amber-600 md:col-span-2 xl:col-span-6"><i class="fa-solid fa-circle-info mr-1"></i> Belum ada rombel atau mata pelajaran yang tersedia untuk Anda.</p>
            <?php endif; ?>
        </form>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <?php foreach (['Hadir', 'Izin', 'Sakit', 'Alpha'] as $status): ?>
            <?php $tone = $statuses[$status]; ?>
            <div class="card p-4">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold text-stone-500"><?= $status ?></p>
                        <p class="mt-1 text-2xl font-extrabold text-stone-900"><?= number_format((int) ($summary[$status] ?? 0), 0, ',', '.') ?></p>
                    </div>
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-<?= $tone ?>-50 text-<?= $tone ?>-600"><i class="fa-solid fa-<?= $status === 'Hadir' ? 'circle-check' : ($status === 'Izin' ? 'envelope-open-text' : ($status === 'Sakit' ? 'notes-medical' : 'user-xmark')) ?>"></i></span>
                </div>
            </div>
        <?php endforeach; ?>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-700 p-4">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-emerald-200">Total Kehadiran</p>
                    <p class="mt-1 text-2xl font-extrabold text-white"><?= number_format((int) ($summary['total'] ?? 0), 0, ',', '.') ?></p>
                </div>
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/15 text-white"><i class="fa-solid fa-clipboard-check"></i></span>
            </div>
        </div>
    </div>

    <?php if (! $hasFilter): ?>
        <div class="rounded-2xl border border-dashed border-stone-300 bg-white px-6 py-16 text-center">
            <i class="fa-solid fa-clipboard-list mb-3 text-4xl text-stone-300"></i>
            <p class="text-sm font-semibold text-stone-600">Pilih rombel dan mata pelajaran</p>
            <p class="mt-1 text-xs text-stone-400">Daftar anak dan form absensi akan tampil setelah filter dipilih.</p>
        </div>
    <?php else: ?>
        <div class="card">
            <div class="card-header flex flex-col justify-between gap-2 sm:flex-row sm:items-center">
                <div>
                    <h3 class="section-title">Daftar Hadir</h3>
                    <p class="text-xs text-stone-500"><?= count($children) ?> anak &middot; <?= tanggal_id($filters['tanggal'] ?? null) ?></p>
                </div>
                <?php if ($session): ?>
                    <span class="inline-flex items-center gap-1.5 self-start rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700"><i class="fa-solid fa-circle-check"></i> Sudah tersimpan</span>
                <?php endif; ?>
            </div>

            <?php if ($children === []): ?>
                <div class="px-6 py-14 text-center">
                    <i class="fa-solid fa-users-slash mb-3 text-3xl text-stone-300"></i>
                    <p class="page-subtitle">Belum ada anak pada rombel ini.</p>
                </div>
            <?php else: ?>
                <form method="post" action="<?= site_url($prefix . '/absensi/save') ?>" x-data="{ setAll(status) { document.querySelectorAll('input[type=radio][name^=\'status[\']').forEach(el => { if (el.value === status) el.checked = true; }); } }">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id_rombel" value="<?= (int) $filters['id_rombel'] ?>">
                    <input type="hidden" name="id_mata_pelajaran" value="<?= (int) $filters['id_mata_pelajaran'] ?>">
                    <input type="hidden" name="tanggal" value="<?= esc($filters['tanggal'] ?? date('Y-m-d')) ?>">

                    <div class="flex flex-wrap items-center gap-2 border-b border-stone-100 bg-stone-50/60 px-5 py-3">
                        <span class="text-xs font-semibold text-stone-500">Tandai semua:</span>
                        <?php foreach ($statuses as $status => $tone): ?>
                            <button type="button" @click="setAll('<?= $status ?>')" class="inline-flex items-center gap-1.5 rounded-lg bg-<?= $tone ?>-50 px-2.5 py-1.5 text-xs font-semibold text-<?= $tone ?>-700 transition hover:bg-<?= $tone ?>-100"><?= $status ?></button>
                        <?php endforeach; ?>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 font-semibold">No</th>
                                    <th class="px-4 py-3 font-semibold">Nama Anak</th>
                                    <th class="px-4 py-3 font-semibold">Status Kehadiran</th>
                                    <th class="px-4 py-3 font-semibold">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-100">
                                <?php foreach ($children as $index => $child): ?>
                                    <?php
                                    $idAnak  = (int) $child['id_anak'];
                                    $current = $details[$idAnak]['status_kehadiran'] ?? 'Hadir';
                                    $note    = $details[$idAnak]['keterangan'] ?? '';
                                    ?>
                                    <tr class="transition hover:bg-stone-50/70">
                                        <td class="px-4 py-3 text-stone-500"><?= $index + 1 ?></td>
                                        <td class="px-4 py-3">
                                            <p class="font-semibold text-stone-800"><?= esc($child['nama_anak']) ?></p>
                                            <p class="text-xs text-stone-400"><?= esc($child['no_registrasi'] ?: '-') ?> &middot; <?= esc($child['pendidikan'] ?: '-') ?></p>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex flex-wrap gap-1.5">
                                                <?php foreach ($statuses as $status => $tone): ?>
                                                    <label class="cursor-pointer">
                                                        <input type="radio" name="status[<?= $idAnak ?>]" value="<?= $status ?>" <?= $current === $status ? 'checked' : '' ?> class="peer sr-only">
                                                        <span class="inline-flex rounded-lg border border-stone-200 px-2.5 py-1.5 text-xs font-semibold text-stone-500 transition peer-checked:border-<?= $tone ?>-500 peer-checked:bg-<?= $tone ?>-50 peer-checked:text-<?= $tone ?>-700"><?= $status ?></span>
                                                    </label>
                                                <?php endforeach; ?>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="text" name="keterangan[<?= $idAnak ?>]" value="<?= esc($note) ?>" placeholder="Opsional" class="w-full min-w-[10rem] rounded-lg border border-stone-200 bg-stone-50 px-2.5 py-1.5 text-xs outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20">
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="border-t border-stone-100 p-5">
                        <label class="<?= $labelClass ?>">Catatan Sesi</label>
                        <textarea name="catatan" rows="2" class="input" placeholder="Catatan umum sesi absensi (opsional)"><?= esc($session['catatan'] ?? '') ?></textarea>
                        <div class="mt-3 flex justify-end">
                            <button type="submit" class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> Simpan Absensi</button>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="card overflow-hidden">
        <div class="card-header flex items-center justify-between">
            <div>
                <h3 class="section-title">Riwayat Sesi</h3>
                <p class="text-xs text-stone-500"><?= number_format((int) ($total ?? count($recap)), 0, ',', '.') ?> sesi sesuai filter.</p>
            </div>
            <span class="rounded-lg bg-stone-100 px-2.5 py-1 text-xs font-semibold text-stone-600"><?= number_format((int) ($total ?? count($recap)), 0, ',', '.') ?> sesi</span>
        </div>
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th class="px-4 py-3 font-semibold">No</th>
                        <th class="px-4 py-3 font-semibold">Tanggal</th>
                        <th class="px-4 py-3 font-semibold">Rombel</th>
                        <th class="px-4 py-3 font-semibold">Mata Pelajaran</th>
                        <th class="px-4 py-3 font-semibold">Periode</th>
                        <th class="px-4 py-3 font-semibold">Dicatat Oleh</th>
                        <th class="px-4 py-3 font-semibold">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    <?php if ($recap === []): ?>
                        <tr><td colspan="7" class="px-4 py-12 text-center text-sm text-stone-400"><i class="fa-solid fa-clock-rotate-left mb-2 block text-3xl text-stone-300"></i>Belum ada sesi absensi.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($recap as $index => $row): ?>
                        <tr class="transition hover:bg-stone-50/70">
                            <td class="px-4 py-3 text-stone-500"><?= $index + 1 ?></td>
                            <td class="px-4 py-3 font-medium text-stone-700"><?= tanggal_id($row['tanggal_absensi'] ?? null) ?></td>
                            <td class="px-4 py-3 text-stone-600"><?= esc($row['nama_rombel']) ?></td>
                            <td class="px-4 py-3 text-stone-600"><?= esc($row['nama_mapel']) ?></td>
                            <td class="px-4 py-3 text-xs text-stone-500"><?= esc($row['tahun_ajaran']) ?> / Sem. <?= (int) $row['semester'] ?></td>
                            <td class="px-4 py-3 text-stone-600"><?= esc($row['created_by_name'] ?: '-') ?></td>
                            <td class="px-4 py-3 text-xs text-stone-500"><?= esc($row['catatan'] ?: '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="px-5 pb-4">
            <?= view('partials/pagination', ['page' => $page ?? 1, 'perPage' => $perPage ?? 20, 'total' => $total ?? 0]) ?>
        </div>
    </div>
</div>
