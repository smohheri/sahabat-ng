<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeder data dummy menyeluruh untuk seluruh modul:
 * wilayah, guru, pengurus, anak, user, log, laporan,
 * akademik (mapel, rombel, absensi), dan penilaian karakter.
 *
 * Aman dijalankan berulang: tabel yang sudah terisi akan dilewati.
 */
class DummyDataSeeder extends Seeder
{
    private string $now;

    private int $year;
    private int $month;

    /** @var array<int, int> id_provinsi */
    private array $regionProv = [];
    /** @var array<int, array<int, int>> id_provinsi => [id_kabupaten, ...] */
    private array $regionKab = [];

    public function run(): void
    {
        $this->now   = date('Y-m-d H:i:s');
        $this->year  = (int) date('Y');   // 2026
        $this->month = (int) date('n');   // bulan berjalan

        // Urutan penting: wilayah -> guru/pengurus -> anak -> user -> akademik -> absensi -> penilaian
        $this->seedRegions();
        $guruIds     = $this->seedGuru();
        $pengurusIds = $this->seedPengurus();
        $anakIds     = $this->seedAnak();
        $userIds     = $this->seedUsers($guruIds, $pengurusIds, $anakIds);
        $this->seedLogs($userIds['all'], $userIds['by_username']);
        $this->seedLaporan();
        $akademik = $this->seedAkademik($userIds['pengajar'], $anakIds);
        $this->seedAbsensi($akademik, $userIds['pengajar']);
        $this->seedCharacterAssessments($anakIds, $userIds);
    }

    // -------------------------------------------------------------------------
    // Wilayah
    // -------------------------------------------------------------------------
    private function seedRegions(): void
    {
        // Muat wilayah yang sudah ada (untuk path idempotent).
        $this->regionKab   = [];
        $this->regionProv  = [];
        foreach ($this->db->table('kabupaten')->select('id_provinsi, id_kabupaten')->orderBy('id_kabupaten', 'ASC')->get()->getResultArray() as $row) {
            $this->regionKab[(int) $row['id_provinsi']][] = (int) $row['id_kabupaten'];
        }
        $this->regionProv = array_keys($this->regionKab);

        if ($this->db->table('provinsi')->countAllResults() > 0) {
            return;
        }

        $regions = [
            'Jawa Timur'      => ['Kota Surabaya', 'Kota Malang', 'Kabupaten Malang', 'Kabupaten Sidoarjo', 'Kota Kediri'],
            'Jawa Barat'      => ['Kota Bandung', 'Kabupaten Bandung', 'Kota Bogor', 'Kota Depok'],
            'Jawa Tengah'     => ['Kota Semarang', 'Kabupaten Semarang', 'Kota Surakarta', 'Kabupaten Banyumas'],
            'DKI Jakarta'     => ['Kota Jakarta Selatan', 'Kota Jakarta Timur', 'Kota Jakarta Pusat', 'Kota Jakarta Barat'],
            'DI Yogyakarta'   => ['Kota Yogyakarta', 'Kabupaten Sleman', 'Kabupaten Bantul'],
            'Banten'          => ['Kota Tangerang', 'Kota Tangerang Selatan', 'Kabupaten Serang'],
            'Sumatera Utara'  => ['Kota Medan', 'Kabupaten Deli Serdang', 'Kota Binjai'],
            'Sulawesi Selatan'=> ['Kota Makassar', 'Kabupaten Gowa', 'Kota Parepare'],
        ];

        $mapKab = []; // id_provinsi => [id_kabupaten, ...]

        foreach ($regions as $provinsi => $kabupatens) {
            $this->db->table('provinsi')->insert([
                'nama_provinsi' => $provinsi,
                'created_at'    => $this->now,
                'updated_at'    => $this->now,
            ]);
            $idProvinsi = (int) $this->db->insertID();

            foreach ($kabupatens as $kabupaten) {
                $this->db->table('kabupaten')->insert([
                    'id_provinsi'   => $idProvinsi,
                    'nama_kabupaten'=> $kabupaten,
                    'created_at'    => $this->now,
                    'updated_at'    => $this->now,
                ]);
                $mapKab[$idProvinsi][] = (int) $this->db->insertID();
            }
        }

        // Simpan ke properti agar dipakai saat generate anak (tanpa query ulang).
        $this->regionKab = $mapKab;
        $this->regionProv = array_keys($mapKab);
    }

