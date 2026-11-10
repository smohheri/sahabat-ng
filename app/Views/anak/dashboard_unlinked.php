<div class="flex min-h-[60vh] items-center justify-center">
    <div class="w-full max-w-lg rounded-3xl border border-amber-200 bg-white p-8 text-center shadow-sm">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-amber-100 text-amber-600">
            <i class="fa-solid fa-link-slash text-2xl"></i>
        </div>
        <h2 class="mt-5 text-xl font-extrabold text-slate-900">Akun belum terhubung</h2>
        <p class="mt-2 text-sm leading-relaxed text-slate-500">
            Akun Anda belum dikaitkan dengan data anak asuh. Silakan hubungi admin atau pengurus untuk menautkan akun ini dengan data Anda.
        </p>
        <div class="mt-6 flex flex-col gap-2 sm:flex-row sm:justify-center">
            <a href="<?= site_url('logout') ?>" class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-xs font-bold text-white transition hover:bg-slate-800">
                <i class="fa-solid fa-right-from-bracket"></i> Keluar
            </a>
            <?php if (! empty($settings['no_telp']) || ! empty($settings['whatsapp'])): ?>
                <?php $wa = nomor_wa((string) ($settings['whatsapp'] ?? $settings['no_telp'])); ?>
                <a href="https://wa.me/<?= esc($wa) ?>" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 px-5 py-2.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-50">
                    <i class="fa-brands fa-whatsapp text-emerald-600"></i> Hubungi Pengurus
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>
