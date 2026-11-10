<?php
$heroes           = $heroes ?? [];
$firstHero        = $heroes[0] ?? null;
$heroImage        = $settings['hero_image'] ?? ($firstHero['image_name'] ?? null);
$heroTitleDefault = $settings['nama_lksa'] ?? 'LKSA Harapan Bangsa';
$heroSubDefault   = 'Membentuk Generasi Unggul & Berkarakter';
$heroDescDefault  = 'Mendukung tumbuh kembang anak melalui pendidikan, pengasuhan, dan pembinaan karakter yang berkelanjutan.';
$aboutImage       = $settings['about_image'] ?? $heroImage;

$statItems = [
    ['label' => 'Anak Terdaftar', 'value' => (int) ($stats['total'] ?? 0), 'icon' => 'children'],
    ['label' => 'Anak Aktif', 'value' => (int) ($stats['aktif'] ?? 0), 'icon' => 'child-reaching'],
    ['label' => 'Pengurus', 'value' => (int) ($stats['pengurus'] ?? 0), 'icon' => 'user-tie'],
    ['label' => 'Pengajar', 'value' => (int) ($stats['pengajar'] ?? 0), 'icon' => 'chalkboard-user'],
];

$programs = [
    ['icon' => 'book-open-reader', 'title' => 'Pendidikan', 'desc' => 'Dukungan pendidikan formal dan non-formal untuk anak asuh agar siap meraih masa depan.'],
    ['icon' => 'heart-pulse', 'title' => 'Kesehatan', 'desc' => 'Pemantauan kesehatan dan gizi untuk tumbuh kembang yang optimal.'],
    ['icon' => 'hands-holding-child', 'title' => 'Pengasuhan', 'desc' => 'Lingkungan asuh yang aman, hangat, dan penuh perhatian.'],
    ['icon' => 'star', 'title' => 'Karakter', 'desc' => 'Pembinaan karakter melalui asesmen dan pendampingan rutin.'],
];

// Distribusi pendidikan untuk grafik ringkas (dari statistik anak).
$education = [
    ['label' => 'TK', 'value' => (int) ($stats['tk'] ?? 0)],
    ['label' => 'SD/MI', 'value' => (int) ($stats['sd'] ?? 0)],
    ['label' => 'SMP/MTs', 'value' => (int) ($stats['smp'] ?? 0)],
    ['label' => 'SMA/SMK', 'value' => (int) ($stats['sma'] ?? 0)],
    ['label' => 'PT', 'value' => (int) ($stats['pt'] ?? 0)],
];
$educationMax = max(1, (int) max(array_column($education, 'value')));
?>

