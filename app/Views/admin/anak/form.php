<?php
$isEdit   = ! empty($anak);
$action   = $isEdit ? site_url('admin/anak/update/' . $anak['id_anak']) : site_url('admin/anak/store');
$value    = static fn (string $key, mixed $default = '') => old($key, $anak[$key] ?? $default);
$selected = static fn (string $key, mixed $val): string => (string) old($key, $anak[$key] ?? '') === (string) $val ? 'selected' : '';
$checked  = static fn (string $key, mixed $val, bool $default = false): string => ((string) old($key, $anak[$key] ?? ($default ? '1' : '0')) === (string) $val) ? 'checked' : '';

$inputClass = 'w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20';
$labelClass = 'mb-1 block text-[11px] font-semibold uppercase tracking-wider text-slate-400';

$provinsiOptions = array_map(static fn ($row) => [
    'value' => (int) $row['id_provinsi'],
    'label' => $row['nama_provinsi'],
], $options['provinsi']);

$kabupatenOptions = array_map(static fn ($row) => [
    'value'    => (int) $row['id_kabupaten'],
    'label'    => $row['nama_kabupaten'],
    'provinsi' => (int) $row['id_provinsi'],
], $options['kabupaten']);

$regionConfig = json_encode([
    'provinsiId'  => (string) $value('id_provinsi'),
    'kabupatenId' => (string) $value('id_kabupaten'),
    'provinsi'    => $provinsiOptions,
    'kabupaten'   => $kabupatenOptions,
], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
?>

<form method="post" action="<?= $action ?>" enctype="multipart/form-data" class="space-y-5">
    <?= csrf_field() ?>

    <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
        <div>
            <h2 class="text-lg font-extrabold text-slate-900"><?= $isEdit ? 'Edit Data Anak' : 'Tambah Data Anak' ?></h2>
            <p class="text-sm text-slate-500">Lengkapi data identitas, orang tua, pendidikan, dan berkas pendukung.</p>
        </div>
        <a href="<?= site_url('admin/anak_aktif') ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Identitas -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-5 py-4">
            <h3 class="flex items-center gap-2 text-sm font-bold text-slate-900"><i class="fa-solid fa-id-card text-indigo-500"></i> Identitas Anak</h3>
        </div>
        <div class="grid gap-4 p-5 md:grid-cols-2 xl:grid-cols-3">
            <div>
                <label class="<?= $labelClass ?>">No. Registrasi</label>
                <input type="text" name="no_registrasi" value="<?= esc($value('no_registrasi')) ?>" class="<?= $inputClass ?>" placeholder="Otomatis jika dikosongkan">
            </div>
            <div class="md:col-span-2">
                <label class="<?= $labelClass ?>">Nama Lengkap <span class="text-rose-500">*</span></label>
                <input type="text" name="nama_anak" value="<?= esc($value('nama_anak')) ?>" required class="<?= $inputClass ?>" placeholder="Nama lengkap anak">
            </div>
            <div>
                <label class="<?= $labelClass ?>">NIK</label>
                <input type="text" name="nik" value="<?= esc($value('nik')) ?>" class="<?= $inputClass ?>" maxlength="20" placeholder="16 digit NIK">
            </div>
            <div>
                <label class="<?= $labelClass ?>">No. Kartu Keluarga</label>
                <input type="text" name="no_kk" value="<?= esc($value('no_kk')) ?>" class="<?= $inputClass ?>" maxlength="20">
            </div>
            <div>
                <label class="<?= $labelClass ?>">Jenis Kelamin <span class="text-rose-500">*</span></label>
                <select name="jenis_kelamin" required class="<?= $inputClass ?>">
                    <option value="L" <?= $selected('jenis_kelamin', 'L') ?>>Laki-laki</option>
                    <option value="P" <?= $selected('jenis_kelamin', 'P') ?>>Perempuan</option>
                </select>
            </div>
            <div>
                <label class="<?= $labelClass ?>">Tempat Lahir</label>
                <input type="text" name="tempat_lahir" value="<?= esc($value('tempat_lahir')) ?>" class="<?= $inputClass ?>">
            </div>
            <div>
                <label class="<?= $labelClass ?>">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" value="<?= esc($value('tanggal_lahir')) ?>" class="<?= $inputClass ?>">
            </div>
            <div>
                <label class="<?= $labelClass ?>">Agama</label>
                <select name="agama" class="<?= $inputClass ?>">
                    <option value="">Pilih agama</option>
                    <?php foreach ($options['agama'] as $agama): ?>
                        <option value="<?= esc($agama) ?>" <?= $selected('agama', $agama) ?>><?= esc($agama) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="<?= $labelClass ?>">Kewarganegaraan</label>
                <input type="text" name="kewarganegaraan" value="<?= esc($value('kewarganegaraan', 'Indonesia')) ?>" class="<?= $inputClass ?>">
            </div>
            <div>
                <label class="<?= $labelClass ?>">Anak Ke-</label>
                <input type="number" name="anak_ke" min="1" value="<?= esc($value('anak_ke')) ?>" class="<?= $inputClass ?>">
            </div>
            <div>
                <label class="<?= $labelClass ?>">Jumlah Saudara Kandung</label>
                <input type="number" name="jumlah_saudara_kandung" min="0" value="<?= esc($value('jumlah_saudara_kandung')) ?>" class="<?= $inputClass ?>">
            </div>
            <div>
                <label class="<?= $labelClass ?>">Jumlah Saudara Tiri</label>
                <input type="number" name="jumlah_saudara_tiri" min="0" value="<?= esc($value('jumlah_saudara_tiri')) ?>" class="<?= $inputClass ?>">
            </div>
        </div>
    </div>

    <!-- Status & Wilayah -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-5 py-4">
            <h3 class="flex items-center gap-2 text-sm font-bold text-slate-900"><i class="fa-solid fa-location-dot text-indigo-500"></i> Status &amp; Wilayah</h3>
        </div>
        <div class="grid gap-4 p-5 md:grid-cols-2 xl:grid-cols-3">
            <div>
                <label class="<?= $labelClass ?>">Status Anak</label>
                <select name="status_anak" class="<?= $inputClass ?>">
                    <?php foreach ($options['status'] as $status): ?>
                        <option value="<?= esc($status) ?>" <?= $selected('status_anak', $status) ?>><?= esc($status) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="<?= $labelClass ?>">Status Tinggal</label>
                <select name="status_tinggal" class="<?= $inputClass ?>">
                    <?php foreach ($options['tinggal'] as $tinggal): ?>
                        <option value="<?= esc($tinggal) ?>" <?= $selected('status_tinggal', $tinggal) ?>><?= esc($tinggal) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="<?= $labelClass ?>">Kategori</label>
                <select name="kategori" class="<?= $inputClass ?>">
                    <option value="">Pilih kategori</option>
                    <?php foreach ($options['kategori'] as $kategori): ?>
                        <option value="<?= esc($kategori) ?>" <?= $selected('kategori', $kategori) ?>><?= esc($kategori) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="<?= $labelClass ?>">Tanggal Masuk</label>
                <input type="date" name="tanggal_masuk" value="<?= esc($value('tanggal_masuk')) ?>" class="<?= $inputClass ?>">
            </div>

            <div class="contents" x-data='regionPicker(<?= $regionConfig ?>)'>
                <div>
                    <label class="<?= $labelClass ?>">Provinsi</label>
                    <input type="hidden" name="id_provinsi" :value="provinsiId">
                    <div class="relative" @click.outside="provinsiOpen = false">
                        <input type="text" :value="provinsiOpen ? provinsiSearch : provinsiLabel" @focus="provinsiOpen = true; provinsiSearch = ''" @input="provinsiSearch = $event.target.value" placeholder="Cari provinsi..." class="<?= $inputClass ?> pr-9">
                        <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400"><i class="fa-solid fa-chevron-down text-xs"></i></span>

                        <div x-show="provinsiOpen" x-cloak class="absolute z-20 mt-1 max-h-52 w-full overflow-y-auto rounded-xl border border-slate-200 bg-white shadow-lg">
                            <template x-for="p in filteredProvinsi" :key="p.value">
                                <button type="button" @click="selectProvinsi(p)" class="flex w-full items-center justify-between px-3 py-2 text-left text-sm text-slate-700 transition hover:bg-indigo-50">
                                    <span x-text="p.label"></span>
                                    <i x-show="String(p.value) === String(provinsiId)" class="fa-solid fa-check text-xs text-indigo-600"></i>
                                </button>
                            </template>
                            <p x-show="filteredProvinsi.length === 0" class="px-3 py-3 text-center text-xs text-slate-400">Tidak ditemukan</p>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="<?= $labelClass ?>">Kabupaten / Kota</label>
                    <input type="hidden" name="id_kabupaten" :value="kabupatenId">
                    <div class="relative" @click.outside="kabupatenOpen = false">
                        <input type="text" :value="kabupatenOpen ? kabupatenSearch : kabupatenLabel" @focus="kabupatenOpen = true; kabupatenSearch = ''" @input="kabupatenSearch = $event.target.value" placeholder="Cari kabupaten/kota..." class="<?= $inputClass ?> pr-9">
                        <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400"><i class="fa-solid fa-chevron-down text-xs"></i></span>

                        <div x-show="kabupatenOpen" x-cloak class="absolute z-20 mt-1 max-h-52 w-full overflow-y-auto rounded-xl border border-slate-200 bg-white shadow-lg">
                            <template x-for="k in filteredKabupaten" :key="k.value">
                                <button type="button" @click="selectKabupaten(k)" class="flex w-full items-center justify-between px-3 py-2 text-left text-sm text-slate-700 transition hover:bg-indigo-50">
                                    <span x-text="k.label"></span>
                                    <i x-show="String(k.value) === String(kabupatenId)" class="fa-solid fa-check text-xs text-indigo-600"></i>
                                </button>
                            </template>
                            <p x-show="filteredKabupaten.length === 0" class="px-3 py-3 text-center text-xs text-slate-400">Tidak ditemukan</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-4 border-t border-slate-100 p-5 md:grid-cols-2">
            <div>
                <label class="<?= $labelClass ?>">Alamat Orang Tua</label>
                <textarea name="alamat_orang_tua" rows="2" class="<?= $inputClass ?>"><?= esc($value('alamat_orang_tua')) ?></textarea>
            </div>
            <div>
                <label class="<?= $labelClass ?>">Alamat Wali</label>
                <textarea name="alamat_wali" rows="2" class="<?= $inputClass ?>"><?= esc($value('alamat_wali')) ?></textarea>
            </div>
        </div>
    </div>

    <!-- Orang tua & wali -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-5 py-4">
            <h3 class="flex items-center gap-2 text-sm font-bold text-slate-900"><i class="fa-solid fa-people-roof text-indigo-500"></i> Orang Tua &amp; Wali</h3>
        </div>
        <div class="grid gap-4 p-5 md:grid-cols-2 xl:grid-cols-4">
            <div>
                <label class="<?= $labelClass ?>">Nama Ayah Kandung</label>
                <input type="text" name="nama_ayah_kandung" value="<?= esc($value('nama_ayah_kandung')) ?>" class="<?= $inputClass ?>">
            </div>
            <div>
                <label class="<?= $labelClass ?>">Nama Ayah Tiri</label>
                <input type="text" name="nama_ayah_tiri" value="<?= esc($value('nama_ayah_tiri')) ?>" class="<?= $inputClass ?>">
            </div>
            <div>
                <label class="<?= $labelClass ?>">Nama Ibu Kandung</label>
                <input type="text" name="nama_ibu_kandung" value="<?= esc($value('nama_ibu_kandung')) ?>" class="<?= $inputClass ?>">
            </div>
            <div>
                <label class="<?= $labelClass ?>">Nama Ibu Tiri</label>
                <input type="text" name="nama_ibu_tiri" value="<?= esc($value('nama_ibu_tiri')) ?>" class="<?= $inputClass ?>">
            </div>
            <div>
                <label class="<?= $labelClass ?>">Pekerjaan Ayah</label>
                <input type="text" name="pekerjaan_ayah" value="<?= esc($value('pekerjaan_ayah')) ?>" class="<?= $inputClass ?>">
            </div>
            <div>
                <label class="<?= $labelClass ?>">Pekerjaan Ibu</label>
                <input type="text" name="pekerjaan_ibu" value="<?= esc($value('pekerjaan_ibu')) ?>" class="<?= $inputClass ?>">
            </div>
            <div>
                <label class="<?= $labelClass ?>">No. Telp Orang Tua</label>
                <input type="text" name="no_telp_orang_tua" value="<?= esc($value('no_telp_orang_tua')) ?>" class="<?= $inputClass ?>">
            </div>
            <div>
                <label class="<?= $labelClass ?>">Nama Wali</label>
                <input type="text" name="nama_wali" value="<?= esc($value('nama_wali')) ?>" class="<?= $inputClass ?>">
            </div>
            <div>
                <label class="<?= $labelClass ?>">No. Telp Wali</label>
                <input type="text" name="no_telp_wali" value="<?= esc($value('no_telp_wali')) ?>" class="<?= $inputClass ?>">
            </div>
        </div>
    </div>

    <!-- Pendidikan -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-5 py-4">
            <h3 class="flex items-center gap-2 text-sm font-bold text-slate-900"><i class="fa-solid fa-graduation-cap text-indigo-500"></i> Pendidikan</h3>
        </div>
        <div class="grid gap-4 p-5 md:grid-cols-2 xl:grid-cols-3">
            <div>
                <label class="<?= $labelClass ?>">Jenjang Pendidikan</label>
                <select name="pendidikan" class="<?= $inputClass ?>">
                    <option value="">Pilih pendidikan</option>
                    <?php foreach ($options['pendidikan'] as $pendidikan): ?>
                        <option value="<?= esc($pendidikan) ?>" <?= $selected('pendidikan', $pendidikan) ?>><?= esc($pendidikan) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="<?= $labelClass ?>">Kelas</label>
                <input type="text" name="kelas" value="<?= esc($value('kelas')) ?>" class="<?= $inputClass ?>">
            </div>
            <div>
                <label class="<?= $labelClass ?>">Nama Sekolah</label>
                <input type="text" name="nama_sekolah" value="<?= esc($value('nama_sekolah')) ?>" class="<?= $inputClass ?>">
            </div>
            <div>
                <label class="<?= $labelClass ?>">No. Telp Sekolah</label>
                <input type="text" name="no_telp_sekolah" value="<?= esc($value('no_telp_sekolah')) ?>" class="<?= $inputClass ?>">
            </div>
            <div>
                <label class="<?= $labelClass ?>">Biaya SPP</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-xs font-semibold text-slate-400">Rp</span>
                    <input type="number" step="0.01" min="0" name="biaya_spp" value="<?= esc($value('biaya_spp')) ?>" class="<?= $inputClass ?> pl-9">
                </div>
            </div>
            <div class="md:col-span-2 xl:col-span-3">
                <label class="<?= $labelClass ?>">Alamat Sekolah</label>
                <textarea name="alamat_sekolah" rows="2" class="<?= $inputClass ?>"><?= esc($value('alamat_sekolah')) ?></textarea>
            </div>
        </div>
    </div>

    <!-- Berkas -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-5 py-4">
            <h3 class="flex items-center gap-2 text-sm font-bold text-slate-900"><i class="fa-solid fa-folder-open text-indigo-500"></i> Berkas Pendukung</h3>
        </div>
        <div class="grid gap-5 p-5 md:grid-cols-2 xl:grid-cols-4">
            <?php
            $berkas = [
                ['name' => 'foto', 'label' => 'Foto Anak', 'accept' => 'image/*', 'current' => $anak['foto'] ?? null, 'jenis' => 'foto'],
                ['name' => 'file_kk', 'label' => 'Kartu Keluarga', 'accept' => 'image/*,application/pdf', 'current' => $anak['file_kk'] ?? null, 'jenis' => 'kk'],
                ['name' => 'file_akta', 'label' => 'Akta Lahir', 'accept' => 'image/*,application/pdf', 'current' => $anak['file_akta'] ?? null, 'jenis' => 'akta'],
                ['name' => 'file_pendukung', 'label' => 'Dokumen Pendukung', 'accept' => 'image/*,application/pdf', 'current' => $anak['file_pendukung'] ?? null, 'jenis' => 'pendukung'],
            ];
            ?>
            <?php foreach ($berkas as $item): ?>
                <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-bold text-slate-700"><?= esc($item['label']) ?></p>
                    <?php if (! empty($item['current'])): ?>
                        <?php if ($item['jenis'] === 'foto'): ?>
                            <a href="<?= site_url('admin/anak/view_dokumen/' . $anak['id_anak'] . '/foto') ?>" target="_blank" class="mt-2 flex items-center gap-3">
                                <img src="<?= upload_url('foto_anak', $item['current']) ?>" alt="<?= esc($anak['nama_anak'] ?? '') ?>" class="h-20 w-20 rounded-xl object-cover ring-1 ring-slate-200">
                                <span class="text-[11px] font-semibold text-indigo-600 hover:text-indigo-700"><i class="fa-solid fa-up-right-from-square mr-1"></i>Lihat foto</span>
                            </a>
                        <?php else: ?>
                            <a href="<?= site_url('admin/anak/view_dokumen/' . $anak['id_anak'] . '/' . $item['jenis']) ?>" target="_blank" class="mt-2 inline-flex items-center gap-1.5 text-[11px] font-semibold text-indigo-600 hover:text-indigo-700">
                                <i class="fa-solid fa-paperclip"></i> Lihat berkas saat ini
                            </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="mt-2 text-[11px] text-slate-400">Belum ada berkas.</p>
                    <?php endif; ?>
                    <input type="file" name="<?= $item['name'] ?>" accept="<?= $item['accept'] ?>" class="mt-3 block w-full text-xs text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-600 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white hover:file:bg-indigo-700">
                    <p class="mt-1 text-[10px] text-slate-400">Maks. 2 MB (JPG, PNG, WEBP, PDF).</p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="flex flex-col-reverse justify-end gap-3 sm:flex-row">
        <a href="<?= site_url('admin/anak_aktif') ?>" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">Batal</a>
        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700">
            <i class="fa-solid fa-floppy-disk"></i> <?= $isEdit ? 'Simpan Perubahan' : 'Simpan Data' ?>
        </button>
    </div>
</form>
