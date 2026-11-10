<?php
$namaLksa   = $settings['nama_lksa'] ?? 'LKSA Harapan Bangsa';
$bankNama   = ! empty($settings['bank_nama']) ? esc($settings['bank_nama']) : '';
$bankNo     = ! empty($settings['bank_nomor_rekening']) ? esc($settings['bank_nomor_rekening']) : '';
$bankAtas   = ! empty($settings['bank_atas_nama']) ? esc($settings['bank_atas_nama']) : '';
$qris       = $settings['qris_image'] ?? null;
$wa         = nomor_wa((string) ($settings['whatsapp'] ?? $settings['no_telp'] ?? ''));

$tujuan = [
    ['icon' => 'book-open-reader', 'title' => 'Pendidikan', 'desc' => 'Biaya sekolah, perlengkapan belajar, dan bimbingan belajar.'],
    ['icon' => 'utensils', 'title' => 'Pangan & Gizi', 'desc' => 'Makanan bergizi dan kebutuhan pokok sehari-hari anak.'],
    ['icon' => 'bed', 'title' => 'Pengasuhan & Hunian', 'desc' => 'Asrama yang aman, nyaman, dan layak untuk anak asuh.'],
    ['icon' => 'heart-pulse', 'title' => 'Kesehatan', 'desc' => 'Pemeriksaan kesehatan, obat, dan perawatan anak.'],
];
?>
<section class="relative overflow-hidden bg-emerald-950 py-16 sm:py-20">
    <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full bg-emerald-500/20 blur-3xl"></div>
    <div class="absolute -bottom-20 -left-10 h-72 w-72 rounded-full bg-teal-300/10 blur-3xl"></div>
    <div class="relative mx-auto w-full max-w-4xl px-4 text-center sm:px-6 lg:px-8">
        <p class="text-xs font-bold uppercase tracking-[0.2em] text-emerald-300">Donasi</p>
        <h1 class="mt-3 text-3xl font-extrabold text-white sm:text-4xl">Donasi untuk <?= esc($namaLksa) ?></h1>
        <p class="mx-auto mt-4 max-w-2xl text-sm leading-relaxed text-emerald-100/70">
            Setiap donasi Anda tersalurkan langsung untuk pendidikan, pengasuhan, dan kesejahteraan anak-anak asuh kami.
        </p>
    </div>
</section>

