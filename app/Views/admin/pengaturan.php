<?php
$inputClass = 'w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20';
$labelClass = 'mb-1 block text-[11px] font-semibold uppercase tracking-wider text-slate-400';
$val = static fn (string $key, mixed $default = '') => old($key, $item->{$key} ?? $default);
$pengurus = $pengurus ?? [];

$pengurusSelect = static function (string $name, string $label, string $current) use ($pengurus, $inputClass, $labelClass): void {
    ?>
    <div>
        <label class="<?= $labelClass ?>"><?= esc($label) ?></label>
        <select name="<?= $name ?>" class="<?= $inputClass ?>">
            <option value="">Pilih pengurus</option>
            <?php foreach ($pengurus as $p): ?>
                <option value="<?= esc($p['nama_pengurus']) ?>" <?= $current === $p['nama_pengurus'] ? 'selected' : '' ?>>
                    <?= esc($p['nama_pengurus'] . ($p['jabatan'] ? ' - ' . $p['jabatan'] : '')) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php
};
?>

<form method="post" action="<?= site_url('admin/pengaturan/update') ?>" enctype="multipart/form-data" class="space-y-5">
    <?= csrf_field() ?>

    <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
        <div>
            <h2 class="text-lg font-extrabold text-slate-900">Pengaturan Aplikasi</h2>
            <p class="text-sm text-slate-500">Identitas lembaga, media, dan tautan sosial media.</p>
        </div>
        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-xs font-bold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700">
            <i class="fa-solid fa-floppy-disk"></i> Simpan Pengaturan
        </button>
    </div>

    <div class="grid gap-5 lg:grid-cols-3">
        <div class="space-y-5 lg:col-span-2">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h3 class="flex items-center gap-2 text-sm font-bold text-slate-900"><i class="fa-solid fa-building text-indigo-500"></i> Identitas Lembaga</h3>
                </div>
                <div class="grid gap-4 p-5 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label class="<?= $labelClass ?>">Nama LKSA <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_lksa" value="<?= esc($val('nama_lksa')) ?>" required class="<?= $inputClass ?>">
                    </div>
                    <div class="md:col-span-2">
                        <label class="<?= $labelClass ?>">Alamat</label>
                        <textarea name="alamat" rows="2" class="<?= $inputClass ?>"><?= esc($val('alamat')) ?></textarea>
                    </div>
                    <div>
                        <label class="<?= $labelClass ?>">No. Telepon</label>
                        <input type="text" name="no_telp" value="<?= esc($val('no_telp')) ?>" class="<?= $inputClass ?>">
                    </div>
                    <div>
                        <label class="<?= $labelClass ?>">Email</label>
                        <input type="email" name="email" value="<?= esc($val('email')) ?>" class="<?= $inputClass ?>">
                    </div>
                    <div>
                        <label class="<?= $labelClass ?>">Tahun Berdiri</label>
                        <input type="number" name="tahun_berdiri" min="1900" max="<?= date('Y') ?>" value="<?= esc($val('tahun_berdiri')) ?>" class="<?= $inputClass ?>">
                    </div>
                    <?php $pengurusSelect('nama_kepala', 'Nama Kepala LKSA', (string) $val('nama_kepala')); ?>
                    <?php $pengurusSelect('nama_kepala_putra', 'Nama Kepala Putra', (string) $val('nama_kepala_putra')); ?>
                    <?php $pengurusSelect('nama_kepala_putri', 'Nama Kepala Putri', (string) $val('nama_kepala_putri')); ?>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h3 class="flex items-center gap-2 text-sm font-bold text-slate-900"><i class="fa-solid fa-share-nodes text-indigo-500"></i> Media Sosial</h3>
                </div>
                <div class="grid gap-4 p-5 md:grid-cols-2">
                    <?php
                    $socials = [
                        ['facebook', 'Facebook', 'fa-facebook text-blue-600', 'https://facebook.com/...'],
                        ['instagram', 'Instagram', 'fa-instagram text-pink-600', 'https://instagram.com/...'],
                        ['twitter', 'Twitter / X', 'fa-x-twitter text-slate-800', 'https://x.com/...'],
                        ['youtube', 'YouTube', 'fa-youtube text-red-600', 'https://youtube.com/...'],
                        ['linkedin', 'LinkedIn', 'fa-linkedin text-sky-700', 'https://linkedin.com/...'],
                        ['whatsapp', 'WhatsApp', 'fa-whatsapp text-emerald-600', '628123456789'],
                    ];
                    ?>
                    <?php foreach ($socials as [$field, $label, $icon, $placeholder]): ?>
                        <div>
                            <label class="<?= $labelClass ?>"><i class="fa-brands <?= $icon ?> mr-1"></i> <?= esc($label) ?></label>
                            <input type="text" name="<?= $field ?>" value="<?= esc($val($field)) ?>" class="<?= $inputClass ?>" placeholder="<?= $placeholder ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h3 class="flex items-center gap-2 text-sm font-bold text-slate-900"><i class="fa-solid fa-hand-holding-heart text-rose-500"></i> Donasi LKSA</h3>
                </div>
                <div class="grid gap-4 p-5 md:grid-cols-2">
                    <div>
                        <label class="<?= $labelClass ?>">Nama Bank</label>
                        <input type="text" name="bank_nama" value="<?= esc($val('bank_nama')) ?>" class="<?= $inputClass ?>" placeholder="Contoh: Bank BSI">
                    </div>
                    <div>
                        <label class="<?= $labelClass ?>">Nomor Rekening</label>
                        <input type="text" name="bank_nomor_rekening" value="<?= esc($val('bank_nomor_rekening')) ?>" class="<?= $inputClass ?>" placeholder="Contoh: 1234567890">
                    </div>
                    <div class="md:col-span-2">
                        <label class="<?= $labelClass ?>">Atas Nama</label>
                        <input type="text" name="bank_atas_nama" value="<?= esc($val('bank_atas_nama')) ?>" class="<?= $inputClass ?>" placeholder="Contoh: LKSA Harapan Bangsa">
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-5">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h3 class="flex items-center gap-2 text-sm font-bold text-slate-900"><i class="fa-solid fa-images text-indigo-500"></i> Media &amp; Dokumen</h3>
                </div>
                <div class="space-y-5 p-5">
                    <?php
                    $uploads = [
                        ['logo', 'Logo LKSA', 'logos', $item->logo ?? null, 'image/*'],
                        ['kop_surat', 'Kop Surat', 'kop', $item->kop_surat ?? null, 'image/*'],
                        ['hero_image', 'Hero Landing', 'landing', $item->hero_image ?? null, 'image/*'],
                        ['about_image', 'Gambar Tentang', 'landing', $item->about_image ?? null, 'image/*'],
                        ['qris_image', 'QRIS Donasi', 'qris', $item->qris_image ?? null, 'image/*'],
                        ['dokumen_legal', 'Dokumen Legal', 'dokumen', $item->dokumen_legal ?? null, 'image/*,application/pdf'],
                    ];
                    ?>
                    <?php foreach ($uploads as [$field, $label, $folder, $current, $accept]): ?>
                        <div>
                            <label class="<?= $labelClass ?>"><?= esc($label) ?></label>
                            <?php if (! empty($current)): ?>
                                <?php if ($field === 'dokumen_legal'): ?>
                                    <a href="<?= upload_url($folder, $current) ?>" target="_blank" class="mb-2 inline-flex items-center gap-1.5 text-[11px] font-semibold text-indigo-600"><i class="fa-solid fa-paperclip"></i> Lihat dokumen</a>
                                <?php else: ?>
                                    <img src="<?= upload_url($folder, $current) ?>" alt="<?= esc($label) ?>" class="mb-2 h-20 w-full rounded-xl object-cover ring-1 ring-slate-200">
                                <?php endif; ?>
                            <?php endif; ?>
                            <input type="file" name="<?= $field ?>" accept="<?= $accept ?>" class="block w-full text-xs text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-600 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white hover:file:bg-indigo-700">
                        </div>
                    <?php endforeach; ?>
                    <p class="rounded-xl bg-slate-50 px-3 py-2 text-[10px] text-slate-400">Format gambar: JPG, PNG, WEBP. Dokumen legal juga menerima PDF.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-end">
        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700">
            <i class="fa-solid fa-floppy-disk"></i> Simpan Pengaturan
        </button>
    </div>
</form>
