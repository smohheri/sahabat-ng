<?php

namespace App\Models;

use CodeIgniter\Model;

class HeroModel extends Model
{
    protected $table            = 'hero_images';
    protected $primaryKey       = 'id_hero';
    protected $returnType       = 'array';
    protected $allowedFields    = ['image_name', 'title', 'subtitle', 'description', 'sort_order', 'is_active'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    public function active(): array
    {
        return $this->where('is_active', 1)->orderBy('sort_order', 'ASC')->orderBy('id_hero', 'ASC')->findAll();
    }

    public function allOrdered(): array
    {
        return $this->orderBy('sort_order', 'ASC')->orderBy('id_hero', 'ASC')->findAll();
    }
}
