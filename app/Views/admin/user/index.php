<?php
$badgeTone = [
    'admin'    => 'bg-indigo-100 text-indigo-700',
    'petugas'  => 'bg-sky-100 text-sky-700',
    'dinas'    => 'bg-violet-100 text-violet-700',
    'operator' => 'bg-amber-100 text-amber-700',
    'pengajar' => 'bg-emerald-100 text-emerald-700',
    'guru'     => 'bg-emerald-100 text-emerald-700',
    'anak'     => 'bg-rose-100 text-rose-700',
];
?>
<div class="space-y-5">
    <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
        <div>
            <h2 class="text-lg font-extrabold text-slate-900">Manajemen User</h2>
            <p class="text-sm text-slate-500">Total <?= number_format((int) ($total ?? count($items)), 0, ',', '.') ?> akun terdaftar.</p>
        </div>
        <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
        <a href="<?= site_url('admin/user/tambah') ?>" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-bold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700">
            <i class="fa-solid fa-plus"></i> Tambah User
        </a>
        <?php endif; ?>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <form method="get" action="<?= site_url('admin/user') ?>" class="grid gap-3 sm:grid-cols-4">
            <div class="relative sm:col-span-2">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i class="fa-solid fa-magnifying-glass text-xs"></i></span>
                <input type="text" name="q" value="<?= esc($search) ?>" placeholder="Cari nama, username, atau role..."
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-9 pr-3 text-sm outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20">
            </div>
            <select name="role" class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20">
                <option value="">Semua role</option>
                <?php foreach ($roles as $value => $label): ?>
                    <option value="<?= esc($value) ?>" <?= $role === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
                <?php endforeach; ?>
            </select>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-slate-800"><i class="fa-solid fa-filter mr-1"></i> Filter</button>
                <a href="<?= site_url('admin/user') ?>" class="rounded-xl border border-slate-200 px-3 py-2.5 text-xs font-semibold text-slate-500 transition hover:bg-slate-50"><i class="fa-solid fa-rotate-left"></i></a>
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-4 py-3 font-semibold">User</th>
                        <th class="px-4 py-3 font-semibold">Role</th>
                        <th class="px-4 py-3 font-semibold">Terhubung Dengan</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        <th class="px-4 py-3 font-semibold">Login Terakhir</th>
                        <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if ($items === []): ?>
                        <tr><td colspan="6" class="px-4 py-12 text-center text-sm text-slate-500"><i class="fa-solid fa-users-slash mb-2 block text-3xl text-slate-300"></i>Tidak ada user yang cocok.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($items as $item): ?>
                        <tr class="transition hover:bg-slate-50/70">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-xs font-bold text-slate-600"><?= esc(strtoupper(mb_substr((string) $item['nama'], 0, 1))) ?></span>
                                    <div>
                                        <p class="font-semibold text-slate-800"><?= esc($item['nama']) ?></p>
                                        <p class="text-[11px] text-slate-400">@<?= esc($item['username']) ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold <?= $badgeTone[$item['role'] ?? ''] ?? 'bg-slate-100 text-slate-600' ?>"><?= esc($item['role_label'] ?? $item['role'] ?? '-') ?></span>
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                <?php if (! empty($item['nama_anak'])): ?>
                                    <p class="text-xs"><i class="fa-solid fa-child mr-1 text-slate-400"></i><?= esc($item['nama_anak']) ?></p>
                                <?php endif; ?>
                                <?php if (! empty($item['nama_guru'])): ?>
                                    <p class="text-xs"><i class="fa-solid fa-chalkboard-user mr-1 text-slate-400"></i><?= esc($item['nama_guru']) ?></p>
                                <?php endif; ?>
                                <?php if (empty($item['nama_anak']) && empty($item['nama_guru'])): ?>
                                    <span class="text-xs text-slate-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold <?= (int) ($item['is_active'] ?? 0) === 1 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' ?>">
                                    <?= (int) ($item['is_active'] ?? 0) === 1 ? 'Aktif' : 'Nonaktif' ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-[11px] text-slate-500"><?= $item['last_login_at'] ? tanggal_id($item['last_login_at'], true) : 'Belum pernah' ?></td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
                                    <a href="<?= site_url('admin/user/edit/' . $item['id_user']) ?>" class="rounded-lg border border-slate-200 p-2 text-slate-500 transition hover:bg-indigo-50 hover:text-indigo-600" title="Edit"><i class="fa-solid fa-pen-to-square text-xs"></i></a>
                                    <form method="post" action="<?= site_url('admin/user/delete/' . $item['id_user']) ?>" data-confirm="Hapus user <?= esc($item['nama'], 'attr') ?>?" class="inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="rounded-lg border border-slate-200 p-2 text-slate-500 transition hover:bg-rose-50 hover:text-rose-600" title="Hapus"><i class="fa-solid fa-trash text-xs"></i></button>
                                    </form>
                                    <?php else: ?>
                                    <span class="text-[11px] text-slate-400">—</span>
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
