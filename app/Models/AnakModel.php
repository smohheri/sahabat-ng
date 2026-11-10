<?php

namespace App\Models;

use CodeIgniter\Model;

class AnakModel extends Model
{
    protected $table            = 'anak';
    protected $primaryKey       = 'id_anak';
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'no_registrasi', 'nama_anak', 'nik', 'no_kk', 'jenis_kelamin',
        'tempat_lahir', 'tanggal_lahir', 'agama', 'kewarganegaraan',
        'anak_ke', 'jumlah_saudara_kandung', 'jumlah_saudara_tiri',
        'nama_wali', 'no_telp_wali', 'alamat_wali',
        'nama_ayah_kandung', 'nama_ayah_tiri', 'nama_ibu_kandung', 'nama_ibu_tiri',
        'pekerjaan_ayah', 'pekerjaan_ibu', 'no_telp_orang_tua', 'alamat_orang_tua',
        'pendidikan', 'status_anak', 'status_tinggal', 'kategori', 'tanggal_masuk',
        'id_provinsi', 'id_kabupaten', 'nama_sekolah', 'kelas', 'alamat_sekolah',
        'no_telp_sekolah', 'biaya_spp', 'file_kk', 'file_akta', 'file_pendukung', 'foto',
    ];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $validationRules  = [
        'nama_anak'     => 'required|min_length[2]|max_length[150]',
        'jenis_kelamin' => 'required|in_list[L,P]',
    ];

    public function byId(int $id): ?array
    {
        return $this->select('anak.*, provinsi.nama_provinsi, kabupaten.nama_kabupaten')
            ->join('provinsi', 'provinsi.id_provinsi = anak.id_provinsi', 'left')
            ->join('kabupaten', 'kabupaten.id_kabupaten = anak.id_kabupaten', 'left')
            ->where('anak.id_anak', $id)
            ->first();
    }

    public function getList(array $filters = [], int $limit = 0, int $offset = 0): array
    {
        $builder = $this->select('anak.*, provinsi.nama_provinsi, kabupaten.nama_kabupaten')
            ->join('provinsi', 'provinsi.id_provinsi = anak.id_provinsi', 'left')
            ->join('kabupaten', 'kabupaten.id_kabupaten = anak.id_kabupaten', 'left');

        $this->applyFilters($builder, $filters);

        $sort = $filters['sort'] ?? 'nama_anak';
        $dir  = strtoupper($filters['dir'] ?? 'ASC') === 'DESC' ? 'DESC' : 'ASC';
        $validSort = ['nama_anak', 'jenis_kelamin', 'pendidikan', 'status_anak', 'tanggal_masuk', 'kategori', 'nama_sekolah', 'created_at'];

        $builder->orderBy(in_array($sort, $validSort, true) ? 'anak.' . $sort : 'anak.nama_anak', $dir);

        if ($limit > 0) {
            $builder->limit($limit, max(0, $offset));
        }

        return $builder->findAll();
    }

    public function countList(array $filters = []): int
    {
        $builder = $this->builder();
        $this->applyFilters($builder, $filters);

        return $builder->countAllResults();
    }

    private function applyFilters($builder, array $filters): void
    {
        if (! empty($filters['status_anak'])) {
            if (is_array($filters['status_anak'])) {
                $builder->whereIn('anak.status_anak', $filters['status_anak']);
            } else {
                $builder->where('anak.status_anak', $filters['status_anak']);
            }
        }

        if (! empty($filters['jenis_kelamin'])) {
            $builder->where('anak.jenis_kelamin', $filters['jenis_kelamin']);
        }

        if (! empty($filters['pendidikan'])) {
            $builder->where('anak.pendidikan', $filters['pendidikan']);
        }

        if (! empty($filters['kategori'])) {
            $builder->where('anak.kategori', $filters['kategori']);
        }

        if (! empty($filters['search'])) {
            $search = trim((string) $filters['search']);
            $builder->groupStart()
                ->like('anak.nama_anak', $search)
                ->orLike('anak.nik', $search)
                ->orLike('anak.no_kk', $search)
                ->orLike('anak.no_registrasi', $search)
                ->orLike('anak.tempat_lahir', $search)
                ->orLike('anak.kategori', $search)
                ->orLike('anak.nama_sekolah', $search)
                ->orLike('anak.nama_wali', $search)
                ->orLike('anak.nama_ayah_kandung', $search)
                ->orLike('anak.nama_ibu_kandung', $search)
                ->groupEnd();
        }
    }

    public function allAktif(): array
    {
        return $this->where('status_anak', 'Aktif')->orderBy('nama_anak', 'ASC')->findAll();
    }

    public function generateNoRegistrasi(): string
    {
        $prefix = 'LKSA-' . date('Ymd') . '-';
        $last   = $this->like('no_registrasi', $prefix, 'after')->orderBy('id_anak', 'DESC')->first();
        $next   = 1;

        if ($last && preg_match('/(\d+)$/', (string) $last['no_registrasi'], $m)) {
            $next = ((int) $m[1]) + 1;
        }

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    public function statistics(): array
    {
        $rows = $this->select('jenis_kelamin, pendidikan, status_anak, kategori, tanggal_lahir, tanggal_masuk, tempat_lahir')
            ->findAll();

        $stats = [
            'total' => count($rows), 'aktif' => 0, 'nonaktif' => 0, 'alumni' => 0,
            'laki' => 0, 'perempuan' => 0,
            'sd' => 0, 'smp' => 0, 'sma' => 0, 'pt' => 0, 'tk' => 0,
            'usia_0_5' => 0, 'usia_5_12' => 0, 'usia_13_17' => 0, 'usia_17' => 0,
            'kategori' => [], 'tahun_masuk' => [], 'tempat_lahir' => [],
        ];

        foreach ($rows as $row) {
            $status = strtolower((string) $row['status_anak']);
            if ($status === 'aktif') {
                $stats['aktif']++;
            } elseif ($status === 'alumni') {
                $stats['alumni']++;
            } else {
                $stats['nonaktif']++;
            }

            $row['jenis_kelamin'] === 'L' ? $stats['laki']++ : $stats['perempuan']++;

            $pend = strtolower((string) $row['pendidikan']);
            if (str_contains($pend, 'tk')) {
                $stats['tk']++;
            } elseif (str_contains($pend, 'sd') || str_contains($pend, 'mi')) {
                $stats['sd']++;
            } elseif (str_contains($pend, 'smp') || str_contains($pend, 'mts')) {
                $stats['smp']++;
            } elseif (str_contains($pend, 'sma') || str_contains($pend, 'smk') || str_contains($pend, 'ma')) {
                $stats['sma']++;
            } elseif (str_contains($pend, 'pt') || str_contains($pend, 'univ') || str_contains($pend, 'd3') || str_contains($pend, 's1')) {
                $stats['pt']++;
            }

            if (! empty($row['tanggal_lahir'])) {
                $age = (new \DateTime($row['tanggal_lahir']))->diff(new \DateTime())->y;
                if ($age < 5) {
                    $stats['usia_0_5']++;
                } elseif ($age <= 12) {
                    $stats['usia_5_12']++;
                } elseif ($age <= 17) {
                    $stats['usia_13_17']++;
                } else {
                    $stats['usia_17']++;
                }
            }

            $kat = trim((string) $row['kategori']);
            if ($kat !== '') {
                $stats['kategori'][$kat] = ($stats['kategori'][$kat] ?? 0) + 1;
            }

            $tahun = ! empty($row['tanggal_masuk']) ? substr((string) $row['tanggal_masuk'], 0, 4) : '';
            if ($tahun !== '') {
                $stats['tahun_masuk'][$tahun] = ($stats['tahun_masuk'][$tahun] ?? 0) + 1;
            }

            $tempat = trim((string) $row['tempat_lahir']);
            if ($tempat !== '') {
                $stats['tempat_lahir'][$tempat] = ($stats['tempat_lahir'][$tempat] ?? 0) + 1;
            }
        }

        arsort($stats['kategori']);
        ksort($stats['tahun_masuk']);
        arsort($stats['tempat_lahir']);

        return $stats;
    }
}
