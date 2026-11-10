<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table            = 'roles';
    protected $primaryKey       = 'id_role';
    protected $returnType       = 'array';
    protected $allowedFields    = ['name', 'label', 'description'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    public function options(): array
    {
        $rows = $this->orderBy('label', 'ASC')->findAll();
        $out  = [];

        foreach ($rows as $row) {
            $out[$row['name']] = $row['label'];
        }

        return $out;
    }
}
