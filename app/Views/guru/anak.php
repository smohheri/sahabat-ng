<?php
$items = $items ?? [];
?>

<div class="space-y-5" x-data="{ q: '' }">
    <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
        <div>
            <h2 class="text-lg font-extrabold text-slate-900">Data Anak</h2>
            <p class="text-sm text-slate-500"><?= number_format((int) ($total ?? count($items)), 0, ',', '.') ?> anak dalam lingkup pengajaran Anda.</p>
        </div>
        <a href="<?= site_url('guru/perkembangan-anak') ?>" class="inline-flex items-center gap-2 self-start rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50">
            <i class="fa-solid fa-chart-line text-indigo-500"></i> Lihat Perkembangan
        </a>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i class="fa-solid fa-magnifying-glass text-xs"></i></span>
            <input type="text" x-model="q" placeholder="Cari nama, nomor registrasi, atau sekolah..."
                   class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-9 pr-3 text-sm outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20">
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-4 py-3 font-semibold">No</th>
                        <th class="px-4 py-3 font-semibold">Nama Anak</th>
                        <th class="px-4 py-3 font-semibold">No. Registrasi</th>
                        <th class="px-4 py-3 font-semibold">L/P</th>
                        <th class="px-4 py-3 font-semibold">Pendidikan</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if ($items === []): ?>
                        <tr><td colspan="7" class="px-4 py-12 text-center text-sm text-slate-400"><i class="fa-solid fa-children mb-2 block text-3xl text-slate-300"></i>Belum ada data anak.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($items as $index => $item): ?>
                        <?php $haystack = strtolower(($item['nama_anak'] ?? '') . ' ' . ($item['no_registrasi'] ?? '') . ' ' . ($item['nama_sekolah'] ?? '')); ?>
                        <tr class="transition hover:bg-slate-50/70" x-show="q === '' || '<?= esc($haystack, 'js') ?>'.includes(q.toLowerCase())">
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
                                        <p class="truncate text-[11px] text-slate-400"><?= esc($item['nama_sekolah'] ?: 'Sekolah belum diisi') ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3"><span class="inline-flex rounded-md bg-slate-100 px-2 py-1 font-mono text-[11px] font-semibold text-slate-600"><?= esc($item['no_registrasi'] ?: '-') ?></span></td>
                            <td class="px-4 py-3 text-slate-600"><?= ($item['jenis_kelamin'] ?? '') === 'L' ? 'L' : 'P' ?></td>
                            <td class="px-4 py-3 text-slate-600"><?= esc($item['pendidikan'] ?: '-') ?></td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold ring-1 ring-inset <?= status_badge($item['status_anak'] ?? '') ?>"><?= esc($item['status_anak'] ?: '-') ?></span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="<?= site_url('guru/perkembangan-anak/detail/' . $item['id_anak']) ?>" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 py-2 text-[11px] font-semibold text-slate-600 transition hover:bg-indigo-50 hover:text-indigo-600">
                                    <i class="fa-solid fa-chart-line"></i> Perkembangan
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="px-4 pb-4">
            <?= view('partials/pagination', ['page' => $page ?? 1, 'perPage' => $perPage ?? 15, 'total' => $total ?? 0]) ?>
        </div>
    </div>
</div>
