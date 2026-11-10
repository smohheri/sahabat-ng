<?php
$banks = [
    ['bank' => 'Bank BSI', 'label' => 'Rekening Utama', 'number' => '7252957170', 'holder' => 'Moh. Heri Setiawan', 'tone' => 'indigo', 'icon' => 'building-columns'],
    ['bank' => 'Bank BRI', 'label' => 'Rekening Alternatif', 'number' => '057201014816537', 'holder' => 'Moh. Heri Setiawan', 'tone' => 'sky', 'icon' => 'building-columns'],
];
$wa = '6287842255688';
?>

<div class="space-y-6">
    <div class="relative overflow-hidden rounded-3xl bg-indigo-600 p-6 sm:p-8">
        <div class="absolute -right-10 -top-10 h-48 w-48 rounded-full bg-white/10 blur-2xl"></div>
        <div class="relative max-w-2xl">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-200">Dukung Kami</p>
            <h2 class="mt-2 text-2xl font-extrabold text-white sm:text-3xl">Bantu pengembangan sistem SAHABAT-NG</h2>
            <p class="mt-3 text-sm leading-relaxed text-indigo-100">
                Dukungan Anda membantu kami menghadirkan layanan digital yang lebih baik bagi pengelolaan kesejahteraan anak.
                Aplikasi ini gratis digunakan; donasi sepenuhnya bersifat sukarela.
            </p>
        </div>
    </div>

    <div class="grid gap-5 md:grid-cols-2">
        <?php foreach ($banks as $bank): ?>
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm" x-data="{ copied: false }">
                <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50 px-5 py-4">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-<?= $bank['tone'] ?>-50 text-<?= $bank['tone'] ?>-600">
                            <i class="fa-solid fa-<?= $bank['icon'] ?>"></i>
                        </span>
                        <div>
                            <p class="text-sm font-bold text-slate-900"><?= esc($bank['bank']) ?></p>
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400"><?= esc($bank['label']) ?></p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-semibold text-emerald-700"><i class="fa-solid fa-circle-check"></i> Aktif</span>
                </div>
                <div class="p-5">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Nomor Rekening</p>
                    <div class="mt-2 flex items-center gap-3">
                        <p class="font-mono text-2xl font-extrabold tracking-wide text-slate-900"><?= esc($bank['number']) ?></p>
                        <button type="button"
                                @click="navigator.clipboard.writeText('<?= esc($bank['number'], 'js') ?>'); copied = true; setTimeout(() => copied = false, 2000)"
                                class="rounded-lg border border-slate-200 p-2 text-slate-500 transition hover:bg-slate-50">
                            <i class="fa-solid" :class="copied ? 'fa-check text-emerald-500' : 'fa-copy'"></i>
                        </button>
                    </div>
                    <p class="mt-3 flex items-center gap-2 text-sm text-slate-500"><i class="fa-solid fa-user text-slate-400"></i> a.n. <?= esc($bank['holder']) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="grid gap-5 md:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600"><i class="fa-solid fa-qrcode text-lg"></i></div>
            <h3 class="mt-4 text-base font-extrabold text-slate-900">QRIS</h3>
            <p class="mt-1 text-xs text-slate-500">Scan untuk donasi melalui aplikasi pembayaran digital.</p>
            <img src="<?= base_url('assets/img/QRIS.png') ?>" alt="QRIS" class="mx-auto mt-5 w-52 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm">
        </div>

        <div class="flex flex-col justify-between rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600"><i class="fa-solid fa-hands-praying text-lg"></i></div>
                <h3 class="mt-4 text-base font-extrabold text-slate-900">Terima kasih</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-500">
                    Setiap dukungan, sekecil apa pun, sangat berarti untuk keberlanjutan pengembangan sistem ini.
                    Kami juga terbuka terhadap masukan dan kontribusi.
                </p>
            </div>

            <div class="mt-6 space-y-2">
                <?php if ($wa !== ''): ?>
                    <a href="https://wa.me/<?= esc($wa) ?>" target="_blank" rel="noopener" class="flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-emerald-700">
                        <i class="fa-brands fa-whatsapp"></i> Hubungi via WhatsApp
                    </a>
                <?php endif; ?>
                <a href="mailto:smohheri@gmail.com" class="flex items-center justify-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-50">
                    <i class="fa-solid fa-envelope"></i> smohheri@gmail.com
                </a>
                <a href="<?= site_url('donasi') ?>" target="_blank" class="flex items-center justify-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-50">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i> Halaman Donasi Publik
                </a>
            </div>
        </div>
    </div>
</div>
