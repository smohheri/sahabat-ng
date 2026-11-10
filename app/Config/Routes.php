<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->get('/', 'Landing::index');
$routes->get('lisensi', 'Landing::license');
$routes->get('donasi', 'Landing::donasi');

$routes->match(['get', 'post'], 'login', 'Auth::login');
$routes->get('logout', 'Auth::logout');

// ---------------------------------------------------------------------
// Admin / Petugas / Dinas / Operator
// ---------------------------------------------------------------------
$routes->group('admin', ['filter' => ['auth', 'role:admin,petugas,dinas,operator']], static function ($routes) {
    $routes->get('/', 'Admin\Dashboard::index');
    $routes->get('', 'Admin\Dashboard::index');

    // Anak
    $routes->get('anak', 'Admin\Anak::anak_aktif');
    $routes->get('anak_aktif', 'Admin\Anak::anak_aktif');
    $routes->get('anak_nonaktif', 'Admin\Anak::anak_nonaktif');
    $routes->get('anak/tambah', 'Admin\Anak::create');
    $routes->get('anak/edit/(:num)', 'Admin\Anak::edit/$1');
    $routes->post('anak/store', 'Admin\Anak::store');
    $routes->post('anak/update/(:num)', 'Admin\Anak::update/$1');
    $routes->post('anak/delete/(:num)', 'Admin\Anak::delete/$1');
    $routes->post('anak/upload_kk/(:num)', 'Admin\Anak::upload_kk/$1');
    $routes->post('anak/upload_akta/(:num)', 'Admin\Anak::upload_akta/$1');
    $routes->post('anak/upload_pendukung/(:num)', 'Admin\Anak::upload_pendukung/$1');
    $routes->post('anak/upload_foto/(:num)', 'Admin\Anak::upload_foto/$1');
    $routes->get('anak/view_dokumen/(:num)/(:any)', 'Admin\Anak::view_dokumen/$1/$2');
    $routes->post('anak/activate-account/(:num)', 'Admin\Anak::activate_akun/$1');
    $routes->get('anak_ajax', 'Admin\Anak::ajax');
    $routes->post('anak/export', 'Admin\Laporan::export_anak');

    // Pengurus
    $routes->get('pengurus', 'Admin\Pengurus::index');
    $routes->get('pengurus/tambah', 'Admin\Pengurus::create');
    $routes->get('pengurus/edit/(:num)', 'Admin\Pengurus::edit/$1');
    $routes->post('pengurus/store', 'Admin\Pengurus::store');
    $routes->post('pengurus/update/(:num)', 'Admin\Pengurus::update/$1');
    $routes->post('pengurus/delete/(:num)', 'Admin\Pengurus::delete/$1');
    $routes->post('pengurus/upload_ktp/(:num)', 'Admin\Pengurus::upload_ktp/$1');
    $routes->get('pengurus/view_ktp/(:num)', 'Admin\Pengurus::view_ktp/$1');

    // User
    $routes->get('user', 'Admin\User::index');
    $routes->get('user/tambah', 'Admin\User::create');
    $routes->get('user/edit/(:num)', 'Admin\User::edit/$1');
    $routes->post('user/store', 'Admin\User::store');
    $routes->post('user/update/(:num)', 'Admin\User::update/$1');
    $routes->post('user/delete/(:num)', 'Admin\User::delete/$1');

    // Pengaturan
    $routes->get('pengaturan', 'Admin\Pengaturan::index');
    $routes->post('pengaturan/update', 'Admin\Pengaturan::update');
    $routes->post('pengaturan/upload_logo', 'Admin\Pengaturan::upload_logo');
    $routes->post('pengaturan/upload_dokumen', 'Admin\Pengaturan::upload_dokumen');
    $routes->post('pengaturan/upload_kop', 'Admin\Pengaturan::upload_kop');
    $routes->post('landing/upload_about_image', 'Admin\Pengaturan::upload_about_image');

    // Landing content
    $routes->get('landing', 'Admin\LandingContent::hero');
    $routes->get('carousel', 'Admin\LandingContent::carousel');
    $routes->post('carousel/upload', 'Admin\LandingContent::upload_carousel_image');
    $routes->post('carousel/update', 'Admin\LandingContent::update_carousel_image');
    $routes->post('carousel/delete/(:num)', 'Admin\LandingContent::delete_carousel_image/$1');
    $routes->post('carousel/update_sort_order', 'Admin\LandingContent::update_carousel_sort_order');

    $routes->get('hero', 'Admin\LandingContent::hero');
    $routes->post('hero/upload', 'Admin\LandingContent::upload_hero_image');
    $routes->post('hero/update/(:num)', 'Admin\LandingContent::update_hero_image/$1');
    $routes->post('hero/delete/(:num)', 'Admin\LandingContent::delete_hero_image/$1');

    $routes->get('facilities', 'Admin\LandingContent::facilities');
    $routes->post('facilities/upload', 'Admin\LandingContent::facilities_upload');
    $routes->post('facilities/update', 'Admin\LandingContent::facilities_update');
    $routes->post('facilities/delete/(:num)', 'Admin\LandingContent::facilities_delete/$1');

    // Laporan + export (definisikan rute spesifik lebih dulu)
    $routes->get('export_pdf_anak', 'Admin\Laporan::export_pdf_anak');
    $routes->get('export_excel_anak', 'Admin\Laporan::export_excel_anak');
    $routes->get('export_pdf_pengurus', 'Admin\Laporan::export_pdf_pengurus');
    $routes->get('export_excel_pengurus', 'Admin\Laporan::export_excel_pengurus');
    $routes->get('export_pdf_dokumen', 'Admin\Laporan::export_pdf_dokumen');
    $routes->get('export_excel_dokumen', 'Admin\Laporan::export_excel_dokumen');
    $routes->get('export_pdf_statistik', 'Admin\Laporan::export_pdf_statistik');
    $routes->get('generate_pdf_statistik', 'Admin\Laporan::generate_pdf_statistik');
    $routes->get('export_pdf_karakter', 'Admin\Laporan::export_pdf_karakter');
    $routes->get('export_pdf_eksternal', 'Admin\Laporan::export_pdf_eksternal');
    $routes->get('export_excel_eksternal', 'Admin\Laporan::export_excel_eksternal');
    $routes->get('laporan', 'Admin\Laporan::index');
    $routes->get('laporan/(:any)', 'Admin\Laporan::index/$1');

    // Logs
    $routes->get('logs', 'Admin\Logs::index');
    $routes->get('logs_ajax', 'Admin\Logs::ajax');

    // Backup
    $routes->get('backup', 'Admin\Backup::index');
    $routes->post('backup_database', 'Admin\Backup::backup_database');
    $routes->post('backup_files', 'Admin\Backup::backup_files');
    $routes->get('download_backup/(:any)/(:any)', 'Admin\Backup::download_backup/$1/$2');
    $routes->post('delete_backup', 'Admin\Backup::delete_backup');
    $routes->post('restore_database', 'Admin\Backup::restore_database');
    $routes->post('restore_files', 'Admin\Backup::restore_files');
    $routes->post('delete_temp_file', 'Admin\Backup::delete_temp_file');

    // Halaman statis
    $routes->get('dukung_kami', 'Admin\Dashboard::dukung_kami');
    $routes->get('kontak', 'Admin\Dashboard::kontak');
    $routes->get('changelog', 'Admin\Dashboard::changelog');

    // Akademik
    $routes->get('akademik', 'Akademik::mapel');
    $routes->get('akademik/mapel', 'Akademik::mapel');
    $routes->post('akademik/mapel/store', 'Akademik::mapel_store');
    $routes->post('akademik/mapel/update/(:num)', 'Akademik::mapel_update/$1');
    $routes->post('akademik/mapel/delete/(:num)', 'Akademik::mapel_delete/$1');
    $routes->get('akademik/rombel', 'Akademik::rombel');
    $routes->post('akademik/rombel/store', 'Akademik::rombel_store');
    $routes->post('akademik/rombel/update/(:num)', 'Akademik::rombel_update/$1');
    $routes->post('akademik/rombel/delete/(:num)', 'Akademik::rombel_delete/$1');
    $routes->get('akademik/absensi', 'Akademik::absensi');
    $routes->post('akademik/absensi/save', 'Akademik::absensi_save');
    $routes->get('akademik/export/mapel/pdf', 'Akademik::export_mapel_pdf');
    $routes->get('akademik/export/mapel/excel', 'Akademik::export_mapel_excel');
    $routes->get('akademik/export/rombel/pdf', 'Akademik::export_rombel_pdf');
    $routes->get('akademik/export/rombel/excel', 'Akademik::export_rombel_excel');
    $routes->get('akademik/export/absensi/pdf', 'Akademik::export_absensi_pdf');
    $routes->get('akademik/export/absensi/excel', 'Akademik::export_absensi_excel');

    // Penilaian karakter
    $routes->get('penilaian-karakter', 'Admin\PenilaianKarakter::master');
    $routes->get('penilaian-karakter/master', 'Admin\PenilaianKarakter::master');
    $routes->post('penilaian-karakter/scale/store', 'Admin\PenilaianKarakter::store_scale');
    $routes->post('penilaian-karakter/scale/update/(:num)', 'Admin\PenilaianKarakter::update_scale/$1');
    $routes->post('penilaian-karakter/scale/delete/(:num)', 'Admin\PenilaianKarakter::delete_scale/$1');
    $routes->post('penilaian-karakter/aspect/store', 'Admin\PenilaianKarakter::store_aspect');
    $routes->post('penilaian-karakter/aspect/update/(:num)', 'Admin\PenilaianKarakter::update_aspect/$1');
    $routes->post('penilaian-karakter/aspect/delete/(:num)', 'Admin\PenilaianKarakter::delete_aspect/$1');
    $routes->post('penilaian-karakter/indicator/store', 'Admin\PenilaianKarakter::store_indicator');
    $routes->post('penilaian-karakter/indicator/update/(:num)', 'Admin\PenilaianKarakter::update_indicator/$1');
    $routes->post('penilaian-karakter/indicator/delete/(:num)', 'Admin\PenilaianKarakter::delete_indicator/$1');

    $routes->get('penilaian-karakter/data-penilaian', 'Admin\PenilaianKarakter::data');
    $routes->get('penilaian-karakter/detail-penilaian/(:num)', 'Admin\PenilaianKarakter::detail/$1');
    $routes->get('penilaian-karakter/catatan-kualitatif', 'Admin\PenilaianKarakter::catatan');
    $routes->get('penilaian-karakter/ringkasan-mingguan', 'Admin\PenilaianKarakter::ringkasan_mingguan');
    $routes->get('penilaian-karakter/ringkasan-bulanan', 'Admin\PenilaianKarakter::ringkasan_bulanan');
    $routes->get('penilaian-karakter/laporan', 'Admin\PenilaianKarakter::laporan');
    $routes->get('penilaian-karakter/laporan/detail/(:num)', 'Admin\PenilaianKarakter::laporan_detail/$1');
    $routes->get('penilaian-karakter/laporan/detail/(:num)/export-pdf', 'Admin\PenilaianKarakter::laporan_detail_export_pdf/$1');
});

