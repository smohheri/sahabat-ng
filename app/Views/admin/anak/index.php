<?php
$statusLabel = $statusMode === 'aktif' ? 'Anak Aktif' : 'Anak Nonaktif';
$exportQuery = http_build_query(array_filter([
    'q'             => $filters['search'] ?? '',
    'jenis_kelamin' => $filters['jenis_kelamin'] ?? '',
    'pendidikan'    => $filters['pendidikan'] ?? '',
    'kategori'      => $filters['kategori'] ?? '',
]));

// Render satu field statis untuk modal detail (nilai diambil via Alpine x-text).
$modalField = static function (string $label, string $expr): void {
    ?>
    <div>
        <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400"><?= esc($label) ?></p>
        <p class="mt-1 break-words text-sm font-medium text-slate-800" x-text="<?= $expr ?>"></p>
    </div>
    <?php
};
?>

<div x-data="{ detailOpen: false, detailAnak: {}, exportOpen: false }">
    <div class="space-y-5">
    <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
        <div>
            <h2 class="text-lg font-extrabold text-slate-900"><?= esc($statusLabel) ?></h2>
            <p class="text-sm text-slate-500">Total <?= number_format($total, 0, ',', '.') ?> data ditemukan.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button type="button" @click="exportOpen = true" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50">
                <i class="fa-solid fa-file-export text-indigo-600"></i> Export
            </button>
            <a href="<?= site_url('admin/anak/tambah') ?>" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-bold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700">
                <i class="fa-solid fa-plus"></i> Tambah Anak
            </a>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <form method="get" action="<?= current_url() ?>" class="grid gap-3 md:grid-cols-2 xl:grid-cols-6">
            <div class="xl:col-span-2">
                <label class="mb-1 block text-[11px] font-semibold uppercase tracking-wider text-slate-400">Cari</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i class="fa-solid fa-magnifying-glass text-xs"></i></span>
                    <input type="text" name="q" value="<?= esc($filters['search'] ?? '') ?>" placeholder="Nama, NIK, No. KK, sekolah..."
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-9 pr-3 text-sm outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20">
                </div>
            </div>

            <div>
                <label class="mb-1 block text-[11px] font-semibold uppercase tracking-wider text-slate-400">Jenis Kelamin</label>
                <select name="jenis_kelamin" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20">
                    <option value="">Semua</option>
                    <option value="L" <?= ($filters['jenis_kelamin'] ?? '') === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                    <option value="P" <?= ($filters['jenis_kelamin'] ?? '') === 'P' ? 'selected' : '' ?>>Perempuan</option>
                </select>
            </div>

            <div>
                <label class="mb-1 block text-[11px] font-semibold uppercase tracking-wider text-slate-400">Pendidikan</label>
                <select name="pendidikan" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20">
                    <option value="">Semua</option>
                    <?php foreach ($options['pendidikan'] as $pendidikan): ?>
                        <option value="<?= esc($pendidikan) ?>" <?= ($filters['pendidikan'] ?? '') === $pendidikan ? 'selected' : '' ?>><?= esc($pendidikan) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="mb-1 block text-[11px] font-semibold uppercase tracking-wider text-slate-400">Kategori</label>
                <select name="kategori" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20">
                    <option value="">Semua</option>
                    <?php foreach ($options['kategori'] as $kategori): ?>
                        <option value="<?= esc($kategori) ?>" <?= ($filters['kategori'] ?? '') === $kategori ? 'selected' : '' ?>><?= esc($kategori) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-slate-800">
                    <i class="fa-solid fa-filter mr-1"></i> Filter
                </button>
                <a href="<?= current_url() ?>" class="rounded-xl border border-slate-200 px-3 py-2.5 text-xs font-semibold text-slate-500 transition hover:bg-slate-50" title="Reset"><i class="fa-solid fa-rotate-left"></i></a>
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-4 py-3 font-semibold">No</th>
                        <th class="px-4 py-3 font-semibold">Anak</th>
                        <th class="px-4 py-3 font-semibold">L/P</th>
                        <th class="px-4 py-3 font-semibold">Tempat / Tgl Lahir</th>
                        <th class="px-4 py-3 font-semibold">Umur</th>
                        <th class="px-4 py-3 font-semibold">Kategori</th>
                        <th class="px-4 py-3 font-semibold">Pendidikan</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        <th class="px-4 py-3 font-semibold">Status Berkas</th>
                        <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if ($items === []): ?>
                        <tr>
                            <td colspan="10" class="px-4 py-12 text-center">
                                <i class="fa-solid fa-inbox mb-3 text-3xl text-slate-300"></i>
                                <p class="text-sm text-slate-500">Tidak ada data anak yang sesuai filter.</p>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($items as $index => $anak):
                            $detail = $anak;
                            $detail['umur_text']       = umur($anak['tanggal_lahir'] ?? null);
                            $detail['foto_url']        = ! empty($anak['foto']) ? upload_url('foto_anak', $anak['foto']) : '';
                            $detail['jk_label']        = ($anak['jenis_kelamin'] ?? '') === 'L' ? 'Laki-laki' : 'Perempuan';
                            $detail['ttl']             = trim(($anak['tempat_lahir'] ?: '-') . ', ' . tanggal_id($anak['tanggal_lahir']));
                            $detail['tanggal_masuk_id']  = tanggal_id($anak['tanggal_masuk'] ?? null);
                            $detail['spp_rupiah']      = ! empty($anak['biaya_spp']) ? rupiah($anak['biaya_spp']) : '-';
                            $detail['view_foto']       = ! empty($anak['foto']) ? site_url('admin/anak/view_dokumen/' . $anak['id_anak'] . '/foto') : '';
                            $detail['view_kk']         = ! empty($anak['file_kk']) ? site_url('admin/anak/view_dokumen/' . $anak['id_anak'] . '/kk') : '';
                            $detail['view_akta']       = ! empty($anak['file_akta']) ? site_url('admin/anak/view_dokumen/' . $anak['id_anak'] . '/akta') : '';
                            $detail['view_pendukung']  = ! empty($anak['file_pendukung']) ? site_url('admin/anak/view_dokumen/' . $anak['id_anak'] . '/pendukung') : '';
                            $detailJson = json_encode($detail, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                        ?>
                        <tr class="transition hover:bg-slate-50/70">
                            <td class="px-4 py-3 text-slate-500"><?= (($page - 1) * $perPage) + $index + 1 ?></td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <?php if (! empty($anak['foto'])): ?>
                                        <a href="<?= site_url('admin/anak/view_dokumen/' . $anak['id_anak'] . '/foto') ?>" target="_blank" title="Lihat foto">
                                            <img src="<?= upload_url('foto_anak', $anak['foto']) ?>" alt="<?= esc($anak['nama_anak']) ?>" class="h-10 w-10 rounded-lg object-cover ring-1 ring-slate-200">
                                        </a>
                                    <?php else: ?>
                                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-50 text-xs font-bold text-indigo-600"><?= esc(strtoupper(mb_substr((string) $anak['nama_anak'], 0, 1))) ?></span>
                                    <?php endif; ?>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800"><?= esc($anak['nama_anak']) ?></p>
                                        <p class="text-[11px] text-slate-400"><?= esc($anak['no_registrasi'] ?: 'Tanpa No. Registrasi') ?></p>
                                        <?php if (! empty($anak['nik'])): ?><p class="text-[10px] text-slate-400">NIK: <?= esc($anak['nik']) ?></p><?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex h-6 w-6 items-center justify-center rounded-md text-[11px] font-bold <?= $anak['jenis_kelamin'] === 'L' ? 'bg-sky-50 text-sky-600' : 'bg-rose-50 text-rose-600' ?>">
                                    <?= esc($anak['jenis_kelamin']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                <p><?= esc($anak['tempat_lahir'] ?: '-') ?></p>
                                <p class="text-[11px] text-slate-400"><?= tanggal_id($anak['tanggal_lahir'] ?? null) ?></p>
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                <span class="inline-flex rounded-md bg-slate-100 px-2 py-1 text-[11px] font-semibold text-slate-600"><?= esc(umur($anak['tanggal_lahir'] ?? null)) ?></span>
                            </td>
                            <td class="px-4 py-3 text-slate-600"><?= esc($anak['kategori'] ?: '-') ?></td>
                            <td class="px-4 py-3">
                                <p class="text-slate-600"><?= esc($anak['pendidikan'] ?: '-') ?></p>
                                <?php if (! empty($anak['nama_sekolah'])): ?><p class="text-[11px] text-slate-400"><?= esc($anak['nama_sekolah']) ?></p><?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold ring-1 ring-inset <?= status_badge($anak['status_anak'] ?? '') ?>"><?= esc($anak['status_anak'] ?: '-') ?></span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1.5">
                                    <?php
                                    $berkasStatus = [
                                        ['Foto', 'fa-camera', $anak['foto'] ?? null],
                                        ['Kartu Keluarga', 'fa-file-lines', $anak['file_kk'] ?? null],
                                        ['Akta Lahir', 'fa-file-shield', $anak['file_akta'] ?? null],
                                        ['Dokumen Pendukung', 'fa-paperclip', $anak['file_pendukung'] ?? null],
                                    ];
                                    foreach ($berkasStatus as [$label, $icon, $val]): ?>
                                        <span title="<?= esc($label . ($val ? ': Ada' : ': Belum ada')) ?>" class="inline-flex h-8 w-8 items-center justify-center rounded-lg <?= $val ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/30' : 'bg-white text-slate-300 ring-1 ring-inset ring-slate-200' ?>">
                                            <i class="fa-solid <?= $icon ?> text-xs"></i>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <button type="button" @click='detailAnak = <?= $detailJson ?>; detailOpen = true' class="rounded-lg border border-slate-200 p-2 text-slate-500 transition hover:bg-indigo-50 hover:text-indigo-600" title="Detail">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </button>
                                    <a href="<?= site_url('admin/anak/edit/' . $anak['id_anak']) ?>" class="rounded-lg border border-slate-200 p-2 text-slate-500 transition hover:bg-indigo-50 hover:text-indigo-600" title="Edit">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </a>
                                    <form method="post" action="<?= site_url('admin/anak/activate-account/' . $anak['id_anak']) ?>" class="inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="rounded-lg border border-slate-200 p-2 text-slate-500 transition hover:bg-emerald-50 hover:text-emerald-600" title="Aktifkan akun anak">
                                            <i class="fa-solid fa-user-plus text-xs"></i>
                                        </button>
                                    </form>
                                    <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
                                    <form method="post" action="<?= site_url('admin/anak/delete/' . $anak['id_anak']) ?>" data-confirm="Hapus data <?= esc($anak['nama_anak'], 'attr') ?>? Tindakan ini tidak dapat dibatalkan." class="inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="rounded-lg border border-slate-200 p-2 text-slate-500 transition hover:bg-rose-50 hover:text-rose-600" title="Hapus">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="px-4 pb-4">
            <?= view('partials/pagination', ['page' => $page, 'perPage' => $perPage, 'total' => $total]) ?>
        </div>
    </div>
    </div>

    <!-- Modal Detail Anak -->
    <div x-cloak x-show="detailOpen" @keydown.escape.window="detailOpen = false" class="fixed inset-0 z-50 overflow-y-auto p-4 sm:p-6" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="detailOpen = false"></div>

        <div class="relative mx-auto w-full max-w-4xl overflow-hidden rounded-3xl bg-slate-50 shadow-2xl ring-1 ring-slate-200">
            <!-- Header -->
            <div class="relative overflow-hidden bg-indigo-600 px-6 py-6 sm:px-8">
                <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10 blur-2xl"></div>
                <div class="absolute -bottom-16 right-24 h-32 w-32 rounded-full bg-sky-300/20 blur-3xl"></div>
                <div class="relative flex items-start justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <img x-show="detailAnak.foto_url" :src="detailAnak.foto_url" :alt="detailAnak.nama_anak || ''" class="h-20 w-20 shrink-0 rounded-2xl object-cover ring-2 ring-white/40">
                        <span x-show="!detailAnak.foto_url" class="flex h-20 w-20 shrink-0 items-center justify-center rounded-2xl bg-white/20 text-2xl font-extrabold text-white" x-text="detailAnak.nama_anak ? detailAnak.nama_anak.charAt(0).toUpperCase() : 'A'"></span>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-indigo-200">Profil Anak</p>
                            <h3 class="mt-1 text-xl font-extrabold text-white sm:text-2xl" x-text="detailAnak.nama_anak"></h3>
                            <p class="mt-1 text-xs text-indigo-100" x-text="detailAnak.no_registrasi || 'Tanpa No. Registrasi'"></p>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <span class="inline-flex rounded-full bg-white/15 px-2.5 py-1 text-[11px] font-semibold text-white" x-text="detailAnak.status_anak || '-'"></span>
                                <span class="inline-flex rounded-full bg-white/15 px-2.5 py-1 text-[11px] font-semibold text-white" x-text="detailAnak.kategori || '-'"></span>
                                <span class="inline-flex rounded-full bg-white/15 px-2.5 py-1 text-[11px] font-semibold text-white" x-text="detailAnak.jk_label"></span>
                            </div>
                        </div>
                    </div>
                    <button type="button" @click="detailOpen = false" class="shrink-0 rounded-xl bg-white/15 p-2.5 text-white transition hover:bg-white/25" title="Tutup">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>

            <!-- Body -->
            <div class="max-h-[calc(100vh-13rem)] space-y-6 overflow-y-auto p-6 sm:p-8">
                <!-- Identitas -->
                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h4 class="flex items-center gap-2 text-sm font-bold text-slate-900"><i class="fa-solid fa-id-card text-indigo-500"></i> Identitas Anak</h4>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                        <?php $modalField('Nama Lengkap', "detailAnak.nama_anak || '-'"); ?>
                        <?php $modalField('NIK', "detailAnak.nik || '-'"); ?>
                        <?php $modalField('No. Kartu Keluarga', "detailAnak.no_kk || '-'"); ?>
                        <?php $modalField('Jenis Kelamin', 'detailAnak.jk_label'); ?>
                        <?php $modalField('Tempat, Tgl Lahir', 'detailAnak.ttl'); ?>
                        <?php $modalField('Umur', 'detailAnak.umur_text'); ?>
                        <?php $modalField('Agama', "detailAnak.agama || '-'"); ?>
                        <?php $modalField('Kewarganegaraan', "detailAnak.kewarganegaraan || '-'"); ?>
                        <?php $modalField('Anak Ke-', "detailAnak.anak_ke ?? '-'"); ?>
                        <?php $modalField('Saudara Kandung', "detailAnak.jumlah_saudara_kandung ?? '-'"); ?>
                        <?php $modalField('Saudara Tiri', "detailAnak.jumlah_saudara_tiri ?? '-'"); ?>
                    </div>
                </section>

                <!-- Status & Wilayah -->
                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h4 class="flex items-center gap-2 text-sm font-bold text-slate-900"><i class="fa-solid fa-location-dot text-indigo-500"></i> Status &amp; Wilayah</h4>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                        <?php $modalField('Status Anak', "detailAnak.status_anak || '-'"); ?>
                        <?php $modalField('Status Tinggal', "detailAnak.status_tinggal || '-'"); ?>
                        <?php $modalField('Kategori', "detailAnak.kategori || '-'"); ?>
                        <?php $modalField('Tanggal Masuk', 'detailAnak.tanggal_masuk_id'); ?>
                        <?php $modalField('Provinsi', "detailAnak.nama_provinsi || '-'"); ?>
                        <?php $modalField('Kabupaten / Kota', "detailAnak.nama_kabupaten || '-'"); ?>
                    </div>
                </section>

                <!-- Orang Tua & Wali -->
                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h4 class="flex items-center gap-2 text-sm font-bold text-slate-900"><i class="fa-solid fa-people-roof text-indigo-500"></i> Orang Tua &amp; Wali</h4>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                        <?php $modalField('Ayah Kandung', "detailAnak.nama_ayah_kandung || '-'"); ?>
                        <?php $modalField('Ayah Tiri', "detailAnak.nama_ayah_tiri || '-'"); ?>
                        <?php $modalField('Ibu Kandung', "detailAnak.nama_ibu_kandung || '-'"); ?>
                        <?php $modalField('Ibu Tiri', "detailAnak.nama_ibu_tiri || '-'"); ?>
                        <?php $modalField('Pekerjaan Ayah', "detailAnak.pekerjaan_ayah || '-'"); ?>
                        <?php $modalField('Pekerjaan Ibu', "detailAnak.pekerjaan_ibu || '-'"); ?>
                        <?php $modalField('No. Telp Orang Tua', "detailAnak.no_telp_orang_tua || '-'"); ?>
                        <?php $modalField('Nama Wali', "detailAnak.nama_wali || '-'"); ?>
                        <?php $modalField('No. Telp Wali', "detailAnak.no_telp_wali || '-'"); ?>
                    </div>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <?php $modalField('Alamat Orang Tua', "detailAnak.alamat_orang_tua || '-'"); ?>
                        <?php $modalField('Alamat Wali', "detailAnak.alamat_wali || '-'"); ?>
                    </div>
                </section>

                <!-- Pendidikan -->
                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h4 class="flex items-center gap-2 text-sm font-bold text-slate-900"><i class="fa-solid fa-graduation-cap text-indigo-500"></i> Pendidikan</h4>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                        <?php $modalField('Jenjang', "detailAnak.pendidikan || '-'"); ?>
                        <?php $modalField('Kelas', "detailAnak.kelas || '-'"); ?>
                        <?php $modalField('Nama Sekolah', "detailAnak.nama_sekolah || '-'"); ?>
                        <?php $modalField('No. Telp Sekolah', "detailAnak.no_telp_sekolah || '-'"); ?>
                        <?php $modalField('Biaya SPP', 'detailAnak.spp_rupiah'); ?>
                        <?php $modalField('Alamat Sekolah', "detailAnak.alamat_sekolah || '-'"); ?>
                    </div>
                </section>

                <!-- Berkas -->
                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h4 class="flex items-center gap-2 text-sm font-bold text-slate-900"><i class="fa-solid fa-folder-open text-indigo-500"></i> Berkas Pendukung</h4>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        <!-- Foto -->
                        <div class="rounded-2xl border border-slate-200 p-3">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Foto</p>
                            <img x-show="detailAnak.foto_url" :src="detailAnak.foto_url" class="mt-2 h-32 w-full rounded-xl object-cover ring-1 ring-slate-200">
                            <div x-show="!detailAnak.foto_url" class="mt-2 flex h-32 w-full items-center justify-center rounded-xl bg-slate-100 text-center text-xs text-slate-400"><i class="fa-solid fa-camera block text-xl"></i><span class="mt-1 block">Belum ada</span></div>
                            <a x-show="detailAnak.view_foto" :href="detailAnak.view_foto" target="_blank" class="mt-2 inline-flex items-center gap-1 text-[11px] font-semibold text-indigo-600 hover:text-indigo-700"><i class="fa-solid fa-up-right-from-square"></i> Lihat</a>
                        </div>

                        <!-- KK -->
                        <div class="rounded-2xl border border-slate-200 p-3">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Kartu Keluarga</p>
                            <div class="mt-2 flex h-32 w-full flex-col items-center justify-center rounded-xl bg-slate-50 text-center">
                                <i class="fa-solid fa-file-lines text-2xl" :class="detailAnak.file_kk ? 'text-emerald-500' : 'text-slate-300'"></i>
                                <p class="mt-1 text-xs font-semibold" :class="detailAnak.file_kk ? 'text-emerald-600' : 'text-slate-400'" x-text="detailAnak.file_kk ? 'Tersedia' : 'Belum ada'"></p>
                            </div>
                            <a x-show="detailAnak.view_kk" :href="detailAnak.view_kk" target="_blank" class="mt-2 inline-flex items-center gap-1 text-[11px] font-semibold text-indigo-600 hover:text-indigo-700"><i class="fa-solid fa-up-right-from-square"></i> Lihat</a>
                        </div>

                        <!-- Akta -->
                        <div class="rounded-2xl border border-slate-200 p-3">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Akta Lahir</p>
                            <div class="mt-2 flex h-32 w-full flex-col items-center justify-center rounded-xl bg-slate-50 text-center">
                                <i class="fa-solid fa-file-shield text-2xl" :class="detailAnak.file_akta ? 'text-emerald-500' : 'text-slate-300'"></i>
                                <p class="mt-1 text-xs font-semibold" :class="detailAnak.file_akta ? 'text-emerald-600' : 'text-slate-400'" x-text="detailAnak.file_akta ? 'Tersedia' : 'Belum ada'"></p>
                            </div>
                            <a x-show="detailAnak.view_akta" :href="detailAnak.view_akta" target="_blank" class="mt-2 inline-flex items-center gap-1 text-[11px] font-semibold text-indigo-600 hover:text-indigo-700"><i class="fa-solid fa-up-right-from-square"></i> Lihat</a>
                        </div>

                        <!-- Pendukung -->
                        <div class="rounded-2xl border border-slate-200 p-3">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Dokumen Pendukung</p>
                            <div class="mt-2 flex h-32 w-full flex-col items-center justify-center rounded-xl bg-slate-50 text-center">
                                <i class="fa-solid fa-paperclip text-2xl" :class="detailAnak.file_pendukung ? 'text-emerald-500' : 'text-slate-300'"></i>
                                <p class="mt-1 text-xs font-semibold" :class="detailAnak.file_pendukung ? 'text-emerald-600' : 'text-slate-400'" x-text="detailAnak.file_pendukung ? 'Tersedia' : 'Belum ada'"></p>
                            </div>
                            <a x-show="detailAnak.view_pendukung" :href="detailAnak.view_pendukung" target="_blank" class="mt-2 inline-flex items-center gap-1 text-[11px] font-semibold text-indigo-600 hover:text-indigo-700"><i class="fa-solid fa-up-right-from-square"></i> Lihat</a>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <!-- Modal Export -->
    <div x-cloak x-show="exportOpen" @keydown.escape.window="exportOpen = false" class="fixed inset-0 z-50 overflow-y-auto p-4 sm:p-6" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="exportOpen = false"></div>
        <div class="relative mx-auto w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-slate-200">
            <form method="post" action="<?= site_url('admin/anak/export') ?>" class="flex max-h-[90vh] flex-col">
                <?= csrf_field() ?>
                <input type="hidden" name="status_mode" value="<?= esc($statusMode) ?>">
                <input type="hidden" name="q" value="<?= esc($filters['search'] ?? '') ?>">
                <input type="hidden" name="jenis_kelamin" value="<?= esc($filters['jenis_kelamin'] ?? '') ?>">
                <input type="hidden" name="pendidikan" value="<?= esc($filters['pendidikan'] ?? '') ?>">
                <input type="hidden" name="kategori" value="<?= esc($filters['kategori'] ?? '') ?>">

                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Export Data <?= esc($statusLabel) ?></h3>
                        <p class="text-xs text-slate-500">Pilih kolom dan format hasil export.</p>
                    </div>
                    <button type="button" @click="exportOpen = false" class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"><i class="fa-solid fa-xmark"></i></button>
                </div>

                <div class="flex-1 overflow-y-auto p-5">
                    <p class="mb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400">Kolom yang Diexport</p>
                    <div class="grid gap-2 sm:grid-cols-2">
                        <?php foreach ($exportColumns as $key => $label): ?>
                            <label class="flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-700">
                                <input type="checkbox" name="columns[]" value="<?= esc($key) ?>" checked class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <?= esc($label) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <p class="mb-2 mt-4 text-[11px] font-semibold uppercase tracking-wider text-slate-400">Format Hasil</p>
                    <div class="flex gap-3">
                        <label class="flex flex-1 cursor-pointer items-center justify-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm font-semibold text-slate-700">
                            <input type="radio" name="format" value="excel" checked class="h-4 w-4 border-slate-300 text-indigo-600 focus:ring-indigo-500"><i class="fa-solid fa-file-excel text-emerald-600"></i> Excel
                        </label>
                        <label class="flex flex-1 cursor-pointer items-center justify-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm font-semibold text-slate-700">
                            <input type="radio" name="format" value="pdf" class="h-4 w-4 border-slate-300 text-indigo-600 focus:ring-indigo-500"><i class="fa-solid fa-file-pdf text-rose-500"></i> PDF
                        </label>
                    </div>
                </div>

                <div class="flex justify-end gap-2 border-t border-slate-100 bg-slate-50 px-5 py-4">
                    <button type="button" @click="exportOpen = false" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-50">Batal</button>
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-xs font-bold text-white transition hover:bg-indigo-700"><i class="fa-solid fa-file-export"></i> Export</button>
                </div>
            </form>
        </div>
    </div>
</div>
