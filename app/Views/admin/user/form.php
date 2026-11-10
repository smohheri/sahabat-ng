<?php
$isEdit = ! empty($item);
$action = $isEdit ? site_url('admin/user/update/' . $item['id_user']) : site_url('admin/user/store');
$inputClass = 'w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20';
$labelClass = 'mb-1 block text-[11px] font-semibold uppercase tracking-wider text-slate-400';
?>

<form method="post" action="<?= $action ?>" class="space-y-5" x-data="{ role: '<?= esc(old('role', $item['role'] ?? 'admin'), 'js') ?>' }">
    <?= csrf_field() ?>

    <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
        <div>
            <h2 class="text-lg font-extrabold text-slate-900"><?= $isEdit ? 'Edit User' : 'Tambah User' ?></h2>
            <p class="text-sm text-slate-500">Akun login dan tautan ke data anak, guru, atau pengurus.</p>
        </div>
        <a href="<?= site_url('admin/user') ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
    </div>

    <div class="grid gap-5 lg:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm lg:col-span-2">
            <div class="border-b border-slate-100 px-5 py-4">
                <h3 class="flex items-center gap-2 text-sm font-bold text-slate-900"><i class="fa-solid fa-user-shield text-indigo-500"></i> Kredensial Akun</h3>
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
                    <label class="<?= $labelClass ?>">Password <?= $isEdit ? '<span class="normal-case text-slate-400">(kosongkan jika tidak diubah)</span>' : '<span class="text-rose-500">*</span>' ?></label>
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
                    <label class="mt-1 inline-flex cursor-pointer items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5">
                        <input type="checkbox" name="is_active" value="1" <?= old('is_active', $item['is_active'] ?? 1) ? 'checked' : '' ?> class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm font-medium text-slate-700">Akun aktif</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4">
                <h3 class="flex items-center gap-2 text-sm font-bold text-slate-900"><i class="fa-solid fa-link text-indigo-500"></i> Tautan Data</h3>
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

                <p class="rounded-xl bg-slate-50 px-3 py-2.5 text-[11px] leading-relaxed text-slate-500">
                    <i class="fa-solid fa-circle-info mr-1 text-indigo-500"></i>
                    Menautkan akun ke data anak/guru akan membatasi data yang dapat diakses sesuai perannya.
                </p>
            </div>
        </div>
    </div>

    <div class="flex flex-col-reverse justify-end gap-3 sm:flex-row">
        <a href="<?= site_url('admin/user') ?>" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">Batal</a>
        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700"><i class="fa-solid fa-floppy-disk"></i> <?= $isEdit ? 'Simpan Perubahan' : 'Simpan User' ?></button>
    </div>
</form>