// ---------------------------------------------------------------------
// Pengajar / Guru
// ---------------------------------------------------------------------
$routes->group('guru', ['filter' => ['auth', 'role:pengajar,guru']], static function ($routes) {
    $routes->get('/', 'Guru::index');
    $routes->get('', 'Guru::index');
    $routes->get('anak', 'Guru::anak');
    $routes->get('profil-pengajar', 'Guru::profil_pengajar');
    $routes->get('profil-pengajar/edit', 'Guru::edit_profil_pengajar');
    $routes->post('profil-pengajar/update', 'Guru::update_profil_pengajar');

    $routes->get('penilaian-karakter', 'Guru::penilaian_karakter');
    $routes->post('penilaian-karakter/save', 'Guru::penilaian_karakter_save');
    $routes->get('penilaian-karakter/template', 'Guru::penilaian_karakter_template');
    $routes->post('penilaian-karakter/import', 'Guru::penilaian_karakter_import');

    $routes->get('perkembangan-anak', 'Guru::perkembangan_anak');
    $routes->get('perkembangan-anak/detail/(:num)', 'Guru::perkembangan_anak_detail/$1');
    $routes->get('perkembangan-anak/detail/(:num)/export-pdf', 'Guru::perkembangan_anak_detail_export_pdf/$1');

    // Akademik untuk pengajar
    $routes->get('akademik', 'Akademik::absensi');
    $routes->get('akademik/mapel', 'Akademik::mapel');
    $routes->get('akademik/rombel', 'Akademik::rombel');
    $routes->get('akademik/absensi', 'Akademik::absensi');
    $routes->post('akademik/absensi/save', 'Akademik::absensi_save');
    $routes->get('akademik/export/mapel/pdf', 'Akademik::export_mapel_pdf');
    $routes->get('akademik/export/mapel/excel', 'Akademik::export_mapel_excel');
    $routes->get('akademik/export/rombel/pdf', 'Akademik::export_rombel_pdf');
    $routes->get('akademik/export/rombel/excel', 'Akademik::export_rombel_excel');
    $routes->get('akademik/export/absensi/pdf', 'Akademik::export_absensi_pdf');
    $routes->get('akademik/export/absensi/excel', 'Akademik::export_absensi_excel');
});

// ---------------------------------------------------------------------
// Anak asuh
// ---------------------------------------------------------------------
$routes->group('anak', ['filter' => ['auth', 'role:anak']], static function ($routes) {
    $routes->get('/', 'Anak::index');
    $routes->get('', 'Anak::index');
    $routes->get('profil', 'Anak::profil');
    $routes->match(['get', 'post'], 'asesmen-mandiri', 'Anak::asesmen_mandiri');
});
