<section class="relative overflow-hidden bg-emerald-950 py-16 sm:py-20">
    <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full bg-emerald-500/20 blur-3xl"></div>
    <div class="relative mx-auto w-full max-w-4xl px-4 text-center sm:px-6 lg:px-8">
        <p class="text-xs font-bold uppercase tracking-[0.2em] text-emerald-300">Lisensi</p>
        <h1 class="mt-3 text-3xl font-extrabold text-white sm:text-4xl">Lisensi Penggunaan Aplikasi</h1>
        <p class="mx-auto mt-4 max-w-2xl text-sm leading-relaxed text-emerald-100/70">
            Informasi lisensi dan kebijakan penggunaan aplikasi <?= esc($settings['nama_lksa'] ?? 'SAHABAT-NG') ?>.
        </p>
    </div>
</section>

<section class="bg-emerald-50/50 py-14 sm:py-20">
    <div class="mx-auto w-full max-w-4xl space-y-6 px-4 sm:px-6 lg:px-8">
        <div class="rounded-3xl border border-emerald-100 bg-white p-7 shadow-sm sm:p-10">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600"><i class="fa-solid fa-scale-balanced text-lg"></i></div>
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900">Lisensi Gratis</h2>
                    <p class="text-sm text-slate-500">Bebas digunakan untuk lembaga kesejahteraan sosial anak.</p>
                </div>
            </div>

            <div class="mt-7 space-y-6 text-sm leading-relaxed text-slate-600">
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
                    <h3 class="flex items-center gap-2 text-base font-bold text-emerald-800"><i class="fa-solid fa-circle-check"></i> Aplikasi Ini Gratis</h3>
                    <p class="mt-2 text-emerald-700">Aplikasi SAHABAT-NG dapat digunakan secara gratis oleh semua pengguna tanpa biaya lisensi atau biaya penggunaan.</p>
                </div>

                <p>Aplikasi ini dikembangkan untuk membantu lembaga kesejahteraan sosial anak dalam mengelola data dan program kesejahteraan anak secara lebih efektif dan efisien.</p>

                <div class="rounded-2xl border border-emerald-100 bg-emerald-50/50 p-5">
                    <h3 class="flex items-center gap-2 text-base font-bold text-emerald-800"><i class="fa-solid fa-heart"></i> Tidak Wajib Donasi</h3>
                    <p class="mt-2 text-emerald-700">Penggunaan aplikasi ini tidak mewajibkan donasi atau kontribusi finansial. Aplikasi disediakan secara gratis untuk mendukung misi kesejahteraan anak di Indonesia.</p>
                </div>

                <p>Meskipun donasi tidak diwajibkan, dukungan dari komunitas pengguna sangat dihargai dan akan membantu pengembangan fitur-fitur baru serta pemeliharaan aplikasi.</p>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl border border-emerald-100 bg-emerald-50/40 p-5 text-center">
                        <i class="fa-solid fa-hand-holding-heart text-2xl text-emerald-600"></i>
                        <h3 class="mt-3 text-base font-bold text-slate-900">Ingin Mendukung?</h3>
                        <p class="mt-2 text-xs text-slate-500">Salurkan donasi Anda untuk anak-anak asuh LKSA.</p>
                        <a href="<?= site_url('donasi') ?>" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-emerald-700">
                            <i class="fa-solid fa-hand-holding-heart"></i> Halaman Donasi
                        </a>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 text-center">
                        <i class="fa-brands fa-github text-2xl text-slate-800"></i>
                        <h3 class="mt-3 text-base font-bold text-slate-900">Kontribusi Kode</h3>
                        <p class="mt-2 text-xs text-slate-500">Bantu pengembangan melalui pull request di GitHub.</p>
                        <a href="https://github.com/smohheri/sahabat" target="_blank" rel="noopener" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-slate-800">
                            <i class="fa-brands fa-github"></i> Repository GitHub
                        </a>
                    </div>
                </div>

                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
                    <h3 class="flex items-center gap-2 text-base font-bold text-amber-800"><i class="fa-solid fa-triangle-exclamation"></i> Pembatasan Modifikasi</h3>
                    <p class="mt-2 text-amber-700">Modifikasi terhadap aplikasi ini hanya boleh dilakukan dengan sepengetahuan dan persetujuan dari developer. Setiap perubahan kode, fitur, atau struktur aplikasi harus dikonsultasikan terlebih dahulu untuk menjaga integritas dan keamanan sistem.</p>
                </div>

                <div>
                    <h3 class="text-base font-bold text-slate-900">Persyaratan Penggunaan</h3>
                    <ul class="mt-3 grid gap-2 sm:grid-cols-2">
                        <?php foreach ([
                            'Aplikasi digunakan untuk tujuan kesejahteraan sosial anak yang positif',
                            'Data anak yang dikelola harus dijaga kerahasiaannya',
                            'Pengguna bertanggung jawab atas keamanan data yang diinput',
                            'Laporan yang dihasilkan digunakan secara bertanggung jawab',
                            'Modifikasi aplikasi hanya dengan persetujuan developer',
                            'Menjaga integritas dan keamanan sistem aplikasi',
                        ] as $requirement): ?>
                            <li class="flex items-start gap-2 rounded-xl bg-emerald-50/50 px-3 py-2.5 text-xs text-slate-600">
                                <i class="fa-solid fa-circle-check mt-0.5 text-emerald-500"></i>
                                <span><?= esc($requirement) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <p>Dengan menggunakan aplikasi ini, Anda menyetujui persyaratan penggunaan di atas dan berkomitmen untuk menggunakan aplikasi ini sesuai dengan tujuan kesejahteraan sosial anak.</p>

                <div class="border-t border-emerald-100 pt-6">
                    <h3 class="text-base font-bold text-slate-900">Lisensi Teknis</h3>
                    <p class="mt-2">Aplikasi ini dibangun menggunakan framework CodeIgniter yang menggunakan lisensi MIT. Untuk informasi lengkap tentang lisensi teknis, silakan lihat file <code class="rounded bg-slate-100 px-1.5 py-0.5">license.txt</code> di root direktori aplikasi.</p>
                    <div class="mt-4 rounded-2xl border border-emerald-100 bg-emerald-50/40 p-5">
                        <h4 class="text-sm font-bold text-slate-800">The MIT License (MIT)</h4>
                        <p class="mt-2 text-xs text-slate-500">Copyright (c) 2019 - <?= date('Y') ?>, CodeIgniter Foundation</p>
                        <p class="mt-2 text-xs leading-relaxed text-slate-500">Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center">
            <a href="<?= site_url('/') ?>" class="inline-flex items-center gap-2 rounded-xl border border-emerald-100 bg-white px-5 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-emerald-50">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</section>