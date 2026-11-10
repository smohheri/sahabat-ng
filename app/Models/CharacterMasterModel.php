<?php

namespace App\Models;

use CodeIgniter\Model;

class CharacterMasterModel extends Model
{
    protected $table         = 'character_aspects';
    protected $primaryKey    = 'id_aspect';
    protected $returnType    = 'array';
    protected $allowedFields = ['aspect_name', 'aspect_code', 'description', 'sort_order'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function isSchemaReady(): bool
    {
        foreach (['character_scale', 'character_aspects', 'character_indicators', 'character_assessments', 'character_assessment_details'] as $table) {
            if (! $this->db->tableExists($table)) {
                return false;
            }
        }

        return true;
    }

    public function getScales(): array
    {
        return $this->db->table('character_scale')->orderBy('score', 'ASC')->get()->getResultArray();
    }

    public function getAspects(): array
    {
        return $this->orderBy('sort_order', 'ASC')->orderBy('id_aspect', 'ASC')->findAll();
    }

    public function getAspectsWithIndicators(): array
    {
        $aspects = $this->getAspects();

        foreach ($aspects as &$aspect) {
            $aspect['indicators'] = $this->db->table('character_indicators')
                ->where('id_aspect', (int) $aspect['id_aspect'])
                ->orderBy('sort_order', 'ASC')
                ->orderBy('id_indicator', 'ASC')
                ->get()->getResultArray();
        }

        return $aspects;
    }

    public function getIndicators(?int $idAspect = null): array
    {
        $builder = $this->db->table('character_indicators ci')
            ->select('ci.*, ca.aspect_name, ca.aspect_code')
            ->join('character_aspects ca', 'ca.id_aspect = ci.id_aspect');

        if ($idAspect !== null) {
            $builder->where('ci.id_aspect', $idAspect);
        }

        return $builder->orderBy('ca.sort_order', 'ASC')->orderBy('ci.sort_order', 'ASC')->get()->getResultArray();
    }

    public function countIndicators(): int
    {
        return $this->db->table('character_indicators')->countAllResults();
    }

    public function insertScale(array $data): int|false
    {
        return $this->db->table('character_scale')->insert($data) ? (int) $this->db->insertID() : false;
    }

    public function updateScale(int $id, array $data): bool
    {
        return $this->db->table('character_scale')->where('id_scale', $id)->update($data);
    }

    public function deleteScale(int $id): bool
    {
        return $this->db->table('character_scale')->where('id_scale', $id)->delete();
    }

    public function insertIndicator(array $data): int|false
    {
        return $this->db->table('character_indicators')->insert($data) ? (int) $this->db->insertID() : false;
    }

    public function updateIndicator(int $id, array $data): bool
    {
        return $this->db->table('character_indicators')->where('id_indicator', $id)->update($data);
    }

    public function deleteIndicator(int $id): bool
    {
        return $this->db->table('character_indicators')->where('id_indicator', $id)->delete();
    }

    public function isAspectCodeTaken(string $code, ?int $exceptId = null): bool
    {
        $builder = $this->where('aspect_code', $code);

        if ($exceptId !== null) {
            $builder->where('id_aspect !=', $exceptId);
        }

        return $builder->countAllResults() > 0;
    }
}
