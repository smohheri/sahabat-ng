<?php
$items = $items ?? [];
?>

<div class="space-y-5">
    <div class="page-header">
        <div>
            <h2 class="page-title">Catatan Kualitatif</h2>
            <p class="page-subtitle">Kumpulan catatan pendampingan dari penilaian karakter.</p>
        </div>
        <span class="inline-flex items-center gap-2 self-start rounded-xl bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700">
            <i class="fa-solid fa-comment-dots"></i> <?= number_format((int) ($total ?? count($items)), 0, ',', '.') ?> catatan
        </span>
    </div>

    <?php if ($items === []): ?>
        <div class="rounded-2xl border border-dashed border-stone-300 bg-white px-6 py-14 text-center">
            <i class="fa-solid fa-comments mb-3 text-3xl text-stone-300"></i>
            <p class="page-subtitle">Belum ada catatan kualitatif.</p>
        </div>
    <?php else: ?>
        <div class="grid gap-5 lg:grid-cols-2">
            <?php foreach ($items as $item): ?>
                <div class="card overflow-hidden">
                    <div class="flex items-center justify-between gap-3 border-b border-stone-100 bg-stone-50/60 px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-50 text-xs font-bold text-emerald-700"><?= esc(strtoupper(mb_substr((string) $item['nama_anak'], 0, 1))) ?></span>
                            <div>
                                <p class="text-sm font-bold text-stone-900"><?= esc($item['nama_anak']) ?></p>
                                <p class="text-xs text-stone-400"><?= tanggal_id($item['assessment_date'] ?? null) ?> &middot; <?= esc($item['assessor_name'] ?: '-') ?></p>
                            </div>
                        </div>
                        <a href="<?= site_url('admin/penilaian-karakter/detail-penilaian/' . $item['id_assessment']) ?>" class="rounded-lg border border-stone-200 bg-white p-2 text-stone-500 transition hover:bg-emerald-50 hover:text-emerald-700" title="Lihat penilaian">
                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                        </a>
                    </div>

                    <div class="space-y-3 p-5">
                        <?php
                        $fields = [
                            ['Kekuatan', $item['strengths'] ?? null, 'emerald', 'thumbs-up'],
                            ['Perkembangan Teramati', $item['development_observed'] ?? null, 'sky', 'arrow-trend-up'],
                            ['Area Pendampingan', $item['areas_to_support'] ?? null, 'amber', 'triangle-exclamation'],
                            ['Strategi Dukungan', $item['support_strategy'] ?? null, 'emerald', 'lightbulb'],
                        ];
                        ?>
                        <?php foreach ($fields as [$label, $value, $tone, $icon]): ?>
                            <?php if (! empty($value)): ?>
                                <div class="rounded-xl border border-<?= $tone ?>-100 bg-<?= $tone ?>-50/60 p-3">
                                    <p class="flex items-center gap-1.5 text-[10px] font-bold   text-<?= $tone ?>-700"><i class="fa-solid fa-<?= $icon ?>"></i> <?= $label ?></p>
                                    <p class="mt-1 text-xs leading-relaxed text-stone-600"><?= nl2br(esc($value)) ?></p>
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