<!-- ================= Beranda / Hero ================= -->
<section id="beranda" class="relative overflow-hidden bg-emerald-950" x-data="{ hero: 0, heroTotal: <?= max(1, count($heroes)) ?> }" x-init="setInterval(() => { hero = (hero + 1) % heroTotal }, 5000)">
    <?php if (count($heroes) > 0): ?>
        <?php foreach ($heroes as $i => $h): ?>
            <div x-cloak x-show="hero === <?= $i ?>" x-transition.opacity.duration.700ms class="absolute inset-0">
                <?php if (! empty($h['image_name'])): ?>
                    <img src="<?= upload_url('landing', $h['image_name']) ?>" alt="" class="absolute inset-0 h-full w-full object-cover opacity-30">
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <?php if (! empty($heroImage)): ?>
            <img src="<?= upload_url('landing', $heroImage) ?>" alt="" class="absolute inset-0 h-full w-full object-cover opacity-30">
        <?php endif; ?>
    <?php endif; ?>

    <div class="absolute inset-0 bg-gradient-to-br from-emerald-950/95 via-emerald-900/85 to-teal-900/80"></div>
    <div class="absolute -right-24 top-10 h-72 w-72 rounded-full bg-emerald-500/20 blur-3xl"></div>
    <div class="absolute -left-20 bottom-0 h-80 w-80 rounded-full bg-teal-400/10 blur-3xl"></div>

    <div class="relative mx-auto w-full max-w-7xl px-4 pb-16 pt-20 sm:px-6 sm:pb-20 sm:pt-24 lg:px-8 lg:pt-28">
        <div class="max-w-3xl">
            <p class="mb-5 inline-flex items-center gap-2 rounded-full border border-emerald-400/20 bg-emerald-400/10 px-4 py-1.5 text-xs font-semibold text-emerald-200 backdrop-blur">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                <?= ! empty($settings['tahun_berdiri']) ? 'Berdiri sejak ' . esc($settings['tahun_berdiri']) : 'Lembaga Kesejahteraan Sosial Anak' ?>
            </p>

            <?php if (count($heroes) > 0): ?>
                <?php foreach ($heroes as $i => $h): ?>
                    <div x-cloak x-show="hero === <?= $i ?>" x-transition.opacity.duration.700ms>
                        <h1 class="text-4xl font-extrabold leading-[1.1] text-white sm:text-5xl lg:text-6xl">
                            <?= esc($h['title'] ?? $heroTitleDefault) ?>
                            <span class="mt-2 block bg-gradient-to-r from-emerald-300 to-teal-200 bg-clip-text text-transparent"><?= esc($h['subtitle'] ?? $heroSubDefault) ?></span>
                        </h1>
                        <p class="mt-6 max-w-2xl text-base leading-relaxed text-emerald-100/70 sm:text-lg">
                            <?= esc($h['description'] ?? $heroDescDefault) ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <h1 class="text-4xl font-extrabold leading-[1.1] text-white sm:text-5xl lg:text-6xl">
                    <?= esc($heroTitleDefault) ?>
                    <span class="mt-2 block bg-gradient-to-r from-emerald-300 to-teal-200 bg-clip-text text-transparent"><?= esc($heroSubDefault) ?></span>
                </h1>
                <p class="mt-6 max-w-2xl text-base leading-relaxed text-emerald-100/70 sm:text-lg">
                    <?= esc($heroDescDefault) ?>
                </p>
            <?php endif; ?>

            <div class="mt-9 flex flex-wrap items-center gap-3">
                <a href="#tentang" class="inline-flex items-center gap-2 rounded-xl bg-emerald-500 px-5 py-3 text-sm font-bold text-emerald-950 shadow-xl shadow-emerald-500/25 transition hover:bg-emerald-400">
                    <i class="fa-solid fa-circle-info"></i> Tentang Kami
                </a>
                <a href="<?= site_url('donasi') ?>" class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/5 px-5 py-3 text-sm font-bold text-white backdrop-blur transition hover:bg-white/10">
                    <i class="fa-solid fa-hand-holding-heart"></i> Dukung Program
                </a>
                <a href="<?= session()->get('is_logged_in') ? role_home((string) session()->get('role')) : site_url('login') ?>" class="inline-flex items-center gap-2 px-2 py-3 text-sm font-semibold text-emerald-100/60 transition hover:text-white">
                    <?= session()->get('is_logged_in') ? 'Buka Panel' : 'Masuk ke Sistem' ?> <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>

        <?php if (count($heroes) > 1): ?>
            <div class="mt-12 flex gap-2">
                <?php foreach ($heroes as $i => $h): ?>
                    <button type="button" @click="hero = <?= $i ?>" class="h-2 rounded-full transition-all" :class="hero === <?= $i ?> ? 'w-7 bg-emerald-400' : 'w-2 bg-white/30'"></button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- ================= Statistik ================= -->
<section class="relative z-10 -mt-10 px-4 sm:px-6 lg:px-8">
    <div class="mx-auto grid w-full max-w-7xl gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <?php foreach ($statItems as $item): ?>
            <div class="flex items-center gap-4 rounded-2xl border border-emerald-100 bg-white p-5 shadow-lg shadow-emerald-900/5">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-600/10 text-emerald-600">
                    <i class="fa-solid fa-<?= $item['icon'] ?> text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-extrabold text-slate-900"><?= number_format($item['value'], 0, ',', '.') ?></p>
                    <p class="text-xs font-medium text-slate-500"><?= esc($item['label']) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- ================= Tentang ================= -->
