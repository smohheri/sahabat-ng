<?php

namespace App\Models;

use CodeIgniter\Model;

class RombelModel extends Model
{
    protected $table            = 'rombel';
    protected $primaryKey       = 'id_rombel';
    protected $returnType       = 'array';
    protected $allowedFields    = ['kode_rombel', 'nama_rombel', 'tahun_ajaran', 'semester', 'is_active', 'keterangan'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $validationRules  = [
        'kode_rombel'  => 'required|max_length[50]',
        'nama_rombel'  => 'required|max_length[150]',
        'tahun_ajaran' => 'required|max_length[20]',
        'semester'     => 'required|in_list[1,2]',
    ];

    public function getList(array $filters = []): array
    {
        $builder = $this->builder();

        if (! empty($filters['search'])) {
            $search = trim((string) $filters['search']);
            $builder->groupStart()
                ->like('kode_rombel', $search)
                ->orLike('nama_rombel', $search)
                ->orLike('tahun_ajaran', $search)
                ->groupEnd();
        }

        if (! empty($filters['tahun_ajaran'])) {
            $builder->where('tahun_ajaran', $filters['tahun_ajaran']);
        }

        if (! empty($filters['semester'])) {
            $builder->where('semester', (int) $filters['semester']);
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $builder->where('is_active', (int) $filters['is_active']);
        }

        return $builder->orderBy('tahun_ajaran', 'DESC')->orderBy('semester', 'DESC')->orderBy('nama_rombel', 'ASC')->get()->getResultArray();
    }

    public function activeByPeriod(?string $tahunAjaran = null, ?int $semester = null): array
    {
        $builder = $this->where('is_active', 1);

        if ($tahunAjaran !== null && $tahunAjaran !== '') {
            $builder->where('tahun_ajaran', $tahunAjaran);
        }

        if ($semester !== null && $semester > 0) {
            $builder->where('semester', $semester);
        }

        return $builder->orderBy('nama_rombel', 'ASC')->findAll();
    }

    public function getChildren(int $idRombel): array
    {
        return $this->db->table('rombel_anak ra')
            ->select('anak.*')
            ->join('anak', 'anak.id_anak = ra.id_anak')
            ->where('ra.id_rombel', $idRombel)
            ->orderBy('anak.nama_anak', 'ASC')
            ->get()->getResultArray();
    }

    public function getMapel(int $idRombel): array
    {
        return $this->db->table('rombel_mata_pelajaran rm')
            ->select('mata_pelajaran.*, users.nama AS pengampu')
            ->join('mata_pelajaran', 'mata_pelajaran.id_mata_pelajaran = rm.id_mata_pelajaran')
            ->join('users', 'users.id_user = mata_pelajaran.id_user_pengampu', 'left')
            ->where('rm.id_rombel', $idRombel)
            ->orderBy('mata_pelajaran.nama_mapel', 'ASC')
            ->get()->getResultArray();
    }

    public function anakIds(int $idRombel): array
    {
        $rows = $this->db->table('rombel_anak')->select('id_anak')->where('id_rombel', $idRombel)->get()->getResultArray();

        return array_map('intval', array_column($rows, 'id_anak'));
    }

    public function mapelIds(int $idRombel): array
    {
        $rows = $this->db->table('rombel_mata_pelajaran')->select('id_mata_pelajaran')->where('id_rombel', $idRombel)->get()->getResultArray();

        return array_map('intval', array_column($rows, 'id_mata_pelajaran'));
    }

    public function saveWithRelations(?int $idRombel, array $data, array $anakIds, array $mapelIds): int|false
    {
        $db = $this->db;
        $db->transBegin();

        try {
            if ($idRombel === null || $idRombel <= 0) {
                $idRombel = (int) $this->insert($data, true);
            } else {
                $this->update($idRombel, $data);
            }

            if ($idRombel <= 0) {
                throw new \RuntimeException('Gagal menyimpan rombel.');
            }

            $db->table('rombel_anak')->where('id_rombel', $idRombel)->delete();
            $db->table('rombel_mata_pelajaran')->where('id_rombel', $idRombel)->delete();

            if ($anakIds !== []) {
                $rows = array_map(static fn ($id) => ['id_rombel' => $idRombel, 'id_anak' => (int) $id], array_unique($anakIds));
                $db->table('rombel_anak')->insertBatch($rows);
            }

            if ($mapelIds !== []) {
                $rows = array_map(static fn ($id) => ['id_rombel' => $idRombel, 'id_mata_pelajaran' => (int) $id], array_unique($mapelIds));
                $db->table('rombel_mata_pelajaran')->insertBatch($rows);
            }

            $db->transCommit();

            return $idRombel;
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Simpan rombel gagal: ' . $e->getMessage());

            return false;
        }
    }

    public function isKodeTaken(string $kode, ?int $exceptId = null): bool
    {
        $builder = $this->where('kode_rombel', $kode);

        if ($exceptId !== null) {
            $builder->where('id_rombel !=', $exceptId);
        }

        return $builder->countAllResults() > 0;
    }

    public function isNameTaken(string $nama, string $tahun, int $semester, ?int $exceptId = null): bool
    {
        $builder = $this->where('nama_rombel', $nama)->where('tahun_ajaran', $tahun)->where('semester', $semester);

        if ($exceptId !== null) {
            $builder->where('id_rombel !=', $exceptId);
        }

        return $builder->countAllResults() > 0;
    }

    public function isMapelInRombel(int $idRombel, int $idMapel): bool
    {
        return $this->db->table('rombel_mata_pelajaran')
            ->where('id_rombel', $idRombel)
            ->where('id_mata_pelajaran', $idMapel)
            ->countAllResults() > 0;
    }

    public function yearOptions(): array
    {
        $rows = $this->select('tahun_ajaran')->distinct()->orderBy('tahun_ajaran', 'DESC')->findAll();

        return array_column($rows, 'tahun_ajaran');
    }
}
