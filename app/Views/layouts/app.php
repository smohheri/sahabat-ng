<?php
$role = strtolower((string) ($currentUser['role'] ?? ''));
?>
<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <title><?= esc($title ?? 'Dashboard') ?></title>
    <link rel="icon" href="<?= favicon_url() ?>">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.2/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <script>
        // Komponen searchable-select (ala Select2) untuk pilihan Wilayah.
        document.addEventListener('alpine:init', () => {
            Alpine.data('regionPicker', (config = {}) => ({
                provinsi: config.provinsi || [],
                kabupaten: config.kabupaten || [],
                provinsiId: config.provinsiId || '',
                kabupatenId: config.kabupatenId || '',

                provinsiOpen: false,
                kabupatenOpen: false,
                provinsiSearch: '',
                kabupatenSearch: '',

                get filteredProvinsi() {
                    const s = this.provinsiSearch.trim().toLowerCase();
                    return s === '' ? this.provinsi : this.provinsi.filter(p => String(p.label).toLowerCase().includes(s));
                },
                get provinsiLabel() {
                    const p = this.provinsi.find(x => String(x.value) === String(this.provinsiId));
                    return p ? p.label : '';
                },
                selectProvinsi(p) {
                    this.provinsiId = String(p.value);
                    this.kabupatenId = '';
                    this.provinsiOpen = false;
                    this.provinsiSearch = '';
                },

                get daftarKabupaten() {
                    if (this.provinsiId === '') {
                        return this.kabupaten;
                    }
                    return this.kabupaten.filter(k => String(k.provinsi) === String(this.provinsiId));
                },
                get filteredKabupaten() {
                    const s = this.kabupatenSearch.trim().toLowerCase();
                    return s === '' ? this.daftarKabupaten : this.daftarKabupaten.filter(k => String(k.label).toLowerCase().includes(s));
                },
                get kabupatenLabel() {
                    const k = this.kabupaten.find(x => String(x.value) === String(this.kabupatenId));
                    return k ? k.label : '';
                },
                selectKabupaten(k) {
                    this.kabupatenId = String(k.value);
                    this.kabupatenOpen = false;
                    this.kabupatenSearch = '';
                },
            }));
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                },
            },
        };
    </script>

    <style>
        [x-cloak] { display: none !important; }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
        ::-webkit-scrollbar-track { background: transparent; }
        .scrollbar-hidden { scrollbar-width: none; -ms-overflow-style: none; }
        .scrollbar-hidden::-webkit-scrollbar { display: none; }
    </style>

    <?= $this->renderSection('head') ?>
