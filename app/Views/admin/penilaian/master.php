<?php
$inputClass = 'input';
$labelClass = 'label';
$toneByScore = [1 => 'rose', 2 => 'amber', 3 => 'sky', 4 => 'emerald'];
?>

<div class="space-y-6" x-data="{ tab: 'skala', newAspect: false }">
    <div class="page-header">
        <div>
            <h2 class="page-title">Master Penilaian Karakter</h2>
            <p class="page-subtitle">Atur skala penilaian, aspek, dan indikator yang digunakan pengajar.</p>
        </div>
        <div class="page-actions">
            <span class="inline-flex items-center gap-2 rounded-xl bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700"><i class="fa-solid fa-layer-group"></i> <?= count($aspects) ?> aspek</span>
            <span class="inline-flex items-center gap-2 rounded-xl bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-600"><i class="fa-solid fa-list-check"></i> <?= array_sum(array_map(static fn ($a) => count($a['indicators']), $aspects)) ?> indikator</span>
        </div>
    </div>

    <div class="page-actions">
        <button type="button" @click="tab = 'skala'" :class="tab === 'skala' ? 'border-emerald-600 bg-emerald-700 text-white' : 'border-stone-200 bg-white text-stone-600 hover:bg-stone-50'" class="inline-flex items-center gap-2 rounded-xl border px-4 py-2.5 text-xs font-semibold transition">
            <i class="fa-solid fa-scale-balanced"></i> Skala Penilaian
        </button>
        <button type="button" @click="tab = 'aspek'" :class="tab === 'aspek' ? 'border-emerald-600 bg-emerald-700 text-white' : 'border-stone-200 bg-white text-stone-600 hover:bg-stone-50'" class="inline-flex items-center gap-2 rounded-xl border px-4 py-2.5 text-xs font-semibold transition">
            <i class="fa-solid fa-layer-group"></i> Aspek &amp; Indikator
        </button>
    </div>

    <!-- SKALA -->
    <div x-show="tab === 'skala'" class="space-y-5">
        <div class="card">
            <div class="card-header">
                <h3 class="section-title">Skala Penilaian</h3>
                <p class="text-xs text-stone-500">Skor 1-4 beserta kategori dan deskripsinya.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 font-semibold">Skor</th>
                            <th class="px-4 py-3 font-semibold">Kategori</th>
                            <th class="px-4 py-3 font-semibold">Deskripsi</th>
                            <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        <?php if ($scales === []): ?>
                            <tr><td colspan="4" class="px-4 py-10 text-center text-sm text-stone-400">Belum ada skala. Tambahkan skor 1-4.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($scales as $scale): ?>
                            <tr x-data="{ edit: false }" class="transition hover:bg-stone-50/70">
                                <td class="px-4 py-3">
                                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-<?= $toneByScore[(int) $scale['score']] ?? 'stone' ?>-100 text-sm font-extrabold text-<?= $toneByScore[(int) $scale['score']] ?? 'stone' ?>-700"><?= (int) $scale['score'] ?></span>
                                </td>
                                <td class="px-4 py-3 font-semibold text-stone-800"><?= esc($scale['category']) ?></td>
                                <td class="px-4 py-3 text-stone-600"><?= esc($scale['description'] ?: '-') ?></td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <button type="button" @click="edit = !edit" class="btn-icon" title="Edit"><i class="fa-solid fa-pen-to-square text-xs"></i></button>
                                        <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
                                        <form method="post" action="<?= site_url('admin/penilaian-karakter/scale/delete/' . $scale['id_scale']) ?>" data-confirm="Hapus skala <?= esc($scale['category'], 'attr') ?>?">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn-icon btn-icon-danger" title="Hapus"><i class="fa-solid fa-trash text-xs"></i></button>
                                        </form>
                                        <?php endif; ?>
                                    </div>

                                    <form x-cloak x-show="edit" x-transition method="post" action="<?= site_url('admin/penilaian-karakter/scale/update/' . $scale['id_scale']) ?>" class="mt-3 grid gap-2 rounded-xl bg-stone-50 p-3 sm:grid-cols-4">
                                        <?= csrf_field() ?>
                                        <input type="number" name="score" min="1" max="10" value="<?= (int) $scale['score'] ?>" required class="<?= $inputClass ?>">
                                        <input type="text" name="category" value="<?= esc($scale['category']) ?>" required class="<?= $inputClass ?>">
                                        <input type="text" name="description" value="<?= esc($scale['description']) ?>" class="<?= $inputClass ?> sm:col-span-2">
                                        <button type="submit" class="btn-primary sm:col-span-4"><i class="fa-solid fa-floppy-disk mr-1"></i> Simpan Perubahan</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card p-5">
            <h3 class="section-title"><i class="fa-solid fa-plus text-emerald-600"></i> Tambah Skala</h3>
            <form method="post" action="<?= site_url('admin/penilaian-karakter/scale/store') ?>" class="mt-4 grid gap-3 sm:grid-cols-4">
                <?= csrf_field() ?>
                <div>
                    <label class="<?= $labelClass ?>">Skor</label>
                    <input type="number" name="score" min="1" max="10" required class="<?= $inputClass ?>" placeholder="1-4">
                </div>
                <div>
                    <label class="<?= $labelClass ?>">Kategori</label>
                    <input type="text" name="category" required class="<?= $inputClass ?>" placeholder="Contoh: Baik">
                </div>
                <div class="sm:col-span-2">
                    <label class="<?= $labelClass ?>">Deskripsi</label>
                    <input type="text" name="description" class="<?= $inputClass ?>" placeholder="Penjelasan singkat kategori">
                </div>
                <div class="sm:col-span-4">
                    <button type="submit" class="btn-primary"><i class="fa-solid fa-plus mr-1"></i> Tambah Skala</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ASPEK -->
    <div x-show="tab === 'aspek'" class="space-y-5">
        <div class="flex justify-end">
            <button type="button" @click="newAspect = !newAspect" class="btn-primary">
                <i class="fa-solid fa-plus"></i> Tambah Aspek
            </button>
        </div>

        <div x-cloak x-show="newAspect" x-transition class="card p-5">
            <form method="post" action="<?= site_url('admin/penilaian-karakter/aspect/store') ?>" class="grid gap-3 md:grid-cols-4">
                <?= csrf_field() ?>
                <div class="md:col-span-2">
                    <label class="<?= $labelClass ?>">Nama Aspek</label>
                    <input type="text" name="aspect_name" required class="<?= $inputClass ?>" placeholder="Contoh: Kedisiplinan">
                </div>
                <div>
                    <label class="<?= $labelClass ?>">Kode</label>
                    <input type="text" name="aspect_code" required class="<?= $inputClass ?>" placeholder="DISC">
                </div>
                <div>
                    <label class="<?= $labelClass ?>">Urutan</label>
                    <input type="number" name="sort_order" value="0" min="0" class="<?= $inputClass ?>">
                </div>
                <div class="md:col-span-4">
                    <label class="<?= $labelClass ?>">Deskripsi</label>
                    <textarea name="description" rows="2" class="<?= $inputClass ?>"></textarea>
                </div>
                <div class="flex justify-end gap-2 md:col-span-4">
                    <button type="button" @click="newAspect = false" class="rounded-xl border border-stone-200 px-4 py-2.5 text-xs font-semibold text-stone-600 transition hover:bg-stone-50">Batal</button>
                    <button type="submit" class="btn-primary"><i class="fa-solid fa-floppy-disk mr-1"></i> Simpan Aspek</button>
                </div>
            </form>
        </div>

        <?php if ($aspects === []): ?>
            <div class="rounded-2xl border border-dashed border-stone-300 bg-white px-6 py-14 text-center">
                <i class="fa-solid fa-layer-group mb-3 text-3xl text-stone-300"></i>
                <p class="page-subtitle">Belum ada aspek penilaian.</p>
            </div>
        <?php endif; ?>

        <?php foreach ($aspects as $aspect): ?>
            <div class="card overflow-hidden" x-data="{ open: true, editAspect: false, newIndicator: false }">
                <div class="flex flex-col gap-3 border-b border-stone-100 bg-stone-50/60 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <button type="button" @click="open = !open" class="flex h-9 w-9 items-center justify-center rounded-lg bg-white text-stone-500 shadow-sm transition hover:text-emerald-700">
                            <i class="fa-solid fa-chevron-down text-xs transition" :class="open ? '' : '-rotate-90'"></i>
                        </button>
                        <div>
                            <p class="text-sm font-bold text-stone-900"><?= esc($aspect['aspect_name']) ?></p>
                            <p class="text-xs font-semibold text-stone-500">Kode: <?= esc($aspect['aspect_code']) ?> &middot; <?= count($aspect['indicators']) ?> indikator &middot; urutan <?= (int) $aspect['sort_order'] ?></p>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" @click="newIndicator = !newIndicator" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-700 px-3 py-2 text-xs font-bold text-white transition hover:bg-emerald-800"><i class="fa-solid fa-plus"></i> Indikator</button>
                        <button type="button" @click="editAspect = !editAspect" class="inline-flex items-center gap-1.5 rounded-lg border border-stone-200 bg-white px-3 py-2 text-xs font-semibold text-stone-600 transition hover:bg-stone-50"><i class="fa-solid fa-pen-to-square"></i> Edit</button>
                        <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
                        <form method="post" action="<?= site_url('admin/penilaian-karakter/aspect/delete/' . $aspect['id_aspect']) ?>" data-confirm="Hapus aspek <?= esc($aspect['aspect_name'], 'attr') ?> beserta indikatornya?">
                            <?= csrf_field() ?>
                            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg border border-stone-200 bg-white px-3 py-2 text-xs font-semibold text-rose-600 transition hover:bg-rose-50"><i class="fa-solid fa-trash"></i> Hapus</button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>

                <div x-cloak x-show="editAspect" x-transition class="border-b border-stone-100 bg-emerald-50/40 p-5">
                    <form method="post" action="<?= site_url('admin/penilaian-karakter/aspect/update/' . $aspect['id_aspect']) ?>" class="grid gap-3 md:grid-cols-4">
                        <?= csrf_field() ?>
                        <div class="md:col-span-2">
                            <label class="<?= $labelClass ?>">Nama Aspek</label>
                            <input type="text" name="aspect_name" value="<?= esc($aspect['aspect_name']) ?>" required class="<?= $inputClass ?>">
                        </div>
                        <div>
                            <label class="<?= $labelClass ?>">Kode</label>
                            <input type="text" name="aspect_code" value="<?= esc($aspect['aspect_code']) ?>" required class="<?= $inputClass ?>">
                        </div>
                        <div>
                            <label class="<?= $labelClass ?>">Urutan</label>
                            <input type="number" name="sort_order" value="<?= (int) $aspect['sort_order'] ?>" min="0" class="<?= $inputClass ?>">
                        </div>
                        <div class="md:col-span-4">
                            <label class="<?= $labelClass ?>">Deskripsi</label>
                            <textarea name="description" rows="2" class="<?= $inputClass ?>"><?= esc($aspect['description']) ?></textarea>
                        </div>
                        <div class="flex justify-end md:col-span-4">
                            <button type="submit" class="btn-primary"><i class="fa-solid fa-floppy-disk mr-1"></i> Simpan Aspek</button>
                        </div>
                    </form>
                </div>

                <div x-cloak x-show="newIndicator" x-transition class="border-b border-stone-100 bg-emerald-50/40 p-5">
                    <form method="post" action="<?= site_url('admin/penilaian-karakter/indicator/store') ?>" class="grid gap-3 md:grid-cols-4">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id_aspect" value="<?= (int) $aspect['id_aspect'] ?>">
                        <div class="md:col-span-2">
                            <label class="<?= $labelClass ?>">Nama Indikator</label>
                            <input type="text" name="indicator_name" required class="<?= $inputClass ?>" placeholder="Contoh: Datang tepat waktu">
                        </div>
                        <div>
                            <label class="<?= $labelClass ?>">Kode</label>
                            <input type="text" name="indicator_code" class="<?= $inputClass ?>" placeholder="DISC-01">
                        </div>
                        <div>
                            <label class="<?= $labelClass ?>">Urutan</label>
                            <input type="number" name="sort_order" value="0" min="0" class="<?= $inputClass ?>">
                        </div>
                        <div class="md:col-span-4">
                            <label class="<?= $labelClass ?>">Deskripsi</label>
                            <textarea name="description" rows="2" class="<?= $inputClass ?>"></textarea>
                        </div>
                        <div class="flex justify-end gap-2 md:col-span-4">
                            <button type="button" @click="newIndicator = false" class="rounded-xl border border-stone-200 px-4 py-2.5 text-xs font-semibold text-stone-600 transition hover:bg-stone-50">Batal</button>
                            <button type="submit" class="rounded-xl bg-emerald-600 px-5 py-2.5 text-xs font-bold text-white transition hover:bg-emerald-700"><i class="fa-solid fa-plus mr-1"></i> Simpan Indikator</button>
                        </div>
                    </form>
                </div>

                <div x-show="open" class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="px-4 py-2.5 font-semibold">Kode</th>
                                <th class="px-4 py-2.5 font-semibold">Indikator</th>
                                <th class="px-4 py-2.5 font-semibold">Deskripsi</th>
                                <th class="px-4 py-2.5 font-semibold">Urutan</th>
                                <th class="px-4 py-2.5 text-right font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            <?php if ($aspect['indicators'] === []): ?>
                                <tr><td colspan="5" class="px-4 py-6 text-center text-xs text-stone-400">Belum ada indikator pada aspek ini.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($aspect['indicators'] as $indicator): ?>
                                <tr x-data="{ edit: false }" class="transition hover:bg-stone-50/70">
                                    <td class="px-4 py-3">
                                        <span class="inline-flex rounded-md bg-stone-100 px-2 py-1 font-mono text-[10px] font-semibold text-stone-600"><?= esc($indicator['indicator_code'] ?: '-') ?></span>
                                    </td>
                                    <td class="px-4 py-3 font-medium text-stone-800"><?= esc($indicator['indicator_name']) ?></td>
                                    <td class="px-4 py-3 text-xs text-stone-500"><?= esc($indicator['description'] ?: '-') ?></td>
                                    <td class="px-4 py-3 text-stone-500"><?= (int) $indicator['sort_order'] ?></td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-end gap-1">
                                            <button type="button" @click="edit = !edit" class="btn-icon" title="Edit"><i class="fa-solid fa-pen-to-square text-xs"></i></button>
                                            <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
                                            <form method="post" action="<?= site_url('admin/penilaian-karakter/indicator/delete/' . $indicator['id_indicator']) ?>" data-confirm="Hapus indikator <?= esc($indicator['indicator_name'], 'attr') ?>?">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn-icon btn-icon-danger" title="Hapus"><i class="fa-solid fa-trash text-xs"></i></button>
                                            </form>
                                            <?php endif; ?>
                                        </div>

                                        <form x-cloak x-show="edit" x-transition method="post" action="<?= site_url('admin/penilaian-karakter/indicator/update/' . $indicator['id_indicator']) ?>" class="mt-3 grid gap-2 rounded-xl bg-stone-50 p-3 sm:grid-cols-4">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id_aspect" value="<?= (int) $aspect['id_aspect'] ?>">
                                            <input type="text" name="indicator_name" value="<?= esc($indicator['indicator_name']) ?>" required class="<?= $inputClass ?> sm:col-span-2">
                                            <input type="text" name="indicator_code" value="<?= esc($indicator['indicator_code']) ?>" class="<?= $inputClass ?>" placeholder="Kode">
                                            <input type="number" name="sort_order" value="<?= (int) $indicator['sort_order'] ?>" min="0" class="<?= $inputClass ?>">
                                            <input type="text" name="description" value="<?= esc($indicator['description']) ?>" class="<?= $inputClass ?> sm:col-span-4" placeholder="Deskripsi">
                                            <button type="submit" class="btn-primary sm:col-span-4"><i class="fa-solid fa-floppy-disk mr-1"></i> Simpan Perubahan</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
