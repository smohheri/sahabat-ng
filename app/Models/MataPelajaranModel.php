<?php

namespace App\Models;

use CodeIgniter\Model;

class MataPelajaranModel extends Model
{
    protected $table            = 'mata_pelajaran';
    protected $primaryKey       = 'id_mata_pelajaran';
    protected $returnType       = 'array';
    protected $allowedFields    = ['kode_mapel', 'nama_mapel', 'id_user_pengampu', 'is_active'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $validationRules  = [
        'kode_mapel' => 'required|max_length[50]',
        'nama_mapel' => 'required|max_length[150]',
    ];

    public function getList(array $filters = []): array
    {
        $builder = $this->select('mata_pelajaran.*, users.nama AS pengampu')
            ->join('users', 'users.id_user = mata_pelajaran.id_user_pengampu', 'left');

        if (! empty($filters['search'])) {
            $search = trim((string) $filters['search']);
            $builder->groupStart()
                ->like('kode_mapel', $search)
                ->orLike('nama_mapel', $search)
                ->orLike('users.nama', $search)
                ->groupEnd();
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $builder->where('mata_pelajaran.is_active', (int) $filters['is_active']);
        }

        if (! empty($filters['id_user_pengampu'])) {
            $builder->where('mata_pelajaran.id_user_pengampu', (int) $filters['id_user_pengampu']);
        }

        return $builder->orderBy('mata_pelajaran.nama_mapel', 'ASC')->findAll();
    }

    public function activeOptions(): array
    {
        $out = [];

        foreach ($this->where('is_active', 1)->orderBy('nama_mapel', 'ASC')->findAll() as $row) {
            $out[(int) $row['id_mata_pelajaran']] = $row['kode_mapel'] . ' - ' . $row['nama_mapel'];
        }

        return $out;
    }

    public function isKodeTaken(string $kode, ?int $exceptId = null): bool
    {
        $builder = $this->where('kode_mapel', $kode);

        if ($exceptId !== null) {
            $builder->where('id_mata_pelajaran !=', $exceptId);
        }

        return $builder->countAllResults() > 0;
    }
}
