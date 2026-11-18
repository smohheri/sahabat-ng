<?php
$badgeTone = [
    'admin'    => 'bg-emerald-100 text-emerald-700',
    'petugas'  => 'bg-sky-100 text-sky-700',
    'dinas'    => 'bg-violet-100 text-violet-700',
    'operator' => 'bg-amber-100 text-amber-700',
    'pengajar' => 'bg-emerald-100 text-emerald-700',
    'guru'     => 'bg-emerald-100 text-emerald-700',
    'anak'     => 'bg-rose-100 text-rose-700',
];
?>
<div class="space-y-5">
    <div class="page-header">
        <div>
            <h2 class="page-title">Manajemen User</h2>
            <p class="page-subtitle">Total <?= number_format((int) ($total ?? count($items)), 0, ',', '.') ?> akun terdaftar.</p>
        </div>
        <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
        <a href="<?= site_url('admin/user/tambah') ?>" class="btn-primary">
            <i class="fa-solid fa-plus"></i> Tambah User
        </a>
        <?php endif; ?>
    </div>

    <div class="card p-4">
        <form method="get" action="<?= site_url('admin/user') ?>" class="grid gap-3 sm:grid-cols-4">
            <div class="relative sm:col-span-2">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-stone-400"><i class="fa-solid fa-magnifying-glass text-xs"></i></span>
                <input type="text" name="q" value="<?= esc($search) ?>" placeholder="Cari nama, username, atau role..."
                       class="w-full rounded-xl border border-stone-200 bg-stone-50 py-2.5 pl-9 pr-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20">
            </div>
            <select name="role" class="rounded-xl border border-stone-200 bg-stone-50 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20">
                <option value="">Semua role</option>
                <?php foreach ($roles as $value => $label): ?>
                    <option value="<?= esc($value) ?>" <?= $role === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
                <?php endforeach; ?>
            </select>
            <div class="page-actions">
                <button type="submit" class="btn-dark flex-1"><i class="fa-solid fa-filter mr-1"></i> Filter</button>
                <a href="<?= site_url('admin/user') ?>" class="btn-secondary"><i class="fa-solid fa-rotate-left"></i></a>
            </div>
        </form>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th class="px-4 py-3 font-semibold">User</th>
                        <th class="px-4 py-3 font-semibold">Role</th>
                        <th class="px-4 py-3 font-semibold">Terhubung Dengan</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        <th class="px-4 py-3 font-semibold">Login Terakhir</th>
                        <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    <?php if ($items === []): ?>
                        <tr><td colspan="6" class="empty-state"><i class="fa-solid fa-users-slash empty-icon"></i><p class="empty-title">Tidak ada user yang cocok</p><p class="empty-desc">Coba ubah kata kunci atau filter pencarian.</p></td></tr>
                    <?php endif; ?>
                    <?php foreach ($items as $item): ?>
                        <tr class="transition hover:bg-stone-50/70">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-stone-100 text-xs font-bold text-stone-600"><?= esc(strtoupper(mb_substr((string) $item['nama'], 0, 1))) ?></span>
                                    <div>
                                        <p class="font-semibold text-stone-800"><?= esc($item['nama']) ?></p>
                                        <p class="text-xs text-stone-400">@<?= esc($item['username']) ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold <?= $badgeTone[$item['role'] ?? ''] ?? 'bg-stone-100 text-stone-600' ?>"><?= esc($item['role_label'] ?? $item['role'] ?? '-') ?></span>
                            </td>
                            <td class="px-4 py-3 text-stone-600">
                                <?php if (! empty($item['nama_anak'])): ?>
                                    <p class="text-xs"><i class="fa-solid fa-child mr-1 text-stone-400"></i><?= esc($item['nama_anak']) ?></p>
                                <?php endif; ?>
                                <?php if (! empty($item['nama_guru'])): ?>
                                    <p class="text-xs"><i class="fa-solid fa-chalkboard-user mr-1 text-stone-400"></i><?= esc($item['nama_guru']) ?></p>
                                <?php endif; ?>
                                <?php if (empty($item['nama_anak']) && empty($item['nama_guru'])): ?>
                                    <span class="text-xs text-stone-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold <?= (int) ($item['is_active'] ?? 0) === 1 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' ?>">
                                    <?= (int) ($item['is_active'] ?? 0) === 1 ? 'Aktif' : 'Nonaktif' ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-stone-500"><?= $item['last_login_at'] ? tanggal_id($item['last_login_at'], true) : 'Belum pernah' ?></td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
                                    <a href="<?= site_url('admin/user/edit/' . $item['id_user']) ?>" class="btn-icon" title="Edit"><i class="fa-solid fa-pen-to-square text-xs"></i></a>
                                    <form method="post" action="<?= site_url('admin/user/delete/' . $item['id_user']) ?>" data-confirm="Hapus user <?= esc($item['nama'], 'attr') ?>?" class="inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn-icon btn-icon-danger" title="Hapus"><i class="fa-solid fa-trash text-xs"></i></button>
                                    </form>
                                    <?php else: ?>
                                    <span class="text-xs text-stone-400">—</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="px-4 pb-4">
            <?= view('partials/pagination', ['page' => $page ?? 1, 'perPage' => $perPage ?? 15, 'total' => $total ?? 0]) ?>
        </div>
    </div>
</div>
