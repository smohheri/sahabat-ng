<?php
$inputClass = 'input';
$labelClass = 'label';
$scoreTone  = [1 => 'rose', 2 => 'amber', 3 => 'sky', 4 => 'emerald'];
$scales     = $scales ?? [];
$aspects    = $aspects ?? [];
$history    = $history ?? [];
$monthly    = $monthly ?? [];

$monthlyByAspect = [];
foreach ($monthly as $row) {
    $monthlyByAspect[$row['aspect_name']][] = $row;
}

$months = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
?>

<div class="space-y-5">
    <div class="page-header">
        <div>
            <h2 class="page-title">Penilaian Karakter</h2>
            <p class="page-subtitle">Isi skor 1-4 untuk setiap indikator, lalu simpan. Ringkasan mingguan dan bulanan dibuat otomatis.</p>
        </div>
        <div class="flex flex-wrap gap-2" x-data>
            <a href="<?= site_url('guru/penilaian-karakter/template') ?>" class="btn-secondary"><i class="fa-solid fa-file-excel text-emerald-600"></i> Template</a>
            <button type="button" @click="$refs.importExcel.click()" class="btn-secondary"><i class="fa-solid fa-file-import text-sky-600"></i> Import</button>
            <form method="post" action="<?= site_url('guru/penilaian-karakter/import') ?>" enctype="multipart/form-data" x-ref="importForm">
                <?= csrf_field() ?>
                <input type="file" name="file" accept=".xlsx,.xls" class="hidden" x-ref="importExcel" @change="$refs.importForm.submit()">
            </form>
        </div>
    </div>

    <?php if ($children === []): ?>
        <div class="rounded-2xl border border-dashed border-stone-300 bg-white px-6 py-16 text-center">
            <i class="fa-solid fa-star mb-3 text-4xl text-stone-300"></i>
            <p class="text-sm font-semibold text-stone-600">Belum ada anak untuk dinilai</p>
            <p class="mt-1 text-xs text-stone-400">Pastikan Anda sudah terhubung dengan rombel atau mata pelajaran.</p>
        </div>
    <?php else: ?>
        <div class="card p-4">
            <form method="get" action="<?= site_url('guru/penilaian-karakter') ?>" class="flex flex-col gap-3 sm:flex-row sm:items-end">
                <div class="flex-1">
                    <label class="<?= $labelClass ?>">Pilih Anak</label>
                    <select name="id_anak" class="<?= $inputClass ?>" onchange="this.form.submit()">
                        <?php foreach ($children as $child): ?>
                            <option value="<?= (int) $child['id_anak'] ?>" <?= (int) ($selected['id_anak'] ?? 0) === (int) $child['id_anak'] ? 'selected' : '' ?>><?= esc($child['nama_anak'] . ($child['no_registrasi'] ? ' - ' . $child['no_registrasi'] : '')) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn-dark"><i class="fa-solid fa-arrow-right"></i></button>
            </form>
        </div>

        <?php if ($selected): ?>
            <form method="post" action="<?= site_url('guru/penilaian-karakter/save') ?>" class="space-y-5">
                <?= csrf_field() ?>
                <input type="hidden" name="id_anak" value="<?= (int) $selected['id_anak'] ?>">

                <div class="card p-5">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <?php if (! empty($selected['foto'])): ?>
                                <img src="<?= upload_url('foto_anak', $selected['foto']) ?>" alt="<?= esc($selected['nama_anak']) ?>" class="h-12 w-12 rounded-xl object-cover ring-1 ring-stone-200">
                            <?php else: ?>
                                <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-700 text-lg font-extrabold text-white"><?= esc(strtoupper(mb_substr((string) $selected['nama_anak'], 0, 1))) ?></span>
                            <?php endif; ?>
                            <div>
                                <p class="text-base font-extrabold text-stone-900"><?= esc($selected['nama_anak']) ?></p>
                                <p class="text-xs text-stone-500"><?= esc($selected['no_registrasi'] ?: 'Tanpa No. Registrasi') ?> &middot; <?= esc($selected['pendidikan'] ?: '-') ?></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 rounded-xl bg-stone-50 px-3 py-2">
                            <span class="text-xs font-semibold text-stone-500">Tanggal</span>
                            <span class="text-xs font-bold text-stone-700"><?= tanggal_id(date('Y-m-d')) ?></span>
                        </div>
                    </div>
                </div>

                <?php if ($scales !== []): ?>
                    <div class="page-actions">
                        <?php foreach ($scales as $scale): ?>
                            <?php $tone = $scoreTone[(int) $scale['score']] ?? 'stone'; ?>
                            <div class="flex items-center gap-2 rounded-xl border border-<?= $tone ?>-100 bg-<?= $tone ?>-50 px-3 py-2">
                                <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-<?= $tone ?>-500 text-xs font-extrabold text-white"><?= (int) $scale['score'] ?></span>
                                <div>
                                    <p class="text-xs font-bold text-<?= $tone ?>-700"><?= esc($scale['category']) ?></p>
                                    <?php if (! empty($scale['description'])): ?><p class="text-[10px] text-stone-500"><?= esc($scale['description']) ?></p><?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ($aspects === []): ?>
                    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-800"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Master aspek dan indikator belum diatur oleh admin.</div>
                <?php endif; ?>

                <div class="space-y-4">
                    <?php foreach ($aspects as $aspect): ?>
                        <div class="card overflow-hidden" x-data="{ open: true }">
                            <button type="button" @click="open = !open" class="flex w-full items-center justify-between gap-3 border-b border-stone-100 bg-stone-50/60 px-5 py-3.5 text-left">
                                <div class="page-actions">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700"><i class="fa-solid fa-layer-group text-xs"></i></span>
                                    <div>
                                        <p class="text-sm font-bold text-stone-900"><?= esc($aspect['aspect_name']) ?></p>
                                        <p class="text-xs text-stone-400"><?= esc($aspect['aspect_code']) ?> &middot; <?= count($aspect['indicators']) ?> indikator</p>
                                    </div>
                                </div>
                                <i class="fa-solid fa-chevron-down text-xs text-stone-400 transition" :class="open ? '' : '-rotate-90'"></i>
                            </button>

                            <div x-show="open" class="divide-y divide-stone-100">
                                <?php if ($aspect['indicators'] === []): ?>
                                    <p class="px-5 py-5 text-xs text-stone-400">Belum ada indikator pada aspek ini.</p>
                                <?php endif; ?>
                                <?php foreach ($aspect['indicators'] as $indicator): ?>
                                    <?php $idIndicator = (int) $indicator['id_indicator']; ?>
                                    <div class="flex flex-col gap-3 px-5 py-4 lg:flex-row lg:items-center lg:justify-between" x-data="{ score: <?= (int) old('scores.' . $idIndicator, 0) ?> }">
                                        <div class="min-w-0 lg:max-w-md">
                                            <p class="text-sm font-semibold text-stone-800"><?= esc($indicator['indicator_name']) ?></p>
                                            <p class="text-xs text-stone-400"><?= esc($indicator['indicator_code'] ?: '-') ?><?= ! empty($indicator['description']) ? ' · ' . esc($indicator['description']) : '' ?></p>
                                        </div>
                                        <div class="flex flex-wrap gap-1.5">
                                            <input type="hidden" name="scores[<?= $idIndicator ?>]" :value="score">
                                            <?php foreach ([1, 2, 3, 4] as $value): ?>
                                                <?php $tone = $scoreTone[$value]; ?>
                                                <button type="button" @click="score = <?= $value ?>"
                                                        class="flex h-11 w-11 flex-col items-center justify-center rounded-xl border text-xs font-extrabold transition"
                                                        :class="score === <?= $value ?> ? 'border-<?= $tone ?>-500 bg-<?= $tone ?>-50 text-<?= $tone ?>-700 ring-2 ring-<?= $tone ?>-500/20' : 'border-stone-200 bg-white text-stone-400 hover:border-<?= $tone ?>-300 hover:text-<?= $tone ?>-600'">
                                                    <?= $value ?>
                                                </button>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="card p-5">
                    <h3 class="section-title"><i class="fa-solid fa-comment-dots text-emerald-600"></i> Catatan Kualitatif</h3>
                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="<?= $labelClass ?>">Kekuatan</label>
                            <textarea name="strengths" rows="2" class="<?= $inputClass ?>" placeholder="Hal positif yang menonjol"></textarea>
                        </div>
                        <div>
                            <label class="<?= $labelClass ?>">Perkembangan Teramati</label>
                            <textarea name="development_observed" rows="2" class="<?= $inputClass ?>" placeholder="Perubahan yang terlihat"></textarea>
                        </div>
                        <div>
                            <label class="<?= $labelClass ?>">Area Pendampingan</label>
                            <textarea name="areas_to_support" rows="2" class="<?= $inputClass ?>" placeholder="Aspek yang perlu bantuan"></textarea>
                        </div>
                        <div>
                            <label class="<?= $labelClass ?>">Strategi Dukungan</label>
                            <textarea name="support_strategy" rows="2" class="<?= $inputClass ?>" placeholder="Rencana tindak lanjut"></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="<?= $labelClass ?>">Catatan Penilaian</label>
                            <textarea name="notes" rows="2" class="<?= $inputClass ?>" placeholder="Catatan umum sesi penilaian"></textarea>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> Simpan Penilaian</button>
                    </div>
                </div>
            </form>

            <div class="grid gap-5 lg:grid-cols-2">
                <div class="card overflow-hidden">
                    <div class="card-header flex items-center justify-between">
                        <h3 class="section-title">Riwayat Penilaian</h3>
                        <span class="rounded-lg bg-stone-100 px-2.5 py-1 text-xs font-semibold text-stone-600"><?= count($history) ?> data</span>
                    </div>
                    <?php if ($history === []): ?>
                        <div class="px-5 py-10 text-center text-sm text-stone-400">Belum ada riwayat penilaian.</div>
                    <?php else: ?>
                        <div class="divide-y divide-stone-100">
                            <?php foreach ($history as $item): ?>
                                <?php $avg = (float) ($item['avg_score'] ?? 0); ?>
                                <div class="flex items-center justify-between gap-3 px-5 py-3">
                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold text-stone-800"><?= tanggal_id($item['assessment_date'] ?? null) ?></p>
                                        <p class="text-xs text-stone-400"><?= esc($item['assessor_name'] ?: '-') ?> &middot; <?= ($item['assessor_type'] ?? '') === 'anak_asuh' ? 'Anak Asuh' : 'Guru' ?> &middot; <?= (int) ($item['indicator_count'] ?? 0) ?> indikator</p>
                                    </div>
                                    <span class="shrink-0 rounded-lg px-2.5 py-1 text-xs font-extrabold <?= $avg >= 3 ? 'bg-emerald-100 text-emerald-700' : ($avg >= 2 ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') ?>"><?= number_format($avg, 2) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="card overflow-hidden">
                    <div class="card-header">
                        <h3 class="section-title">Rata-rata Indikator Bulan Ini</h3>
                        <p class="text-xs text-stone-500"><?= $months[(int) date('n')] ?> <?= date('Y') ?></p>
                    </div>
                    <?php if ($monthlyByAspect === []): ?>
                        <div class="px-5 py-10 text-center text-sm text-stone-400">Belum ada data bulan ini.</div>
                    <?php else: ?>
                        <div class="max-h-80 space-y-4 overflow-y-auto p-5">
                            <?php foreach ($monthlyByAspect as $aspectName => $rows): ?>
                                <div>
                                    <p class="text-xs font-bold   text-stone-400"><?= esc($aspectName) ?></p>
                                    <div class="mt-2 space-y-2">
                                        <?php foreach ($rows as $row): ?>
                                            <?php $avg = (float) $row['avg_score']; ?>
                                            <div class="flex items-center justify-between gap-2">
                                                <p class="truncate text-xs text-stone-600"><?= esc($row['indicator_name']) ?></p>
                                                <div class="flex shrink-0 items-center gap-2">
                                                    <div class="h-1.5 w-16 overflow-hidden rounded-full bg-stone-100">
                                                        <div class="h-full rounded-full <?= $avg >= 3 ? 'bg-emerald-500' : ($avg >= 2 ? 'bg-amber-500' : 'bg-rose-500') ?>" style="width: <?= min(100, ($avg / 4) * 100) ?>%"></div>
                                                    </div>
                                                    <span class="w-8 text-right text-xs font-bold text-stone-700"><?= number_format($avg, 2) ?></span>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
