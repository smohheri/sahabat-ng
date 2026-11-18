# PRD — Revamp UI SAHABAT-NG (Landing + Panel)

## 1. Tujuan
Merombak seluruh halaman (landing page, auth, panel admin/guru/anak) agar tampilan tidak lagi terlihat "AI slop" (gradient hero blur, ALL-CAPS eyebrow, card seragam, palet default, layout prediktabel) dan menjadi UI yang hangat, terpercaya, manusiawi, sesuai konteks LKSA (kesejahteraan anak).

## 2. Scope
- Landing: `home.php`, `donasi.php`, `license.php`, layout `landing.php`
- Auth: `login.php`, layout `auth.php`
- Panel: layout `app.php`, `sidebar.php`, `flash.php`, `pagination.php`, semua views admin/guru/anak/akademik (±50 file)
- Shared CSS: `public/assets/css/app.css`

## 3. Design Tokens
### Palet (brand tunggal: teal)
- Primary: emerald-700 `#059669` (hover emerald-800)
- Accent hangat: amber-600 `#d97706` (tetap, sebagai warnakan sekunder)
- Neutral: stone (warm) menggantikan slate (tetap sebagai warna dasar)
- Status: emerald digunakan sebagai primary/accent, rose=danger, amber=warning, sky=info (tetap)
### Tipografi
- Sans: Plus Jakarta Sans (400/500/600/700/800) — di-import sungguh-sungguh di semua layout. Tidak pakai Inter.
- Heading: font-extrabold tracking-tight; body tracking-normal (bukan tracked uppercase kickers).
### Radius & spacing
- Card: rounded-2xl; button/input: rounded-xl; badge: rounded-full
- Spacing konsisten 4/6/8
### Shadow
- Card: shadow-sm (bukan shadow-xl berlebihan); hindari colored glow shadow dekoratif
### Motion
- Transisi 150–200ms ease; hindari animasi dekoratif/blur orb/gradient berlapis; hormati prefers-reduced-motion

## 4. Pola yang Dihapus (AI slop)
- Blur orb/backdrop-blur dekoratif di hero/card/header/modal overlay
- ALL-CAPS tracked eyebrow dekoratif (tracking lebar/uppercase kickers) di section
- Card seragam `rounded-2xl border shadow-sm` tanpa variasi
- Palet default Tailwind (indigo/slate/teal) → remap ke emerald/stone
- `scrollbar-hidden` di sidebar
- Shadow glow berwarna pada CTA dan gradient overlay dekoratif
- Empty state generik satu pola

## 5. Komponen Standar
- `.btn-primary`, `.btn-secondary`, `.btn-ghost`, `.btn-danger`
- `.card`, `.card-header`
- `.input`, `.label`
- `.badge` + tone
- `.empty-state`
- `.page-header` (judul + deskripsi + aksi)

## 6. Page-by-page
### Landing
- Home: hero tanpa blur orb, grid fasilitas/program dengan variasi, footer bersih
- Donasi: informasi rekening jelas, hero/section sederhana tanpa gradient dekoratif
- License: typografi bacaan nyaman
### Auth
- Login: card bersih, layout split sederhana tanpa gradient background berlebihan
### Panel
- Sidebar: label normal case, scrollbar standar, active state emerald
- Flash: tone hangat, ikon sederhana
- Dashboard (admin/guru/anak): hierarki jelas, stat cards dengan ikon, chart tanpa dekorasi berlebihan, empty state kontekstual
- CRUD pages: page-header konsisten, form dengan label normal, empty state kontekstual

## 7. Aksesibilitas
- Kontras WCAG AA, focus ring terlihat, semantic headings, label form, alt text

## 8. Fase Implementasi
- Fase 1: Design tokens + shared foundations (layouts, sidebar, flash, pagination, app.css)
- Fase 2: Dashboards + landing + auth
- Fase 3: CRUD pages (bulk normalization + targeted edits)
- Fase 4: QA (lint PHP, smoke test routes)

## 9. Kriteria Sukses
- Tidak ada blur-orb/backdrop-blur dekoratif/gradient hero dekoratif
- Font Plus Jakarta Sans ter-load
- Palet emerald/stone konsisten di seluruh halaman
- Semua halaman render tanpa error
