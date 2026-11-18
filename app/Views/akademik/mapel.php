<?php
$inputClass = 'input';
$labelClass = 'label';
?>

<div class="space-y-5" x-data="{ addOpen: false }">
    <div class="page-header">
        <div>
            <h2 class="page-title">Mata Pelajaran</h2>
            <p class="page-subtitle"><?= $isTeacher ? 'Daftar mata pelajaran yang Anda ampu.' : 'Kelola mata pelajaran dan pengampu.' ?></p>
        </div>
        <div class="page-actions">
            <a href="<?= site_url($prefix . '/export/mapel/excel') ?>" class="btn-secondary"><i class="fa-solid fa-file-excel text-emerald-600"></i> Excel</a>
            <a href="<?= site_url($prefix . '/export/mapel/pdf') ?>" target="_blank" class="btn-secondary"><i class="fa-solid fa-file-pdf text-rose-500"></i> PDF</a>
            <?php if (! $isTeacher): ?>
                <button type="button" @click="addOpen = !addOpen" class="btn-primary"><i class="fa-solid fa-plus"></i> Tambah Mapel</button>
            <?php endif; ?>
        </div>
    </div>

    <?php if (! $isTeacher): ?>
        <div x-cloak x-show="addOpen" x-transition class="card p-5">
            <form method="post" action="<?= site_url($prefix . '/mapel/store') ?>" class="grid gap-3 md:grid-cols-4">
                <?= csrf_field() ?>
                <div>
                    <label class="<?= $labelClass ?>">Kode</label>
                    <input type="text" name="kode_mapel" required class="<?= $inputClass ?>" placeholder="MTK">
                </div>
                <div class="md:col-span-2">
                    <label class="<?= $labelClass ?>">Nama Mapel</label>
                    <input type="text" name="nama_mapel" required class="<?= $inputClass ?>" placeholder="Matematika">
                </div>
                <div>
                    <label class="<?= $labelClass ?>">Pengampu</label>
                    <select name="id_user_pengampu" class="<?= $inputClass ?>">
                        <option value="">Belum ditentukan</option>
                        <?php foreach ($pengajar as $id => $nama): ?>
                            <option value="<?= (int) $id ?>"><?= esc($nama) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <label class="inline-flex items-center gap-2 text-sm text-stone-700 md:col-span-2">
                    <input type="checkbox" name="is_active" value="1" checked class="h-4 w-4 rounded border-stone-300 text-emerald-700 focus:ring-emerald-500"> Aktif
                </label>
                <div class="flex justify-end gap-2 md:col-span-4">
                    <button type="button" @click="addOpen = false" class="rounded-xl border border-stone-200 px-4 py-2.5 text-xs font-semibold text-stone-600 transition hover:bg-stone-50">Batal</button>
                    <button type="submit" class="btn-primary"><i class="fa-solid fa-floppy-disk mr-1"></i> Simpan</button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <div class="card p-4">
        <form method="get" action="<?= site_url($prefix . '/mapel') ?>" class="flex flex-col gap-3 sm:flex-row">
            <div class="relative flex-1">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-stone-400"><i class="fa-solid fa-magnifying-glass text-xs"></i></span>
                <input type="text" name="q" value="<?= esc($search) ?>" placeholder="Cari kode atau nama mapel..."
                       class="w-full rounded-xl border border-stone-200 bg-stone-50 py-2.5 pl-9 pr-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20">
            </div>
            <div class="page-actions">
                <button type="submit" class="btn-dark"><i class="fa-solid fa-filter mr-1"></i> Cari</button>
                <a href="<?= site_url($prefix . '/mapel') ?>" class="btn-secondary"><i class="fa-solid fa-rotate-left"></i></a>
            </div>
        </form>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th class="px-4 py-3 font-semibold">No</th>
                        <th class="px-4 py-3 font-semibold">Kode</th>
                        <th class="px-4 py-3 font-semibold">Nama Mata Pelajaran</th>
                        <th class="px-4 py-3 font-semibold">Pengampu</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        <?php if (! $isTeacher): ?><th class="px-4 py-3 text-right font-semibold">Aksi</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    <?php if ($items === []): ?>
                        <tr><td colspan="<?= $isTeacher ? 5 : 6 ?>" class="px-4 py-12 text-center text-sm text-stone-400"><i class="fa-solid fa-book mb-2 block text-3xl text-stone-300"></i>Belum ada data mata pelajaran.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($items as $index => $item): ?>
                        <tr x-data="{ edit: false }" class="transition hover:bg-stone-50/70">
                            <td class="px-4 py-3 text-stone-500"><?= $index + 1 ?></td>
                            <td class="px-4 py-3"><span class="inline-flex rounded-md bg-stone-100 px-2 py-1 font-mono text-xs font-semibold text-stone-600"><?= esc($item['kode_mapel']) ?></span></td>
                            <td class="px-4 py-3 font-semibold text-stone-800"><?= esc($item['nama_mapel']) ?></td>
                            <td class="px-4 py-3 text-stone-600"><?= esc($item['pengampu'] ?: '-') ?></td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold <?= (int) $item['is_active'] === 1 ? 'bg-emerald-100 text-emerald-700' : 'bg-stone-100 text-stone-500' ?>"><?= (int) $item['is_active'] === 1 ? 'Aktif' : 'Nonaktif' ?></span>
                            </td>
                            <?php if (! $isTeacher): ?>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <button type="button" @click="edit = !edit" class="btn-icon" title="Edit"><i class="fa-solid fa-pen-to-square text-xs"></i></button>
                                        <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
                                        <form method="post" action="<?= site_url($prefix . '/mapel/delete/' . $item['id_mata_pelajaran']) ?>" data-confirm="Hapus mapel <?= esc($item['nama_mapel'], 'attr') ?>?">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn-icon btn-icon-danger" title="Hapus"><i class="fa-solid fa-trash text-xs"></i></button>
                                        </form>
                                        <?php endif; ?>
                                    </div>

                                    <form x-cloak x-show="edit" x-transition method="post" action="<?= site_url($prefix . '/mapel/update/' . $item['id_mata_pelajaran']) ?>" class="mt-3 grid gap-2 rounded-xl bg-stone-50 p-3 sm:grid-cols-4">
                                        <?= csrf_field() ?>
                                        <input type="text" name="kode_mapel" value="<?= esc($item['kode_mapel']) ?>" required class="<?= $inputClass ?>">
                                        <input type="text" name="nama_mapel" value="<?= esc($item['nama_mapel']) ?>" required class="<?= $inputClass ?> sm:col-span-2">
                                        <select name="id_user_pengampu" class="<?= $inputClass ?>">
                                            <option value="">Belum ditentukan</option>
                                            <?php foreach ($pengajar as $id => $nama): ?>
                                                <option value="<?= (int) $id ?>" <?= (int) ($item['id_user_pengampu'] ?? 0) === (int) $id ? 'selected' : '' ?>><?= esc($nama) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label class="inline-flex items-center gap-2 text-sm text-stone-700 sm:col-span-2">
                                            <input type="checkbox" name="is_active" value="1" <?= (int) $item['is_active'] === 1 ? 'checked' : '' ?> class="h-4 w-4 rounded border-stone-300 text-emerald-700 focus:ring-emerald-500"> Aktif
                                        </label>
                                        <button type="submit" class="btn-primary sm:col-span-2"><i class="fa-solid fa-floppy-disk mr-1"></i> Simpan Perubahan</button>
                                    </form>
                                </td>
                            <?php endif; ?>
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
