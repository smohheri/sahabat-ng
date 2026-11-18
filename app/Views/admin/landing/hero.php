<?php
$inputClass = 'input';
$labelClass = 'label';
?>

<div class="space-y-6" x-data="{ addOpen: false }">
    <div class="page-header">
        <div>
            <h2 class="page-title">Hero Landing</h2>
            <p class="page-subtitle">Gambar utama dan teks pembuka pada halaman beranda.</p>
        </div>
        <button type="button" @click="addOpen = !addOpen" class="btn-primary">
            <i class="fa-solid fa-plus"></i> Tambah Hero
        </button>
    </div>

    <div x-cloak x-show="addOpen" x-transition class="card p-5">
        <form method="post" action="<?= site_url('admin/hero/upload') ?>" enctype="multipart/form-data" class="grid gap-4 md:grid-cols-2">
            <?= csrf_field() ?>
            <div class="md:col-span-2">
                <label class="<?= $labelClass ?>">Gambar Hero <span class="text-rose-500">*</span></label>
                <input type="file" name="image" accept="image/*" required class="block w-full text-xs text-stone-500 file:mr-3 file:rounded-lg file:border-0 file:bg-emerald-700 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-white hover:file:bg-emerald-800">
            </div>
            <div>
                <label class="<?= $labelClass ?>">Judul</label>
                <input type="text" name="title" class="<?= $inputClass ?>" placeholder="Judul utama">
            </div>
            <div>
                <label class="<?= $labelClass ?>">Subjudul</label>
                <input type="text" name="subtitle" class="<?= $inputClass ?>" placeholder="Subjudul">
            </div>
            <div class="md:col-span-2">
                <label class="<?= $labelClass ?>">Deskripsi</label>
                <textarea name="description" rows="2" class="<?= $inputClass ?>"></textarea>
            </div>
            <div>
                <label class="<?= $labelClass ?>">Urutan</label>
                <input type="number" name="sort_order" value="0" min="0" class="<?= $inputClass ?>">
            </div>
            <label class="inline-flex items-center gap-2 text-sm text-stone-700">
                <input type="checkbox" name="is_active" value="1" checked class="h-4 w-4 rounded border-stone-300 text-emerald-700 focus:ring-emerald-500">
                Tampilkan di landing
            </label>
            <div class="flex justify-end gap-2 md:col-span-2">
                <button type="button" @click="addOpen = false" class="rounded-xl border border-stone-200 px-4 py-2.5 text-xs font-semibold text-stone-600 transition hover:bg-stone-50">Batal</button>
                <button type="submit" class="btn-primary"><i class="fa-solid fa-upload mr-1"></i> Upload</button>
            </div>
        </form>
    </div>

    <?php if ($items === []): ?>
        <div class="rounded-2xl border border-dashed border-stone-300 bg-white px-6 py-14 text-center">
            <i class="fa-solid fa-panorama mb-3 text-3xl text-stone-300"></i>
            <p class="page-subtitle">Belum ada gambar hero.</p>
        </div>
    <?php else: ?>
        <div class="grid gap-5 lg:grid-cols-2">
            <?php foreach ($items as $item): ?>
                <div class="card overflow-hidden" x-data="{ edit: false }">
                    <div class="relative aspect-[16/7] bg-stone-100">
                        <img src="<?= upload_url('landing', $item['image_name']) ?>" alt="<?= esc($item['title'] ?: 'Hero') ?>" class="h-full w-full object-cover">
                        <div class="absolute inset-0 bg-stone-950/40"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-5">
                            <p class="text-lg font-extrabold text-white"><?= esc($item['title'] ?: 'Tanpa judul') ?></p>
                            <p class="text-xs font-semibold text-emerald-200"><?= esc($item['subtitle'] ?: '') ?></p>
                        </div>
                        <span class="absolute left-3 top-3 inline-flex rounded-full px-2.5 py-1 text-[10px] font-bold <?= (int) $item['is_active'] === 1 ? 'bg-emerald-500 text-white' : 'bg-stone-700 text-white' ?>">
                            <?= (int) $item['is_active'] === 1 ? 'Aktif' : 'Nonaktif' ?>
                        </span>
                    </div>
                    <div class="p-4">
                        <p class="line-clamp-2 text-xs text-stone-500"><?= esc($item['description'] ?: 'Tidak ada deskripsi.') ?></p>

                        <div class="mt-3 flex items-center justify-between">
                            <button type="button" @click="edit = !edit" class="text-xs font-semibold text-emerald-700 hover:text-emerald-800"><i class="fa-solid fa-pen-to-square mr-1"></i> Edit</button>
                            <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
                            <form method="post" action="<?= site_url('admin/hero/delete/' . $item['id_hero']) ?>" data-confirm="Hapus gambar hero ini?">
                                <?= csrf_field() ?>
                                <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><i class="fa-solid fa-trash mr-1"></i> Hapus</button>
                            </form>
                            <?php endif; ?>
                        </div>

                        <form x-cloak x-show="edit" x-transition method="post" action="<?= site_url('admin/hero/update/' . $item['id_hero']) ?>" enctype="multipart/form-data" class="mt-4 space-y-3 border-t border-stone-100 pt-4">
                            <?= csrf_field() ?>
                            <div>
                                <label class="<?= $labelClass ?>">Judul</label>
                                <input type="text" name="title" value="<?= esc($item['title']) ?>" class="<?= $inputClass ?>">
                            </div>
                            <div>
                                <label class="<?= $labelClass ?>">Subjudul</label>
                                <input type="text" name="subtitle" value="<?= esc($item['subtitle']) ?>" class="<?= $inputClass ?>">
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
                                    <input type="file" name="image" accept="image/*" class="block w-full text-[10px] text-stone-500 file:mr-2 file:rounded-lg file:border-0 file:bg-stone-800 file:px-2 file:py-1.5 file:text-[10px] file:font-semibold file:text-white">
                                </div>
                            </div>
                            <label class="inline-flex items-center gap-2 text-sm text-stone-700">
                                <input type="checkbox" name="is_active" value="1" <?= (int) $item['is_active'] === 1 ? 'checked' : '' ?> class="h-4 w-4 rounded border-stone-300 text-emerald-700 focus:ring-emerald-500">
                                Aktif
                            </label>
                            <button type="submit" class="btn-primary w-full justify-center"><i class="fa-solid fa-floppy-disk mr-1"></i> Simpan</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
