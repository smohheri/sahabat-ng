<?php
$items = $items ?? [];
$totalAnak = (int) ($totalAnak ?? count($items));
$assessed  = (int) ($assessed ?? 0);
$avgAll    = (float) ($avgAll ?? 0);
?>

<div class="space-y-5">
    <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
        <div>
            <h2 class="text-lg font-extrabold text-slate-900">Perkembangan Anak</h2>
            <p class="text-sm text-slate-500">Ringkasan capaian karakter seluruh anak dalam lingkup pengajaran Anda.</p>
        </div>
        <a href="<?= site_url('guru/penilaian-karakter') ?>" class="inline-flex items-center gap-2 self-start rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-bold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700">
            <i class="fa-solid fa-star"></i> Nilai Karakter
        </a>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Total Anak</p>
                    <p class="mt-1 text-2xl font-extrabold text-slate-900"><?= number_format($totalAnak, 0, ',', '.') ?></p>
                </div>
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600"><i class="fa-solid fa-children"></i></span>
            </div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Sudah Dinilai</p>
                    <p class="mt-1 text-2xl font-extrabold text-slate-900"><?= number_format($assessed, 0, ',', '.') ?></p>
                </div>
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600"><i class="fa-solid fa-clipboard-check"></i></span>
            </div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Rata-rata Keseluruhan</p>
                    <p class="mt-1 text-2xl font-extrabold text-slate-900"><?= number_format($avgAll, 2) ?><span class="text-sm font-semibold text-slate-400"> / 4</span></p>
                </div>
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-600"><i class="fa-solid fa-star"></i></span>
            </div>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <h3 class="text-sm font-bold text-slate-900">Daftar Perkembangan</h3>
            <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-600"><?= number_format((int) ($total ?? count($items)), 0, ',', '.') ?> anak</span>
        </div>

        <?php if ($items === []): ?>
            <div class="px-5 py-12 text-center">
                <i class="fa-solid fa-chart-line mb-3 text-3xl text-slate-300"></i>
                <p class="text-sm text-slate-500">Belum ada data anak.</p>
            </div>
        <?php else: ?>
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
                        <?php foreach ($items as $index => $item): ?>
                            <?php
                            $progress = $item['progress'] ?? null;
                            $score    = (float) ($progress['avg_score'] ?? 0);
                            $count    = (int) ($progress['total_assessment'] ?? 0);
                            ?>
                            <tr class="transition hover:bg-slate-50/70">
                                <td class="px-4 py-3 text-slate-500"><?= $index + 1 ?></td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <?php if (! empty($item['foto'])): ?>
                                            <img src="<?= upload_url('foto_anak', $item['foto']) ?>" alt="<?= esc($item['nama_anak']) ?>" class="h-9 w-9 rounded-lg object-cover ring-1 ring-slate-200">
                                        <?php else: ?>
                                            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-50 text-xs font-bold text-indigo-600"><?= esc(strtoupper(mb_substr((string) $item['nama_anak'], 0, 1))) ?></span>
                                        <?php endif; ?>
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-slate-800"><?= esc($item['nama_anak']) ?></p>
                                            <p class="text-[11px] text-slate-400"><?= esc($item['no_registrasi'] ?: '-') ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-slate-600">
                                    <?php if ($count > 0): ?>
                                        <span class="inline-flex rounded-full bg-sky-100 px-2.5 py-1 text-[11px] font-semibold text-sky-700"><?= $count ?> penilaian</span>
                                    <?php else: ?>
                                        <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-500">Belum dinilai</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 font-bold text-slate-800"><?= $count > 0 ? number_format($score, 2) : '-' ?></td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-1">
                                        <?php for ($i = 1; $i <= 4; $i++): ?>
                                            <span class="h-2 w-8 rounded-full <?= $count > 0 && $i <= round($score) ? 'bg-indigo-500' : 'bg-slate-100' ?>"></span>
                                        <?php endfor; ?>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="<?= site_url('guru/perkembangan-anak/detail/' . $item['id_anak']) ?>" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 py-2 text-[11px] font-semibold text-slate-600 transition hover:bg-indigo-50 hover:text-indigo-600">
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
        <?php endif; ?>
    </div>
</div>
