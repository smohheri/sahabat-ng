<?php $query = $search !== '' ? '?q=' . urlencode($search) : ''; ?>
<div class="space-y-5">
    <div class="page-header">
        <div>
            <h2 class="page-title">Data Pengurus</h2>
            <p class="page-subtitle">Total <?= number_format($total, 0, ',', '.') ?> pengurus terdaftar.</p>
        </div>
        <div class="page-actions">
            <a href="<?= site_url('admin/export_excel_pengurus') ?>" class="btn-secondary">
                <i class="fa-solid fa-file-excel text-emerald-600"></i> Excel
            </a>
            <a href="<?= site_url('admin/export_pdf_pengurus') ?>" target="_blank" class="btn-secondary">
                <i class="fa-solid fa-file-pdf text-rose-500"></i> PDF
            </a>
            <a href="<?= site_url('admin/pengurus/tambah') ?>" class="btn-primary">
                <i class="fa-solid fa-plus"></i> Tambah
            </a>
        </div>
    </div>

    <div class="card p-4">
        <form method="get" action="<?= site_url('admin/pengurus') ?>" class="flex flex-col gap-3 sm:flex-row">
            <div class="relative flex-1">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-stone-400"><i class="fa-solid fa-magnifying-glass text-xs"></i></span>
                <input type="text" name="q" value="<?= esc($search) ?>" placeholder="Cari nama, jabatan, email, atau no. HP..."
                       class="w-full rounded-xl border border-stone-200 bg-stone-50 py-2.5 pl-9 pr-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20">
            </div>
            <div class="page-actions">
                <button type="submit" class="btn-dark"><i class="fa-solid fa-filter mr-1"></i> Filter</button>
                <a href="<?= site_url('admin/pengurus') ?>" class="btn-secondary"><i class="fa-solid fa-rotate-left"></i></a>
            </div>
        </form>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th class="px-4 py-3 font-semibold">No</th>
                        <th class="px-4 py-3 font-semibold">Pengurus</th>
                        <th class="px-4 py-3 font-semibold">Jabatan</th>
                        <th class="px-4 py-3 font-semibold">Kontak</th>
                        <th class="px-4 py-3 font-semibold">KTP</th>
                        <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    <?php if ($items === []): ?>
                        <tr><td colspan="6" class="empty-state"><i class="fa-solid fa-inbox empty-icon"></i><p class="empty-title">Belum ada data pengurus</p><p class="empty-desc">Tambahkan pengurus baru untuk mulai mengelola data.</p></td></tr>
                    <?php endif; ?>
                    <?php foreach ($items as $index => $item): ?>
                        <tr class="transition hover:bg-stone-50/70">
                            <td class="px-4 py-3 text-stone-500"><?= (($page - 1) * $perPage) + $index + 1 ?></td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <?php if (! empty($item['foto'])): ?>
                                        <img src="<?= upload_url('foto_pengurus', $item['foto']) ?>" alt="<?= esc($item['nama_pengurus']) ?>" class="h-9 w-9 rounded-lg object-cover ring-1 ring-stone-200">
                                    <?php else: ?>
                                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-50 text-xs font-bold text-emerald-700"><?= esc(strtoupper(mb_substr((string) $item['nama_pengurus'], 0, 1))) ?></span>
                                    <?php endif; ?>
                                    <p class="font-semibold text-stone-800"><?= esc($item['nama_pengurus']) ?></p>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-stone-600"><?= esc($item['jabatan'] ?: '-') ?></td>
                            <td class="px-4 py-3 text-stone-600">
                                <p><i class="fa-solid fa-phone mr-1 text-[10px] text-stone-400"></i><?= esc($item['no_hp'] ?: '-') ?></p>
                                <p class="text-xs text-stone-400"><i class="fa-solid fa-envelope mr-1 text-[10px]"></i><?= esc($item['email'] ?: '-') ?></p>
                            </td>
                            <td class="px-4 py-3">
                                <?php if (! empty($item['file_ktp'])): ?>
                                    <a href="<?= site_url('admin/pengurus/view_ktp/' . $item['id_pengurus']) ?>" target="_blank" class="badge badge-emerald"><i class="fa-solid fa-check"></i> Ada</a>
                                <?php else: ?>
                                    <span class="inline-flex rounded-full bg-stone-100 px-2.5 py-1 text-xs font-semibold text-stone-500">Belum ada</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="<?= site_url('admin/pengurus/edit/' . $item['id_pengurus']) ?>" class="btn-icon" title="Edit"><i class="fa-solid fa-pen-to-square text-xs"></i></a>
                                    <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
                                    <form method="post" action="<?= site_url('admin/pengurus/delete/' . $item['id_pengurus']) ?>" data-confirm="Hapus pengurus <?= esc($item['nama_pengurus'], 'attr') ?>?" class="inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn-icon btn-icon-danger" title="Hapus"><i class="fa-solid fa-trash text-xs"></i></button>
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