    // -------------------------------------------------------------------------
    // Guru
    // -------------------------------------------------------------------------
    private function seedGuru(): array
    {
        if ($this->db->table('guru')->countAllResults() > 0) {
            return array_column($this->db->table('guru')->select('id_guru')->orderBy('id_guru', 'ASC')->get()->getResultArray(), 'id_guru');
        }

        $rows = [
            ['Ahmad Fauzi', '198612052010011001', 'Pengajar', 'S1', 'Matematika'],
            ['Siti Rahayu', '198803142011012002', 'Pengajar', 'S1', 'Bahasa Indonesia'],
            ['Budi Hartono', '198905072012011003', 'Pengajar', 'S1', 'Pendidikan Agama'],
            ['Dewi Lestari', '199007192013012004', 'Pengajar', 'S1', 'IPA Terpadu'],
            ['Hadi Prasetyo', '199111232014011005', 'Pengajar', 'S1', 'IPS Terpadu'],
            ['Rina Marlina', '199303082015012006', 'Guru Kelas', 'D4', 'Pendidikan Anak Usia Dini'],
            ['Agus Santoso', '199506172016011007', 'Guru Kelas', 'S1', 'Pendidikan Guru SD'],
            ['Maya Anggraini', '199710212017012008', 'Pengajar', 'S1', 'Bahasa Inggris'],
        ];

        $ids = [];

        foreach ($rows as $i => $g) {
            $this->db->table('guru')->insert([
                'nama_guru'           => $g[0],
                'nip'                 => $g[1],
                'jabatan'             => $g[2],
                'pendidikan_terakhir' => $g[3],
                'bidang_keahlian'     => $g[4],
                'sertifikasi'         => 'Sertifikasi Pendidik ' . ($i + 1),
                'pengalaman_tahun'    => 6 + ($i % 12),
                'status_kepegawaian'  => ($i % 3 === 0) ? 'PNS' : 'Honorer',
                'no_hp'               => '08123' . str_pad((string) (4500000 + $i * 11), 7, '0', STR_PAD_LEFT),
                'email'               => strtolower(str_replace(' ', '.', $g[0])) . '@lksa.id',
                'alamat'              => 'Jl. Pendidikan No. ' . ($i + 1) . ', Kota Surabaya',
                'is_active'           => 1,
                'created_at'          => $this->now,
                'updated_at'          => $this->now,
            ]);
            $ids[] = (int) $this->db->insertID();
        }

        return $ids;
    }

    // -------------------------------------------------------------------------
    // Pengurus
    // -------------------------------------------------------------------------
    private function seedPengurus(): array
    {
        if ($this->db->table('pengurus')->countAllResults() > 0) {
            return array_column($this->db->table('pengurus')->select('id_pengurus')->orderBy('id_pengurus', 'ASC')->get()->getResultArray(), 'id_pengurus');
        }

        $rows = [
            ['Budi Santoso', 'Ketua'],
            ['Siti Aminah', 'Sekretaris'],
            ['Andi Wijaya', 'Bendahara'],
            ['Joko Susilo', 'Kabid Pendidikan'],
            ['Ratna Sari', 'Kabid Kesejahteraan'],
            ['Hendra Gunawan', 'Kabid Sarana Prasarana'],
        ];

        $ids = [];

        foreach ($rows as $i => $p) {
            $this->db->table('pengurus')->insert([
                'nama_pengurus' => $p[0],
                'jabatan'       => $p[1],
                'no_hp'         => '0851' . str_pad((string) (2300000 + $i * 7), 7, '0', STR_PAD_LEFT),
                'email'         => strtolower(str_replace(' ', '.', $p[0])) . '@lksa.id',
                'created_at'    => $this->now,
                'updated_at'    => $this->now,
            ]);
            $ids[] = (int) $this->db->insertID();
        }

        return $ids;
    }

