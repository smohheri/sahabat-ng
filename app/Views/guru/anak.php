<?php
$items = $items ?? [];
?>

<div class="space-y-5" x-data="{ q: '' }">
    <div class="page-header">
        <div>
            <h2 class="page-title">Data Anak</h2>
            <p class="page-subtitle"><?= number_format((int) ($total ?? count($items)), 0, ',', '.') ?> anak dalam lingkup pengajaran Anda.</p>
        </div>
        <a href="<?= site_url('guru/perkembangan-anak') ?>" class="btn-secondary">
            <i class="fa-solid fa-chart-line text-emerald-600"></i> Lihat Perkembangan
        </a>
    </div>

    <div class="card p-4">
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-stone-400"><i class="fa-solid fa-magnifying-glass text-xs"></i></span>
            <input type="text" x-model="q" placeholder="Cari nama, nomor registrasi, atau sekolah..."
                   class="w-full rounded-xl border border-stone-200 bg-stone-50 py-2.5 pl-9 pr-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20">
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
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
                <tbody class="divide-y divide-stone-100">
                    <?php if ($items === []): ?>
                        <tr><td colspan="7" class="px-4 py-12 text-center text-sm text-stone-400"><i class="fa-solid fa-children mb-2 block text-3xl text-stone-300"></i>Belum ada data anak.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($items as $index => $item): ?>
                        <?php $haystack = strtolower(($item['nama_anak'] ?? '') . ' ' . ($item['no_registrasi'] ?? '') . ' ' . ($item['nama_sekolah'] ?? '')); ?>
                        <tr class="transition hover:bg-stone-50/70" x-show="q === '' || '<?= esc($haystack, 'js') ?>'.includes(q.toLowerCase())">
                            <td class="px-4 py-3 text-stone-500"><?= $index + 1 ?></td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <?php if (! empty($item['foto'])): ?>
                                        <img src="<?= upload_url('foto_anak', $item['foto']) ?>" alt="<?= esc($item['nama_anak']) ?>" class="h-9 w-9 rounded-lg object-cover ring-1 ring-stone-200">
                                    <?php else: ?>
                                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-50 text-xs font-bold text-emerald-700"><?= esc(strtoupper(mb_substr((string) $item['nama_anak'], 0, 1))) ?></span>
                                    <?php endif; ?>
                                    <div class="min-w-0">
                                        <p class="truncate font-semibold text-stone-800"><?= esc($item['nama_anak']) ?></p>
                                        <p class="truncate text-xs text-stone-400"><?= esc($item['nama_sekolah'] ?: 'Sekolah belum diisi') ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3"><span class="inline-flex rounded-md bg-stone-100 px-2 py-1 font-mono text-xs font-semibold text-stone-600"><?= esc($item['no_registrasi'] ?: '-') ?></span></td>
                            <td class="px-4 py-3 text-stone-600"><?= ($item['jenis_kelamin'] ?? '') === 'L' ? 'L' : 'P' ?></td>
                            <td class="px-4 py-3 text-stone-600"><?= esc($item['pendidikan'] ?: '-') ?></td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset <?= status_badge($item['status_anak'] ?? '') ?>"><?= esc($item['status_anak'] ?: '-') ?></span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="<?= site_url('guru/perkembangan-anak/detail/' . $item['id_anak']) ?>" class="inline-flex items-center gap-1.5 rounded-lg border border-stone-200 px-3 py-2 text-xs font-semibold text-stone-600 transition hover:bg-emerald-50 hover:text-emerald-700">
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
