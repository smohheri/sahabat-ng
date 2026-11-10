<?php
$groups = $groups ?? [];

$yearFilter     = max(0, (int) ($_GET['year'] ?? 0));
$assessorFilter = (string) ($_GET['assessor'] ?? 'guru');

// Tahun yang tersedia dari data.
$years = [];
foreach ($groups as $g) {
    foreach ($g['rows'] as $r) {
        $years[(int) $r['year']] = (int) $r['year'];
    }
}
krsort($years);
$years = array_values($years);
if ($yearFilter <= 0) {
    $yearFilter = $years[0] ?? (int) date('Y');
}

$weeks = range(1, 52);

$tone = static fn (float $s): string => $s >= 3.5
    ? 'bg-emerald-100 text-emerald-700 ring-emerald-300/60'
    : ($s >= 2.5
        ? 'bg-sky-100 text-sky-700 ring-sky-300/60'
        : ($s >= 1.5
            ? 'bg-amber-100 text-amber-700 ring-amber-300/60'
            : 'bg-rose-100 text-rose-700 ring-rose-300/60'));
?>

<div class="space-y-5">
    <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
        <div>
            <h2 class="text-lg font-extrabold text-slate-900">Ringkasan Mingguan</h2>
            <p class="text-sm text-slate-500">Matriks skor karakter per aspek &amp; minggu.</p>
        </div>
        <div class="flex gap-2">
            <a href="<?= site_url('admin/penilaian-karakter/ringkasan-bulanan') ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50"><i class="fa-solid fa-calendar-days text-sky-500"></i> Bulanan</a>
            <a href="<?= site_url('admin/penilaian-karakter/data-penilaian') ?>" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-bold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700"><i class="fa-solid fa-clipboard-list"></i> Data Penilaian</a>
        </div>
    </div>

    <!-- Filter -->
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <form method="get" action="<?= site_url('admin/penilaian-karakter/ringkasan-mingguan') ?>" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="mb-1 block text-[11px] font-semibold uppercase tracking-wider text-slate-400">Tahun</label>
                <select name="year" class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20">
                    <?php foreach ($years as $y): ?>
                        <option value="<?= $y ?>" <?= $y === $yearFilter ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="mb-1 block text-[11px] font-semibold uppercase tracking-wider text-slate-400">Penilai</label>
                <select name="assessor" class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20">
                    <option value="guru" <?= $assessorFilter === 'guru' ? 'selected' : '' ?>>Guru</option>
                    <option value="anak_asuh" <?= $assessorFilter === 'anak_asuh' ? 'selected' : '' ?>>Anak Asuh</option>
                    <option value="all" <?= $assessorFilter === 'all' ? 'selected' : '' ?>>Semua (rata-rata)</option>
                </select>
            </div>
            <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-slate-800">Terapkan</button>
        </form>
    </div>

    <?php if ($groups === []): ?>
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
            <i class="fa-solid fa-calendar-week mb-3 text-3xl text-slate-300"></i>
            <p class="text-sm text-slate-500">Belum ada ringkasan mingguan. Ringkasan dibuat otomatis setelah penilaian disimpan.</p>
        </div>
    <?php else: ?>
        <div class="space-y-5">
            <?php
            $renderedAny = false;
            foreach ($groups as $group):
                $aspectRows = [];
                foreach ($group['rows'] as $row) {
                    if ((int) $row['year'] !== $yearFilter) {
                        continue;
                    }
                    $assessor = $row['assessor_type'] ?? 'guru';
                    if ($assessorFilter !== 'all' && $assessor !== $assessorFilter) {
                        continue;
                    }
                    $idAspect = (int) $row['id_aspect'];
                    $week     = (int) $row['week_number'];
                    $aspectRows[$idAspect]['name'] = $row['aspect_name'];
                    $aspectRows[$idAspect]['weeks'][$week]['sum'] = ($aspectRows[$idAspect]['weeks'][$week]['sum'] ?? 0) + (float) $row['avg_score'];
                    $aspectRows[$idAspect]['weeks'][$week]['n']   = ($aspectRows[$idAspect]['weeks'][$week]['n'] ?? 0) + 1;
                }
                ksort($aspectRows);
                if ($aspectRows === []) {
                    continue;
                }
                $renderedAny = true;
            ?>
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/60 px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-50 text-xs font-bold text-indigo-600"><?= esc(strtoupper(mb_substr((string) $group['nama_anak'], 0, 1))) ?></span>
                            <div>
                                <p class="text-sm font-bold text-slate-900"><?= esc($group['nama_anak']) ?></p>
                                <p class="text-[11px] text-slate-400">Tahun <?= $yearFilter ?> · <?= $assessorFilter === 'all' ? 'Guru & Anak Asuh' : ($assessorFilter === 'anak_asuh' ? 'Anak Asuh' : 'Guru') ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-white text-[10px] uppercase tracking-wider text-slate-400">
                                <tr>
                                    <th class="sticky left-0 z-10 bg-white px-4 py-2.5 font-semibold">Aspek</th>
                                    <?php foreach ($weeks as $w): ?>
                                        <th class="px-1.5 py-2.5 text-center font-semibold" title="Minggu ke-<?= $w ?>">W<?= $w ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php foreach ($aspectRows as $idAspect => $aspect): ?>
                                    <tr class="transition hover:bg-slate-50/70">
                                        <td class="sticky left-0 z-10 whitespace-nowrap bg-white px-4 py-2.5 font-medium text-slate-800"><?= esc($aspect['name']) ?></td>
                                        <?php foreach ($weeks as $w): ?>
                                            <td class="px-1.5 py-2 text-center">
                                                <?php if (isset($aspect['weeks'][$w])):
                                                    $avg = $aspect['weeks'][$w]['sum'] / max(1, $aspect['weeks'][$w]['n']); ?>
                                                    <span class="inline-flex h-7 min-w-[30px] items-center justify-center rounded-md px-1 text-[11px] font-bold ring-1 <?= $tone($avg) ?>"><?= number_format($avg, 1) ?></span>
                                                <?php else: ?>
                                                    <span class="text-slate-300">–</span>
                                                <?php endif; ?>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (! $renderedAny): ?>
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
                    <i class="fa-solid fa-filter-circle-xmark mb-3 text-3xl text-slate-300"></i>
                    <p class="text-sm text-slate-500">Tidak ada data untuk filter ini.</p>
                </div>
            <?php endif; ?>
        </div>

        <?= view('partials/pagination', ['page' => $page ?? 1, 'perPage' => $perPage ?? 10, 'total' => $total ?? 0]) ?>
    <?php endif; ?>
</div>