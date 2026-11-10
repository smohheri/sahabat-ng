<?php
$inputClass = 'w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20';
$labelClass = 'mb-1 block text-[11px] font-semibold uppercase tracking-wider text-slate-400';
$guru       = $guru ?? [];
$value      = static fn (string $key) => old($key, $guru[$key] ?? '');
?>

<div class="space-y-5">
    <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
        <div>
            <h2 class="text-lg font-extrabold text-slate-900">Edit Profil Pengajar</h2>
            <p class="text-sm text-slate-500">Perbarui data kepegawaian, kontak, dan berkas pendukung.</p>
        </div>
        <a href="<?= site_url('guru/profil-pengajar') ?>" class="inline-flex items-center gap-2 self-start rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <form method="post" action="<?= site_url('guru/profil-pengajar/update') ?>" enctype="multipart/form-data" class="space-y-5">
        <?= csrf_field() ?>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="flex items-center gap-2 text-sm font-bold text-slate-900"><i class="fa-solid fa-user text-indigo-500"></i> Identitas Pengajar</h3>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="<?= $labelClass ?>">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_guru" value="<?= esc($guru['nama_guru'] ?? ($currentUser['nama'] ?? '')) ?>" required class="<?= $inputClass ?>" placeholder="Nama lengkap pengajar">
                </div>
                <div>
                    <label class="<?= $labelClass ?>">NIP</label>
                    <input type="text" name="nip" value="<?= esc($guru['nip'] ?? '') ?>" class="<?= $inputClass ?>" placeholder="Nomor induk pegawai">
                </div>
                <div>
                    <label class="<?= $labelClass ?>">Jabatan</label>
                    <input type="text" name="jabatan" value="<?= esc($guru['jabatan'] ?? '') ?>" class="<?= $inputClass ?>" placeholder="Contoh: Guru Matematika">
                </div>
                <div>
                    <label class="<?= $labelClass ?>">Status Kepegawaian</label>
                    <input type="text" name="status_kepegawaian" value="<?= esc($guru['status_kepegawaian'] ?? '') ?>" class="<?= $inputClass ?>" placeholder="Tetap / Honorer / Kontrak">
                </div>
                <div>
                    <label class="<?= $labelClass ?>">Pengalaman (tahun)</label>
                    <input type="number" name="pengalaman_tahun" min="0" max="80" value="<?= esc($guru['pengalaman_tahun'] ?? '') ?>" class="<?= $inputClass ?>" placeholder="0">
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="flex items-center gap-2 text-sm font-bold text-slate-900"><i class="fa-solid fa-graduation-cap text-emerald-500"></i> Kualifikasi</h3>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div>
                    <label class="<?= $labelClass ?>">Pendidikan Terakhir</label>
                    <input type="text" name="pendidikan_terakhir" value="<?= esc($guru['pendidikan_terakhir'] ?? '') ?>" class="<?= $inputClass ?>" placeholder="Contoh: S1 Pendidikan Matematika">
                </div>
                <div>
                    <label class="<?= $labelClass ?>">Bidang Keahlian</label>
                    <input type="text" name="bidang_keahlian" value="<?= esc($guru['bidang_keahlian'] ?? '') ?>" class="<?= $inputClass ?>" placeholder="Contoh: Matematika, IPA">
                </div>
                <div class="md:col-span-2">
                    <label class="<?= $labelClass ?>">Sertifikasi</label>
                    <input type="text" name="sertifikasi" value="<?= esc($guru['sertifikasi'] ?? '') ?>" class="<?= $inputClass ?>" placeholder="Contoh: Sertifikat Pendidik 2023">
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="flex items-center gap-2 text-sm font-bold text-slate-900"><i class="fa-solid fa-address-book text-sky-500"></i> Kontak</h3>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div>
                    <label class="<?= $labelClass ?>">No. HP / WhatsApp</label>
                    <input type="text" name="no_hp" value="<?= esc($guru['no_hp'] ?? '') ?>" class="<?= $inputClass ?>" placeholder="08xxxxxxxxxx">
                </div>
                <div>
                    <label class="<?= $labelClass ?>">Email</label>
                    <input type="email" name="email" value="<?= esc($guru['email'] ?? '') ?>" class="<?= $inputClass ?>" placeholder="nama@email.com">
                </div>
                <div class="md:col-span-2">
                    <label class="<?= $labelClass ?>">Alamat</label>
                    <textarea name="alamat" rows="3" class="<?= $inputClass ?>" placeholder="Alamat lengkap"><?= esc($guru['alamat'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="flex items-center gap-2 text-sm font-bold text-slate-900"><i class="fa-solid fa-paperclip text-amber-500"></i> Berkas</h3>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div>
                    <label class="<?= $labelClass ?>">Foto Profil</label>
                    <?php if (! empty($guru['foto'])): ?>
                        <div class="mb-2 flex items-center gap-2">
                            <img src="<?= upload_url('foto_guru', $guru['foto']) ?>" alt="Foto" class="h-12 w-12 rounded-lg object-cover ring-1 ring-slate-200">
                            <span class="text-[11px] text-slate-400">Foto saat ini</span>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="foto" accept="image/*" class="block w-full text-xs text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-600 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-white hover:file:bg-indigo-700">
                    <p class="mt-1 text-[10px] text-slate-400">JPG, PNG, atau WEBP. Maksimal 2 MB.</p>
                </div>
                <div>
                    <label class="<?= $labelClass ?>">Ijazah Terakhir</label>
                    <?php if (! empty($guru['file_ijazah_terakhir'])): ?>
                        <div class="mb-2 flex items-center gap-2 text-[11px] text-slate-500">
                            <i class="fa-solid fa-file-pdf text-rose-500"></i>
                            <a href="<?= upload_url('file_ijazah', $guru['file_ijazah_terakhir']) ?>" target="_blank" class="font-semibold text-indigo-600 hover:underline">Lihat berkas saat ini</a>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="file_ijazah_terakhir" accept=".pdf,.jpg,.jpeg,.png" class="block w-full text-xs text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-800 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-white hover:file:bg-slate-700">
                    <p class="mt-1 text-[10px] text-slate-400">PDF atau gambar. Maksimal 5 MB.</p>
                </div>
            </div>
        </div>

        <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
            <a href="<?= site_url('guru/profil-pengajar') ?>" class="rounded-xl border border-slate-200 px-5 py-2.5 text-center text-xs font-semibold text-slate-600 transition hover:bg-slate-50">Batal</a>
            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-6 py-2.5 text-xs font-bold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Profil
            </button>
        </div>
    </form>
</div>
