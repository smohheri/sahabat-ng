<?php
$role = strtolower((string) ($currentUser['role'] ?? ''));
?>
<!DOCTYPE html>
<html lang="id" class="h-full bg-stone-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <title><?= esc($title ?? 'Dashboard') ?> · SAHABAT-NG</title>
    <link rel="icon" href="<?= favicon_url() ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
                    colors: { brand: { 50:'#ecfdf5',100:'#d1fae5',200:'#a7f3d0',300:'#6ee7b7',400:'#34d399',500:'#10b981',600:'#059669',700:'#047857',800:'#065f46',900:'#064e3b' } }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.2/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('regionPicker', (config = {}) => ({
                provinsi: config.provinsi || [],
                kabupaten: config.kabupaten || [],
                provinsiId: config.initialProvinsiId ? String(config.initialProvinsiId) : '',
                kabupatenId: config.initialKabupatenId ? String(config.initialKabupatenId) : '',
                provinsiOpen: false,
                kabupatenOpen: false,
                provinsiQuery: '',
                kabupatenQuery: '',
                get provinsiName() {
                    const found = this.provinsi.find(item => String(item.id) === String(this.provinsiId));
                    return found ? found.name : '';
                },
                get kabupatenName() {
                    const found = this.kabupaten.find(item => String(item.id) === String(this.kabupatenId));
                    return found ? found.name : '';
                },
                get filteredProvinsi() {
                    const q = this.provinsiQuery.toLowerCase().trim();
                    if (!q) return this.provinsi;
                    return this.provinsi.filter(item => item.name.toLowerCase().includes(q));
                },
                get filteredKabupaten() {
                    const q = this.kabupatenQuery.toLowerCase().trim();
                    if (!q) return this.kabupaten;
                    return this.kabupaten.filter(item => item.name.toLowerCase().includes(q));
                },
                init() {
                    if (this.provinsiId) {
                        this.loadKabupaten(this.provinsiId, this.kabupatenId);
                    }
                },
                selectProvinsi(item) {
                    this.provinsiId = String(item.id);
                    this.provinsiQuery = '';
                    this.provinsiOpen = false;
                    this.loadKabupaten(item.id, '');
                },
                selectKabupaten(item) {
                    this.kabupatenId = String(item.id);
                    this.kabupatenQuery = '';
                    this.kabupatenOpen = false;
                },
                async loadKabupaten(provId, selectedId) {
                    this.kabupaten = [];
                    this.kabupatenId = selectedId ? String(selectedId) : '';
                    if (!provId) return;
                    try {
                        const response = await fetch('<?= site_url('wilayah/kabupaten/') ?>' + provId);
                        if (!response.ok) throw new Error('Gagal memuat kabupaten');
                        this.kabupaten = await response.json();
                    } catch (error) {
                        this.kabupaten = [];
                    }
                }
            }));
        });
    </script>
    <style>[x-cloak]{display:none !important}</style>
    <?= $this->renderSection('head') ?>