<section class="bg-emerald-50/50 py-14 sm:py-20">
    <div class="mx-auto w-full max-w-5xl space-y-8 px-4 sm:px-6 lg:px-8">
        <!-- Tujuan donasi -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <?php foreach ($tujuan as $item): ?>
                <div class="rounded-3xl border border-emerald-100 bg-white p-6 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-600/10 text-xl text-emerald-600">
                        <i class="fa-solid fa-<?= $item['icon'] ?>"></i>
                    </div>
                    <h2 class="mt-4 text-sm font-bold text-slate-900"><?= esc($item['title']) ?></h2>
                    <p class="mt-1.5 text-xs leading-relaxed text-slate-500"><?= esc($item['desc']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Saluran donasi -->
        <div class="grid gap-5 lg:grid-cols-2">
            <?php if ($bankNo !== ''): ?>
                <div class="overflow-hidden rounded-3xl border border-emerald-100 bg-white shadow-sm">
                    <div class="flex items-center gap-3 border-b border-emerald-100 bg-emerald-50/60 px-6 py-4">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-600/10 text-emerald-600">
                            <i class="fa-solid fa-building-columns"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-900">Transfer Bank<?= $bankNama !== '' ? ' ' . $bankNama : '' ?></p>
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Rekening Resmi LKSA</p>
                        </div>
                    </div>

                    <div class="p-6" x-data="{ copied: false }">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Nomor Rekening</p>
                        <div class="mt-2 flex items-center gap-3">
                            <p class="font-mono text-2xl font-extrabold tracking-wide text-slate-900"><?= $bankNo ?></p>
                            <button type="button"
                                    @click="navigator.clipboard.writeText('<?= esc($bankNo, 'js') ?>'); copied = true; setTimeout(() => copied = false, 2000)"
                                    class="rounded-lg border border-emerald-100 p-2 text-slate-500 transition hover:bg-emerald-50 hover:text-emerald-700">
                                <i class="fa-solid" :class="copied ? 'fa-check text-emerald-500' : 'fa-copy'"></i>
                            </button>
                        </div>
                        <?php if ($bankAtas !== ''): ?>
                            <p class="mt-3 flex items-center gap-2 text-sm text-slate-500">
                                <i class="fa-solid fa-user text-slate-400"></i> a.n. <?= $bankAtas ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (! empty($qris)): ?>
                <div class="rounded-3xl border border-emerald-100 bg-white p-7 text-center shadow-sm">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                        <i class="fa-solid fa-qrcode text-lg"></i>
                    </div>
                    <h2 class="mt-4 text-lg font-extrabold text-slate-900">Pembayaran Digital (QRIS)</h2>
                    <p class="mt-1 text-sm text-slate-500">Scan kode QRIS untuk donasi cepat dan mudah.</p>
                    <img src="<?= upload_url('qris', $qris) ?>" alt="QR Code QRIS" class="mx-auto mt-5 w-56 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm">
                    <p class="mt-3 text-xs font-semibold uppercase tracking-wider text-slate-400">QRIS</p>
                </div>
            <?php else: ?>
                <div class="flex flex-col justify-between rounded-3xl border border-emerald-100 bg-white p-7 shadow-sm">
                    <div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                            <i class="fa-solid fa-hands-praying text-lg"></i>
                        </div>
                        <h2 class="mt-4 text-lg font-extrabold text-slate-900">Terima Kasih atas Kebaikan Anda</h2>
                        <p class="mt-2 text-sm leading-relaxed text-slate-500">
                            Donasi Anda memberikan harapan dan masa depan yang lebih baik bagi anak-anak yang kami asuh.
                        </p>
                    </div>

                    <div class="mt-6 rounded-2xl border border-emerald-100 bg-emerald-50/60 p-4">
                        <p class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                            <i class="fa-solid fa-heart text-rose-500"></i> Setiap dukungan berarti bagi anak-anak
                        </p>
                        <?php if ($wa !== ''): ?>
                            <a href="https://wa.me/<?= esc($wa) ?>" target="_blank" rel="noopener" class="mt-3 inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-emerald-700">
                                <i class="fa-brands fa-whatsapp"></i> Hubungi Kami untuk Berdonasi
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Kontak -->
        <div class="rounded-3xl border border-emerald-100 bg-white p-6 sm:p-8">
            <div class="flex flex-col items-center justify-between gap-4 text-center sm:flex-row sm:text-left">
                <div>
                    <h2 class="text-lg font-extrabold text-slate-900">Punya pertanyaan tentang donasi?</h2>
                    <p class="mt-1 text-sm text-slate-500">Tim kami siap membantu Anda menyalurkan kebaikan dengan tepat.</p>
                </div>
                <div class="flex flex-wrap justify-center gap-2.5">
                    <?php if ($wa !== ''): ?>
                        <a href="https://wa.me/<?= esc($wa) ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-emerald-700">
                            <i class="fa-brands fa-whatsapp"></i> WhatsApp
                        </a>
                    <?php endif; ?>
                    <?php if (! empty($settings['email'])): ?>
                        <a href="mailto:<?= esc($settings['email']) ?>" class="inline-flex items-center gap-2 rounded-xl border border-emerald-100 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 transition hover:bg-emerald-50">
                            <i class="fa-solid fa-envelope"></i> Email
                        </a>
                    <?php endif; ?>
                    <a href="<?= site_url('/') ?>" class="inline-flex items-center gap-2 rounded-xl border border-emerald-100 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 transition hover:bg-emerald-50">
                        <i class="fa-solid fa-arrow-left"></i> Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>