    // -------------------------------------------------------------------------
    // Anak
    // -------------------------------------------------------------------------
    private function seedAnak(): array
    {
        if ($this->db->table('anak')->countAllResults() > 0) {
            return array_column($this->db->table('anak')->select('id_anak')->orderBy('id_anak', 'ASC')->get()->getResultArray(), 'id_anak');
        }

        // nama => [jenis_kelamin, tanggal_lahir, status_anak, status_tinggal]
        $entries = [
            ['Ahmad Fadli Ramadhan', 'L', '2011-03-12', 'Aktif'],
            ['Aisyah Putri Ramadhani', 'P', '2012-07-21', 'Aktif'],
            ['Bagus Saputra', 'L', '2010-11-02', 'Aktif'],
            ['Bella Anggraini', 'P', '2013-01-15', 'Aktif'],
            ['Cahyo Nugroho', 'L', '2009-05-30', 'Aktif'],
            ['Citra Lestari', 'P', '2014-09-08', 'Aktif'],
            ['Dimas Prasetyo', 'L', '2015-02-14', 'Aktif'],
            ['Dinda Ayu Lestari', 'P', '2011-12-25', 'Aktif'],
            ['Eko Wijayanto', 'L', '2008-04-18', 'Aktif'],
            ['Eka Putri Maharani', 'P', '2012-06-09', 'Aktif'],
            ['Fajar Ramadhan', 'L', '2010-08-01', 'Aktif'],
            ['Fitri Handayani', 'P', '2016-10-11', 'Aktif'],
            ['Gilang Permana', 'L', '2007-03-22', 'Aktif'],
            ['Gita Savitri', 'P', '2013-05-17', 'Aktif'],
            ['Hendra Kurniawan', 'L', '2009-11-29', 'Aktif'],
            ['Hana Safira', 'P', '2014-07-04', 'Aktif'],
            ['Ilham Maulana', 'L', '2017-01-19', 'Aktif'],
            ['Intan Permatasari', 'P', '2010-09-13', 'Aktif'],
            ['Joko Prasetyo', 'L', '2006-02-27', 'Aktif'],
            ['Julia Rahmawati', 'P', '2012-04-05', 'Aktif'],
            ['Krisna Wijaya', 'L', '2008-08-16', 'Aktif'],
            ['Kartika Dewi', 'P', '2015-12-30', 'Aktif'],
            ['Lukman Hakim', 'L', '2011-10-23', 'Aktif'],
            ['Laila Nurjanah', 'P', '2016-03-07', 'Aktif'],
            ['Muhammad Rizki', 'L', '2009-06-26', 'Nonaktif'],
            ['Mega Puspita', 'P', '2013-08-19', 'Nonaktif'],
            ['Nanda Pratama', 'L', '2007-12-01', 'Nonaktif'],
            ['Nabila Salsabila', 'P', '2010-02-08', 'Nonaktif'],
            ['Oktavianus Putra', 'L', '2006-07-14', 'Alumni'],
            ['Olivia Maharani', 'P', '2005-11-05', 'Alumni'],
            ['Pradipta Mahesa', 'L', '2005-04-20', 'Alumni'],
            ['Putri Ayu Wulandari', 'P', '2006-09-28', 'Alumni'],
            ['Rendi Ardiansyah', 'L', '2012-01-31', 'Aktif'],
            ['Rizka Amelia', 'P', '2014-11-11', 'Aktif'],
            ['Samsul Arifin', 'L', '2013-06-02', 'Aktif'],
            ['Salsabila Zahra', 'P', '2015-04-25', 'Aktif'],
        ];

        $kategoriPool  = ['Yatim', 'Piatu', 'Yatim Piatu', 'Dhuafa', 'Terlantar'];
        $agamaPool     = ['Islam', 'Islam', 'Islam', 'Kristen', 'Katolik'];
        $ayahPool      = ['Slamet Riyadi', 'Sutrisno', 'Wahyudi', 'Purnomo', 'Tarmuji', 'Supardi', 'Mulyono', 'Hartono'];
        $ibuPool       = ['Sumiati', 'Tutik', 'Endang', 'Suyati', 'Siti Romlah', 'Sri Wahyuni', 'Murni', 'Lastri'];
        $pekerjaanPool = ['Buruh', 'Petani', 'Pedagang', 'Wiraswasta', 'Nelayan', 'Karyawan Swasta'];
        $sekolahPool   = ['SDN 1 Sukolilo', 'SDN 2 Rungkut', 'SMPN 5 Surabaya', 'SMAN 3 Surabaya', 'TK Harapan', 'MTs Nurul Huda'];
        $idProvinsiRef = $this->regionProv ?? [1];

        $ids = [];
        $usedNik  = [];

        foreach ($entries as $i => $e) {
            [$nama, $jk, $tglLahir, $status] = $e;

            $birthYear = (int) substr($tglLahir, 0, 4);
            $age       = $this->year - $birthYear;

            if ($age <= 6) {
                [$pendidikan, $kelas] = ['TK', 'TK B'];
            } elseif ($age <= 9) {
                [$pendidikan, $kelas] = ['SD', 'Kelas ' . max(1, $age - 6)];
            } elseif ($age <= 12) {
                [$pendidikan, $kelas] = ['SD', 'Kelas ' . max(4, $age - 6)];
            } elseif ($age <= 15) {
                [$pendidikan, $kelas] = ['SMP', 'Kelas ' . max(7, $age - 6)];
            } else {
                [$pendidikan, $kelas] = ['SMA', 'Kelas ' . min(12, $age - 6)];
            }

            if ($status === 'Alumni') {
                [$pendidikan, $kelas] = ['SMA', 'Lulus'];
            }

            // NIK unik 16 digit (prefix 35 = Jawa Timur).
            do {
                $nik = '35' . str_pad((string) random_int(0, 99_999_999), 8, '0', STR_PAD_LEFT)
                     . str_pad((string) random_int(0, 999_999), 6, '0', STR_PAD_LEFT);
            } while (in_array($nik, $usedNik, true));
            $usedNik[] = $nik;

            $idProvinsi = (int) $idProvinsiRef[array_rand($idProvinsiRef)];
            $kabList    = $this->regionKab[$idProvinsi] ?? [null];
            $idKabupaten = $kabList ? (int) $kabList[array_rand($kabList)] : null;

            $tanggalMasuk = ($status === 'Alumni')
                ? sprintf('%04d-%02d-%02d', $this->year - 6 - ($i % 3), 7, 1 + ($i % 28))
                : sprintf('%04d-%02d-%02d', $this->year - ($i % 6), (($i % 12) + 1), 1 + ($i % 27));

            $this->db->table('anak')->insert([
                'no_registrasi'         => 'LKSA-' . date('Ymd') . '-' . str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT),
                'nama_anak'             => $nama,
                'nik'                   => $nik,
                'no_kk'                 => '35' . str_pad((string) random_int(0, 999_999_999_999), 12, '0', STR_PAD_LEFT) . '01',
                'jenis_kelamin'         => $jk,
                'tempat_lahir'          => ['Surabaya', 'Malang', 'Sidoarjo', 'Gresik', 'Mojokerto'][$i % 5],
                'tanggal_lahir'         => $tglLahir,
                'agama'                 => $agamaPool[$i % count($agamaPool)],
                'kewarganegaraan'       => 'Indonesia',
                'anak_ke'               => 1 + ($i % 3),
                'jumlah_saudara_kandung'=> $i % 4,
                'jumlah_saudara_tiri'   => ($i % 2 === 0) ? 0 : 1,
                'nama_wali'             => ($i % 4 === 0) ? $ayahPool[$i % count($ayahPool)] : null,
                'no_telp_wali'          => ($i % 4 === 0) ? '0821' . str_pad((string) (3300000 + $i * 3), 7, '0', STR_PAD_LEFT) : null,
                'alamat_wali'           => ($i % 4 === 0) ? 'Jl. Wali No. ' . ($i + 1) . ', Surabaya' : null,
                'nama_ayah_kandung'     => $ayahPool[$i % count($ayahPool)],
                'nama_ayah_tiri'        => ($i % 7 === 0) ? $ayahPool[($i + 2) % count($ayahPool)] : null,
                'nama_ibu_kandung'      => $ibuPool[$i % count($ibuPool)],
                'nama_ibu_tiri'         => ($i % 9 === 0) ? $ibuPool[($i + 1) % count($ibuPool)] : null,
                'pekerjaan_ayah'        => $pekerjaanPool[$i % count($pekerjaanPool)],
                'pekerjaan_ibu'         => ($i % 3 === 0) ? 'Ibu Rumah Tangga' : $pekerjaanPool[($i + 1) % count($pekerjaanPool)],
                'no_telp_orang_tua'     => '0812' . str_pad((string) (4400000 + $i * 5), 7, '0', STR_PAD_LEFT),
                'alamat_orang_tua'      => 'Jl. Melati No. ' . ($i + 1) . ', ' . ['Surabaya', 'Malang', 'Sidoarjo'][$i % 3],
                'pendidikan'            => $pendidikan,
                'status_anak'           => $status,
                'status_tinggal'        => ($status === 'Alumni') ? 'Tinggal di Orang Tua' : (($i % 3 === 0) ? 'Tinggal di Orang Tua' : 'Tinggal di LKSA'),
                'kategori'              => $kategoriPool[$i % count($kategoriPool)],
                'tanggal_masuk'         => $tanggalMasuk,
                'id_provinsi'           => $idProvinsi,
                'id_kabupaten'          => $idKabupaten,
                'nama_sekolah'          => $sekolahPool[$i % count($sekolahPool)],
                'kelas'                 => $kelas,
                'alamat_sekolah'        => 'Jl. Raya ' . ($i + 1) . ', Surabaya',
                'no_telp_sekolah'       => '0318' . str_pad((string) (120000 + $i), 6, '0', STR_PAD_LEFT),
                'biaya_spp'             => ($i % 4 === 0) ? 150000 + ($i % 5) * 50000 : null,
                'created_at'            => $this->now,
                'updated_at'            => $this->now,
            ]);
            $ids[] = (int) $this->db->insertID();
        }

