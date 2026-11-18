<?php
$isEdit = ! empty($item);
$action = $isEdit ? site_url('admin/pengurus/update/' . $item['id_pengurus']) : site_url('admin/pengurus/store');
$inputClass = 'input';
$labelClass = 'label';
?>

<form method="post" action="<?= $action ?>" enctype="multipart/form-data" class="space-y-5">
    <?= csrf_field() ?>

    <div class="page-header">
        <div>
            <h2 class="page-title"><?= $isEdit ? 'Edit Pengurus' : 'Tambah Pengurus' ?></h2>
            <p class="page-subtitle">Data pengurus LKSA beserta kontak dan berkas KTP.</p>
        </div>
        <a href="<?= site_url('admin/pengurus') ?>" class="btn-secondary"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
    </div>

    <div class="grid gap-5 lg:grid-cols-3">
        <div class=card lg:col-span-2">
            <div class="card-header">
                <h3 class="section-title"><i class="fa-solid fa-user-tie text-emerald-600"></i> Informasi Pengurus</h3>
            </div>
            <div class="grid gap-4 p-5 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="<?= $labelClass ?>">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_pengurus" value="<?= esc(old('nama_pengurus', $item['nama_pengurus'] ?? '')) ?>" required class="<?= $inputClass ?>">
                </div>
                <div>
                    <label class="<?= $labelClass ?>">Jabatan</label>
                    <input type="text" name="jabatan" value="<?= esc(old('jabatan', $item['jabatan'] ?? '')) ?>" class="<?= $inputClass ?>" placeholder="Contoh: Ketua, Bendahara">
                </div>
                <div>
                    <label class="<?= $labelClass ?>">No. HP</label>
                    <input type="text" name="no_hp" value="<?= esc(old('no_hp', $item['no_hp'] ?? '')) ?>" class="<?= $inputClass ?>">
                </div>
                <div class="md:col-span-2">
                    <label class="<?= $labelClass ?>">Email</label>
                    <input type="email" name="email" value="<?= esc(old('email', $item['email'] ?? '')) ?>" class="<?= $inputClass ?>">
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="section-title"><i class="fa-solid fa-paperclip text-emerald-600"></i> Berkas</h3>
            </div>
            <div class="space-y-4 p-5">
                <div>
                    <label class="<?= $labelClass ?>">Foto</label>
                    <?php if (! empty($item['foto'])): ?>
                        <img src="<?= upload_url('foto_pengurus', $item['foto']) ?>" alt="Foto" class="mb-2 h-24 w-24 rounded-xl object-cover ring-1 ring-stone-200">
                    <?php endif; ?>
                    <input type="file" name="foto" accept="image/*" class="block w-full text-xs text-stone-500 file:mr-3 file:rounded-lg file:border-0 file:bg-emerald-700 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white hover:file:bg-emerald-800">
                </div>
                <div>
                    <label class="<?= $labelClass ?>">File KTP</label>
                    <?php if (! empty($item['file_ktp'])): ?>
                        <a href="<?= site_url('admin/pengurus/view_ktp/' . $item['id_pengurus']) ?>" target="_blank" class="mb-2 inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700"><i class="fa-solid fa-paperclip"></i> Lihat KTP saat ini</a>
                    <?php endif; ?>
                    <input type="file" name="file_ktp" accept="image/*,application/pdf" class="block w-full text-xs text-stone-500 file:mr-3 file:rounded-lg file:border-0 file:bg-emerald-700 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white hover:file:bg-emerald-800">
                </div>
                <p class="rounded-xl bg-stone-50 px-3 py-2 text-[10px] leading-relaxed text-stone-400">Format: JPG, PNG, WEBP, atau PDF. Maksimal 2 MB per berkas.</p>
            </div>
        </div>
    </div>

    <div class="flex flex-col-reverse justify-end gap-3 sm:flex-row">
        <a href="<?= site_url('admin/pengurus') ?>" class="btn-secondary">Batal</a>
        <button type="submit" class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> <?= $isEdit ? 'Simpan Perubahan' : 'Simpan Data' ?></button>
    </div>
</form>