</head>
<body class="h-full font-sans text-stone-800 antialiased">
    <div x-data="{ sidebarOpen: false }" class="flex h-full min-h-screen bg-stone-100">
        <div x-cloak x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-stone-900/50 lg:hidden"></div>

        <div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-72 transition-transform duration-200 lg:static lg:translate-x-0">
            <?= view('partials/sidebar') ?>
        </div>

        <div class="flex min-h-screen min-w-0 flex-1 flex-col">
            <header class="sticky top-0 z-30 border-b border-stone-200 bg-white">
                <div class="flex items-center justify-between gap-3 px-4 py-3 sm:px-6 lg:px-8">
                    <div class="flex min-w-0 items-center gap-3">
                        <button type="button" @click="sidebarOpen = true" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-stone-200 text-stone-600 hover:bg-stone-50 lg:hidden" aria-label="Buka menu">
                            <i class="fa-solid fa-bars"></i>
                        </button>
                        <div class="min-w-0">
                            <h1 class="truncate text-base font-bold text-stone-900 sm:text-lg"><?= esc($title ?? 'Dashboard') ?></h1>
                            <p class="truncate text-xs text-stone-500"><?= esc($settings['nama_lksa'] ?? 'SAHABAT-NG') ?></p>
                        </div>
                    </div>

                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = !open" class="flex items-center gap-2 rounded-xl border border-stone-200 bg-white px-3 py-2 text-sm text-stone-700 hover:bg-stone-50">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100 font-semibold text-emerald-700">
                                <?= esc(mb_strtoupper(mb_substr($currentUser['nama'] ?? 'U', 0, 1))) ?>
                            </span>
                            <span class="hidden max-w-[10rem] truncate sm:inline"><?= esc($currentUser['nama'] ?? 'User') ?></span>
                            <i class="fa-solid fa-chevron-down text-xs text-stone-400"></i>
                        </button>

                        <div x-cloak x-show="open" @click.outside="open = false" class="absolute right-0 z-50 mt-2 w-52 overflow-hidden rounded-xl border border-stone-200 bg-white shadow-lg">
                            <div class="border-b border-stone-100 px-4 py-3">
                                <p class="truncate text-sm font-semibold text-stone-800"><?= esc($currentUser['nama'] ?? '') ?></p>
                                <p class="truncate text-xs text-stone-500">@<?= esc($currentUser['username'] ?? '') ?></p>
                            </div>
                            <a href="<?= site_url('logout') ?>" class="flex items-center gap-2 px-4 py-2.5 text-sm text-rose-600 hover:bg-rose-50">
                                <i class="fa-solid fa-right-from-bracket"></i> Keluar
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                <div class="w-full">
                    <?= view('partials/flash') ?>
                    <?= $content ?>
                </div>
            </main>

            <footer class="border-t border-stone-200 bg-white px-4 py-3 text-center text-xs text-stone-500 sm:px-6 lg:px-8">
                &copy; <?= date('Y') ?> <?= esc($settings['nama_lksa'] ?? 'LKSA Harapan Bangsa') ?> · SAHABAT-NG
            </footer>
        </div>
    </div>

    <div id="confirm-modal" class="fixed inset-0 z-[70] hidden items-center justify-center p-4" role="dialog" aria-modal="true">
        <div id="confirm-backdrop" class="absolute inset-0 bg-stone-900/50"></div>
        <div class="relative w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-xl ring-1 ring-stone-200">
            <div class="flex items-start gap-4 px-5 pt-5">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-600">
                    <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <h3 class="text-base font-bold text-stone-900">Konfirmasi tindakan</h3>
                    <p id="confirm-message" class="mt-1.5 text-sm leading-relaxed text-stone-600"></p>
                </div>
            </div>
            <div class="mt-5 flex items-center justify-end gap-2 border-t border-stone-100 bg-stone-50 px-5 py-3">
                <button type="button" id="confirm-cancel" class="rounded-xl border border-stone-200 bg-white px-4 py-2 text-xs font-semibold text-stone-600 hover:bg-stone-100">Batal</button>
                <button type="button" id="confirm-accept" class="btn-primary">Ya, lanjutkan</button>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const modal = document.getElementById('confirm-modal');
            if (!modal) return;
            const backdrop = document.getElementById('confirm-backdrop');
            const message = document.getElementById('confirm-message');
            const btnCancel = document.getElementById('confirm-cancel');
            const btnAccept = document.getElementById('confirm-accept');
            let pendingForm = null;
            function open(text, form) {
                pendingForm = form;
                message.textContent = text || 'Lanjutkan tindakan ini?';
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                btnAccept.focus();
            }
            function close() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                pendingForm = null;
            }
            btnCancel.addEventListener('click', close);
            backdrop.addEventListener('click', close);
            btnAccept.addEventListener('click', function () {
                if (pendingForm) { const form = pendingForm; pendingForm = null; close(); form.submit(); } else { close(); }
            });
            document.addEventListener('keydown', function (event) { if (event.key === 'Escape') close(); });
            document.addEventListener('submit', function (event) {
                const form = event.target;
                if (!(form instanceof HTMLFormElement)) return;
                if (form.dataset.confirm) { event.preventDefault(); open(form.dataset.confirm, form); }
            });
        })();

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.sidebar-nav').forEach(function (nav) {
                const active = nav.querySelector('a[data-active="1"]');
                if (active) { active.scrollIntoView({ block: 'center' }); }
            });
        });
    </script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
