<?php
$aspects = $aspects ?? [];
$scales  = $scales ?? [];
$months  = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$scoreTone = [1 => 'rose', 2 => 'amber', 3 => 'sky', 4 => 'emerald'];
$labelClass = 'mb-1 block text-[11px] font-semibold uppercase tracking-wider text-slate-400';
$inputClass = 'w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20';
?>

<div class="space-y-5">
    <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
        <div>
            <h2 class="text-lg font-extrabold text-slate-900">Asesmen Mandiri</h2>
            <p class="text-sm text-slate-500">Nilai dirimu sendiri dengan jujur untuk periode <?= $months[(int) $month] ?> <?= (int) $year ?>.</p>
        </div>
        <a href="<?= site_url('anak') ?>" class="inline-flex items-center gap-2 self-start rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
    </div>

    <?php if ($alreadySubmit): ?>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-6 text-center">
            <i class="fa-solid fa-circle-check mb-3 text-4xl text-emerald-500"></i>
            <p class="text-base font-bold text-emerald-800">Asesmen mandiri bulan ini sudah dikirim</p>
            <p class="mt-1 text-sm text-emerald-700">Terima kasih sudah mengisi penilaian diri untuk <?= $months[(int) $month] ?> <?= (int) $year ?>. Kamu dapat mengisi kembali bulan depan.</p>
            <a href="<?= site_url('anak') ?>" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-emerald-700"><i class="fa-solid fa-house"></i> Ke Dashboard</a>
        </div>
    <?php else: ?>
        <?php if ($scales !== []): ?>
            <div class="flex flex-wrap gap-2">
                <?php foreach ($scales as $scale): ?>
                    <?php $tone = $scoreTone[(int) $scale['score']] ?? 'slate'; ?>
                    <div class="flex items-center gap-2 rounded-xl border border-<?= $tone ?>-100 bg-<?= $tone ?>-50 px-3 py-2">
                        <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-<?= $tone ?>-500 text-[11px] font-extrabold text-white"><?= (int) $scale['score'] ?></span>
                        <div>
                            <p class="text-[11px] font-bold text-<?= $tone ?>-700"><?= esc($scale['category']) ?></p>
                            <?php if (! empty($scale['description'])): ?><p class="text-[10px] text-slate-500"><?= esc($scale['description']) ?></p><?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" action="<?= site_url('anak/asesmen-mandiri') ?>" class="space-y-5">
            <?= csrf_field() ?>

            <?php if ($aspects === []): ?>
                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-800"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Master aspek dan indikator belum tersedia.</div>
            <?php endif; ?>

            <?php foreach ($aspects as $aspect): ?>
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm" x-data="{ open: true }">
                    <button type="button" @click="open = !open" class="flex w-full items-center justify-between gap-3 border-b border-slate-100 bg-slate-50/60 px-5 py-3.5 text-left">
                        <div class="flex items-center gap-2">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600"><i class="fa-solid fa-layer-group text-xs"></i></span>
                            <div>
                                <p class="text-sm font-bold text-slate-900"><?= esc($aspect['aspect_name']) ?></p>
                                <p class="text-[11px] text-slate-400"><?= esc($aspect['aspect_code']) ?> &middot; <?= count($aspect['indicators']) ?> indikator</p>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition" :class="open ? '' : '-rotate-90'"></i>
                    </button>

                    <div x-show="open" class="divide-y divide-slate-100">
                        <?php if ($aspect['indicators'] === []): ?>
                            <p class="px-5 py-5 text-xs text-slate-400">Belum ada indikator pada aspek ini.</p>
                        <?php endif; ?>
                        <?php foreach ($aspect['indicators'] as $indicator): ?>
                            <?php $idIndicator = (int) $indicator['id_indicator']; ?>
                            <div class="flex flex-col gap-3 px-5 py-4 lg:flex-row lg:items-center lg:justify-between" x-data="{ score: <?= (int) old('scores.' . $idIndicator, 0) ?> }">
                                <div class="min-w-0 lg:max-w-md">
                                    <p class="text-sm font-semibold text-slate-800"><?= esc($indicator['indicator_name']) ?></p>
                                    <p class="text-[11px] text-slate-400"><?= esc($indicator['description'] ?: ($indicator['indicator_code'] ?: '-')) ?></p>
                                </div>
                                <div class="flex flex-wrap gap-1.5">
                                    <input type="hidden" name="scores[<?= $idIndicator ?>]" :value="score">
                                    <?php foreach ([1, 2, 3, 4] as $value): ?>
                                        <?php $tone = $scoreTone[$value]; ?>
                                        <button type="button" @click="score = <?= $value ?>"
                                                class="flex h-11 w-11 items-center justify-center rounded-xl border text-xs font-extrabold transition"
                                                :class="score === <?= $value ?> ? 'border-<?= $tone ?>-500 bg-<?= $tone ?>-50 text-<?= $tone ?>-700 ring-2 ring-<?= $tone ?>-500/20' : 'border-slate-200 bg-white text-slate-400 hover:border-<?= $tone ?>-300 hover:text-<?= $tone ?>-600'">
                                            <?= $value ?>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <label class="<?= $labelClass ?>">Catatan untuk Dirimu (opsional)</label>
                <textarea name="notes" rows="3" class="<?= $inputClass ?>" placeholder="Ceritakan hal yang sudah baik atau yang ingin kamu perbaiki bulan ini..."><?= esc(old('notes')) ?></textarea>
                <div class="mt-4 flex flex-col-reverse gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-[11px] text-slate-400"><i class="fa-solid fa-circle-info mr-1"></i> Penilaian hanya bisa dikirim sekali per bulan.</p>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-6 py-2.5 text-xs font-bold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700">
                        <i class="fa-solid fa-paper-plane"></i> Kirim Asesmen
                    </button>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>
