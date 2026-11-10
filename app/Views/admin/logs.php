<?php
$toneMap = [
    'success' => 'bg-emerald-100 text-emerald-600',
    'warning' => 'bg-amber-100 text-amber-600',
    'danger'  => 'bg-rose-100 text-rose-600',
    'primary' => 'bg-indigo-100 text-indigo-600',
    'info'    => 'bg-sky-100 text-sky-600',
];
?>

<div class="space-y-5">
    <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
        <div>
            <h2 class="text-lg font-extrabold text-slate-900">Log Aktivitas</h2>
            <p class="text-sm text-slate-500">Total <?= number_format((int) $total, 0, ',', '.') ?> aktivitas tercatat.</p>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <form method="get" action="<?= site_url('admin/logs') ?>" class="grid gap-3 sm:grid-cols-4">
            <div class="relative sm:col-span-2">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i class="fa-solid fa-magnifying-glass text-xs"></i></span>
                <input type="text" name="q" value="<?= esc($filters['search'] ?? '') ?>" placeholder="Cari nama, username, atau deskripsi..."
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-9 pr-3 text-sm outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20">
            </div>
            <select name="activity" class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20">
                <option value="">Semua aktivitas</option>
                <?php foreach ($activities as $activity): ?>
                    <option value="<?= esc($activity) ?>" <?= ($filters['activity'] ?? '') === $activity ? 'selected' : '' ?>><?= esc($activity) ?></option>
                <?php endforeach; ?>
            </select>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-slate-800"><i class="fa-solid fa-filter mr-1"></i> Filter</button>
                <a href="<?= site_url('admin/logs') ?>" class="rounded-xl border border-slate-200 px-3 py-2.5 text-xs font-semibold text-slate-500 transition hover:bg-slate-50"><i class="fa-solid fa-rotate-left"></i></a>
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Aktivitas</th>
                        <th class="px-4 py-3 font-semibold">Pengguna</th>
                        <th class="px-4 py-3 font-semibold">Deskripsi</th>
                        <th class="px-4 py-3 font-semibold">IP</th>
                        <th class="px-4 py-3 font-semibold">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if ($items === []): ?>
                        <tr><td colspan="5" class="px-4 py-12 text-center text-sm text-slate-400"><i class="fa-solid fa-clock-rotate-left mb-2 block text-3xl text-slate-300"></i>Belum ada aktivitas.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($items as $item): ?>
                        <tr class="transition hover:bg-slate-50/70">
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-2">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-lg <?= $toneMap[activity_color($item['activity'])] ?? 'bg-slate-100 text-slate-500' ?>">
                                        <i class="fa-solid fa-<?= activity_icon($item['activity']) ?> text-xs"></i>
                                    </span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600"><?= esc($item['activity']) ?></span>
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-semibold text-slate-800"><?= esc($item['nama'] ?: 'Sistem') ?></p>
                                <p class="text-[11px] text-slate-400">@<?= esc($item['username'] ?: '-') ?></p>
                            </td>
                            <td class="px-4 py-3 text-slate-600"><?= esc($item['description'] ?: '-') ?></td>
                            <td class="px-4 py-3 font-mono text-[11px] text-slate-500"><?= esc($item['ip_address'] ?: '-') ?></td>
                            <td class="px-4 py-3 text-[11px] text-slate-500"><?= tanggal_id($item['created_at'] ?? null, true) ?></td>
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
