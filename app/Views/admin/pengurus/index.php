<?php $query = $search !== '' ? '?q=' . urlencode($search) : ''; ?>
<div class="space-y-5">
    <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
        <div>
            <h2 class="text-lg font-extrabold text-slate-900">Data Pengurus</h2>
            <p class="text-sm text-slate-500">Total <?= number_format($total, 0, ',', '.') ?> pengurus terdaftar.</p>
        </div>
        <div class="flex gap-2">
            <a href="<?= site_url('admin/export_excel_pengurus') ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50">
                <i class="fa-solid fa-file-excel text-emerald-600"></i> Excel
            </a>
            <a href="<?= site_url('admin/export_pdf_pengurus') ?>" target="_blank" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50">
                <i class="fa-solid fa-file-pdf text-rose-500"></i> PDF
            </a>
            <a href="<?= site_url('admin/pengurus/tambah') ?>" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-bold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700">
                <i class="fa-solid fa-plus"></i> Tambah
            </a>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <form method="get" action="<?= site_url('admin/pengurus') ?>" class="flex flex-col gap-3 sm:flex-row">
            <div class="relative flex-1">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i class="fa-solid fa-magnifying-glass text-xs"></i></span>
                <input type="text" name="q" value="<?= esc($search) ?>" placeholder="Cari nama, jabatan, email, atau no. HP..."
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-9 pr-3 text-sm outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-slate-800"><i class="fa-solid fa-filter mr-1"></i> Filter</button>
                <a href="<?= site_url('admin/pengurus') ?>" class="rounded-xl border border-slate-200 px-3 py-2.5 text-xs font-semibold text-slate-500 transition hover:bg-slate-50"><i class="fa-solid fa-rotate-left"></i></a>
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-4 py-3 font-semibold">No</th>
                        <th class="px-4 py-3 font-semibold">Pengurus</th>
                        <th class="px-4 py-3 font-semibold">Jabatan</th>
                        <th class="px-4 py-3 font-semibold">Kontak</th>
                        <th class="px-4 py-3 font-semibold">KTP</th>
                        <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if ($items === []): ?>
                        <tr><td colspan="6" class="px-4 py-12 text-center text-sm text-slate-500"><i class="fa-solid fa-inbox mb-2 block text-3xl text-slate-300"></i>Belum ada data pengurus.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($items as $index => $item): ?>
                        <tr class="transition hover:bg-slate-50/70">
                            <td class="px-4 py-3 text-slate-500"><?= (($page - 1) * $perPage) + $index + 1 ?></td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <?php if (! empty($item['foto'])): ?>
                                        <img src="<?= upload_url('foto_pengurus', $item['foto']) ?>" alt="<?= esc($item['nama_pengurus']) ?>" class="h-9 w-9 rounded-lg object-cover ring-1 ring-slate-200">
                                    <?php else: ?>
                                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-50 text-xs font-bold text-indigo-600"><?= esc(strtoupper(mb_substr((string) $item['nama_pengurus'], 0, 1))) ?></span>
                                    <?php endif; ?>
                                    <p class="font-semibold text-slate-800"><?= esc($item['nama_pengurus']) ?></p>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-slate-600"><?= esc($item['jabatan'] ?: '-') ?></td>
                            <td class="px-4 py-3 text-slate-600">
                                <p><i class="fa-solid fa-phone mr-1 text-[10px] text-slate-400"></i><?= esc($item['no_hp'] ?: '-') ?></p>
                                <p class="text-[11px] text-slate-400"><i class="fa-solid fa-envelope mr-1 text-[10px]"></i><?= esc($item['email'] ?: '-') ?></p>
                            </td>
                            <td class="px-4 py-3">
                                <?php if (! empty($item['file_ktp'])): ?>
                                    <a href="<?= site_url('admin/pengurus/view_ktp/' . $item['id_pengurus']) ?>" target="_blank" class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700"><i class="fa-solid fa-check"></i> Ada</a>
                                <?php else: ?>
                                    <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-500">Belum ada</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="<?= site_url('admin/pengurus/edit/' . $item['id_pengurus']) ?>" class="rounded-lg border border-slate-200 p-2 text-slate-500 transition hover:bg-indigo-50 hover:text-indigo-600" title="Edit"><i class="fa-solid fa-pen-to-square text-xs"></i></a>
                                    <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
                                    <form method="post" action="<?= site_url('admin/pengurus/delete/' . $item['id_pengurus']) ?>" data-confirm="Hapus pengurus <?= esc($item['nama_pengurus'], 'attr') ?>?" class="inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="rounded-lg border border-slate-200 p-2 text-slate-500 transition hover:bg-rose-50 hover:text-rose-600" title="Hapus"><i class="fa-solid fa-trash text-xs"></i></button>
                                    </form>
                                    <?php endif; ?>
                                </div>
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
