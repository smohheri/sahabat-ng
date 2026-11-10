<?php

namespace App\Models;

use CodeIgniter\Model;

class FasilitasModel extends Model
{
    protected $table            = 'fasilitas';
    protected $primaryKey       = 'id_fasilitas';
    protected $returnType       = 'array';
    protected $allowedFields    = ['nama_fasilitas', 'deskripsi', 'gambar', 'icon', 'sort_order', 'is_active'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    public function active(): array
    {
        return $this->where('is_active', 1)->orderBy('sort_order', 'ASC')->orderBy('id_fasilitas', 'ASC')->findAll();
    }

    public function allOrdered(): array
    {
        return $this->orderBy('sort_order', 'ASC')->orderBy('id_fasilitas', 'ASC')->findAll();
    }
}
