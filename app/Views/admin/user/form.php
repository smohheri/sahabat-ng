<?php
$isEdit = ! empty($item);
$action = $isEdit ? site_url('admin/user/update/' . $item['id_user']) : site_url('admin/user/store');
$inputClass = 'input';
$labelClass = 'label';
?>

<form method="post" action="<?= $action ?>" class="space-y-5" x-data="{ role: '<?= esc(old('role', $item['role'] ?? 'admin'), 'js') ?>' }">
    <?= csrf_field() ?>

    <div class="page-header">
        <div>
            <h2 class="page-title"><?= $isEdit ? 'Edit User' : 'Tambah User' ?></h2>
            <p class="page-subtitle">Akun login dan tautan ke data anak, guru, atau pengurus.</p>
        </div>
        <a href="<?= site_url('admin/user') ?>" class="btn-secondary"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
    </div>

    <div class="grid gap-5 lg:grid-cols-3">
        <div class=card lg:col-span-2">
            <div class="card-header">
                <h3 class="section-title"><i class="fa-solid fa-user-shield text-emerald-600"></i> Kredensial Akun</h3>
            </div>
            <div class="grid gap-4 p-5 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="<?= $labelClass ?>">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama" value="<?= esc(old('nama', $item['nama'] ?? '')) ?>" required class="<?= $inputClass ?>">
                </div>
                <div>
                    <label class="<?= $labelClass ?>">Username <span class="text-rose-500">*</span></label>
                    <input type="text" name="username" value="<?= esc(old('username', $item['username'] ?? '')) ?>" required class="<?= $inputClass ?>" autocomplete="off">
                </div>
                <div>
                    <label class="<?= $labelClass ?>">Password <?= $isEdit ? '<span class="normal-case text-stone-400">(kosongkan jika tidak diubah)</span>' : '<span class="text-rose-500">*</span>' ?></label>
                    <input type="password" name="password" class="<?= $inputClass ?>" autocomplete="new-password" placeholder="<?= $isEdit ? '••••••••' : 'Minimal 6 karakter' ?>">
                </div>
                <div>
                    <label class="<?= $labelClass ?>">Role <span class="text-rose-500">*</span></label>
                    <select name="role" x-model="role" required class="<?= $inputClass ?>">
                        <?php foreach ($options['roles'] as $value => $label): ?>
                            <option value="<?= esc($value) ?>"><?= esc($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="<?= $labelClass ?>">Status Akun</label>
                    <label class="mt-1 inline-flex cursor-pointer items-center gap-2 rounded-xl border border-stone-200 bg-stone-50 px-3 py-2.5">
                        <input type="checkbox" name="is_active" value="1" <?= old('is_active', $item['is_active'] ?? 1) ? 'checked' : '' ?> class="h-4 w-4 rounded border-stone-300 text-emerald-700 focus:ring-emerald-500">
                        <span class="text-sm font-medium text-stone-700">Akun aktif</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="section-title"><i class="fa-solid fa-link text-emerald-600"></i> Tautan Data</h3>
            </div>
            <div class="space-y-4 p-5">
                <div x-show="role === 'anak'" x-cloak>
                    <label class="<?= $labelClass ?>">Data Anak</label>
                    <select name="id_anak" class="<?= $inputClass ?>">
                        <option value="">Tidak ditautkan</option>
                        <?php foreach ($options['anak'] as $id => $nama): ?>
                            <option value="<?= (int) $id ?>" <?= (string) old('id_anak', $item['id_anak'] ?? '') === (string) $id ? 'selected' : '' ?>><?= esc($nama) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div x-show="role === 'pengajar' || role === 'guru'" x-cloak>
                    <label class="<?= $labelClass ?>">Data Guru / Pengajar</label>
                    <select name="id_guru" class="<?= $inputClass ?>">
                        <option value="">Tidak ditautkan</option>
                        <?php foreach ($options['guru'] as $id => $nama): ?>
                            <option value="<?= (int) $id ?>" <?= (string) old('id_guru', $item['id_guru'] ?? '') === (string) $id ? 'selected' : '' ?>><?= esc($nama) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div x-show="role === 'petugas' || role === 'operator'" x-cloak>
                    <label class="<?= $labelClass ?>">Data Pengurus</label>
                    <select name="id_pengurus" class="<?= $inputClass ?>">
                        <option value="">Tidak ditautkan</option>
                        <?php foreach ($options['pengurus'] as $id => $nama): ?>
                            <option value="<?= (int) $id ?>" <?= (string) old('id_pengurus', $item['id_pengurus'] ?? '') === (string) $id ? 'selected' : '' ?>><?= esc($nama) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <p class="rounded-xl bg-stone-50 px-3 py-2.5 text-xs leading-relaxed text-stone-500">
                    <i class="fa-solid fa-circle-info mr-1 text-emerald-600"></i>
                    Menautkan akun ke data anak/guru akan membatasi data yang dapat diakses sesuai perannya.
                </p>
            </div>
        </div>
    </div>

    <div class="flex flex-col-reverse justify-end gap-3 sm:flex-row">
        <a href="<?= site_url('admin/user') ?>" class="btn-secondary">Batal</a>
        <button type="submit" class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> <?= $isEdit ? 'Simpan Perubahan' : 'Simpan User' ?></button>
    </div>
</form>
