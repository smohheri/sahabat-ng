<?php

namespace App\Models;

use CodeIgniter\Model;

class PengurusModel extends Model
{
    protected $table            = 'pengurus';
    protected $primaryKey       = 'id_pengurus';
    protected $returnType       = 'array';
    protected $allowedFields    = ['nama_pengurus', 'jabatan', 'no_hp', 'email', 'file_ktp', 'foto'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $validationRules  = [
        'nama_pengurus' => 'required|min_length[2]|max_length[150]',
        'email'         => 'permit_empty|valid_email',
    ];

    public function options(): array
    {
        $out = [];

        foreach ($this->orderBy('nama_pengurus', 'ASC')->findAll() as $row) {
            $out[(int) $row['id_pengurus']] = $row['nama_pengurus'];
        }

        return $out;
    }
}