        return $ids;
    }

    // -------------------------------------------------------------------------
    // User
    // -------------------------------------------------------------------------
    private function seedUsers(array $guruIds, array $pengurusIds, array $anakIds): array
    {
        $roles = [];
        foreach ($this->db->table('roles')->get()->getResultArray() as $r) {
            $roles[$r['name']] = (int) $r['id_role'];
        }

        $all        = [];
        $byUsername = [];
        $pengajar   = [];
        $anakUsers  = [];

        // Ambil semus username yang sudah ada agar tidak duplikat.
        $existing = array_column(
            $this->db->table('users')->select('username')->get()->getResultArray(),
            'username'
        );

        $insertUser = function (array $data) use (&$all, &$byUsername, $existing): void {
            if (in_array($data['username'], $existing, true)) {
                $row = $this->db->table('users')->where('username', $data['username'])->get()->getRowArray();
                if ($row) {
                    $all[] = (int) $row['id_user'];
                    $byUsername[$data['username']] = (int) $row['id_user'];
                }

                return;
            }

            $this->db->table('users')->insert($data);
            $id = (int) $this->db->insertID();
            $existing[] = $data['username'];
            $all[] = $id;
            $byUsername[$data['username']] = $id;
        };

        // Standalone user peran.
        $standalone = [
            ['Petugas Lapangan', 'petugas', 'petugas123', 'petugas'],
            ['Dinas Sosial', 'dinas', 'dinas123', 'dinas'],
            ['Operator Akademik', 'operator', 'operator123', 'operator'],
        ];

        foreach ($standalone as [$nama, $username, $pass, $role]) {
            $insertUser([
                'nama'       => $nama,
                'username'   => $username,
                'password'   => password_hash($pass, PASSWORD_BCRYPT),
                'id_role'    => $roles[$role],
                'is_active'  => 1,
                'created_at' => $this->now,
                'updated_at' => $this->now,
            ]);
        }

        // Account guru -> role pengajar.
        foreach ($guruIds as $i => $idGuru) {
            $guru = $this->db->table('guru')->where('id_guru', $idGuru)->get()->getRowArray();
            $username = 'guru' . ($i + 1);
            $insertUser([
                'nama'       => $guru['nama_guru'],
                'username'   => $username,
                'password'   => password_hash('guru123', PASSWORD_BCRYPT),
                'id_role'    => $roles['pengajar'],
                'id_guru'    => (int) $idGuru,
                'is_active'  => 1,
                'created_at' => $this->now,
                'updated_at' => $this->now,
            ]);
            $pengajar[] = $byUsername[$username];
        }

        // Account pengurus -> role petugas.
        foreach ($pengurusIds as $i => $idPengurus) {
            $peng = $this->db->table('pengurus')->where('id_pengurus', $idPengurus)->get()->getRowArray();
            $username = 'pengurus' . ($i + 1);
            $insertUser([
                'nama'         => $peng['nama_pengurus'],
                'username'     => $username,
                'password'     => password_hash('pengurus123', PASSWORD_BCRYPT),
                'id_role'      => $roles['petugas'],
                'id_pengurus'  => (int) $idPengurus,
                'is_active'    => 1,
                'created_at'   => $this->now,
                'updated_at'   => $this->now,
            ]);
        }

        // Account anak -> role anak.
        foreach ($anakIds as $i => $idAnak) {
            $anak = $this->db->table('anak')->where('id_anak', $idAnak)->get()->getRowArray();
            $username = 'anak' . ($i + 1);
            $insertUser([
                'nama'       => $anak['nama_anak'],
                'username'   => $username,
                'password'   => password_hash('anak123', PASSWORD_BCRYPT),
                'id_role'    => $roles['anak'],
                'id_anak'    => (int) $idAnak,
                'is_active'  => 1,
                'created_at' => $this->now,
                'updated_at' => $this->now,
            ]);
            $anakUsers[] = $byUsername[$username];
        }

        return [
            'all'        => $all,
            'by_username'=> $byUsername,
            'pengajar'   => $pengajar,
            'anak'       => $anakUsers,
        ];
    }

    // -------------------------------------------------------------------------
    // Log Aktivitas
    // -------------------------------------------------------------------------
    private function seedLogs(array $allUserIds, array $byUsername): void
    {
        if ($this->db->table('user_logs')->countAllResults() >= 15) {
            return;
        }

        $activities = [
            ['login', 'Masuk ke sistem'],
            ['add_anak', 'Menambahkan data anak baru'],
            ['edit_anak', 'Memperbarui data anak'],
            ['add_pengurus', 'Menambahkan data pengurus'],
            ['edit_user', 'Memperbarui data pengguna'],
            ['update_settings', 'Memperbarui pengaturan LKSA'],
            ['upload_foto', 'Mengunggah foto anak'],
            ['export_pdf', 'Mengekspor laporan PDF'],
            ['export_excel', 'Mengekspor laporan Excel'],
            ['backup_database', 'Membuat cadangan database'],
            ['delete_anak', 'Menghapus data anak'],
            ['add_user', 'Menambahkan pengguna baru'],
        ];

        $rows = [];
        for ($i = 0; $i < 40; $i++) {
            $act = $activities[$i % count($activities)];
            $idUser = $allUserIds[array_rand($allUserIds)];
            $rows[] = [
                'id_user'     => $idUser,
                'activity'    => $act[0],
                'description' => $act[1],
                'ip_address'  => '192.168.1.' . (2 + ($i % 200)),
                'user_agent'  => 'Mozilla/5.0 (Dummy)',
                'created_at'  => date('Y-m-d H:i:s', strtotime('-' . ($i * 7 + 2) . ' hours')),
            ];
        }

        // Urutkan agar yang di-insert berurutan waktunya (opsional).
        usort($rows, static fn ($a, $b) => $b['created_at'] <=> $a['created_at']);

        foreach (array_reverse($rows) as $row) {
            $this->db->table('user_logs')->insert($row);
        }
    }

    // -------------------------------------------------------------------------
    // Laporan / Arsip
    // -------------------------------------------------------------------------
    private function seedLaporan(): void
    {
        if ($this->db->table('laporan')->countAllResults() > 0) {
            return;
        }

        $rows = [
            ['Laporan Tahunan', $this->year - 1, 'laporan_tahunan_' . ($this->year - 1) . '.pdf'],
            ['Laporan Semester Ganjil', $this->year - 1, 'laporan_smt_ganjil_' . ($this->year - 1) . '.pdf'],
            ['Laporan Semester Genap', $this->year - 1, 'laporan_smt_genap_' . ($this->year - 1) . '.pdf'],
            ['Laporan Penilaian Karakter', $this->year, 'laporan_karakter_' . $this->year . '.pdf'],
            ['Laporan Data Anak', $this->year, 'laporan_anak_' . $this->year . '.pdf'],
            ['Laporan Kegiatan Sosial', $this->year - 2, 'laporan_kegiatan_' . ($this->year - 2) . '.pdf'],
        ];

        foreach ($rows as $r) {
            $this->db->table('laporan')->insert([
                'jenis_laporan' => $r[0],
                'tahun'         => $r[1],
                'file_laporan'  => $r[2],
                'created_at'    => $this->now,
            ]);
        }
    }

    // -------------------------------------------------------------------------
    // Akademik: mapel, rombel, rombel_anak, rombel_mata_pelajaran
    // -------------------------------------------------------------------------
    private function seedAkademik(array $pengajarUserIds, array $anakIds): array
    {
        if ($this->db->table('mata_pelajaran')->countAllResults() > 0) {
            // Ambil id yang sudah ada agar tetap bisa lanjut ke absensi.
            return [
                'mapel'         => array_column($this->db->table('mata_pelajaran')->select('id_mata_pelajaran')->orderBy('id_mata_pelajaran', 'ASC')->get()->getResultArray(), 'id_mata_pelajaran'),
                'rombel'        => array_column($this->db->table('rombel')->select('id_rombel')->orderBy('id_rombel', 'ASC')->get()->getResultArray(), 'id_rombel'),
                'rombel_anak'   => [],
            ];
        }

        $tahunAjaran = $this->year . '/' . ($this->year + 1);
        $semester    = 1;

        // Mata pelajaran
        $mapelRows = [
            ['MTK', 'Matematika'],
            ['BIN', 'Bahasa Indonesia'],
            ['BIG', 'Bahasa Inggris'],
            ['AGM', 'Pendidikan Agama'],
            ['IPA', 'IPA Terpadu'],
            ['IPS', 'IPS Terpadu'],
            ['PKN', 'Pendidikan Kewarganegaraan'],
            ['SEN', 'Seni Budaya'],
            ['PJOK', 'Pendidikan Jasmani'],
            ['BK', 'Bimbingan Konseling'],
        ];

        $mapelIds = [];
        foreach ($mapelRows as $i => $m) {
            $this->db->table('mata_pelajaran')->insert([
                'kode_mapel'       => $m[0],
                'nama_mapel'       => $m[1],
                'id_user_pengampu' => $pengajarUserIds[$i % count($pengajarUserIds)] ?? null,
                'is_active'        => 1,
                'created_at'       => $this->now,
                'updated_at'       => $this->now,
            ]);
            $mapelIds[] = (int) $this->db->insertID();
        }

        // Rombel
        $rombelRows = [
            ['TK-A', 'TK A'],
            ['SD-A', 'SD Kelas 1-3'],
            ['SD-B', 'SD Kelas 4-6'],
            ['SMP', 'SMP'],
            ['SMA', 'SMA'],
            ['SMK', 'SMK'],
        ];

        $rombelIds = [];
        foreach ($rombelRows as $i => $r) {
            $this->db->table('rombel')->insert([
                'kode_rombel'  => $r[0],
                'nama_rombel'  => $r[1],
                'tahun_ajaran' => $tahunAjaran,
                'semester'     => $semester,
                'is_active'    => 1,
                'keterangan'   => 'Rombongan belajar ' . $r[1],
                'created_at'   => $this->now,
                'updated_at'   => $this->now,
            ]);
            $rombelIds[] = (int) $this->db->insertID();
        }

        // Mapping anak -> rombel berdasarkan pendidikan.
        $rombelAnakRows = [];
        $anakRows       = $this->db->table('anak')
            ->select('id_anak, nama_anak, pendidikan, kelas, status_anak')
            ->get()->getResultArray();

        foreach ($anakRows as $a) {
            if ($a['status_anak'] !== 'Aktif') {
                continue;
            }

            $pend = $a['pendidikan'];
            $kelas = $a['kelas'];

            $idRombel = null;

            if ($pend === 'TK') {
                $idRombel = $rombelIds[0];
            } elseif ($pend === 'SD') {
                $nomor = (int) filter_var($kelas, FILTER_SANITIZE_NUMBER_INT);
                $idRombel = ($nomor >= 4) ? $rombelIds[2] : $rombelIds[1];
            } elseif ($pend === 'SMP') {
                $idRombel = $rombelIds[3];
            } else {
                $idRombel = ($pend === 'SMK') ? $rombelIds[5] : $rombelIds[4];
            }

            if ($idRombel !== null) {
                $rombelAnakRows[] = [
                    'id_rombel'  => (int) $idRombel,
                    'id_anak'    => (int) $a['id_anak'],
                    'created_at' => $this->now,
                ];
            }
        }

        if ($rombelAnakRows !== []) {
            $this->db->table('rombel_anak')->insertBatch($rombelAnakRows);
        }

        // Mapping mapel -> rombel (semua rombel mendapat semua mapel, kecuali TK).
        $rombelMapelRows = [];
        foreach ($rombelIds as $i => $idRombel) {
            foreach ($mapelIds as $idMapel) {
                // TK cukup mapel dasar.
                if ($i === 0 && ! in_array($idMapel, [ $mapelIds[5], $mapelIds[7], $mapelIds[8] ], true)) {
                    continue;
                }
                $rombelMapelRows[] = [
                    'id_rombel'         => $idRombel,
                    'id_mata_pelajaran' => $idMapel,
                    'created_at'        => $this->now,
                ];
            }
        }

        if ($rombelMapelRows !== []) {
            $this->db->table('rombel_mata_pelajaran')->insertBatch($rombelMapelRows);
        }

        return [
            'mapel'       => $mapelIds,
            'rombel'      => $rombelIds,
            'rombel_anak' => $rombelAnakRows,
        ];
    }

    // -------------------------------------------------------------------------
    // Absensi Mapel
    // -------------------------------------------------------------------------
    private function seedAbsensi(array $akademik, array $pengajarUserIds): void
    {
        if ($this->db->table('absensi_mapel_sessions')->countAllResults() > 0) {
            return;
        }

        if (empty($akademik['rombel']) || empty($akademik['mapel'])) {
            return;
        }

        $rombelMapel = $this->db->table('rombel_mata_pelajaran')->get()->getResultArray();
        if ($rombelMapel === []) {
            return;
        }

        $tahunAjaran = $this->year . '/' . ($this->year + 1);
        $semester    = 1;
        $statuses    = ['Hadir', 'Hadir', 'Hadir', 'Hadir', 'Izin', 'Sakit', 'Alpha'];

        $sessionCount  = 0;
        $createdByPool = $pengajarUserIds;

        foreach ($rombelMapel as $rm) {
            if ($sessionCount >= 60) {
                break;
            }

            $idRombel = $rm['id_rombel'];
            $idMapel  = $rm['id_mata_pelajaran'];

            $anakInRombel = $this->db->table('rombel_anak')
                ->select('id_anak')
                ->where('id_rombel', $idRombel)
                ->get()->getResultArray();

            if ($anakInRombel === []) {
                continue;
            }

            // 2 - 3 sesi absensi per rombel-mapel.
            $nSesi = 2 + ($idRombel % 2);
            for ($s = 0; $s < $nSesi; $s++) {
                $tanggal = date('Y-m-d', strtotime('-' . (($sessionCount % 20) + $s + 1) . ' days'));

                $this->db->table('absensi_mapel_sessions')->insert([
                    'id_rombel'         => (int) $idRombel,
                    'id_mata_pelajaran' => (int) $idMapel,
                    'tahun_ajaran'      => $tahunAjaran,
                    'semester'          => $semester,
                    'tanggal_absensi'   => $tanggal,
                    'catatan'           => 'Absensi mata pelajaran',
                    'created_by'        => $createdByPool[array_rand($createdByPool)] ?? null,
                    'created_at'        => $this->now,
                    'updated_at'        => $this->now,
                ]);
                $idSession = (int) $this->db->insertID();

                $details = [];
                foreach ($anakInRombel as $a) {
                    $status = $statuses[array_rand($statuses)];
                    $details[] = [
                        'id_session'       => $idSession,
                        'id_anak'          => (int) $a['id_anak'],
                        'status_kehadiran' => $status,
                        'keterangan'       => ($status === 'Hadir') ? null : ucfirst(strtolower($status)),
                        'created_at'       => $this->now,
                        'updated_at'       => $this->now,
                    ];
                }

                if ($details !== []) {
                    $this->db->table('absensi_mapel_details')->insertBatch($details);
                }

                $sessionCount++;
            }
        }
    }

    // -------------------------------------------------------------------------
    // Penilaian Karakter
    // -------------------------------------------------------------------------
    private function seedCharacterAssessments(array $anakIds, array $userIds): void
    {
        if ($this->db->table('character_assessments')->countAllResults() > 0) {
            return;
        }

        $indicators = $this->db->table('character_indicators')->select('id_indicator')->orderBy('id_indicator', 'ASC')->get()->getResultArray();
        $indicatorIds = array_column($indicators, 'id_indicator');
        if ($indicatorIds === []) {
            return;
        }

        $assessorGuru = $userIds['pengajar'][0] ?? null;
        if ($assessorGuru === null) {
            return;
        }

        $anakAktif = $this->db->table('anak')
            ->select('id_anak')
            ->where('status_anak', 'Aktif')
            ->orderBy('id_anak', 'ASC')
            ->get()->getResultArray();

        $strengths = [
            'Gemar membantu teman dan menunjukkan keramahan yang baik.',
            'Menunjukkan rasa ingin tahu yang tinggi dalam belajar.',
            'Mampu menyelesaikan tugas secara mandiri dan tepat waktu.',
            'Berperilaku sopan serta menghargai pendapat orang lain.',
        ];
        $development = [
            'Mulai konsisten dalam menjaga kebersihan diri dan lingkungan.',
            'Perkembangan emosi semakin stabil saat menghadapi masalah.',
            'Keberanian berbicara di depan umum terus meningkat.',
        ];
        $support = [
            'Perlu pendampingan saat menyelesaikan tugas kelompok.',
            'Memerlukan penguatan motivasi dalam kegiatan harian.',
            'Butuh latihan rutin pengendalian emosi.',
        ];
        $strategy = [
            'Memberikan aktivitas kelompok untuk melatih kerja sama.',
            'Mengajak anak berdiskusi ringan untuk membangun rasa percaya diri.',
            'Menerapkan jadwal rutin dan pendampingan belajar bertahap.',
        ];

        $assessmentBatch = [];

        foreach ($anakAktif as $a) {
            $idAnak = (int) $a['id_anak'];

            // Satu penilaian guru per bulan (Januari sampai bulan berjalan).
            for ($m = 1; $m <= $this->month; $m++) {
                $assessmentDate = sprintf('%04d-%02d-%02d', $this->year, $m, 5 + ($idAnak % 20));
                $week           = (int) ceil(((($m - 1) * 30) + ($idAnak % 28) + 1) / 7);

                $assessmentBatch[] = [
                    'id_anak'         => $idAnak,
                    'id_assessor'     => $assessorGuru,
                    'assessor_type'   => 'guru',
                    'assessment_date' => $assessmentDate,
                    'week_number'     => max(1, min(52, $week)),
                    'month'           => $m,
                    'year'            => $this->year,
                    'notes'           => 'Penilaian karakter bulanan',
                    'status'          => 'completed',
                    'created_at'      => $this->now,
                    'updated_at'      => $this->now,
                ];
            }
        }

        if ($assessmentBatch === []) {
            return;
        }

        // Insert assessments per-batch kecil lain.
        foreach (array_chunk($assessmentBatch, 500) as $chunk) {
            $this->db->table('character_assessments')->insertBatch($chunk);
        }

        // Untuk detail + note, kita perlu id_assessment. Ambil ulang hasil insert.
        $allAssessments = $this->db->table('character_assessments')
            ->where('assessor_type', 'guru')
            ->orderBy('id_assessment', 'ASC')
            ->get()->getResultArray();

        $dRows = [];
        $nRows = [];

        foreach ($allAssessments as $ass) {
            foreach ($indicatorIds as $idIndicator) {
                $dRows[] = [
                    'id_assessment' => (int) $ass['id_assessment'],
                    'id_indicator'  => (int) $idIndicator,
                    'score'         => random_int(1, 4),
                    'created_at'    => $this->now,
                    'updated_at'    => $this->now,
                ];
            }

            $nRows[] = [
                'id_assessment'        => (int) $ass['id_assessment'],
                'strengths'            => $strengths[$ass['id_assessment'] % count($strengths)],
                'development_observed' => $development[$ass['id_assessment'] % count($development)],
                'areas_to_support'     => $support[$ass['id_assessment'] % count($support)],
                'support_strategy'     => $strategy[$ass['id_assessment'] % count($strategy)],
                'created_at'           => $this->now,
                'updated_at'           => $this->now,
            ];

            // Insert bertahap agar tidak memori membengkak.
            if (count($dRows) >= 2000) {
                $this->db->table('character_assessment_details')->insertBatch($dRows);
                $dRows = [];
            }
            if (count($nRows) >= 500) {
                $this->db->table('character_qualitative_notes')->insertBatch($nRows);
                $nRows = [];
            }
        }

        if ($dRows !== []) {
            $this->db->table('character_assessment_details')->insertBatch($dRows);
        }
        if ($nRows !== []) {
            $this->db->table('character_qualitative_notes')->insertBatch($nRows);
        }

        $this->rebuildSummaries();
    }

    // Hitung ulang ringkasan mingguan & bulanan dari data detail.
    private function rebuildSummaries(): void
    {
        $this->db->table('character_weekly_summary')->truncate();
        $this->db->table('character_monthly_summary')->truncate();

        $this->db->query("
            INSERT INTO character_monthly_summary
                (id_anak, id_aspect, month, year, assessor_type, avg_score, last_updated)
            SELECT a.id_anak, i.id_aspect, a.month, a.year, a.assessor_type,
                   ROUND(AVG(d.score), 2), NOW()
            FROM character_assessments a
            JOIN character_assessment_details d ON d.id_assessment = a.id_assessment
            JOIN character_indicators i ON i.id_indicator = d.id_indicator
            WHERE a.month IS NOT NULL AND a.year IS NOT NULL
            GROUP BY a.id_anak, i.id_aspect, a.month, a.year, a.assessor_type
        ");

        $this->db->query("
            INSERT INTO character_weekly_summary
                (id_anak, id_aspect, week_number, year, assessor_type, avg_score, last_updated)
            SELECT a.id_anak, i.id_aspect, a.week_number, a.year, a.assessor_type,
                   ROUND(AVG(d.score), 2), NOW()
            FROM character_assessments a
            JOIN character_assessment_details d ON d.id_assessment = a.id_assessment
            JOIN character_indicators i ON i.id_indicator = d.id_indicator
            WHERE a.week_number IS NOT NULL AND a.year IS NOT NULL
            GROUP BY a.id_anak, i.id_aspect, a.week_number, a.year, a.assessor_type
        ");
    }
}