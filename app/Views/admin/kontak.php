<?php
$contacts = [
    ['label' => 'Alamat', 'value' => $settings['alamat'] ?? '', 'icon' => 'location-dot', 'tone' => 'indigo', 'href' => null],
    ['label' => 'Telepon', 'value' => $settings['no_telp'] ?? '', 'icon' => 'phone', 'tone' => 'sky', 'href' => ! empty($settings['no_telp']) ? 'tel:' . preg_replace('/[^0-9+]/', '', (string) $settings['no_telp']) : null],
    ['label' => 'Email', 'value' => $settings['email'] ?? '', 'icon' => 'envelope', 'tone' => 'emerald', 'href' => ! empty($settings['email']) ? 'mailto:' . $settings['email'] : null],
    ['label' => 'WhatsApp', 'value' => $settings['whatsapp'] ?? '', 'icon' => 'brands fa-whatsapp', 'tone' => 'emerald', 'href' => ! empty($settings['whatsapp']) ? 'https://wa.me/' . nomor_wa((string) $settings['whatsapp']) : null],
];

$socials = [
    ['facebook', 'fa-facebook-f', 'Facebook', 'text-blue-600 bg-blue-50'],
    ['instagram', 'fa-instagram', 'Instagram', 'text-pink-600 bg-pink-50'],
    ['twitter', 'fa-x-twitter', 'Twitter / X', 'text-slate-800 bg-slate-100'],
    ['youtube', 'fa-youtube', 'YouTube', 'text-red-600 bg-red-50'],
    ['linkedin', 'fa-linkedin-in', 'LinkedIn', 'text-sky-700 bg-sky-50'],
];
?>

<div class="space-y-6">
    <div>
        <h2 class="text-lg font-extrabold text-slate-900">Kontak Lembaga</h2>
        <p class="text-sm text-slate-500">Informasi kontak resmi yang ditampilkan pada halaman publik.</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <?php foreach ($contacts as $contact): ?>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-<?= $contact['tone'] ?>-50 text-<?= $contact['tone'] ?>-600">
                    <i class="fa-<?= str_starts_with($contact['icon'], 'brands') ? 'brands' : 'solid' ?> <?= str_starts_with($contact['icon'], 'brands') ? str_replace('brands ', '', $contact['icon']) : 'fa-' . $contact['icon'] ?>"></i>
                </span>
                <p class="mt-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400"><?= esc($contact['label']) ?></p>
                <?php if (! empty($contact['value']) && $contact['href']): ?>
                    <a href="<?= esc($contact['href']) ?>" target="_blank" rel="noopener" class="mt-1 block break-words text-sm font-semibold text-slate-800 hover:text-indigo-600"><?= esc($contact['value']) ?></a>
                <?php else: ?>
                    <p class="mt-1 break-words text-sm font-semibold text-slate-800"><?= esc($contact['value'] ?: 'Belum diatur') ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="grid gap-5 lg:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
            <h3 class="flex items-center gap-2 text-sm font-bold text-slate-900"><i class="fa-solid fa-share-nodes text-indigo-500"></i> Media Sosial</h3>
            <p class="mt-1 text-xs text-slate-500">Tautan ini dapat diubah melalui menu Pengaturan.</p>

            <div class="mt-5 grid gap-3 sm:grid-cols-2">
                <?php foreach ($socials as [$key, $icon, $label, $tone]): ?>
                    <?php $url = $settings[$key] ?? ''; ?>
                    <div class="flex items-center justify-between gap-3 rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                        <div class="flex items-center gap-3">
                            <span class="flex h-9 w-9 items-center justify-center rounded-lg <?= $tone ?>"><i class="fa-brands <?= $icon ?>"></i></span>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-800"><?= esc($label) ?></p>
                                <p class="truncate text-[11px] text-slate-400"><?= esc($url ?: 'Belum diatur') ?></p>
                            </div>
                        </div>
                        <?php if (! empty($url)): ?>
                            <a href="<?= esc($url) ?>" target="_blank" rel="noopener" class="rounded-lg border border-slate-200 bg-white p-2 text-slate-500 transition hover:bg-indigo-50 hover:text-indigo-600"><i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i></a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="flex items-center gap-2 text-sm font-bold text-slate-900"><i class="fa-solid fa-building text-indigo-500"></i> Identitas</h3>
            <div class="mt-5 space-y-4 text-sm">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Nama LKSA</p>
                    <p class="mt-0.5 font-semibold text-slate-800"><?= esc($settings['nama_lksa'] ?? '-') ?></p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Tahun Berdiri</p>
                    <p class="mt-0.5 font-semibold text-slate-800"><?= esc($settings['tahun_berdiri'] ?? '-') ?></p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Kepala LKSA</p>
                    <p class="mt-0.5 font-semibold text-slate-800"><?= esc($settings['nama_kepala'] ?? '-') ?></p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Kepala Putra</p>
                    <p class="mt-0.5 font-semibold text-slate-800"><?= esc($settings['nama_kepala_putra'] ?? '-') ?></p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Kepala Putri</p>
                    <p class="mt-0.5 font-semibold text-slate-800"><?= esc($settings['nama_kepala_putri'] ?? '-') ?></p>
                </div>
            </div>
            <a href="<?= site_url('admin/pengaturan') ?>" class="mt-5 flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-indigo-700">
                <i class="fa-solid fa-gear"></i> Ubah Pengaturan
            </a>
        </div>
    </div>
</div>