<section id="tentang" class="bg-white py-20 sm:py-24">
    <div class="mx-auto grid w-full max-w-7xl items-center gap-12 px-4 sm:px-6 lg:grid-cols-2 lg:px-8">
        <div class="relative">
            <div class="absolute -left-4 -top-4 h-full w-full rounded-3xl bg-emerald-100"></div>
            <?php if (! empty($aboutImage)): ?>
                <img src="<?= upload_url('landing', $aboutImage) ?>" alt="Tentang <?= esc($settings['nama_lksa'] ?? '') ?>" class="relative aspect-[4/3] w-full rounded-3xl object-cover shadow-xl">
            <?php else: ?>
                <div class="relative flex aspect-[4/3] w-full items-center justify-center rounded-3xl bg-emerald-600 shadow-xl">
                    <i class="fa-solid fa-children text-6xl text-white/80"></i>
                </div>
            <?php endif; ?>

            <div class="absolute -bottom-6 right-4 rounded-2xl border border-emerald-100 bg-white p-4 shadow-xl sm:right-8">
                <p class="text-3xl font-extrabold text-emerald-600"><?= number_format((int) ($stats['aktif'] ?? 0), 0, ',', '.') ?></p>
                <p class="text-xs font-semibold text-slate-500">Anak aktif dibina</p>
            </div>
        </div>

        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-emerald-600">Tentang Kami</p>
            <h2 class="mt-3 text-3xl font-extrabold leading-tight text-slate-900 sm:text-4xl">Tumbuh bersama dalam lingkungan yang penuh kasih</h2>
            <p class="mt-5 text-base leading-relaxed text-slate-600">
                <?= esc($settings['nama_lksa'] ?? 'LKSA Harapan Bangsa') ?> hadir untuk memastikan setiap anak mendapatkan hak atas pengasuhan, pendidikan, dan masa depan yang lebih baik.
            </p>

            <div class="mt-6 flex flex-wrap gap-x-6 gap-y-2 text-sm text-slate-500">
                <?php if (! empty($settings['tahun_berdiri'])): ?>
                    <span class="inline-flex items-center gap-2"><i class="fa-solid fa-calendar text-emerald-500"></i> Berdiri <?= esc($settings['tahun_berdiri']) ?></span>
                <?php endif; ?>
                <?php if (! empty($settings['nama_kepala'])): ?>
                    <span class="inline-flex items-center gap-2"><i class="fa-solid fa-user-tie text-emerald-500"></i> Kepala: <?= esc($settings['nama_kepala']) ?></span>
                <?php endif; ?>
            </div>

            <div class="mt-8 space-y-4">
                <div class="flex items-start gap-4 rounded-2xl border border-emerald-100 bg-emerald-50/50 p-5">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-600 text-white">
                        <i class="fa-solid fa-bullseye"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-900">Visi Kami</p>
                        <p class="mt-1 text-sm leading-relaxed text-slate-600">Mewujudkan anak asuh yang berkarakter, mandiri, dan berprestasi melalui pengasuhan serta pendidikan yang berkualitas.</p>
                    </div>
                </div>
                <div class="flex items-start gap-4 rounded-2xl border border-slate-100 bg-slate-50 p-5">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-900 text-white">
                        <i class="fa-solid fa-seedling"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-900">Misi Kami</p>
                        <p class="mt-1 text-sm leading-relaxed text-slate-600">Memberikan akses pendidikan, kesehatan, dan pendampingan karakter secara berkelanjutan bagi setiap anak yang kami asuh.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ================= Program ================= -->
