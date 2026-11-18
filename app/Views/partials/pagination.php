<?php
$page     = max(1, (int) ($page ?? 1));
$perPage  = max(1, (int) ($perPage ?? 15));
$total    = max(0, (int) ($total ?? 0));
$lastPage = max(1, (int) ceil($total / $perPage));

$query = service('request')->getGet();
unset($query['page']);
$queryString = $query === [] ? '?' : '?' . http_build_query($query) . '&';
$base = current_url() . $queryString;

$start = max(1, min($page - 2, $lastPage - 4));
$end   = min($lastPage, max($page + 2, 5));
?>

<?php if ($total > 0): ?>
    <div class="mt-4 flex flex-col items-center justify-between gap-3 border-t border-stone-100 pt-4 text-sm sm:flex-row">
        <p class="text-stone-500">
            Menampilkan <span class="font-semibold text-stone-700"><?= (($page - 1) * $perPage) + 1 ?></span>
            &ndash; <span class="font-semibold text-stone-700"><?= min($page * $perPage, $total) ?></span>
            dari <span class="font-semibold text-stone-700"><?= $total ?></span> data
        </p>

        <?php if ($lastPage > 1): ?>
            <nav class="flex items-center gap-1" aria-label="Paginasi">
                <a href="<?= $base ?>page=<?= max(1, $page - 1) ?>" class="rounded-lg border border-stone-200 px-3 py-1.5 text-stone-600 transition hover:bg-stone-50 <?= $page <= 1 ? 'pointer-events-none opacity-40' : '' ?>" aria-label="Sebelumnya">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                </a>

                <?php for ($i = $start; $i <= $end; $i++): ?>
                    <a href="<?= $base ?>page=<?= $i ?>" class="rounded-lg border px-3 py-1.5 font-medium transition <?= $i === $page ? 'border-emerald-700 bg-emerald-700 text-white' : 'border-stone-200 text-stone-600 hover:bg-stone-50' ?>"><?= $i ?></a>
                <?php endfor; ?>

                <a href="<?= $base ?>page=<?= min($lastPage, $page + 1) ?>" class="rounded-lg border border-stone-200 px-3 py-1.5 text-stone-600 transition hover:bg-stone-50 <?= $page >= $lastPage ? 'pointer-events-none opacity-40' : '' ?>" aria-label="Berikutnya">
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </a>
            </nav>
        <?php endif; ?>
    </div>
<?php endif; ?>
