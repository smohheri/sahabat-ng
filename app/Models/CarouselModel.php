<?php

namespace App\Models;

use CodeIgniter\Model;

class CarouselModel extends Model
{
    protected $table            = 'carousel_images';
    protected $primaryKey       = 'id_carousel';
    protected $returnType       = 'array';
    protected $allowedFields    = ['image_name', 'title', 'description', 'sort_order', 'is_active'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    public function active(): array
    {
        return $this->where('is_active', 1)->orderBy('sort_order', 'ASC')->orderBy('id_carousel', 'ASC')->findAll();
    }

    public function allOrdered(): array
    {
        return $this->orderBy('sort_order', 'ASC')->orderBy('id_carousel', 'ASC')->findAll();
    }
}
