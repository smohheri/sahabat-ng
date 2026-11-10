<?php
$inputClass = 'w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20';
$labelClass = 'mb-1 block text-[11px] font-semibold uppercase tracking-wider text-slate-400';
?>

<div class="space-y-6" x-data="{ addOpen: false }">
    <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
        <div>
            <h2 class="text-lg font-extrabold text-slate-900">Carousel Landing</h2>
            <p class="text-sm text-slate-500">Kelola gambar galeri yang tampil di halaman beranda.</p>
        </div>
        <button type="button" @click="addOpen = !addOpen" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-bold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700">
            <i class="fa-solid fa-plus"></i> Tambah Gambar
        </button>
    </div>

    <div x-cloak x-show="addOpen" x-transition class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="post" action="<?= site_url('admin/carousel/upload') ?>" enctype="multipart/form-data" class="grid gap-4 md:grid-cols-2">
            <?= csrf_field() ?>
            <div class="md:col-span-2">
                <label class="<?= $labelClass ?>">Gambar <span class="text-rose-500">*</span></label>
                <input type="file" name="image" accept="image/*" required class="block w-full text-xs text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-600 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-white hover:file:bg-indigo-700">
            </div>
            <div>
                <label class="<?= $labelClass ?>">Judul</label>
                <input type="text" name="title" class="<?= $inputClass ?>" placeholder="Judul gambar">
            </div>
            <div>
                <label class="<?= $labelClass ?>">Urutan</label>
                <input type="number" name="sort_order" value="0" min="0" class="<?= $inputClass ?>">
            </div>
            <div class="md:col-span-2">
                <label class="<?= $labelClass ?>">Deskripsi</label>
                <textarea name="description" rows="2" class="<?= $inputClass ?>" placeholder="Deskripsi singkat"></textarea>
            </div>
            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                <input type="checkbox" name="is_active" value="1" checked class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                Tampilkan di landing
            </label>
            <div class="flex justify-end gap-2 md:col-span-2">
                <button type="button" @click="addOpen = false" class="rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-50">Batal</button>
                <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-xs font-bold text-white transition hover:bg-indigo-700"><i class="fa-solid fa-upload mr-1"></i> Upload</button>
            </div>
        </form>
    </div>

    <?php if ($items === []): ?>
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
            <i class="fa-solid fa-images mb-3 text-3xl text-slate-300"></i>
            <p class="text-sm text-slate-500">Belum ada gambar carousel.</p>
        </div>
    <?php else: ?>
        <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
            <?php foreach ($items as $item): ?>
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm" x-data="{ edit: false }">
                    <div class="relative aspect-video bg-slate-100">
                        <img src="<?= upload_url('landing', $item['image_name']) ?>" alt="<?= esc($item['title'] ?: 'Carousel') ?>" class="h-full w-full object-cover">
                        <span class="absolute left-3 top-3 inline-flex rounded-full px-2.5 py-1 text-[10px] font-bold <?= (int) $item['is_active'] === 1 ? 'bg-emerald-500 text-white' : 'bg-slate-700 text-white' ?>">
                            <?= (int) $item['is_active'] === 1 ? 'Aktif' : 'Nonaktif' ?>
                        </span>
                        <span class="absolute right-3 top-3 inline-flex h-7 w-7 items-center justify-center rounded-full bg-slate-900/70 text-[10px] font-bold text-white"><?= (int) $item['sort_order'] ?></span>
                    </div>
                    <div class="p-4">
                        <p class="truncate text-sm font-bold text-slate-900"><?= esc($item['title'] ?: 'Tanpa judul') ?></p>
                        <p class="mt-1 line-clamp-2 text-xs text-slate-500"><?= esc($item['description'] ?: 'Tidak ada deskripsi.') ?></p>

                        <div class="mt-3 flex items-center justify-between">
                            <button type="button" @click="edit = !edit" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700"><i class="fa-solid fa-pen-to-square mr-1"></i> Edit</button>
                            <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
                            <form method="post" action="<?= site_url('admin/carousel/delete/' . $item['id_carousel']) ?>" data-confirm="Hapus gambar carousel ini?">
                                <?= csrf_field() ?>
                                <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><i class="fa-solid fa-trash mr-1"></i> Hapus</button>
                            </form>
                            <?php endif; ?>
                        </div>

                        <form x-cloak x-show="edit" x-transition method="post" action="<?= site_url('admin/carousel/update') ?>" enctype="multipart/form-data" class="mt-4 space-y-3 border-t border-slate-100 pt-4">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id_carousel" value="<?= (int) $item['id_carousel'] ?>">
                            <div>
                                <label class="<?= $labelClass ?>">Judul</label>
                                <input type="text" name="title" value="<?= esc($item['title']) ?>" class="<?= $inputClass ?>">
                            </div>
                            <div>
                                <label class="<?= $labelClass ?>">Deskripsi</label>
                                <textarea name="description" rows="2" class="<?= $inputClass ?>"><?= esc($item['description']) ?></textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="<?= $labelClass ?>">Urutan</label>
                                    <input type="number" name="sort_order" value="<?= (int) $item['sort_order'] ?>" min="0" class="<?= $inputClass ?>">
                                </div>
                                <div>
                                    <label class="<?= $labelClass ?>">Ganti Gambar</label>
                                    <input type="file" name="image" accept="image/*" class="block w-full text-[10px] text-slate-500 file:mr-2 file:rounded-lg file:border-0 file:bg-slate-800 file:px-2 file:py-1.5 file:text-[10px] file:font-semibold file:text-white">
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