<section id="program" class="bg-emerald-50/60 py-20 sm:py-24">
    <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-emerald-600">Program Unggulan</p>
            <h2 class="mt-3 text-3xl font-extrabold text-slate-900 sm:text-4xl">Layanan &amp; pembinaan utama</h2>
            <p class="mt-4 text-sm leading-relaxed text-slate-500">Empat pilar yang menopang tumbuh kembang anak secara menyeluruh.</p>
        </div>

        <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <?php foreach ($programs as $program): ?>
                <div class="group rounded-3xl border border-emerald-100 bg-white p-6 transition hover:-translate-y-1 hover:border-emerald-200 hover:shadow-xl">
                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-600/10 text-xl text-emerald-600 transition group-hover:bg-emerald-600 group-hover:text-white">
                        <i class="fa-solid fa-<?= $program['icon'] ?>"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-900"><?= esc($program['title']) ?></h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-500"><?= esc($program['desc']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (array_sum(array_column($education, 'value')) > 0): ?>
            <div class="mt-12 grid items-center gap-6 rounded-3xl border border-emerald-100 bg-white p-6 sm:p-8 lg:grid-cols-2">
                <div>
                    <h3 class="text-lg font-extrabold text-slate-900">Sebaran jenjang pendidikan anak asuh</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-500">Distribusi anak berdasarkan jenjang pendidikan yang sedang ditempuh.</p>
                </div>
                <div class="space-y-3">
                    <?php foreach ($education as $edu): ?>
                        <div>
                            <div class="flex items-center justify-between text-xs font-semibold">
                                <span class="text-slate-600"><?= esc($edu['label']) ?></span>
                                <span class="text-emerald-600"><?= number_format($edu['value'], 0, ',', '.') ?></span>
                            </div>
                            <div class="mt-1.5 h-2.5 overflow-hidden rounded-full bg-emerald-100">
                                <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-400" style="width: <?= round($edu['value'] / $educationMax * 100) ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- ================= Fasilitas ================= -->
<section id="fasilitas" class="bg-white py-20 sm:py-24">
    <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-emerald-600">Fasilitas</p>
            <h2 class="mt-3 text-3xl font-extrabold text-slate-900 sm:text-4xl">Sarana pendukung tumbuh kembang anak</h2>
            <p class="mt-4 text-sm leading-relaxed text-slate-500">Lingkungan yang nyaman dan aman untuk belajar, bermain, dan berkembang.</p>
        </div>

        <?php if ($facilities !== []): ?>
            <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <?php foreach ($facilities as $facility): ?>
                    <div class="group overflow-hidden rounded-3xl border border-slate-100 bg-white transition hover:-translate-y-1 hover:border-emerald-100 hover:shadow-xl">
                        <?php if (! empty($facility['gambar'])): ?>
                            <img src="<?= upload_url('landing', $facility['gambar']) ?>" alt="<?= esc($facility['nama_fasilitas']) ?>" class="h-36 w-full object-cover">
                        <?php else: ?>
                            <div class="flex h-36 w-full items-center justify-center bg-emerald-50 text-emerald-400">
                                <i class="fa-solid fa-<?= esc($facility['icon'] ?: 'building') ?> text-3xl"></i>
                            </div>
                        <?php endif; ?>
                        <div class="p-5">
                            <h3 class="text-sm font-bold text-slate-900"><?= esc($facility['nama_fasilitas']) ?></h3>
                            <p class="mt-1.5 text-xs leading-relaxed text-slate-500"><?= esc($facility['deskripsi'] ?? '') ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="mt-10 text-center text-sm text-slate-500">Data fasilitas belum tersedia.</p>
        <?php endif; ?>
    </div>
</section>

<!-- ================= Galeri ================= -->
<?php if ($carousel !== []): ?>
<section id="galeri" class="bg-emerald-50/60 py-20 sm:py-24">
    <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-emerald-600">Galeri</p>
            <h2 class="mt-3 text-3xl font-extrabold text-slate-900 sm:text-4xl">Momen &amp; kegiatan</h2>
        </div>

        <div class="relative mt-12 overflow-hidden rounded-3xl bg-emerald-950" x-data="{ active: 0, total: <?= count($carousel) ?> }">
            <div class="relative aspect-[16/9] w-full sm:aspect-[21/9]">
                <?php foreach ($carousel as $index => $image): ?>
                    <div x-show="active === <?= $index ?>" x-transition.opacity.duration.500ms class="absolute inset-0">
                        <img src="<?= upload_url('landing', $image['image_name']) ?>" alt="<?= esc($image['title'] ?? 'Galeri') ?>" class="h-full w-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-emerald-950/90 via-emerald-950/30 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-6 sm:p-10">
                            <h3 class="text-xl font-extrabold text-white sm:text-2xl"><?= esc($image['title'] ?? '') ?></h3>
                            <?php if (! empty($image['description'])): ?>
                                <p class="mt-1 max-w-2xl text-sm text-emerald-100/70"><?= esc($image['description']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="button" @click="active = active === 0 ? total - 1 : active - 1" class="absolute left-4 top-1/2 z-10 -translate-y-1/2 rounded-full bg-white/20 p-3 text-white backdrop-blur transition hover:bg-white/30">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <button type="button" @click="active = active === total - 1 ? 0 : active + 1" class="absolute right-4 top-1/2 z-10 -translate-y-1/2 rounded-full bg-white/20 p-3 text-white backdrop-blur transition hover:bg-white/30">
                <i class="fa-solid fa-chevron-right"></i>
            </button>

            <div class="absolute bottom-4 left-1/2 z-10 flex -translate-x-1/2 gap-2">
                <?php foreach ($carousel as $index => $image): ?>
                    <button type="button" @click="active = <?= $index ?>" class="h-2 rounded-full transition-all" :class="active === <?= $index ?> ? 'w-7 bg-emerald-400' : 'w-2 bg-white/50'"></button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ================= CTA ================= -->
<section class="relative overflow-hidden bg-emerald-600 py-16 sm:py-20">
    <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full bg-emerald-400/30 blur-3xl"></div>
    <div class="absolute -bottom-20 -left-10 h-72 w-72 rounded-full bg-teal-300/20 blur-3xl"></div>
    <div class="relative mx-auto flex w-full max-w-7xl flex-col items-start justify-between gap-8 px-4 sm:px-6 lg:flex-row lg:items-center lg:px-8">
        <div class="max-w-2xl">
            <h2 class="text-3xl font-extrabold text-white sm:text-4xl">Bersama membangun masa depan anak</h2>
            <p class="mt-3 text-sm leading-relaxed text-emerald-50 sm:text-base">
                Setiap dukungan Anda membantu anak-anak mendapatkan pendidikan, pengasuhan, dan kesempatan untuk tumbuh dengan baik.
            </p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="<?= site_url('donasi') ?>" class="inline-flex items-center gap-2 rounded-xl bg-emerald-950 px-5 py-3 text-sm font-bold text-white shadow-xl transition hover:bg-emerald-900">
                <i class="fa-solid fa-hand-holding-heart"></i> Donasi Sekarang
            </a>
            <a href="<?= site_url('/') ?>#fasilitas" class="inline-flex items-center gap-2 rounded-xl border border-white/30 px-5 py-3 text-sm font-bold text-white transition hover:bg-white/10">
                <i class="fa-solid fa-images"></i> Lihat Fasilitas
            </a>
        </div>
    </div>
</section>