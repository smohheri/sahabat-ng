<?php
$items = $items ?? [];
?>

<div class="space-y-5">
    <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
        <div>
            <h2 class="text-lg font-extrabold text-slate-900">Catatan Kualitatif</h2>
            <p class="text-sm text-slate-500">Kumpulan catatan pendampingan dari penilaian karakter.</p>
        </div>
        <span class="inline-flex items-center gap-2 self-start rounded-xl bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-600">
            <i class="fa-solid fa-comment-dots"></i> <?= number_format((int) ($total ?? count($items)), 0, ',', '.') ?> catatan
        </span>
    </div>

    <?php if ($items === []): ?>
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
            <i class="fa-solid fa-comments mb-3 text-3xl text-slate-300"></i>
            <p class="text-sm text-slate-500">Belum ada catatan kualitatif.</p>
        </div>
    <?php else: ?>
        <div class="grid gap-5 lg:grid-cols-2">
            <?php foreach ($items as $item): ?>
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between gap-3 border-b border-slate-100 bg-slate-50/60 px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-50 text-xs font-bold text-indigo-600"><?= esc(strtoupper(mb_substr((string) $item['nama_anak'], 0, 1))) ?></span>
                            <div>
                                <p class="text-sm font-bold text-slate-900"><?= esc($item['nama_anak']) ?></p>
                                <p class="text-[11px] text-slate-400"><?= tanggal_id($item['assessment_date'] ?? null) ?> &middot; <?= esc($item['assessor_name'] ?: '-') ?></p>
                            </div>
                        </div>
                        <a href="<?= site_url('admin/penilaian-karakter/detail-penilaian/' . $item['id_assessment']) ?>" class="rounded-lg border border-slate-200 bg-white p-2 text-slate-500 transition hover:bg-indigo-50 hover:text-indigo-600" title="Lihat penilaian">
                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                        </a>
                    </div>

                    <div class="space-y-3 p-5">
                        <?php
                        $fields = [
                            ['Kekuatan', $item['strengths'] ?? null, 'emerald', 'thumbs-up'],
                            ['Perkembangan Teramati', $item['development_observed'] ?? null, 'sky', 'arrow-trend-up'],
                            ['Area Pendampingan', $item['areas_to_support'] ?? null, 'amber', 'triangle-exclamation'],
                            ['Strategi Dukungan', $item['support_strategy'] ?? null, 'indigo', 'lightbulb'],
                        ];
                        ?>
                        <?php foreach ($fields as [$label, $value, $tone, $icon]): ?>
                            <?php if (! empty($value)): ?>
                                <div class="rounded-xl border border-<?= $tone ?>-100 bg-<?= $tone ?>-50/60 p-3">
                                    <p class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-<?= $tone ?>-700"><i class="fa-solid fa-<?= $icon ?>"></i> <?= $label ?></p>
                                    <p class="mt-1 text-xs leading-relaxed text-slate-600"><?= nl2br(esc($value)) ?></p>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?= view('partials/pagination', ['page' => $page ?? 1, 'perPage' => $perPage ?? 12, 'total' => $total ?? 0]) ?>
    <?php endif; ?>
</div>
