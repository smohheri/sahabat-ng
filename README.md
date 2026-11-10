# SAHABAT-NG

Sistem Informasi Manajemen **LKSA** (Lembaga Kesejahteraan Sosial Anak) — ditulis ulang dengan **CodeIgniter 4**.

Aplikasi ini mengelola data anak asuh, pengurus, pengajar, akademik (mata pelajaran, rombongan belajar, absensi), penilaian karakter anak, laporan & ekspor (PDF/Excel), serta konten landing page.

> Versi **2.x** merupakan **rewrite penuh** dari versi sebelumnya yang berbasis CodeIgniter 3 (AdminLTE/Bootstrap/jQuery). Lihat [CHANGELOG.md](CHANGELOG.md) untuk daftar perubahan & breaking changes.

---

## ✨ Fitur Utama

- **Multi-role** — 6 level akses: `admin`, `petugas`, `dinas`, `operator`, `pengajar` (guru), `anak` (anak asuh).
- **Manajemen Data Anak** — CRUD lengkap, dokumen (KK, akta, pendukung), foto, status berkas, pencarian & filter, paginasi.
- **Akademik** — mata pelajaran, rombongan belajar, absensi mapel.
- **Penilaian Karakter** — master aspek/indikator, input penilaian guru & asesmen mandiri anak, ringkasan mingguan/bulanan (matriks), laporan perkembangan.
- **Laporan & Ekspor** — ekspor data anak/pengurus/dokumen/karakter ke **PDF** (mPDF) dan **Excel** (PhpSpreadsheet), lengkap dengan grafik ringkasan.
- **Dashboard** — statistik realtime + grafik (Chart.js), aktivitas terbaru.
- **Landing Page** — hero slideshow, fasilitas, galeri/carousel, donasi, kontak (dikelola dari panel admin).
- **Backup & Restore** database/berkas.
- **Keamanan** — hashing bcrypt, CSRF, filter role & IDOR, pembatasan brute-force login, upload terproteksi, security headers.

---

## 🧰 Teknologi

| Komponen | Keterangan |
|---|---|
| Framework | CodeIgniter 4 (PHP 8.2+) |
| Database | MySQL |
| UI | Tailwind CSS + Alpine.js (via CDN) |
| Chart | Chart.js (via CDN) |
| Ikon | Font Awesome 6 (via CDN) |
| PDF | mPDF (`mpdf/mpdf`) |
| Excel | PhpSpreadsheet (`phpoffice/phpspreadsheet`) |

---

## 📋 Persyaratan

- PHP **8.2** atau lebih baru, dengan ekstensi: `intl`, `mbstring`, `curl`, `gd`, `mysqlnd`.
- MySQL 5.7+ / MariaDB.
- [Composer](https://getcomposer.org/).

---

## 🚀 Instalasi

```bash
# 1. Clone / salin proyek
git clone <url-repo> sahabat-ng
cd sahabat-ng

# 2. Install dependency
composer install

# 3. Salin file environment
cp env .env
```

Lalu sesuaikan `.env`:

```ini
CI_ENVIRONMENT = development            # production untuk deploy
app.baseURL = 'http://localhost:8080/'

database.default.hostname = localhost
database.default.database = db_lksa
database.default.username = root
database.default.password = 'password-anda'
database.default.DBDriver = MySQLi

encryption.key = hex2bin:<64-karakter-heksadesimal-acak>
```

Buat database lalu jalankan migrasi & seeder:

```bash
# Migrasi tabel
php spark migrate

# Seeder data dasar (role, pengaturan, hero/carousel/fasilitas, master karakter, admin)
php spark db:seed DatabaseSeeder

# (Opsional) Data dummy lengkap untuk seluruh modul
php spark db:seed DummyDataSeeder
```

Jalankan server development:

```bash
php spark serve
```

Buka `http://localhost:8080`.

> Untuk production, arahkan document root web server ke folder **`public/`** (bukan root proyek).

---

## 👤 Akun Demo

| Role | Username | Password |
|---|---|---|
| Admin | `admin` | `admin` |
| Petugas | `petugas` | `petugas123` |
| Dinas | `dinas` | `dinas123` |
| Operator | `operator` | `operator123` |
| Pengajar | `guru1` … `guru8` | `guru123` |
| Petugas (pengurus) | `pengurus1` … `pengurus6` | `pengurus123` |
| Anak Asuh | `anak1` … `anak36` | `anak123` |

---

## 📁 Struktur Direktori

```
app/
├── Config/            # Konfigurasi (Routes, App, Session, Security, dll.)
├── Controllers/       # Controller (Admin, Guru, Anak, Akademik, Auth, Landing)
│   └── Admin/         # Controller area admin
├── Database/
│   ├── Migrations/    # Migrasi tabel (forge)
│   └── Seeds/         # Seeder data awal & dummy
├── Filters/           # AuthFilter, RoleFilter
├── Helpers/           # app_helper (tanggal, umur, rupiah, nomor_wa, helper ekspor)
├── Libraries/         # PdfExport, ExcelExport
├── Models/            # Model (Eloquent-like Query Builder)
└── Views/             # View (layout, partials, admin, guru, anak, landing)
public/                # Document root (index.php, uploads, favicon)
writable/              # Cache, logs, session, backup
```

---

## 🔐 Keamanan

Beberapa hal yang sudah diterapkan:

- Password di-hash dengan `bcrypt`.
- Proteksi CSRF aktif (global) + token `setup`.
- Filter `auth` & `role` untuk membatasi akses per halaman.
- Pemisahan hak: aksi sensitif (hapus, backup, manajemen user, pengaturan) hanya untuk role `admin`.
- Pencegahan **IDOR** pada aksi yang mengambil `id` dari URL/body.
- Batasan upload (ekstensi, ukuran 2MB, blokir MIME berbahaya).
- Brute-force login dibatasi (kunci sementara setelah percobaan gagal).
- Security headers (`X-Frame-Options`, `X-Content-Type-Options`, dll.) aktif.

---

## 📜 Lisensi & Kredit

- Proyek internal LKSA. Lisensi sesuai kesepakatan.
- Dibangun di atas [CodeIgniter 4](https://codeigniter.com) (MIT).

## 📄 Changelog

Riwayat perubahan lengkap tersedia di [CHANGELOG.md](CHANGELOG.md).