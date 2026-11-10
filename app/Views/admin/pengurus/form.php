<?php
$isEdit = ! empty($item);
$action = $isEdit ? site_url('admin/pengurus/update/' . $item['id_pengurus']) : site_url('admin/pengurus/store');
$inputClass = 'w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20';
$labelClass = 'mb-1 block text-[11px] font-semibold uppercase tracking-wider text-slate-400';
?>

<form method="post" action="<?= $action ?>" enctype="multipart/form-data" class="space-y-5">
    <?= csrf_field() ?>

    <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
        <div>
            <h2 class="text-lg font-extrabold text-slate-900"><?= $isEdit ? 'Edit Pengurus' : 'Tambah Pengurus' ?></h2>
            <p class="text-sm text-slate-500">Data pengurus LKSA beserta kontak dan berkas KTP.</p>
        </div>
        <a href="<?= site_url('admin/pengurus') ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
    </div>

    <div class="grid gap-5 lg:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm lg:col-span-2">
            <div class="border-b border-slate-100 px-5 py-4">
                <h3 class="flex items-center gap-2 text-sm font-bold text-slate-900"><i class="fa-solid fa-user-tie text-indigo-500"></i> Informasi Pengurus</h3>
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

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4">
                <h3 class="flex items-center gap-2 text-sm font-bold text-slate-900"><i class="fa-solid fa-paperclip text-indigo-500"></i> Berkas</h3>
            </div>
            <div class="space-y-4 p-5">
                <div>
                    <label class="<?= $labelClass ?>">Foto</label>
                    <?php if (! empty($item['foto'])): ?>
                        <img src="<?= upload_url('foto_pengurus', $item['foto']) ?>" alt="Foto" class="mb-2 h-24 w-24 rounded-xl object-cover ring-1 ring-slate-200">
                    <?php endif; ?>
                    <input type="file" name="foto" accept="image/*" class="block w-full text-xs text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-600 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white hover:file:bg-indigo-700">
                </div>
                <div>
                    <label class="<?= $labelClass ?>">File KTP</label>
                    <?php if (! empty($item['file_ktp'])): ?>
                        <a href="<?= site_url('admin/pengurus/view_ktp/' . $item['id_pengurus']) ?>" target="_blank" class="mb-2 inline-flex items-center gap-1.5 text-[11px] font-semibold text-indigo-600"><i class="fa-solid fa-paperclip"></i> Lihat KTP saat ini</a>
                    <?php endif; ?>
                    <input type="file" name="file_ktp" accept="image/*,application/pdf" class="block w-full text-xs text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-600 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white hover:file:bg-indigo-700">
                </div>
                <p class="rounded-xl bg-slate-50 px-3 py-2 text-[10px] leading-relaxed text-slate-400">Format: JPG, PNG, WEBP, atau PDF. Maksimal 2 MB per berkas.</p>
            </div>
        </div>
    </div>

    <div class="flex flex-col-reverse justify-end gap-3 sm:flex-row">
        <a href="<?= site_url('admin/pengurus') ?>" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">Batal</a>
        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700"><i class="fa-solid fa-floppy-disk"></i> <?= $isEdit ? 'Simpan Perubahan' : 'Simpan Data' ?></button>
    </div>
</form>
