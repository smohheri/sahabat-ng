<?php
$inputClass = 'w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20';
$labelClass = 'mb-1 block text-[11px] font-semibold uppercase tracking-wider text-slate-400';
$semesterLabel = static fn (int $s): string => $s === 2 ? 'Genap' : 'Ganjil';
?>

<div class="space-y-5" x-data="{ addOpen: false }">
    <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
        <div>
            <h2 class="text-lg font-extrabold text-slate-900">Rombongan Belajar</h2>
            <p class="text-sm text-slate-500"><?= number_format((int) ($total ?? count($items)), 0, ',', '.') ?> rombel ditemukan<?= $isTeacher ? ' untuk mata pelajaran yang Anda ampu.' : '.' ?></p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="<?= site_url($prefix . '/export/rombel/excel') ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50"><i class="fa-solid fa-file-excel text-emerald-600"></i> Excel</a>
            <a href="<?= site_url($prefix . '/export/rombel/pdf') ?>" target="_blank" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50"><i class="fa-solid fa-file-pdf text-rose-500"></i> PDF</a>
            <?php if (! $isTeacher): ?>
                <button type="button" @click="addOpen = !addOpen" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-bold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700"><i class="fa-solid fa-plus"></i> Tambah Rombel</button>
            <?php endif; ?>
        </div>
    </div>

    <?php if (! $isTeacher): ?>
        <div x-cloak x-show="addOpen" x-transition class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <form method="post" action="<?= site_url($prefix . '/rombel/store') ?>" class="space-y-4">
                <?= csrf_field() ?>
                <div class="grid gap-3 md:grid-cols-5">
                    <div>
                        <label class="<?= $labelClass ?>">Kode</label>
                        <input type="text" name="kode_rombel" required class="<?= $inputClass ?>" placeholder="R-01">
                    </div>
                    <div class="md:col-span-2">
                        <label class="<?= $labelClass ?>">Nama Rombel</label>
                        <input type="text" name="nama_rombel" required class="<?= $inputClass ?>" placeholder="Kelas VII A">
                    </div>
                    <div>
                        <label class="<?= $labelClass ?>">Tahun Ajaran</label>
                        <input type="text" name="tahun_ajaran" value="<?= esc(date('n') >= 7 ? date('Y') . '/' . (date('Y') + 1) : (date('Y') - 1) . '/' . date('Y')) ?>" class="<?= $inputClass ?>">
                    </div>
                    <div>
                        <label class="<?= $labelClass ?>">Semester</label>
                        <select name="semester" class="<?= $inputClass ?>">
                            <option value="1">Ganjil</option>
                            <option value="2">Genap</option>
                        </select>
                    </div>
                    <div class="md:col-span-4">
                        <label class="<?= $labelClass ?>">Keterangan</label>
                        <input type="text" name="keterangan" class="<?= $inputClass ?>">
                    </div>
                    <label class="mt-6 inline-flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="is_active" value="1" checked class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"> Aktif
                    </label>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="<?= $labelClass ?>">Anggota Rombel</label>
                        <div class="max-h-56 space-y-1.5 overflow-y-auto rounded-xl border border-slate-200 bg-slate-50 p-3">
                            <?php foreach ($anakList as $anak): ?>
                                <label class="flex items-center gap-2 rounded-lg px-2 py-1 text-xs text-slate-700 hover:bg-white">
                                    <input type="checkbox" name="anak_ids[]" value="<?= (int) $anak['id_anak'] ?>" class="h-3.5 w-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="font-medium"><?= esc($anak['nama_anak']) ?></span>
                                    <span class="text-[10px] text-slate-400"><?= esc($anak['pendidikan'] ?: '-') ?></span>
                                </label>
                            <?php endforeach; ?>
                            <?php if ($anakList === []): ?><p class="text-xs text-slate-400">Belum ada data anak.</p><?php endif; ?>
                        </div>
                    </div>
                    <div>
                        <label class="<?= $labelClass ?>">Mata Pelajaran</label>
                        <div class="max-h-56 space-y-1.5 overflow-y-auto rounded-xl border border-slate-200 bg-slate-50 p-3">
                            <?php foreach ($mapelList as $id => $nama): ?>
                                <label class="flex items-center gap-2 rounded-lg px-2 py-1 text-xs text-slate-700 hover:bg-white">
                                    <input type="checkbox" name="mapel_ids[]" value="<?= (int) $id ?>" class="h-3.5 w-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="font-medium"><?= esc($nama) ?></span>
                                </label>
                            <?php endforeach; ?>
                            <?php if ($mapelList === []): ?><p class="text-xs text-slate-400">Belum ada mata pelajaran aktif.</p><?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" @click="addOpen = false" class="rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-50">Batal</button>
                    <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-xs font-bold text-white transition hover:bg-indigo-700"><i class="fa-solid fa-floppy-disk mr-1"></i> Simpan Rombel</button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <form method="get" action="<?= site_url($prefix . '/rombel') ?>" class="grid gap-3 md:grid-cols-4">
            <div class="relative md:col-span-2">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i class="fa-solid fa-magnifying-glass text-xs"></i></span>
                <input type="text" name="q" value="<?= esc($filters['search'] ?? '') ?>" placeholder="Cari kode, nama, atau tahun ajaran..."
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-9 pr-3 text-sm outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20">
            </div>
            <select name="tahun_ajaran" class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20">
                <option value="">Semua tahun</option>
                <?php foreach ($tahunOptions as $tahun): ?>
                    <option value="<?= esc($tahun) ?>" <?= ($filters['tahun_ajaran'] ?? '') === $tahun ? 'selected' : '' ?>><?= esc($tahun) ?></option>
                <?php endforeach; ?>
            </select>
            <div class="flex gap-2">
                <select name="semester" class="flex-1 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20">
                    <option value="">Semua</option>
                    <option value="1" <?= (string) ($filters['semester'] ?? '') === '1' ? 'selected' : '' ?>>Ganjil</option>
                    <option value="2" <?= (string) ($filters['semester'] ?? '') === '2' ? 'selected' : '' ?>>Genap</option>
                </select>
                <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-slate-800"><i class="fa-solid fa-filter"></i></button>
                <a href="<?= site_url($prefix . '/rombel') ?>" class="rounded-xl border border-slate-200 px-3 py-2.5 text-xs font-semibold text-slate-500 transition hover:bg-slate-50"><i class="fa-solid fa-rotate-left"></i></a>
            </div>
        </form>
    </div>

    <?php if ($items === []): ?>
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
            <i class="fa-solid fa-users-rectangle mb-3 text-3xl text-slate-300"></i>
            <p class="text-sm text-slate-500">Belum ada rombongan belajar.</p>
        </div>
    <?php endif; ?>

    <div class="grid gap-5 xl:grid-cols-2">
        <?php foreach ($items as $item): ?>
            <?php
            $rel = $relations[(int) $item['id_rombel']] ?? ['anak' => [], 'mapel' => []];
            $childIds = array_map(static fn ($c) => (int) $c['id_anak'], $rel['anak']);
            $mapelIds = array_map(static fn ($m) => (int) $m['id_mata_pelajaran'], $rel['mapel']);
            ?>
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm" x-data="{ edit: false }">
                <div class="border-b border-slate-100 bg-slate-50/60 px-5 py-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex rounded-md bg-slate-900 px-2 py-0.5 font-mono text-[10px] font-bold text-white"><?= esc($item['kode_rombel']) ?></span>
                                <p class="truncate text-sm font-bold text-slate-900"><?= esc($item['nama_rombel']) ?></p>
                            </div>
                            <p class="mt-1 text-[11px] text-slate-500"><?= esc($item['tahun_ajaran']) ?> &middot; Semester <?= esc($semesterLabel((int) $item['semester'])) ?> &middot; <?= count($rel['anak']) ?> anak &middot; <?= count($rel['mapel']) ?> mapel</p>
                        </div>
                        <span class="inline-flex shrink-0 rounded-full px-2.5 py-1 text-[10px] font-semibold <?= (int) $item['is_active'] === 1 ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' ?>"><?= (int) $item['is_active'] === 1 ? 'Aktif' : 'Nonaktif' ?></span>
                    </div>
                    <?php if (! empty($item['keterangan'])): ?>
                        <p class="mt-2 rounded-lg bg-white px-3 py-2 text-[11px] text-slate-500"><?= esc($item['keterangan']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="space-y-4 p-5">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Anggota (<?= count($rel['anak']) ?>)</p>
                        <div class="mt-2 flex flex-wrap gap-1.5">
                            <?php foreach ($rel['anak'] as $child): ?>
                                <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 px-2.5 py-1 text-[10px] font-semibold text-indigo-700"><i class="fa-solid fa-child"></i> <?= esc($child['nama_anak']) ?></span>
                            <?php endforeach; ?>
                            <?php if ($rel['anak'] === []): ?><span class="text-[11px] text-slate-400">Belum ada anggota.</span><?php endif; ?>
                        </div>
                    </div>

                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Mata Pelajaran (<?= count($rel['mapel']) ?>)</p>
                        <div class="mt-2 flex flex-wrap gap-1.5">
                            <?php foreach ($rel['mapel'] as $mapel): ?>
                                <span class="inline-flex items-center gap-1 rounded-full bg-sky-50 px-2.5 py-1 text-[10px] font-semibold text-sky-700"><i class="fa-solid fa-book"></i> <?= esc($mapel['nama_mapel']) ?></span>
                            <?php endforeach; ?>
                            <?php if ($rel['mapel'] === []): ?><span class="text-[11px] text-slate-400">Belum ada mapel.</span><?php endif; ?>
                        </div>
                    </div>

                    <?php if (! $isTeacher): ?>
                        <div class="flex items-center justify-end gap-2 border-t border-slate-100 pt-4">
                            <button type="button" @click="edit = !edit" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 py-2 text-[11px] font-semibold text-slate-600 transition hover:bg-indigo-50 hover:text-indigo-600"><i class="fa-solid fa-pen-to-square"></i> Edit</button>
                            <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
                            <form method="post" action="<?= site_url($prefix . '/rombel/delete/' . $item['id_rombel']) ?>" data-confirm="Hapus rombel <?= esc($item['nama_rombel'], 'attr') ?>?">
                                <?= csrf_field() ?>
                                <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 py-2 text-[11px] font-semibold text-rose-600 transition hover:bg-rose-50"><i class="fa-solid fa-trash"></i> Hapus</button>
                            </form>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (! $isTeacher): ?>
                    <form x-cloak x-show="edit" x-transition method="post" action="<?= site_url($prefix . '/rombel/update/' . $item['id_rombel']) ?>" class="space-y-4 border-t border-slate-100 bg-indigo-50/30 p-5">
                        <?= csrf_field() ?>
                        <div class="grid gap-3 md:grid-cols-5">
                            <div>
                                <label class="<?= $labelClass ?>">Kode</label>
                                <input type="text" name="kode_rombel" value="<?= esc($item['kode_rombel']) ?>" required class="<?= $inputClass ?>">
                            </div>
                            <div class="md:col-span-2">
                                <label class="<?= $labelClass ?>">Nama Rombel</label>
                                <input type="text" name="nama_rombel" value="<?= esc($item['nama_rombel']) ?>" required class="<?= $inputClass ?>">
                            </div>
                            <div>
                                <label class="<?= $labelClass ?>">Tahun Ajaran</label>
                                <input type="text" name="tahun_ajaran" value="<?= esc($item['tahun_ajaran']) ?>" class="<?= $inputClass ?>">
                            </div>
                            <div>
                                <label class="<?= $labelClass ?>">Semester</label>
                                <select name="semester" class="<?= $inputClass ?>">
                                    <option value="1" <?= (int) $item['semester'] === 1 ? 'selected' : '' ?>>Ganjil</option>
                                    <option value="2" <?= (int) $item['semester'] === 2 ? 'selected' : '' ?>>Genap</option>
                                </select>
                            </div>
                            <div class="md:col-span-4">
                                <label class="<?= $labelClass ?>">Keterangan</label>
                                <input type="text" name="keterangan" value="<?= esc($item['keterangan']) ?>" class="<?= $inputClass ?>">
                            </div>
                            <label class="mt-6 inline-flex items-center gap-2 text-sm text-slate-700">
                                <input type="checkbox" name="is_active" value="1" <?= (int) $item['is_active'] === 1 ? 'checked' : '' ?> class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"> Aktif
                            </label>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="<?= $labelClass ?>">Anggota Rombel</label>
                                <div class="max-h-48 space-y-1.5 overflow-y-auto rounded-xl border border-slate-200 bg-white p-3">
                                    <?php foreach ($anakList as $anak): ?>
                                        <label class="flex items-center gap-2 rounded-lg px-2 py-1 text-xs text-slate-700 hover:bg-slate-50">
                                            <input type="checkbox" name="anak_ids[]" value="<?= (int) $anak['id_anak'] ?>" <?= in_array((int) $anak['id_anak'], $childIds, true) ? 'checked' : '' ?> class="h-3.5 w-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                            <span class="font-medium"><?= esc($anak['nama_anak']) ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div>
                                <label class="<?= $labelClass ?>">Mata Pelajaran</label>
                                <div class="max-h-48 space-y-1.5 overflow-y-auto rounded-xl border border-slate-200 bg-white p-3">
                                    <?php foreach ($mapelList as $id => $nama): ?>
                                        <label class="flex items-center gap-2 rounded-lg px-2 py-1 text-xs text-slate-700 hover:bg-slate-50">
                                            <input type="checkbox" name="mapel_ids[]" value="<?= (int) $id ?>" <?= in_array((int) $id, $mapelIds, true) ? 'checked' : '' ?> class="h-3.5 w-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                            <span class="font-medium"><?= esc($nama) ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-xs font-bold text-white transition hover:bg-indigo-700"><i class="fa-solid fa-floppy-disk mr-1"></i> Simpan Perubahan</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <?= view('partials/pagination', ['page' => $page ?? 1, 'perPage' => $perPage ?? 8, 'total' => $total ?? 0]) ?>
</div>
