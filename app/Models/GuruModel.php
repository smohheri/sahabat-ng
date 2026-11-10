<?php

namespace App\Models;

use CodeIgniter\Model;

class GuruModel extends Model
{
    protected $table            = 'guru';
    protected $primaryKey       = 'id_guru';
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'nama_guru', 'nip', 'jabatan', 'pendidikan_terakhir', 'bidang_keahlian',
        'sertifikasi', 'pengalaman_tahun', 'status_kepegawaian', 'no_hp', 'email',
        'alamat', 'foto', 'file_ijazah_terakhir', 'is_active',
    ];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $validationRules  = [
        'nama_guru' => 'required|min_length[2]|max_length[150]',
        'email'     => 'permit_empty|valid_email',
    ];

    public function allWithAccount(): array
    {
        return $this->select('guru.*, users.id_user, users.username, roles.name AS role')
            ->join('users', 'users.id_guru = guru.id_guru', 'left')
            ->join('roles', 'roles.id_role = users.id_role', 'left')
            ->orderBy('guru.nama_guru', 'ASC')
            ->findAll();
    }

    public function options(): array
    {
        $out = [];

        foreach ($this->where('is_active', 1)->orderBy('nama_guru', 'ASC')->findAll() as $row) {
            $out[(int) $row['id_guru']] = $row['nama_guru'];
        }

        return $out;
    }

    public function byName(string $name): ?array
    {
        return $this->where('LOWER(nama_guru)', strtolower(trim($name)))->first();
    }
}
