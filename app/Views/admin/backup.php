<?php
$formatSize = static function (int $bytes): string {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    }
    if ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    }
    if ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    }

    return $bytes . ' B';
};
?>

<div class="space-y-5" x-data="{ restore: false }">
    <div>
        <h2 class="text-lg font-extrabold text-slate-900">Backup &amp; Restore</h2>
        <p class="text-sm text-slate-500">Amankan data aplikasi dan berkas unggahan secara berkala.</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-bold text-slate-900">Backup Database</p>
                    <p class="mt-1 text-xs text-slate-500">Menyimpan seluruh tabel ke file <code class="rounded bg-slate-100 px-1">.sql</code>.</p>
                </div>
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600"><i class="fa-solid fa-database"></i></span>
            </div>
            <form method="post" action="<?= site_url('admin/backup_database') ?>" class="mt-4">
                <?= csrf_field() ?>
                <button type="submit" class="w-full rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-indigo-700"><i class="fa-solid fa-download mr-1"></i> Buat Backup Database</button>
            </form>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-bold text-slate-900">Backup Berkas Upload</p>
                    <p class="mt-1 text-xs text-slate-500">Mengemas folder <code class="rounded bg-slate-100 px-1">uploads</code> menjadi ZIP.</p>
                </div>
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600"><i class="fa-solid fa-file-zipper"></i></span>
            </div>
            <form method="post" action="<?= site_url('admin/backup_files') ?>" class="mt-4">
                <?= csrf_field() ?>
                <button type="submit" class="w-full rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-emerald-700"><i class="fa-solid fa-download mr-1"></i> Buat Backup Berkas</button>
            </form>
        </div>
    </div>

    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
        <div class="flex items-start gap-3">
            <i class="fa-solid fa-triangle-exclamation mt-0.5 text-amber-500"></i>
            <div class="flex-1">
                <p class="text-sm font-bold text-amber-800">Pemulihan Data (Restore)</p>
                <p class="mt-1 text-xs text-amber-700">Restore akan menimpa data yang ada. Pastikan Anda sudah membuat backup terbaru sebelum melanjutkan.</p>
                <button type="button" @click="restore = !restore" class="mt-3 inline-flex items-center gap-2 rounded-xl bg-amber-600 px-4 py-2 text-xs font-bold text-white transition hover:bg-amber-700">
                    <i class="fa-solid fa-rotate-left"></i> <span x-text="restore ? 'Tutup Form Restore' : 'Buka Form Restore'"></span>
                </button>
            </div>
        </div>

        <div x-cloak x-show="restore" x-transition class="mt-4 grid gap-4 border-t border-amber-200 pt-4 md:grid-cols-2">
            <form method="post" action="<?= site_url('admin/restore_database') ?>" enctype="multipart/form-data" class="rounded-xl border border-amber-200 bg-white p-4" data-confirm="Restore database akan menimpa data saat ini. Lanjutkan?">
                <?= csrf_field() ?>
                <p class="text-xs font-bold text-slate-700">Restore Database (.sql)</p>
                <input type="file" name="sql_file" accept=".sql" required class="mt-2 block w-full text-xs text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-amber-600 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white">
                <button type="submit" class="mt-3 w-full rounded-lg bg-amber-600 px-3 py-2 text-xs font-bold text-white transition hover:bg-amber-700">Restore Database</button>
            </form>

            <form method="post" action="<?= site_url('admin/restore_files') ?>" enctype="multipart/form-data" class="rounded-xl border border-amber-200 bg-white p-4" data-confirm="Restore berkas akan menimpa file upload saat ini. Lanjutkan?">
                <?= csrf_field() ?>
                <p class="text-xs font-bold text-slate-700">Restore Berkas (.zip)</p>
                <input type="file" name="zip_file" accept=".zip" required class="mt-2 block w-full text-xs text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-amber-600 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white">
                <button type="submit" class="mt-3 w-full rounded-lg bg-amber-600 px-3 py-2 text-xs font-bold text-white transition hover:bg-amber-700">Restore Berkas</button>
            </form>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <h3 class="text-sm font-bold text-slate-900">Riwayat Backup</h3>
            <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-600"><?= number_format((int) ($total ?? count($files)), 0, ',', '.') ?> file</span>
        </div>

        <?php if ($files === []): ?>
            <div class="px-5 py-12 text-center">
                <i class="fa-solid fa-box-archive mb-3 text-3xl text-slate-300"></i>
                <p class="text-sm text-slate-500">Belum ada file backup.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Nama File</th>
                            <th class="px-4 py-3 font-semibold">Tipe</th>
                            <th class="px-4 py-3 font-semibold">Ukuran</th>
                            <th class="px-4 py-3 font-semibold">Dibuat</th>
                            <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($files as $file): ?>
                            <tr class="transition hover:bg-slate-50/70">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid <?= $file['type'] === 'files' ? 'fa-file-zipper text-emerald-500' : 'fa-database text-indigo-500' ?>"></i>
                                        <span class="font-mono text-xs text-slate-700"><?= esc($file['name']) ?></span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold <?= $file['type'] === 'files' ? 'bg-emerald-100 text-emerald-700' : 'bg-indigo-100 text-indigo-700' ?>">
                                        <?= $file['type'] === 'files' ? 'Berkas' : 'Database' ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-600"><?= $formatSize((int) $file['size']) ?></td>
                                <td class="px-4 py-3 text-[11px] text-slate-500"><?= tanggal_id(date('Y-m-d H:i:s', (int) $file['modified']), true) ?></td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="<?= site_url('admin/download_backup/' . $file['type'] . '/' . $file['name']) ?>" class="rounded-lg border border-slate-200 p-2 text-slate-500 transition hover:bg-indigo-50 hover:text-indigo-600" title="Unduh">
                                            <i class="fa-solid fa-download text-xs"></i>
                                        </a>
                                        <form method="post" action="<?= site_url('admin/delete_backup') ?>" data-confirm="Hapus file backup <?= esc($file['name'], 'attr') ?>?">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="filename" value="<?= esc($file['name']) ?>">
                                            <button type="submit" class="rounded-lg border border-slate-200 p-2 text-slate-500 transition hover:bg-rose-50 hover:text-rose-600" title="Hapus">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="px-5 pb-4">
                <?= view('partials/pagination', ['page' => $page ?? 1, 'perPage' => $perPage ?? 10, 'total' => $total ?? 0]) ?>
            </div>
        <?php endif; ?>
    </div>
</div>
