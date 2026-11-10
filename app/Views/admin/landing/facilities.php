<?php
$inputClass = 'w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20';
$labelClass = 'mb-1 block text-[11px] font-semibold uppercase tracking-wider text-slate-400';
?>

<div class="space-y-6" x-data="{ addOpen: false }">
    <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
        <div>
            <h2 class="text-lg font-extrabold text-slate-900">Fasilitas Landing</h2>
            <p class="text-sm text-slate-500">Sarana dan prasarana yang ditampilkan pada halaman beranda.</p>
        </div>
        <button type="button" @click="addOpen = !addOpen" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-bold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700">
            <i class="fa-solid fa-plus"></i> Tambah Fasilitas
        </button>
    </div>

    <div x-cloak x-show="addOpen" x-transition class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="post" action="<?= site_url('admin/facilities/upload') ?>" enctype="multipart/form-data" class="grid gap-4 md:grid-cols-2">
            <?= csrf_field() ?>
            <div>
                <label class="<?= $labelClass ?>">Nama Fasilitas <span class="text-rose-500">*</span></label>
                <input type="text" name="nama_fasilitas" required class="<?= $inputClass ?>" placeholder="Contoh: Ruang Belajar">
            </div>
            <div>
                <label class="<?= $labelClass ?>">Ikon Font Awesome</label>
                <input type="text" name="icon" class="<?= $inputClass ?>" placeholder="building, book, futbal...">
            </div>
            <div class="md:col-span-2">
                <label class="<?= $labelClass ?>">Deskripsi</label>
                <textarea name="deskripsi" rows="2" class="<?= $inputClass ?>"></textarea>
            </div>
            <div>
                <label class="<?= $labelClass ?>">Gambar</label>
                <input type="file" name="gambar" accept="image/*" class="block w-full text-xs text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-600 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-white hover:file:bg-indigo-700">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="<?= $labelClass ?>">Urutan</label>
                    <input type="number" name="sort_order" value="0" min="0" class="<?= $inputClass ?>">
                </div>
                <label class="mt-6 inline-flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="is_active" value="1" checked class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    Aktif
                </label>
            </div>
            <div class="flex justify-end gap-2 md:col-span-2">
                <button type="button" @click="addOpen = false" class="rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-50">Batal</button>
                <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-xs font-bold text-white transition hover:bg-indigo-700"><i class="fa-solid fa-floppy-disk mr-1"></i> Simpan</button>
            </div>
        </form>
    </div>

    <?php if ($items === []): ?>
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
            <i class="fa-solid fa-building mb-3 text-3xl text-slate-300"></i>
            <p class="text-sm text-slate-500">Belum ada fasilitas.</p>
        </div>
    <?php else: ?>
        <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
            <?php foreach ($items as $item): ?>
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm" x-data="{ edit: false }">
                    <?php if (! empty($item['gambar'])): ?>
                        <img src="<?= upload_url('landing', $item['gambar']) ?>" alt="<?= esc($item['nama_fasilitas']) ?>" class="h-36 w-full object-cover">
                    <?php else: ?>
                        <div class="flex h-36 w-full items-center justify-center bg-indigo-50 text-indigo-500">
                            <i class="fa-solid fa-<?= esc($item['icon'] ?: 'building') ?> text-3xl"></i>
                        </div>
                    <?php endif; ?>

                    <div class="p-4">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-bold text-slate-900"><?= esc($item['nama_fasilitas']) ?></p>
                                <p class="mt-0.5 inline-flex items-center gap-1 text-[10px] font-semibold text-slate-400"><i class="fa-solid fa-<?= esc($item['icon'] ?: 'building') ?>"></i> <?= esc($item['icon'] ?: 'building') ?></p>
                            </div>
                            <span class="inline-flex shrink-0 rounded-full px-2 py-0.5 text-[10px] font-bold <?= (int) $item['is_active'] === 1 ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' ?>">
                                <?= (int) $item['is_active'] === 1 ? 'Aktif' : 'Nonaktif' ?>
                            </span>
                        </div>
                        <p class="mt-2 line-clamp-2 text-xs text-slate-500"><?= esc($item['deskripsi'] ?: 'Tidak ada deskripsi.') ?></p>

                        <div class="mt-3 flex items-center justify-between">
                            <button type="button" @click="edit = !edit" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700"><i class="fa-solid fa-pen-to-square mr-1"></i> Edit</button>
                            <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
                            <form method="post" action="<?= site_url('admin/facilities/delete/' . $item['id_fasilitas']) ?>" data-confirm="Hapus fasilitas <?= esc($item['nama_fasilitas'], 'attr') ?>?">
                                <?= csrf_field() ?>
                                <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><i class="fa-solid fa-trash mr-1"></i> Hapus</button>
                            </form>
                            <?php endif; ?>
                        </div>

                        <form x-cloak x-show="edit" x-transition method="post" action="<?= site_url('admin/facilities/update') ?>" enctype="multipart/form-data" class="mt-4 space-y-3 border-t border-slate-100 pt-4">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id_fasilitas" value="<?= (int) $item['id_fasilitas'] ?>">
                            <div>
                                <label class="<?= $labelClass ?>">Nama Fasilitas</label>
                                <input type="text" name="nama_fasilitas" value="<?= esc($item['nama_fasilitas']) ?>" required class="<?= $inputClass ?>">
                            </div>
                            <div>
                                <label class="<?= $labelClass ?>">Ikon</label>
                                <input type="text" name="icon" value="<?= esc($item['icon']) ?>" class="<?= $inputClass ?>">
                            </div>
                            <div>
                                <label class="<?= $labelClass ?>">Deskripsi</label>
                                <textarea name="deskripsi" rows="2" class="<?= $inputClass ?>"><?= esc($item['deskripsi']) ?></textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="<?= $labelClass ?>">Urutan</label>
                                    <input type="number" name="sort_order" value="<?= (int) $item['sort_order'] ?>" min="0" class="<?= $inputClass ?>">
                                </div>
                                <div>
                                    <label class="<?= $labelClass ?>">Gambar</label>
                                    <input type="file" name="gambar" accept="image/*" class="block w-full text-[10px] text-slate-500 file:mr-2 file:rounded-lg file:border-0 file:bg-slate-800 file:px-2 file:py-1.5 file:text-[10px] file:font-semibold file:text-white">
                                </div>
                            </div>
                            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                <input type="checkbox" name="is_active" value="1" <?= (int) $item['is_active'] === 1 ? 'checked' : '' ?> class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                Aktif
                            </label>
                            <button type="submit" class="w-full rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-indigo-700"><i class="fa-solid fa-floppy-disk mr-1"></i> Simpan</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