</head>
<body class="h-full font-sans text-slate-800 antialiased" x-data="{ mobileSidebar: false }">
    <div class="flex h-full">
        <!-- Desktop sidebar -->
        <div class="hidden lg:block">
            <?= view('partials/sidebar', ['currentUser' => $currentUser, 'settings' => $settings]) ?>
        </div>

        <!-- Mobile sidebar -->
        <div x-cloak x-show="mobileSidebar" class="fixed inset-0 z-40 lg:hidden">
            <div class="absolute inset-0 bg-slate-900/60" @click="mobileSidebar = false"></div>
            <div class="relative z-50 h-full w-72 max-w-[85%]">
                <?= view('partials/sidebar', ['currentUser' => $currentUser, 'settings' => $settings]) ?>
            </div>
        </div>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/90 backdrop-blur">
                <div class="flex h-16 items-center gap-4 px-4 sm:px-6 lg:px-8">
                    <button type="button" @click="mobileSidebar = true" class="rounded-lg border border-slate-200 p-2 text-slate-600 lg:hidden">
                        <i class="fa-solid fa-bars"></i>
                    </button>

                    <div class="min-w-0 flex-1">
                        <h1 class="truncate text-lg font-semibold text-slate-900"><?= esc($pageTitle ?? $title ?? 'Dashboard') ?></h1>
                        <p class="hidden text-xs text-slate-500 sm:block"><?= esc($settings['nama_lksa'] ?? 'LKSA Harapan Bangsa') ?></p>
                    </div>

                    <div x-data="{ open: false }" class="relative">
                        <button type="button" @click="open = !open" class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-3 py-2 text-left transition hover:bg-slate-50">
                            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-600 text-sm font-semibold text-white">
                                <?= esc(strtoupper(mb_substr((string) ($currentUser['nama'] ?? 'U'), 0, 1))) ?>
                            </span>
                            <span class="hidden sm:block">
                                <span class="block text-sm font-semibold leading-4 text-slate-800"><?= esc($currentUser['nama'] ?? 'Pengguna') ?></span>
                                <span class="block text-[11px] capitalize text-slate-500"><?= esc($currentUser['role'] ?? '') ?></span>
                            </span>
                            <i class="fa-solid fa-chevron-down text-xs text-slate-400"></i>
                        </button>

                        <div x-cloak x-show="open" @click.outside="open = false" class="absolute right-0 z-50 mt-2 w-48 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl">
                            <div class="border-b border-slate-100 px-4 py-3">
                                <p class="truncate text-sm font-semibold text-slate-800"><?= esc($currentUser['nama'] ?? '') ?></p>
                                <p class="truncate text-xs text-slate-500">@<?= esc($currentUser['username'] ?? '') ?></p>
                            </div>
                            <a href="<?= site_url('logout') ?>" class="flex items-center gap-2 px-4 py-2.5 text-sm text-rose-600 transition hover:bg-rose-50">
                                <i class="fa-solid fa-right-from-bracket"></i> Keluar
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto scrollbar-hidden p-4 sm:p-6 lg:p-8">
                <div class="w-full">
                    <?= view('partials/flash') ?>
                    <?= $content ?>
                </div>
            </main>

            <footer class="border-t border-slate-200 bg-white px-4 py-3 text-center text-xs text-slate-500 sm:px-6 lg:px-8">
                &copy; <?= date('Y') ?> <?= esc($settings['nama_lksa'] ?? 'LKSA Harapan Bangsa') ?> &middot; SAHABAT-NG
            </footer>
        </div>
    </div>

    <!-- Modal konfirmasi -->
    <div id="confirm-modal" class="fixed inset-0 z-[70] hidden items-center justify-center p-4" role="dialog" aria-modal="true">
        <div id="confirm-backdrop" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
        <div class="relative w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-slate-200">
            <div class="p-6">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-amber-100 text-amber-600">
                    <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                </div>
                <h3 class="mt-4 text-center text-base font-bold text-slate-900">Konfirmasi</h3>
                <p id="confirm-message" class="mt-2 text-center text-sm leading-relaxed text-slate-500"></p>
            </div>
            <div class="flex justify-end gap-2 border-t border-slate-100 bg-slate-50 px-6 py-4">
                <button type="button" id="confirm-cancel" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-50">Batal</button>
                <button type="button" id="confirm-ok" class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 px-5 py-2.5 text-xs font-bold text-white transition hover:bg-indigo-700"><i class="fa-solid fa-check"></i> Ya, Lanjutkan</button>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const modal = document.getElementById('confirm-modal');
            const messageEl = document.getElementById('confirm-message');
            const okBtn = document.getElementById('confirm-ok');
            const cancelBtn = document.getElementById('confirm-cancel');
            const backdrop = document.getElementById('confirm-backdrop');
            let pendingForm = null;

            function open(message, form) {
                messageEl.textContent = message;
                pendingForm = form;
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function close() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                pendingForm = null;
            }

            okBtn.addEventListener('click', function () {
                if (pendingForm) {
                    pendingForm.submit();
                }
                close();
            });

            cancelBtn.addEventListener('click', close);
            backdrop.addEventListener('click', close);

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    close();
                }
            });

            document.addEventListener('submit', function (event) {
                const form = event.target;
                if (!(form instanceof HTMLFormElement)) return;
                if (form.dataset.confirm) {
                    event.preventDefault();
                    open(form.dataset.confirm, form);
                }
            });
        })();
    </script>

    <script>
        // Pastikan menu sidebar yang aktif terlihat sesuai posisinya saat halaman dibuka.
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.sidebar-nav').forEach(function (nav) {
                const active = nav.querySelector('a.bg-indigo-600');
                if (active) {
                    active.scrollIntoView({ block: 'center' });
                }
            });
        });
    </script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
