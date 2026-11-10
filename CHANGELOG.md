# Changelog

Semua perubahan penting pada aplikasi SAHABAT-NG akan didokumentasikan di file ini.

Format berdasarkan [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
dan proyek ini mengikuti [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.0] - 2026-11-06

**Rewrite besar**: Aplikasi ditulis ulang dari CodeIgniter 3 (AdminLTE/Bootstrap/jQuery) ke CodeIgniter 4 (Tailwind/Alpine). Lihat item **BREAKING CHANGE** di bagian Changed untuk detail migrasi.

### Added
- ✨ **Seleksi & Ekspor Data Anak yang Fleksibel**
  - Tombol **Export** tunggal (menggantikan tombol Excel/PDF terpisah) yang membuka dialog pilihan **kolom** dan **format** (Excel/PDF).
  - Hasil ekspor menyertakan **grafik ringkasan** (bar chart) per kolom kategorikal terpilih (jenis kelamin, kategori, pendidikan, status, provinsi, kabupaten), ditempatkan sebelum tabel.
  - Ekspor menghormati konteks halaman: **Anak Aktif** hanya mengekspor anak aktif, **Anak Nonaktif** hanya nonaktif/alumni.
  - Menambahkan kolom **Biaya Pendidikan** (format rupiah) pada ekspor.

- 🎞️ **Hero Landing Slideshow**
  - Hero beranda kini slideshow (rotasi otomatis tiap 5 detik) atas semua gambar hero aktif dari menu **Hero Landing**, dengan transisi fade, indikator titik, dan navigasi manual.

- 🔍 **Dropdown Wilayah Searchable (ala Select2)**
  - Provinsi & Kabupaten/Kota pada form anak memakai dropdown dengan pencarian realtime; Kabupaten otomatis terfilter berdasarkan Provinsi terpilih.

- 🖼️ **Favicon dari Logo (mendukung WebP)**
  - Favicon otomatis dibuat dari logo terunggah; logo WebP dikonversi ke PNG agar kompatibel dengan browser.

- 🧪 **Seeder Data Dummy Menyeluruh**
  - `DummyDataSeeder` untuk mengisi seluruh modul (wilayah, guru, pengurus, anak, user, log, laporan, akademik, absensi, dan penilaian karakter).

- 📈 **Relayout Ringkasan Penilaian Karakter**
  - Ringkasan **Bulanan** dan **Mingguan** diubah menjadi matriks (baris = aspek, kolom = bulan/minggu) dengan skor berwarna, plus filter tahun & penilai.

### Changed
- 💥 **BREAKING CHANGE — Rewrite CodeIgniter 3 → CodeIgniter 4**
  - Struktur direktori: `application/` (CI3) → `app/` (CI4).
  - Frontend: AdminLTE 3 + Bootstrap 4 + jQuery → Tailwind CSS + Alpine.js (jQuery dihapus).
  - Konfigurasi: `application/config/*` → `app/Config/*` + `.env`.
  - Routing: ditulis ulang di `app/Config/Routes.php`.
  - Database: migrasi & seeder dijalankan dengan `php spark migrate` / `php spark db:seed`.
  - Butuh PHP 8.2+ dan dependency Composer (`mpdf`, `phpspreadsheet`).

- 🎨 **Konsistensi UI**
  - Warna solid diterapkan di seluruh halaman (menghapus semua gradien).
  - Konten panel admin menjadi full-width.
  - Scrollbar disembunyikan pada sidebar & konten (tetap bisa di-scroll).
  - Sidebar otomatis meng-scroll ke menu aktif setelah navigasi.
  - Menu "Anak Aktif" & "Anak Nonaktif" digabung menjadi menu induk **Data Anak** dengan submenu.

- 📊 **Paginasi di Semua Halaman Tabel**
  - Paginasi diterapkan ke daftar user, laporan, mapel, rombel, absensi, catatan kualitatif, dan ringkasan.
  - Memperbaiki link paginasi yang hilang tanda `?` saat tanpa filter.

- 👤 **Pengaturan Kepala Terhubung Data Pengurus**
  - Field "Nama Kepala LKSA/Putra/Putri" di Pengaturan menjadi dropdown yang diambil dari data **Pengurus**.

- 📄 **Laporan Dokumen Lebih Jelas**
  - Status berkas menampilkan ikon centang (lengkap) / silang (belum), rata tengah.

- 🌐 **Kontak Donasi**
  - Halaman donasi publik memakai kontak LKSA dari database; halaman admin Dukung Kami memakai kontak hardcode.
  - Helper `nomor_wa()` untuk normalisasi nomor telepon ke format internasional `62...`.

- 🖼️ **Data Anak**
  - Menambahkan kolom **Umur** (list + ekspor) dan kolom **Status Berkas**.
  - Foto anak tampil di list & form edit, plus tombol **Detail** yang membuka modal profil lengkap (preview foto & berkas).

### Security
- 🔐 **Audit & Hardening Keamanan**
  - Perbaikan **IDOR**: pengajar tidak bisa lagi mengakses/menilai anak di luar rombelnya.
  - **Pemisahan role**: backup/restore, manajemen user, pengaturan, dan semua aksi hapus kini hanya untuk role `admin`; menu/tombol sensitif disembunyikan dari role non-admin.
  - **Brute-force login**: pembatasan percobaan gagal (kunci 5 menit setelah 5x gagal).
  - **Upload**: batas ukuran 2 MB + blokir MIME berbahaya.
  - **CSRF `regenerate` dimatikan** (menghindari token basi pada form/modal), security headers aktif (`secureheaders`), cookie `Secure` otomatis di production, `DBDebug` hanya di development.
  - Menghapus fallback verifikasi password plaintext.
  - Konfirmasi aksi hapus/restore memakai **modal** (bukan `window.confirm`).

### Fixed
- 🐛 Memperbaiki teks konfirmasi yang menampilkan `\x20` untuk spasi.
- 🐛 Memperbaiki input Biaya SPP yang merusak nilai desimal (desimal hilang saat disimpan).
- 🐛 Memperbaiki URL ganda pada tombol "Buka Panel"/masuk di landing page.
- 🐛 Memperbaiki hero landing (dari Pengaturan) yang tidak tampil di beranda.

## [1.13.0] - 2026-05-17

### Added
- **Panel Laporan Rekap Absensi untuk Guru**
  - Menambahkan panel khusus guru di halaman input absensi untuk ekspor laporan rekap per anak.

### Changed
- **Akses Akademik Guru Difokuskan ke Input Absensi**
  - Menu akademik guru disederhanakan menjadi **Input Absensi** saja.
  - Route `guru/akademik` dan turunannya diarahkan ke halaman absensi sesuai hak akses guru.
  - Validasi backend absensi guru kini dibatasi hanya untuk mapel yang diampu dan sudah disetting admin pada rombel.

- **Format Ekspor Laporan Absensi Per Anak**
  - Sheet utama ekspor absensi diubah menjadi rekap per anak dengan kolom:
    - Hadir, Izin, Sakit, Alpha, dan Persentase Kehadiran.
  - Sheet rekap per sesi dan detail absensi mapel tetap dipertahankan untuk audit.

- **Pembaruan Cache PWA untuk Rilis 1.13.0**
  - Versi cache service worker dinaikkan ke `sahabat-pwa-v6` agar aset terbaru langsung termuat setelah deploy.

## [1.12.3] - 2026-04-20

### Added
- ✨ **Penambahan Data No KK pada Modul Data Anak (Admin)**
  - Menambahkan kolom **No KK** pada tabel daftar anak di panel admin.
  - Menambahkan input **No KK** pada form tambah dan edit data anak.
  - Menambahkan tampilan **No KK** pada modal detail data anak.
  - Menambahkan file SQL migrasi baru:
    - `database/alter_table_add_no_kk.sql`

### Changed
- 🔄 **Pembaruan CRUD Data Anak untuk No KK**
  - Menyesuaikan proses simpan/update di `Admin::anak()` agar memproses field `no_kk`.
  - Menyesuaikan endpoint DataTable `admin/anak_ajax` untuk menampilkan dan mengurutkan kolom `no_kk`.
  - Menyesuaikan query model `Anak_model` agar pencarian DataTable mendukung `no_kk`.

- 🔄 **Sinkronisasi Skema Database Awal**
  - Menambahkan kolom `no_kk` ke definisi tabel `anak` pada `database/db_lksa.sql` agar instalasi baru langsung kompatibel.

## [1.12.2] - 2026-04-17

### Added
- ✨ **Edit Profil Pengajar Mandiri**
  - Menambahkan halaman edit profil pengajar:
    - `guru/profil-pengajar/edit`
  - Menambahkan tombol **Edit Profil** pada halaman profil pengajar.
  - Menambahkan form edit untuk data profesional pengajar (NIP, jabatan, kontak, pendidikan, keahlian, sertifikasi, pengalaman, status kepegawaian, alamat).

### Changed
- 🔄 **Sinkronisasi Nama Akun dan Data Guru**
  - Perubahan nama akun dari form profil pengajar kini memperbarui:
    - `users.nama`
    - `guru.nama_guru`
  - Session nama pengguna juga diperbarui agar tampilan sidebar/navbar langsung sinkron.

- 🔄 **Penyempurnaan Input Pendidikan Terakhir**
  - Field `pendidikan_terakhir` pada form edit profil pengajar diubah dari input teks menjadi dropdown pilihan.

- 🔄 **Sinkronisasi Konten Landing Page dengan Fitur Aplikasi**
  - Memperbarui konten section **Fitur Unggulan** agar sesuai dengan kemampuan sistem saat ini (panel admin, panel guru/pengajar, panel anak, penilaian karakter, analitik, dan dokumen profil digital).

- 🔄 **Pembaruan Statistik Ringkas Landing Page**
  - Kartu **Anak Asuh** kini mengambil data dari jumlah anak aktif.
  - Kartu terakhir diubah menjadi **Pengajar Aktif** berdasarkan jumlah data pada tabel `guru`.

### Fixed
- 🐛 **Perbaikan Foto Sidebar Panel Guru**
  - Foto profil pada sidebar guru kini mengambil sumber dari relasi akun guru dan file upload yang valid.
  - Menambahkan fallback aman ke avatar default bila file foto tidak tersedia.

- 🐛 **Konsistensi Tampilan Tombol Upload Profil Pengajar**
  - Menyesuaikan style tombol aksi dan tombol pilih file pada halaman profil pengajar agar konsisten dengan tema panel (light/dark mode).

- 🐛 **Sinkronisasi Pilihan Akses Login dengan Enum Role User**
  - Opsi dropdown akses pada halaman login kini dibaca dinamis dari enum kolom `users.role`.
  - Validasi `akses` pada login disesuaikan otomatis berdasarkan nilai enum yang tersedia.
  - Urutan opsi dropdown akses ditampilkan alfabetis untuk memudahkan pemilihan.

### Removed
- 🧹 **Pembersihan Fitur Detail Guru Lama**
  - Menghapus route, method controller, dan view `detail_guru` yang sudah tidak digunakan setelah alur dipusatkan ke profil pengajar.

## [1.12.1] - 2026-04-17

### Added
- ✨ **Penyempurnaan Panel Anak**
  - Menambahkan penyempurnaan tampilan halaman panel anak:
    - Dashboard Anak
    - Asesmen Mandiri
    - Profil Anak

### Changed
- 🔄 **Penyesuaian Tampilan CSS Panel Anak**
  - Menyesuaikan kembali gaya visual dashboard dan asesmen mandiri agar konsisten dengan tampilan yang sebelumnya digunakan.

### Fixed
- 🐛 **Perbaikan Keterbacaan Dark Mode Panel Anak**
  - Memperbaiki warna teks dan kontras elemen di dark mode pada halaman:
    - Dashboard Anak (insight dan teks pendukung)
    - Asesmen Mandiri (panduan skor, indikator, label)
    - Profil Anak (label `text-muted` dan isi data profil)
  - Memperbaiki area komponen yang masih berlatar terang di dark mode agar teks tetap terbaca.

## [1.12.0] - 2026-04-15

### Added
- ✨ **Modul Penilaian Karakter Anak**
  - Menambahkan menu admin baru **Penilaian Karakter** di sidebar dengan submenu:
    - Master Penilaian
    - Data Penilaian
    - Detail Penilaian
    - Catatan Kualitatif
    - Ringkasan Mingguan
    - Ringkasan Bulanan
    - Laporan Karakter
  - Menambahkan route baru untuk seluruh halaman penilaian karakter di `application/config/routes.php`.
  - Menambahkan model baru:
    - `application/models/Character_master_model.php`
    - `application/models/Character_assessment_model.php`
  - Menambahkan view baru:
    - `application/views/admin/penilaian_master.php`
    - `application/views/admin/penilaian_table.php`
    - `application/views/admin/penilaian_laporan.php`

- 📄 **Export PDF Laporan Karakter**
  - Menambahkan endpoint `admin/export_pdf_karakter`.
  - Menambahkan generator PDF ringkasan perkembangan karakter per anak di `application/libraries/Pdf_export.php`.
  - Mendukung filter laporan berdasarkan minggu, bulan, dan rentang tanggal.

- 🧪 **Seed & SQL Pendukung Penilaian Karakter**
  - Menambahkan file SQL seed untuk data uji coba:
    - `database/seed_character_assessment_data.sql`
    - `database/seed_anak_dummy_data.sql`
  - Menambahkan SQL migrasi role:
    - `database/alter_table_add_role_guru.sql`

- 👨‍🏫 **Panel Guru Terpisah dari Admin**
  - Menambahkan route panel guru:
    - `guru`
    - `guru/anak`
    - `guru/penilaian-karakter`
    - `guru/perkembangan-anak`
    - `guru/perkembangan-anak/detail/{id_anak}`
  - Menambahkan controller baru `application/controllers/Guru.php`.
  - Menambahkan sidebar khusus guru `application/views/templates/sidebar_guru.php`.
  - Menambahkan halaman baru panel guru:
    - `application/views/guru/dashboard.php`
    - `application/views/guru/anak.php`
    - `application/views/guru/penilaian.php`
    - `application/views/guru/perkembangan.php`
    - `application/views/guru/perkembangan_detail.php`

- 📈 **Monitoring Perkembangan Anak di Panel Guru**
  - Menambahkan halaman ringkasan perkembangan dengan daftar anak dan skor tiap aspek.
  - Menambahkan halaman detail perkembangan anak dengan:
    - ringkasan skor indikator per aspek,
    - riwayat penilaian,
    - grafik radar nilai per aspek,
    - grafik tren indikator per aspek (multi-line chart).
  - Menambahkan query model pendukung di `application/models/Character_assessment_model.php` untuk:
    - ringkasan skor aspek per anak,
    - detail skor indikator,
    - riwayat penilaian per anak,
    - dataset tren indikator.

- 🧾 **Detail Laporan Karakter Per Anak (Admin)**
  - Menambahkan halaman detail per anak di admin:
    - `admin/penilaian-karakter/laporan/detail/{id_anak}`
  - Menambahkan informasi lengkap pada detail admin:
    - grafik radar nilai per aspek,
    - grafik tren indikator per aspek,
    - tabel indikator per aspek,
    - riwayat penilaian lintas assessor.
  - Menambahkan view baru:
    - `application/views/admin/penilaian_laporan_detail.php`

- 📄 **Export PDF Detail Laporan Karakter (Admin)**
  - Menambahkan endpoint export PDF detail admin:
    - `admin/penilaian-karakter/laporan/detail/{id_anak}/export-pdf`
  - Menambahkan template PDF detail admin:
    - `application/views/admin/penilaian_laporan_detail_pdf.php`
  - Mendukung penyertaan gambar grafik radar dan grafik tren per aspek dari canvas halaman ke file PDF.

- 📄 **Template Rekap PDF Laporan Karakter Admin**
  - Menambahkan template PDF baru untuk rekap keseluruhan aspek dan nilai per aspek per anak:
    - `application/views/admin/penilaian_laporan_pdf.php`

### Changed
- 🔄 **Peningkatan Alur Login dengan Pilihan Akses**
  - Form login sekarang memiliki dropdown `akses`.
  - Validasi login mewajibkan role akun sesuai dengan akses yang dipilih.
  - Mendukung role `guru` dan `pengajar` dengan alur redirect otomatis ke panel guru.

- 🔄 **Penyesuaian Layout Shared untuk Multi-Role**
  - Template layout admin sekarang mendukung pemilihan sidebar dinamis berdasarkan role.
  - Navbar disesuaikan agar tautan utama mengikuti konteks role (admin/guru).

- 🔄 **Pembaruan Manajemen User**
  - Validasi role di halaman user diperluas menjadi:
    - `admin`
    - `petugas`
    - `dinas`
    - `operator`
    - `pengajar`
  - Filter dan opsi role pada form tambah/edit user ikut diperbarui.

- 🔄 **Laporan Karakter Admin Diselaraskan dengan Panel Guru**
  - Halaman `admin/penilaian-karakter/laporan` kini mengikuti pola halaman perkembangan guru, dengan versi insight lebih lengkap untuk admin.
  - Menambahkan statistik tambahan (`Anak Dinilai`, `Perlu Dukungan`) dan tabel skor tiap aspek per anak.
  - Menambahkan tombol aksi `Detail` per anak menuju halaman detail laporan admin.
  - Menambahkan grafik radar agregat seluruh siswa dan grafik tren agregat per aspek (bukan per siswa tunggal).

- 🔄 **Perbaikan Alur Export PDF Laporan Karakter Admin**
  - Export PDF dari halaman laporan admin kini menggunakan data rekap keseluruhan aspek dan nilai per aspek per anak.
  - Export PDF rekap admin kini mendukung pengiriman gambar grafik radar dan grafik tren per aspek dari halaman laporan (canvas ke PDF).
  - Alur export diperbarui menggunakan submit form `POST` agar payload gambar chart dapat diproses stabil.

- 🔄 **Pengelompokan Ulang Sidebar Admin**
  - Sidebar admin direstrukturisasi ke kelompok yang lebih relevan: `OPERASIONAL INTI`, `PENILAIAN & MONITORING`, `LAPORAN & EKSPOR`, `WEBSITE PUBLIK`, dan `SISTEM & AKSES`.
  - Menu `Penilaian Karakter` dipecah menjadi dua grup agar navigasi lebih fokus:
    - `Master & Input`
    - `Ringkasan & Laporan`
  - Label submenu diperpendek untuk keterbacaan pada layar sempit.

- 🔄 **Penyesuaian PWA untuk Area Admin**
  - Service worker tidak lagi mengontrol halaman `/admin` untuk mencegah konflik navigasi dan unduhan file PDF.
  - Registrasi service worker kini otomatis di-unregister pada area admin.
  - Versi cache service worker dinaikkan ke `sahabat-pwa-v4`.

- 🎨 **Konsolidasi CSS Panel Admin/Guru**
  - Menambahkan stylesheet global bersama `assets/css/panel-global.css` untuk komponen berulang (header, stat card, panel, filter, tabel, empty state, alert, dan dark mode).
  - Memuat stylesheet global tersebut di template bersama agar dapat dipakai lintas halaman.
  - Mengurangi duplikasi CSS inline pada berbagai halaman admin dan guru agar desain lebih konsisten dan mudah dirawat.

- 📊 **Paginasi Data Penilaian Karakter (Admin)**
  - Menambahkan paginasi pada halaman:
    - `admin/penilaian-karakter/data-penilaian`
    - `admin/penilaian-karakter/detail-penilaian`
  - Menambahkan dukungan `limit + offset` pada query model `Character_assessment_model` untuk data penilaian dan detail penilaian.
  - Menyesuaikan tata letak kontrol paginasi ke bawah tabel agar lebih nyaman di layar kecil.

- 🧹 **Penyesuaian Kolom Detail Penilaian**
  - Menghapus kolom **ID Detail** dari halaman `admin/penilaian-karakter/detail-penilaian` karena tidak diperlukan pada tampilan operasional.

### Fixed
- 🐛 **Perbaikan Login Tanpa Notifikasi Error**
  - Mengubah session lokal ke driver `files` pada `.env` agar flashdata kembali berfungsi saat tabel session database belum tersedia.
  - Menambahkan pengamanan di `application/helpers/logging_helper.php` agar aktivitas login tidak gagal jika tabel `user_logs` belum ada.

- 🐛 **Perbaikan Export PDF Karakter**
  - Mengatasi error 500 akibat dependency mPDF belum tersedia melalui integrasi Composer.
  - Menambahkan header no-cache pada export PDF untuk menghindari respons stale/cache lama.
  - Menyesuaikan tombol export agar membuka file PDF di tab baru dengan alur yang lebih aman.

- 🐛 **Perbaikan Fallback Logo Aplikasi**
  - Memastikan favicon, preloader, dan logo sidebar hanya memakai file upload jika benar-benar ada.
  - Menambahkan fallback aman ke `assets/img/AdminLTELogo.png` jika file logo upload tidak ditemukan.

- 🐛 **Perbaikan Script Seed Karakter**
  - Memperbaiki query insert dummy anak di `database/seed_character_assessment_data.sql` agar kompatibel dengan parser MySQL/phpMyAdmin.
  - Menyesuaikan seed supaya data penilaian langsung muncul pada filter periode aktif.

- 🐛 **Perbaikan Submit Penilaian Karakter Guru**
  - Menambahkan token CSRF pada form penilaian karakter guru untuk mencegah error `The action you have requested is not allowed` saat proteksi CSRF aktif.

- 🐛 **Perbaikan Error 500 pada Detail Laporan Karakter Admin**
  - Memperbaiki `Undefined property: Admin::$Anak_model` dengan memuat `Anak_model` pada method detail admin.
  - Memperbaiki perhitungan total penilaian pada laporan admin agar konsisten dengan struktur data array.

- 🐛 **Perbaikan Render Grafik ke PDF (Guru/Admin Detail)**
  - Memperbaiki pembacaan payload gambar base64 chart (`radar_chart_image`, `aspect_chart_images`) agar tidak rusak oleh proses sanitasi input.
  - Memastikan grafik radar dan grafik tren per aspek benar-benar ikut tercetak di PDF detail.

- 🐛 **Perbaikan CSRF Export PDF Rekap Admin**
  - Menambahkan token CSRF pada form export laporan karakter admin untuk mencegah error `The action you have requested is not allowed` saat CSRF aktif.

- 🐛 **Perbaikan Navigasi Panel Guru (PWA/Cache)**
  - Mencegah service worker mengontrol rute panel guru (`/guru`) agar tidak terjadi fallback ke halaman dashboard saat membuka menu tertentu.
  - Registrasi service worker kini di-nonaktifkan untuk area panel (`/admin` dan `/guru`) serta pembersihan cache `sahabat-pwa-*` pada area tersebut.

- 🐛 **Perbaikan Layout Kartu Statistik**
  - Menyesuaikan tata letak `.stat-card` agar angka (`stat-number`) tampil di samping ikon sesuai struktur komponen kartu statistik.

---

## [1.11.0] - 2026-03-08

### Added
- ✨ **Standarisasi Global DataTables**
  - Membuat CSS terpusat (`assets/css/datatables-global.css`) untuk styling DataTables.
  - Memuat DataTables beserta plugin (Bootstrap 4, Responsive) secara global di template `head.php` dan `footer.php`.
  - Setup fitur responsif dan terjemahan Bahasa Indonesia otomatis di seluruh file Laporan.
- ✨ **Auto-Detect Device Dark Mode**
  - Menerapkan script JS pintar di `head.php` dan `navbar.php` untuk mendeteksi preferensi tema gelap (*prefers-color-scheme*) perangkat secara otomatis.
  - Tema AdminLTE secara langsung mengikuti tema lokal (tanpa me-_refresh_), namun tetap bisa mengatur ulang melalui tombol khusus panel (override preference).

### Changed
- 🔄 **Refactor Halaman Admin**
  - Menghapus CSS DataTables inline dan import berulang di file `anak.php`, `pengurus.php`, `user.php`, `logs.php`.
  - Mengubah inisialisasi tabel summary di `statistik.php` ke tabel HTML bawaan Bootstrap untuk menghindari malfungsi DataTables terkait atribut `colspan`.
  - Merapikan struktur Sidebar menu (`sidebar_lksa.php`) dengan mengelompokkan menu pendaftaran publik, fasilitas, carousel, kontak, serta changelog ke dalam *dropdown* khusus "Kelola Website" di bawh "WEBSITE & INFO".
  - Menambahkan styling class AdminLTE `nav-compact` dan `nav-child-indent` untuk membuat sidebar admin lebih ringkas dan terstruktur.
- 🔄 **Refactor Landing Page**
  - Mengubah *branding* navbar (sebelumnya menampilkan nama lembaga berbasis teks) menjadi dinamis menggunakan gambar Logo LKSA sesuai pengaturan dengan ukuran proporsional.

### Fixed
- 🐛 **Perbaikan UI Dashboard Admin**
  - Menyesuaikan *padding* pada panel Ringkasan (`.summary-clean` di `dashboard.php`) agar layout label dan angka tidak terlalu menempel ke tepi kotak (*card*).
- 🐛 **Perbaikan Konfigurasi Sistem dan Session**
  - Fungsi `env()` di `config.php` dan `database.php` kini diatur secara murni untuk hanya membaca properti sistem via superglobal variabel `$_ENV`, menyelesaikan malfungsi yang terjadi ketika memuat var environment melalui fallback server host.
  - Menghapus pemuatan properti env yang *redundant* pada pengaturan bawaan CodeIgniter dan mengembalikannya ke pengaturan/nilai native `FALSE`/`TRUE` milik CodeIgniter.
  - Menyelesaikan masalah hilangnya notifikasi *Flashdata* (pesan gagal login) yang diakibatkan oleh kurangnya nama tabel di `sess_save_path`. Konfigurasi dikembalikan menggunakan driver `database` dan disimpan valid ke tabel `ci_sessions`.
  - Memperbarui durasi login aplikasi (**PWA Optimization**) dengan memperpanjang *Session Expiration* menjadi 30 hari (`2592000` detik) dan *Time To Update* (regenerasi ID ID) menjadi 24 jam (`86400` detik) agar sesi tidak cepat berakhir.

---

## [1.10.1] - 2026-03-05

### Fixed
- 🐛 **Perbaikan Error 500 di Production (Session Database)**
  - Memperbaiki parsing nilai environment (`TRUE/FALSE/NULL`) agar terbaca sebagai tipe data yang benar
  - Mencegah `cache_on` aktif tidak sengaja pada koneksi database yang dipakai session
  - Mengatasi exception: `Configured database connection has cache enabled. Aborting.`
  - File: `application/config/database.php`, `application/config/config.php`

- 🐛 **Perbaikan Fatal Error Saat `vendor/` Belum Tersedia di Hosting**
  - Menambahkan pengecekan `file_exists()` sebelum memuat `vendor/autoload.php`
  - Menambahkan fallback aman untuk `composer_autoload` agar tidak memicu fatal error
  - File: `index.php`, `application/config/autoload.php`, `application/config/config.php`

- 🐛 **Perbaikan Rewrite URL Login di Apache**
  - Menyederhanakan rule rewrite agar route CodeIgniter lebih kompatibel di shared hosting
  - Menjaga akses fallback melalui `index.php` saat rewrite friendly URL belum aktif penuh
  - File: `.htaccess`

### Changed
- 🔄 **Penyesuaian Nilai Session Save Path di Environment**
  - Mengubah `sess_save_path` dari `NULL` string menjadi nilai kosong pada `.env`
  - File: `.env`

---

## [1.10.0] - 2026-03-05

### Added
- 🚀 **Progressive Web App (PWA) Support**
  - Menambahkan file manifest: `manifest.webmanifest`
  - Menambahkan service worker: `service-worker.js`
  - Menambahkan ikon PWA:
    - `assets/pwa/icon-192.png`
    - `assets/pwa/icon-512.png`
    - `assets/pwa/icon-maskable-512.png`
  - Menambahkan partial template PWA:
    - `application/views/templates/pwa_head.php`
    - `application/views/templates/pwa_register.php`

### Changed
- 🔄 **Integrasi PWA ke Seluruh Entry Point Utama**
  - Integrasi metadata manifest + theme color pada template:
    - `application/views/templates/head.php`
    - `application/views/templates/login_header.php`
    - `application/views/landingpage/home.php`
  - Integrasi registrasi service worker pada halaman:
    - `application/views/templates/footer.php`
    - `application/views/auth/login.php`
    - `application/views/landingpage/home.php`
- 🔄 **Stabilisasi Session Aplikasi**
  - Migrasi session driver dari `files` ke `database` di `application/config/config.php`
  - Penyesuaian `sess_save_path` ke tabel `ci_sessions`
  - Menonaktifkan default `db cache_on` untuk kompatibilitas session database di `application/config/database.php`

### Fixed
- 🐛 **Perbaikan Error Runtime Landing Page**
  - Menghapus pemanggilan script tidak relevan `assets/landing/js/app.js` yang menyebabkan error `$products is not defined`
  - Menambahkan fallback gambar fasilitas saat Unsplash `503` ke `assets/img/logo_sahabat.png`
  - File: `application/views/landingpage/home.php`

- 🐛 **Perbaikan Error Service Worker**
  - Menambahkan filter request non-HTTP/HTTPS agar tidak mencoba cache skema `chrome-extension://`
  - Menaikkan versi cache service worker untuk invalidasi cache lama
  - File: `service-worker.js`

### Technical
- 🔧 **Sinkronisasi Versi Aplikasi**
  - Update `APP_VERSION` menjadi `1.10.0` di `application/config/constants.php`
  - Update tampilan versi login agar dinamis menggunakan `APP_VERSION`
- 🔧 **Database Session Table**
  - Menambahkan file SQL `database/create_table_ci_sessions.sql` untuk tabel session CodeIgniter 3

---

## [1.9.0] - 2025-02-25

### Added
- ✨ **Penambahan Field Data Anak Lengkap**
  - Kolom `no_registrasi` - Nomor registrasi anak
  - Kolom `agama` - Agama anak
  - Kolom `kewarganegaraan` - Kewarganegaraan anak (default Indonesia)
  - Kolom `anak_ke` - Anak keberapa dalam keluarga
  - Kolom `jumlah_saudara_kandung` - Jumlah saudara kandung
  - Kolom `jumlah_saudara_tiri` - Jumlah saudara tiri
  - Kolom `nama_wali` - Nama wali anak
  - Kolom `no_telp_wali` - Nomor telepon wali
  - Kolom `alamat_wali` - Alamat wali
  - Kolom `nama_ayah_kandung` - Nama ayah kandung
  - Kolom `nama_ayah_tiri` - Nama ayah tiri
  - Kolom `nama_ibu_kandung` - Nama ibu kandung
  - Kolom `nama_ibu_tiri` - Nama ibu tiri
  - Kolom `pekerjaan_ayah` - Pekerjaan ayah
  - Kolom `pekerjaan_ibu` - Pekerjaan ibu
  - Kolom `no_telp_orang_tua` - Nomor telepon orang tua
  - Kolom `alamat_orang_tua` - Alamat orang tua
  - Kolom `kelas` - Kelas anak di sekolah
  - Kolom `alamat_sekolah` - Alamat sekolah
  - Kolom `no_telp_sekolah` - Nomor telepon sekolah
  - File SQL: `database/alter_table_add_new_anak_fields.sql`
  - Update form input di `application/views/admin/anak.php`
  - Update model `application/models/Anak_model.php` untuk handle field baru

### Fixed
- 🐛 **Perbaikan Fungsi Hapus Backup**
  - Menambahkan method `delete_backup()` di controller Admin untuk menghapus file backup
  - Menambahkan route `admin/delete_backup` untuk endpoint penghapusan
  - Menambahkan `delete_backup` ke daftar exclude CSRF
  - Menambahkan logging aktivitas untuk penghapusan backup
  - Update UI tombol hapus di halaman backup dengan AJAX call
  - File: `application/controllers/Admin.php`, `application/config/routes.php`, `application/config/config.php`, `application/helpers/logging_helper.php`, `application/views/admin/backup.php`

- 🐛 **Perbaikan Tombol Backup Database dan Files**
  - Memperbaiki fungsi backup database menggunakan mysqldump command
  - Memperbaiki fungsi backup files menggunakan Zip library
  - Menambahkan validasi keberhasilan backup dengan pengecekan file output
  - Menambahkan logging aktivitas untuk backup berhasil/gagal
  - Update UI dengan indikator loading state pada tombol backup
  - File: `application/controllers/Admin.php`, `application/views/admin/backup.php`

### Changed
- 🔄 **Pembersihan Logging Backup**
  - Menghapus logging debug untuk operasi backup dan download
  - Menghapus log_activity('backup_debug', ...), log_activity('download_start', ...), log_activity('download_path', ...), log_activity('download_info', ...)
  - Logging tetap dilakukan untuk event penting seperti backup berhasil/gagal

- 🔄 **Update Mapping Aktivitas Log**
  - Menambahkan mapping untuk aktivitas `delete_backup` dengan warna danger dan ikon trash
  - Update `application/helpers/logging_helper.php` dengan mapping lengkap untuk semua aktivitas backup

### Technical
- 🔧 **Update Controller Admin**
  - Method `delete_backup()` dengan validasi dan keamanan
  - Menggunakan POST data untuk menghindari parameter URL
  - Flash messages untuk feedback user

---

## [1.8.0] - 2025-02-24

### Added
- ✨ **Halaman Laporan Ekspor Eksternal**
  - Laporan data anak untuk keperluan eksternal panti
  - Filter berdasarkan status, jenis kelamin, kategori
  - Export PDF dan Excel dengan informasi dasar anak
  - File: `application/views/admin/laporan/ekspor_eksternal.php`

- 🌐 **Halaman Donasi Landing Page**
  - Halaman donasi dengan informasi rekening Bank BSI dan BRI
  - Fitur copy to clipboard untuk nomor rekening
  - QRIS payment dengan kode QR
  - File: `application/views/landingpage/donasi.php`

- 📄 **Halaman Lisensi Landing Page**
  - Informasi lisensi gratis aplikasi SAHABAT
  - Kebijakan penggunaan dan persyaratan
  - Link ke halaman donasi dan GitHub repository
  - File: `application/views/landingpage/license.php`

- 📊 **DataTables Server-Side Pagination untuk Data Anak**
  - Implementasi pagination server-side untuk performa yang lebih baik
  - Filter fungsional untuk Status Anak, Jenis Kelamin, Pendidikan
  - AJAX endpoint: `admin/anak_ajax`
  - Styling konsisten dengan halaman logs

- 🔤 **Sorting Data Anak berdasarkan Nama Ascending**
  - Data anak diurutkan berdasarkan nama secara ascending secara default
  - Konfigurasi sorting di database level dan DataTable
  - Kolom Nama Anak dapat di-order

### Changed
- 🔄 **Dashboard Substats Update**
  - Mengganti baris kedua substats dengan Laki-laki, Perempuan, Anak Baru
  - Mengganti Pendidikan TK dengan Anak Asrama di baris pertama
  - Update logika penghitungan berdasarkan status_tinggal (sekolah/asrama/perawatan)

- 🎨 **Efek 3D pada Sidebar Brand**
  - Menambahkan text-shadow untuk efek 3D pada teks "SAHABAT"
  - Update `application/views/templates/sidebar_lksa.php`

- 🔄 **Mapping Aktivitas Log**
  - Menambahkan mapping untuk aktivitas export PDF/Excel pengurus, dokumen, eksternal, statistik
  - Mapping untuk operasi CRUD fasilitas (add, update, delete)
  - Update `application/helpers/logging_helper.php`

### Technical
- 🔧 **File Database Baru**
  - `database/alter_table_add_nama_sekolah_biaya_spp.sql` - Kolom nama_sekolah dan biaya_spp pada tabel anak
  - `database/dump_anak_50_records.sql` - Data dump untuk testing dengan 50 records anak

- 🔧 **Update Controller dan Model**
  - `application/controllers/Admin.php` - Method anak_ajax, update dashboard stats
  - `application/models/Anak_model.php` - Sorting nama_anak ASC, method datatable dengan filter

- 🔧 **Update Libraries**
  - `application/libraries/Excel_export.php` - Perbaikan export data anak
  - `application/libraries/Pdf_export.php` - Perbaikan generate laporan

- 🔧 **Update Views**
  - `application/views/admin/dashboard.php` - Substats baru
  - `application/views/admin/anak.php` - DataTables pagination dan sorting
  - `application/views/admin/carousel.php` - Perbaikan modal
  - `application/views/admin/facilities.php` - Perbaikan modal
  - `application/views/admin/logs.php` - Perbaikan display
  - `application/views/landingpage/home.php` - Update konten
  - `application/views/templates/sidebar_lksa.php` - Efek 3D brand

- 🔧 **Update Routes**
  - `application/config/routes.php` - Route anak_ajax

---

## [1.7.0] - 2025-02-23

### Removed
- ✂️ **Edit Functionality from Facilities Page**
  - Removed edit button from facility cards
  - Removed edit modal and associated form
  - Facilities can now only be added or deleted, not edited
  - Cleaned up JavaScript handlers for edit functionality

### Fixed
- 🐛 **Facilities Modal Issues**
  - Added "fade" class to modals for proper Bootstrap modal animation
  - Fixed JavaScript selector for description text to prevent incorrect data population when facilities have no description
  - Improved modal triggering to use consistent Bootstrap data attributes

### Added
- ✨ **Facilities Management Page**
  - New admin page for managing landing page facilities
  - Add facilities with image upload, description, icon selection
  - Delete facilities with confirmation
  - Grid display with facility cards
  - File: `application/views/admin/facilities.php`
  - Model: `application/models/Fasilitas_model.php`
  - Database: `database/alter_table_add_facilities.sql`

### Changed
- 🔄 **Facilities Page Modifications**
  - Removed edit functionality (button, modal, JavaScript)
  - Updated Select2 initialization to only target add modal
  - Fixed description paragraph selector to use specific class

### Technical
- 🔧 **Database Schema**
  - `database/alter_table_add_facilities.sql` - Tabel fasilitas dengan kolom lengkap
  - Tabel `fasilitas` dengan fields: id_fasilitas, nama_fasilitas, deskripsi, gambar, icon, sort_order, is_active
  - Data default 8 fasilitas (Asrama, Ruang Belajar, Kantin, dll.)
  - `database/alter_anak_table.sql` - Penambahan kolom dokumen dan kategori pada tabel anak
  - Kolom baru: file_kk, file_akta, file_pendukung, kategori (enum Islamic categories)
  - Modifikasi enum pendidikan untuk menambah 'PT' (Perguruan Tinggi)

- 🔧 **Model Baru**
  - `application/models/Fasilitas_model.php` - CRUD operasi fasilitas

- 🔧 **Dashboard Enhancements**
  - Penambahan statistik pendidikan TK (Taman Kanak-Kanak)
  - Penambahan section charts untuk visualisasi data
  - Update statistik kategori anak dengan 7 kategori Islamic

- 🔧 **Data Anak Improvements**
  - Penambahan kolom kategori dengan 7 pilihan: Yatim, Piatu, Yatim Piatu, Dhuafa, Fakir dan Miskin, Ibnu Sabil, Laqith
  - Modifikasi tabel data anak: hapus kolom Usia, Status, Tanggal Masuk; tambah kolom Kategori
  - Update enum pendidikan untuk mendukung Perguruan Tinggi (PT)

- 🔧 **Landing Page Updates**
  - Penambahan section fasilitas di halaman home
  - Styling dan layout untuk menampilkan fasilitas LKSA

- 🔧 **UI/UX Improvements**
  - Modifikasi sidebar admin dengan struktur menu yang lebih baik
  - Optimisasi footer dengan penghapusan jQuery yang redundant
  - Update layout carousel admin dengan grid system yang lebih responsif

- 🔧 **Library Updates**
  - `application/libraries/Excel_export.php` - Perbaikan export data anak
  - `application/libraries/Pdf_export.php` - Perbaikan generate laporan PDF

- 🔧 **Helper Updates**
  - `application/helpers/logging_helper.php` - Perbaikan fungsi logging aktivitas

---

## [1.6.0] - 2025-02-22

### Added
- 🖼️ **Kelola Carousel Landing Page**
  - Halaman admin untuk mengelola gambar carousel hero section
  - Upload, edit, delete gambar carousel dengan validasi
  - Tampilan grid gambar dengan overlay actions
  - Urutan gambar dapat disesuaikan
  - Status aktif/nonaktif untuk setiap gambar
  - Folder upload: assets/uploads/landing/

### Fixed
- 🐛 **Perbaikan Route Carousel**
  - Memperbaiki URL form upload, update, delete carousel
  - Update route admin/carousel/update tanpa parameter ID
  - Controller method update_carousel_image() menggunakan POST data

- 🐛 **Perbaikan Tampilan Gambar Carousel**
  - Menghapus pengecekan file_exists untuk menghindari placeholder
  - Gambar ditampilkan langsung dari database path
  - Perbaikan JavaScript error tooltip tidak ditemukan

### Changed
- 🔄 **Update `application/config/routes.php`**
  - Route carousel update diubah ke POST-based

- 🔄 **Update `application/controllers/Admin.php`**
  - Method update_carousel_image() menggunakan $id dari POST

- 🔄 **Update `application/views/admin/carousel.php`**
  - Form action menggunakan route yang benar
  - Hapus data-toggle tooltip untuk menghindari error JS
  - Tampilan gambar tanpa file_exists check

### Technical
- 🔧 **Model Baru**
  - application/models/Carousel_model.php - CRUD operasi carousel

- 🔧 **View Baru**
  - application/views/admin/carousel.php - Halaman kelola carousel

- 🔧 **File Database Baru**
  - database/alter_table_add_carousel_images.sql - Tabel carousel_images

---

## [1.5.0] - 2025-02-22

### Added
- 📊 **DataTables Server-Side Pagination untuk Log Aktivitas**
  - Implementasi pagination server-side untuk performa yang lebih baik dengan data log besar
  - Search functionality di semua kolom (nama, username, aktivitas, deskripsi, IP address)
  - Sorting berdasarkan kolom waktu, nama, username, aktivitas
  - Stats cards menampilkan data langsung tanpa loading
  - Styling komprehensif untuk semua kontrol DataTables dengan margin dan padding yang tepat
  - Dark mode support lengkap untuk semua kontrol DataTables
  - Bahasa Indonesia untuk interface DataTables

### Changed
- 🔄 **Update `application/models/User_log_model.php`**
  - Method baru `get_logs_datatable()` untuk query paginated dengan search dan sorting
  - Method `count_all_logs()` dan `count_filtered_logs()` untuk DataTables

- 🔄 **Update `application/controllers/Admin.php`**
  - Method `logs_ajax()` baru untuk mengembalikan data JSON DataTables
  - Method `logs()` diupdate untuk load stats data langsung

- 🔄 **Update `application/views/admin/logs.php`**
  - Konfigurasi DataTables untuk server-side processing
  - Stats cards menampilkan data langsung dari controller
  - CSS styling komprehensif untuk pagination, search bar, info text, length selector
  - Dark mode styling untuk semua kontrol DataTables

- 🔄 **Update `application/helpers/logging_helper.php`**
  - Memindahkan fungsi `get_activity_color()` dan `get_activity_icon()` dari view ke helper

- 🔄 **Update `application/config/routes.php`**
  - Route baru `admin/logs_ajax` untuk endpoint DataTables

- 🔄 **Update `application/config/config.php`**
  - Disable CSRF protection untuk development environment

### Technical
- 🔧 **Libraries DataTables**
  - Menambahkan DataTables CSS dan JS libraries ke view logs.php
  - Fix loading order dengan dynamic script loading untuk menghindari konflik jQuery

---

## [1.4.0] - 2025-02-22

### Added
- 🖼️ **Dynamic Landing Page Images**
  - Admin dapat upload gambar hero dan about untuk landing page
  - Fallback otomatis ke gambar Unsplash jika belum ada gambar
  - Menu "Landing Page" di admin sidebar (Pengaturan > Landing Page)
  - Upload gambar dengan validasi (JPG, PNG, max 2MB)
  - Preview gambar di halaman admin
  - Folder upload: assets/uploads/landing/

- 🔧 **Database Schema Update**
  - Kolom hero_image dan about_image di tabel pengaturan
  - File SQL: database/alter_table_add_landing_images.sql

### Changed
- 🔄 **Landing Page** (`application/views/landingpage/home.php`)
  - Gambar hero dan about sekarang dinamis dari database
  - Fallback ke Unsplash jika belum ada gambar upload

- 🔄 **Admin Controller** (`application/controllers/Admin.php`)
  - Method landing() - halaman kelola landing page
  - Method upload_hero_image() - upload gambar hero
  - Method upload_about_image() - upload gambar about

- 🔄 **Admin Sidebar** (`application/views/templates/sidebar_lksa.php`)
  - Menu "Landing Page" di bawah Pengaturan

### Technical
- 🔧 **View Baru**
  - application/views/admin/landing.php - Halaman kelola gambar landing

- 🔧 **File Database Baru**
  - database/alter_table_add_landing_images.sql - Tambah kolom gambar landing

- 🔧 **Direktori Upload Baru**
  - assets/uploads/landing/ - Folder untuk gambar landing page

---

## [1.3.0] - 2025-02-22

### Added
- ✨ **Export PDF Statistik dengan Chart sebagai Gambar**
  - Halaman laporan statistik sekarang bisa export ke PDF dengan gambar chart
  - Menggunakan JavaScript untuk mengkonversi Chart.js canvas ke base64
  - Mengirim data chart ke server via AJAX
  - Chart gambar disematkan di dalam PDF

- 📊 **Dukungan Legend Chart**
  - **Chart Jenis Kelamin** (Doughnut): Menambahkan judul dan legend di bawah chart
  - **Chart Usia** (Bar): Menambahkan judul dan legend di bawah chart
  - **Chart Pendidikan** (Bar): Menambahkan judul dan legend di bawah chart

- 🧹 **Hapus File Temporary Otomatis**
  - File PDF dihapus otomatis dari folder `assets/temp/` setelah didownload
  - Delay 3 detik sebelum dihapus untuk memastikan user sudah download
  - Menambahkan endpoint `admin/delete_temp_file` (POST)

- ⏳ **Indikator Loading**
  - Tombol "Export PDF" menampilkan status "Generating..." saat proses
  - Tombol dinonaktifkan saat PDF sedang dibuat
  - Kembali normal setelah selesai

- ✨ **Modal View Data Anak**
  - Tombol "View Data" (ikon mata) di kolom Aksi halaman anak
  - Modal besar (modal-xl) menampilkan data lengkap anak
  - Layout data terorganisir: Info Pribadi, Pendidikan & Status, Dokumen
  - Link dokumen yang dapat diklik jika tersedia

- 📸 **Fitur Upload Foto Anak**
  - Kolom foto di tabel anak (database/alter_table_add_foto_anak.sql)
  - Upload foto di modal edit anak
  - Tampilan foto di modal view data
  - Folder upload: assets/uploads/foto_anak/
  - Validasi file gambar (JPG, PNG, max 2MB)

- 📊 **Update Dashboard & Laporan**
  - Statistik foto di dashboard (anak dengan foto)
  - Kolom foto di laporan dokumen (tabel dan progress bar)
  - Update counter dokumen: X/4 (termasuk foto)

- 🔐 **Sistem Pencatatan Aktivitas Pengguna**
  - Mencatat aktivitas penting pengguna untuk keamanan dan audit
  - Halaman admin untuk melihat riwayat aktivitas
  - Memudahkan monitoring penggunaan aplikasi

- 📄 **Halaman Changelog**
  - Halaman admin/changelog untuk melihat riwayat perubahan
  - Parsing CHANGELOG.md ke HTML dengan styling GitHub-like
  - Responsive design dengan dark mode support

- 👤 **Role Operator**
  - Role baru 'operator' di tabel users
  - File SQL: database/alter_table_add_role_operator.sql

### Changed
- 🔄 **Update `application/controllers/Admin.php`**
  - Menambahkan method `generate_pdf_statistik()` - generate PDF dengan gambar chart
  - Menambahkan method `delete_temp_file()` - hapus file temp setelah didownload

- 🔄 **Update `application/views/admin/laporan/statistik.php`**
  - Konfigurasi Chart.js menambahkan opsi legend (judul dan label)
  - JavaScript menambahkan fitur hapus file temporary otomatis
  - Menambahkan indikator loading state

- 🔄 **Update `application/libraries/Pdf_export.php`**
  - Menambahkan method `generate_to_file()` - simpan PDF ke file
  - Menambahkan method `generate_laporan_statistik_with_charts()` - sematkan gambar chart di PDF

- 🔄 **Update `application/config/routes.php`**
  - Menambahkan route `admin/delete_temp_file`

- 🔄 **Update `application/controllers/Admin.php`**
  - Method upload_foto() untuk upload foto anak
  - Method logs() untuk halaman log aktivitas
  - Method changelog() untuk halaman changelog

- 🔄 **Update `application/models/Anak_model.php`**
  - Handle field foto di CRUD operations

- 🔄 **Update `application/views/admin/anak.php`**
  - Tombol View Data dengan modal lengkap
  - Upload foto di modal edit

- 🔄 **Update `application/views/admin/dashboard.php`**
  - Statistik foto anak

- 🔄 **Update `application/views/admin/laporan/dokumen.php`**
  - Kolom foto di tabel dan progress

- 🔄 **Update `application/config/routes.php`**
  - Route admin/logs dan admin/changelog

- 🔄 **Update `application/config/constants.php`**
  - Konstanta untuk path upload foto

- 🔄 **Update `database/db_lksa.sql`**
  - Struktur database terbaru dengan kolom foto dan tabel user_logs

### Technical
- 🔧 **Direktori baru** - `assets/temp/` untuk menyimpan file PDF sementara

- 🔧 **Helper Baru**
  - application/helpers/logging_helper.php - Pencatatan aktivitas pengguna
  - application/helpers/ip_helper.php - Mendapatkan alamat IP

- 🔧 **Model Baru**
  - application/models/User_log_model.php - Model untuk user logs

- 🔧 **View Baru**
  - application/views/admin/logs.php - Halaman log aktivitas
  - application/views/admin/changelog.php - Halaman changelog

- 🔧 **File Database Baru**
  - database/alter_table_add_foto_anak.sql - Tambah kolom foto
  - database/alter_table_add_role_operator.sql - Tambah role operator
  - database/alter_table_add_user_logs.sql - Buat tabel user_logs

- 🔧 **Direktori Upload Baru**
  - assets/uploads/foto_anak/ - Folder untuk foto anak

---

## [1.2.0] - 2025-01-18

### Added
- ✨ **Halaman Kontak Pengembang** (`application/views/admin/kontak.php`)
  - Halaman baru untuk menghubungi pengembang aplikasi
  - Info pengembang: Moh. Heri Setiawan (Lead Developer)
  - Link GitHub, LinkedIn, dan email langsung
  - Formulir kontak untuk mengirim pesan
  - Bagian informasi jam operasional
  - Dukungan Dark Mode penuh

- 🎨 **Menu Navbar Kontak**
  - Link "Kontak" di navbar bagian kanan
  - Format teks seperti menu "Dashboard"
  - Icon + teks "Kontak"
  - Langsung menuju halaman kontak pengembang

- 📋 **Struktur Menu Sidebar Terbaru**
  - Menu "Kontak Pengembang" sebagai menu utama
  - Menu "Dukung Kami" di bawah kategori INFORMASI
  - Kategori menu baru: INFORMASI (memuat Kontak Pengembang & Dukung Kami)
  - Icon headsett untuk Kontak Pengembang
  - Posisi menu: Di bawah LAPORAN, di atas PENGATURAN

- 🔧 **Route Baru**
  - Menambahkan route `admin/kontak` di `application/config/routes.php`
  - Controller `Admin.php` dengan fungsi `kontak()`

---

## [1.1.0] - 2025-01-17

### Added
- ✨ **Dark Mode Support** - Menambahkan styles dark mode untuk semua halaman laporan:
  - `application/views/admin/laporan/index.php` - Menu laporan utama
  - `application/views/admin/laporan/data_anak.php` - Laporan data anak
  - `application/views/admin/laporan/keuangan.php` - Laporan keuangan
  - `application/views/admin/laporan/pengurus.php` - Laporan pengurus
  - `application/views/admin/laporan/dokumen.php` - Laporan dokumen
  - `application/views/admin/laporan/statistik.php` - Laporan statistik
  - Cara mengaktifkan: Tambahkan class `dark-mode` pada tag `<body>`

- ✨ **Helper Tanggal Indonesia** (`application/helpers/tanggal_helper.php`)
  - Fungsi `tanggal_indo($date)` - Format: "15 Januari 2024"
  - Fungsi `tanggal_indo_short($date)` - Format: "15 Jan 2024"
  - Fungsi `bulan_indo($month)` - Format: "Januari"
  - Fungsi `umur($tanggal_lahir)` - Format: "10 tahun"
  - Fungsi `waktu_indo($datetime)` - Format: "Senin, 15 Januari 2024 10:30 WIB"
  - Helper di-load secara otomatis melalui autoload

- 📚 **Update Library PDF Export** (`application/libraries/Pdf_export.php`)
  - Menggunakan helper tanggal_indo untuk format tanggal Indonesia
  - Format periode laporan menggunakan bulan_indo
  - Format umur menggunakan fungsi umur()
  - Footer laporan menggunakan format tanggal Indonesia

- 🎨 **Redesain Halaman Dukung Kami** (`application/views/admin/dukung_kami.php`)
  - Hero section dengan animasi dan wave divider
  - Aplikasi badge dengan nama "SAHABAT"
  - Card rekening 2 kolom:
    - **Bank BSI**: 7252957170 (Tema hijau stabilo)
    - **Bank BRI**: 057201014816537 (Tema biru dongker)
  - Copy to clipboard dengan toast notification
  - Full width layout (12 grid)
  - Responsive design
  - Efek glow dan animasi modern

### Changed
- 🔄 **Environment Production** - Mengubah default ENVIRONMENT dari 'development' ke 'production' di `index.php`
- 🔄 **View Laporan** - Menggunakan helper tanggal_indo untuk konsistensi format tanggal:
  - `application/views/admin/laporan/data_anak.php`
  - `application/views/admin/laporan/pengurus.php`
- 🔄 **View Admin** - Menggunakan helper tanggal_indo:
  - `application/views/admin/anak.php`

### Technical
- 🔧 **Konfigurasi Autoload** - Menambahkan 'tanggal' ke helper autoload di `application/config/autoload.php`
- 🔧 **Controller Update** - Menambahkan field media sosial di `application/controllers/Admin.php` fungsi pengaturan()
- 🔧 **Database Schema** - File SQL untuk menambahkan kolom media sosial: `database/alter_table_add_social_media.sql`

- ✨ **Fitur Media Sosial** - Menambahkan kolom media sosial ke database:
  - Facebook, Twitter, Instagram, YouTube, LinkedIn, WhatsApp
  - File SQL: `database/alter_table_add_social_media.sql`

- 📱 **Pengaturan Media Sosial di Admin** (`application/views/admin/pengaturan.php`)
  - Input form untuk 6 media sosial
  - Icon Font Awesome untuk setiap platform

- 🌐 **Media Sosial di Landing Page** (`application/views/landingpage/home.php`)
  - Link media sosial di footer sekarang dinamis dari database
  - Semua link membuka di tab baru dengan `target="_blank"`
  - Keamanan: Menggunakan `rel="noopener noreferrer"`
  - WhatsApp smart detection: Mendukung URL lengkap atau nomor telepon saja

---

## [1.0.0] - 2024-01-15

### Added
- ✨ **Dashboard Admin** dengan statistik real-time
  - Total anak, pengurus, dokumen lengkap/kurang
  - Statistik status anak (aktif/nonaktif)
  - Statistik pendidikan (SD, SMP, SMA, PT)
  - Data anak dan pengurus terbaru
  - Quick actions untuk akses cepat

- 👶 **Manajemen Data Anak**
  - CRUD data anak lengkap (profil, pendidikan, status)
  - Upload dokumen (KK, Akta Kelahiran, Dokumen Pendukung)
  - Folder management berdasarkan NIK dan nama anak
  - Status tinggal (Sekolah, Asrama, Perawatan)
  - Status anak (Aktif/Nonaktif)

- 👔 **Manajemen Data Pengurus**
  - CRUD data pengurus dan jabatan
  - Upload dokumen KTP
  - Manajemen kontak (email, telepon)

- 📑 **Sistem Laporan**
  - Laporan data anak (PDF & Excel)
  - Laporan data pengurus (PDF & Excel)
  - Laporan dokumen anak (PDF & Excel)
  - Laporan statistik
  - Kop surat yang dapat dikustomisasi

- ⚙️ **Pengaturan Sistem**
  - Profile LKSA (nama, alamat, email, telepon, tahun berdiri)
  - Upload logo LKSA
  - Upload dokumen legal (PDF)
  - Upload kop surat untuk laporan
  - Manajemen user dan role

- ❤️ **Menu Dukung Kami**
  - Halaman informasi donasi sukarela
  - Informasi rekening BRI untuk kontribusi
  - Tombol copy nomor rekening
  - Badge nama aplikasi SAHABAT

- 🎨 **UI/UX**
  - Tema AdminLTE 3 dengan Bootstrap 4
  - Dashboard clean dan colorful
  - Responsive design untuk mobile dan desktop
  - Animasi AOS pada landing page
  - Font Awesome icons

- 🔐 **Keamanan**
  - Sistem autentikasi dengan session
  - Password hashing dengan bcrypt
  - CSRF protection
  - XSS filtering
  - Form validation

- 📱 **Landing Page**
  - Hero section dengan animasi
  - Fitur unggulan showcase
  - About section
  - Statistics counter
  - CTA section
  - Footer dengan informasi kontak

### Changed
- 🔄 **Dashboard Layout** - Restruktur layout menjadi lebih clean dengan 4 main stat cards dan 2 rows secondary stats
- 🔄 **Sidebar Brand** - Mengubah dari nama LKSA menjadi "SAHABAT"
- 🔄 **Copyright Footer** - Mengubah menjadi "SAHABAT - Sistem Anak Hebat Berbasis Administrasi Terpadu"

### Fixed
- 🐛 Memperbaiki hook settings yang tidak berjalan di landing page
- 🐛 Memperbaiki layout grid secondary stats menjadi 2 baris x 3 cards
- 🐛 Menghapus stat "Anak Baru" yang tidak diperlukan
- 🐛 Membersihkan CSS media query yang duplikat

### Technical
- 🏗️ **Arsitektur**: CodeIgniter 3.x dengan HMVC pattern
- 🗄️ **Database**: MySQL dengan relasi antar tabel
- 📦 **Dependencies**: 
  - AdminLTE 3.1.0
  - Bootstrap 4.6.0
  - jQuery 3.6.0
  - DataTables 1.10.24
  - TCPDF 6.4.4
  - PhpSpreadsheet 1.18.0

## [0.9.0] - 2024-01-01 (Beta)

### Added
- Initial release dengan fitur dasar
- Autentikasi user
- CRUD data anak dasar
- CRUD data pengurus dasar
- Laporan sederhana

---

## Catatan Rilis

### Versi 1.5.0
- Menambahkan DataTables server-side pagination untuk tabel log aktivitas
- Performa yang lebih baik untuk data log yang besar
- Search dan sorting functionality lengkap
- Stats cards menampilkan data langsung tanpa loading
- Styling komprehensif untuk semua kontrol DataTables
- Dark mode support lengkap

### Versi 1.4.0
- Menambahkan fitur gambar landing page dinamis
- Admin dapat upload gambar hero dan about section
- Fallback otomatis ke gambar Unsplash
- Menu baru di admin sidebar untuk mengelola gambar landing

### Versi 1.3.0
- Menambahkan fitur Export PDF Statistik dengan chart sebagai gambar
- Chart sekarang memiliki judul dan legend
- File temporary PDF dihapus secara otomatis
- Menambahkan indikator loading
- Modal View Data untuk melihat data lengkap anak
- Fitur upload foto anak dengan validasi
- Update dashboard dan laporan dokumen dengan statistik foto
- Sistem pencatatan aktivitas pengguna
- Halaman changelog dan logs admin
- Role operator baru

### Versi 1.2.0
- Menambahkan halaman kontak pengembang
- Memperbarui struktur menu sidebar
- Menambahkan menu Dukung Kami ke sidebar

### Versi 1.1.0
- Menambahkan format tanggal Indonesia di seluruh aplikasi
- Menambahkan Dark Mode support untuk halaman laporan
- Redesain halaman Dukung Kami dengan rekening Bank BSI dan BRI
- Optimasi untuk production deployment
- Konsistensi UI dengan format tanggal lokal

### Versi 1.0.0
Versi stabil pertama dengan semua fitur utama telah diimplementasikan. Aplikasi siap digunakan untuk produksi dengan LKSA.

### Roadmap
- [ ] Multi-level user permissions
- [ ] Notifikasi email/SMS
- [ ] Mobile app (Android/iOS)
- [ ] API untuk integrasi eksternal
- [ ] Backup otomatis ke cloud
- [ ] Multi-language support
- [ ] Advanced reporting dengan grafik
- [ ] Whatsapp integration

---

<p align="center">
  <sub>Dibuat dengan ❤️ oleh Moh. Heri Setiawan</sub>
</p>
