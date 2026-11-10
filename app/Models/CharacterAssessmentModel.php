<?php

namespace App\Models;

use CodeIgniter\Model;

class CharacterAssessmentModel extends Model
{
    protected $table         = 'character_assessments';
    protected $primaryKey    = 'id_assessment';
    protected $returnType    = 'array';
    protected $allowedFields = ['id_anak', 'id_assessor', 'assessor_type', 'assessment_date', 'week_number', 'month', 'year', 'notes', 'status'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function isSchemaReady(): bool
    {
        return $this->db->tableExists('character_assessments')
            && $this->db->tableExists('character_assessment_details')
            && $this->db->tableExists('character_indicators');
    }

    public function createAssessment(array $assessment, array $scores = [], array $note = []): int|false
    {
        $db = $this->db;
        $db->transBegin();

        try {
            $id = (int) $this->insert($assessment, true);

            if ($id <= 0) {
                throw new \RuntimeException('Gagal menyimpan assessment.');
            }

            $rows = [];
            foreach ($scores as $idIndicator => $score) {
                $score = (int) $score;
                if ($score < 1 || $score > 4) {
                    continue;
                }
                $rows[] = [
                    'id_assessment' => $id,
                    'id_indicator'  => (int) $idIndicator,
                    'score'         => $score,
                ];
            }

            if ($rows !== []) {
                $db->table('character_assessment_details')->insertBatch($rows);
            }

            if ($note !== [] && array_filter($note, static fn ($v) => $v !== null && $v !== '')) {
                $note['id_assessment'] = $id;
                $db->table('character_qualitative_notes')->insert($note);
            }

            $db->transCommit();

            $this->rebuildPeriodSummaries($assessment);

            return $id;
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Simpan penilaian karakter gagal: ' . $e->getMessage());

            return false;
        }
    }

    public function hasMonthlyAssessment(int $idAnak, int $month, int $year, string $assessorType): bool
    {
        return $this->where([
            'id_anak'       => $idAnak,
            'month'         => $month,
            'year'          => $year,
            'assessor_type' => $assessorType,
        ])->countAllResults() > 0;
    }

    public function getAssessmentsByAssessor(int $idAssessor, int $limit = 30): array
    {
        return $this->select('character_assessments.*, anak.nama_anak, (SELECT AVG(score) FROM character_assessment_details d WHERE d.id_assessment = character_assessments.id_assessment) AS avg_score')
            ->join('anak', 'anak.id_anak = character_assessments.id_anak')
            ->where('character_assessments.id_assessor', $idAssessor)
            ->orderBy('character_assessments.assessment_date', 'DESC')
            ->orderBy('character_assessments.id_assessment', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    public function getAssessorProgressSummary(int $idAssessor, array $filters = []): array
    {
        $builder = $this->select('character_assessments.id_anak, anak.nama_anak, COUNT(DISTINCT character_assessments.id_assessment) AS total_assessment, AVG(character_assessment_details.score) AS avg_score')
            ->join('anak', 'anak.id_anak = character_assessments.id_anak')
            ->join('character_assessment_details', 'character_assessment_details.id_assessment = character_assessments.id_assessment', 'left')
            ->where('character_assessments.id_assessor', $idAssessor)
            ->groupBy('character_assessments.id_anak, anak.nama_anak')
            ->orderBy('anak.nama_anak', 'ASC');

        $this->applyPeriodFilters($builder, $filters, 'character_assessments');

        return $builder->findAll();
    }

    public function getChildAssessmentHistory(int $idAnak, array $filters = [], int $limit = 25): array
    {
        $builder = $this->select('character_assessments.*, users.nama AS assessor_name, AVG(character_assessment_details.score) AS avg_score, COUNT(character_assessment_details.id_detail) AS indicator_count')
            ->join('users', 'users.id_user = character_assessments.id_assessor', 'left')
            ->join('character_assessment_details', 'character_assessment_details.id_assessment = character_assessments.id_assessment', 'left')
            ->where('character_assessments.id_anak', $idAnak)
            ->groupBy('character_assessments.id_assessment, users.nama')
            ->orderBy('character_assessments.assessment_date', 'DESC')
            ->orderBy('character_assessments.id_assessment', 'DESC')
            ->limit($limit);

        $this->applyPeriodFilters($builder, $filters, 'character_assessments');

        return $builder->findAll();
    }

    public function getChildIndicatorScores(int $idAnak, array $filters = []): array
    {
        $builder = $this->db->table('character_assessment_details d')
            ->select('a.id_assessment, asp.id_aspect, asp.aspect_name, asp.aspect_code, i.id_indicator, i.indicator_name, i.indicator_code, AVG(d.score) AS avg_score, COUNT(d.id_detail) AS total')
            ->join('character_assessments a', 'a.id_assessment = d.id_assessment')
            ->join('character_indicators i', 'i.id_indicator = d.id_indicator')
            ->join('character_aspects asp', 'asp.id_aspect = i.id_aspect')
            ->where('a.id_anak', $idAnak)
            ->groupBy('a.id_assessment, asp.id_aspect, asp.aspect_name, asp.aspect_code, i.id_indicator, i.indicator_name, i.indicator_code')
            ->orderBy('asp.sort_order', 'ASC')
            ->orderBy('i.sort_order', 'ASC');

        $this->applyPeriodFilters($builder, $filters, 'a');

        return $builder->get()->getResultArray();
    }

    public function getAssessorChildIndicatorScores(int $idAssessor, int $idAnak, array $filters = []): array
    {
        $builder = $this->db->table('character_assessment_details d')
            ->select('asp.aspect_name, i.indicator_name, AVG(d.score) AS avg_score, COUNT(d.id_detail) AS total')
            ->join('character_assessments a', 'a.id_assessment = d.id_assessment')
            ->join('character_indicators i', 'i.id_indicator = d.id_indicator')
            ->join('character_aspects asp', 'asp.id_aspect = i.id_aspect')
            ->where('a.id_assessor', $idAssessor)
            ->where('a.id_anak', $idAnak)
            ->groupBy('asp.id_aspect, asp.aspect_name, asp.sort_order, i.id_indicator, i.indicator_name, i.sort_order')
            ->orderBy('asp.sort_order', 'ASC')
            ->orderBy('i.sort_order', 'ASC');

        $this->applyPeriodFilters($builder, $filters, 'a');

        return $builder->get()->getResultArray();
    }

    public function getChildProgressSummary(array $filters = []): array
    {
        $builder = $this->select('character_assessments.id_anak, anak.nama_anak, COUNT(DISTINCT character_assessments.id_assessment) AS total_assessment, AVG(character_assessment_details.score) AS avg_score')
            ->join('anak', 'anak.id_anak = character_assessments.id_anak')
            ->join('character_assessment_details', 'character_assessment_details.id_assessment = character_assessments.id_assessment', 'left')
            ->groupBy('character_assessments.id_anak, anak.nama_anak')
            ->orderBy('anak.nama_anak', 'ASC');

        $this->applyPeriodFilters($builder, $filters, 'character_assessments');

        return $builder->findAll();
    }

    public function getOverallAspectTrend(array $filters = []): array
    {
        $builder = $this->db->table('character_assessment_details d')
            ->select('asp.id_aspect, asp.aspect_name, a.assessment_date, AVG(d.score) AS avg_score')
            ->join('character_assessments a', 'a.id_assessment = d.id_assessment')
            ->join('character_indicators i', 'i.id_indicator = d.id_indicator')
            ->join('character_aspects asp', 'asp.id_aspect = i.id_aspect')
            ->groupBy('asp.id_aspect, asp.aspect_name, a.assessment_date')
            ->orderBy('a.assessment_date', 'ASC');

        $this->applyPeriodFilters($builder, $filters, 'a');

        return $builder->get()->getResultArray();
    }

    public function getAssessments(int $limit = 100, int $offset = 0): array
    {
        return $this->select('character_assessments.*, anak.nama_anak, users.nama AS assessor_name, AVG(character_assessment_details.score) AS avg_score, COUNT(character_assessment_details.id_detail) AS indicator_count')
            ->join('anak', 'anak.id_anak = character_assessments.id_anak', 'left')
            ->join('users', 'users.id_user = character_assessments.id_assessor', 'left')
            ->join('character_assessment_details', 'character_assessment_details.id_assessment = character_assessments.id_assessment', 'left')
            ->groupBy('character_assessments.id_assessment, anak.nama_anak, users.nama')
            ->orderBy('character_assessments.assessment_date', 'DESC')
            ->limit($limit, $offset)
            ->findAll();
    }

    public function getDetails(int $limit = 200, int $offset = 0): array
    {
        return $this->db->table('character_assessment_details d')
            ->select('d.*, a.assessment_date, a.id_anak, anak.nama_anak, i.indicator_name, asp.aspect_name')
            ->join('character_assessments a', 'a.id_assessment = d.id_assessment')
            ->join('anak', 'anak.id_anak = a.id_anak')
            ->join('character_indicators i', 'i.id_indicator = d.id_indicator')
            ->join('character_aspects asp', 'asp.id_aspect = i.id_aspect')
            ->orderBy('a.assessment_date', 'DESC')
            ->limit($limit, $offset)
            ->get()->getResultArray();
    }

    public function getQualitativeNotes(int $limit = 100): array
    {
        $builder = $this->db->table('character_qualitative_notes n')
            ->select('n.*, a.assessment_date, a.id_anak, anak.nama_anak, users.nama AS assessor_name')
            ->join('character_assessments a', 'a.id_assessment = n.id_assessment')
            ->join('anak', 'anak.id_anak = a.id_anak')
            ->join('users', 'users.id_user = a.id_assessor', 'left')
            ->orderBy('a.assessment_date', 'DESC');

        if ($limit > 0) {
            $builder->limit($limit);
        }

        return $builder->get()->getResultArray();
    }

    public function getWeeklySummary(int $limit = 200): array
    {
        $builder = $this->db->table('character_weekly_summary s')
            ->select('s.*, anak.nama_anak, asp.aspect_name')
            ->join('anak', 'anak.id_anak = s.id_anak')
            ->join('character_aspects asp', 'asp.id_aspect = s.id_aspect')
            ->orderBy('s.year', 'DESC')->orderBy('s.week_number', 'DESC');

        if ($limit > 0) {
            $builder->limit($limit);
        }

        return $builder->get()->getResultArray();
    }

    public function getMonthlySummary(int $limit = 200): array
    {
        $builder = $this->db->table('character_monthly_summary s')
            ->select('s.*, anak.nama_anak, asp.aspect_name')
            ->join('anak', 'anak.id_anak = s.id_anak')
            ->join('character_aspects asp', 'asp.id_aspect = s.id_aspect')
            ->orderBy('s.year', 'DESC')->orderBy('s.month', 'DESC');

        if ($limit > 0) {
            $builder->limit($limit);
        }

        return $builder->get()->getResultArray();
    }

    public function getReportRows(array $filters = []): array
    {
        $builder = $this->db->table('character_assessment_details d')
            ->select('a.id_assessment, a.assessment_date, a.id_anak, anak.nama_anak, asp.aspect_name, i.indicator_name, d.score, users.nama AS assessor_name')
            ->join('character_assessments a', 'a.id_assessment = d.id_assessment')
            ->join('anak', 'anak.id_anak = a.id_anak')
            ->join('users', 'users.id_user = a.id_assessor', 'left')
            ->join('character_indicators i', 'i.id_indicator = d.id_indicator')
            ->join('character_aspects asp', 'asp.id_aspect = i.id_aspect')
            ->orderBy('a.assessment_date', 'DESC')
            ->orderBy('anak.nama_anak', 'ASC');

        $this->applyPeriodFilters($builder, $filters, 'a');

        if (! empty($filters['id_anak'])) {
            $builder->where('a.id_anak', (int) $filters['id_anak']);
        }

        return $builder->get()->getResultArray();
    }

    public function countAssessments(): int
    {
        return $this->db->tableExists('character_assessments') ? $this->db->table('character_assessments')->countAllResults() : 0;
    }

    public function countDetails(): int
    {
        return $this->db->tableExists('character_assessment_details') ? $this->db->table('character_assessment_details')->countAllResults() : 0;
    }

    public function countNotes(): int
    {
        return $this->db->tableExists('character_qualitative_notes') ? $this->db->table('character_qualitative_notes')->countAllResults() : 0;
    }

    private function applyPeriodFilters($builder, array $filters, string $alias): void
    {
        $type = $filters['period_type'] ?? '';

        if ($type === 'monthly' && ! empty($filters['month']) && ! empty($filters['year'])) {
            $builder->where($alias . '.month', (int) $filters['month'])
                ->where($alias . '.year', (int) $filters['year']);
        } elseif ($type === 'weekly' && ! empty($filters['week_number']) && ! empty($filters['year'])) {
            $builder->where($alias . '.week_number', (int) $filters['week_number'])
                ->where($alias . '.year', (int) $filters['year']);
        }

        if (! empty($filters['year_only'])) {
            $builder->where($alias . '.year', (int) $filters['year_only']);
        }

        if (! empty($filters['tanggal_dari'])) {
            $builder->where($alias . '.assessment_date >=', $filters['tanggal_dari']);
        }

        if (! empty($filters['tanggal_sampai'])) {
            $builder->where($alias . '.assessment_date <=', $filters['tanggal_sampai']);
        }

        if (! empty($filters['assessor_type'])) {
            $builder->where($alias . '.assessor_type', $filters['assessor_type']);
        }
    }

    private function rebuildPeriodSummaries(array $assessment): void
    {
        if (! $this->db->tableExists('character_weekly_summary') || ! $this->db->tableExists('character_monthly_summary')) {
            return;
        }

        $idAnak       = (int) $assessment['id_anak'];
        $week         = (int) ($assessment['week_number'] ?? 0);
        $month        = (int) ($assessment['month'] ?? 0);
        $year         = (int) ($assessment['year'] ?? 0);
        $assessorType = $assessment['assessor_type'] ?? 'guru';

        if ($week > 0 && $year > 0) {
            $this->rebuildSummary('character_weekly_summary', 'week_number', $week, $idAnak, $year, $assessorType);
        }

        if ($month > 0 && $year > 0) {
            $this->rebuildSummary('character_monthly_summary', 'month', $month, $idAnak, $year, $assessorType);
        }
    }

    private function rebuildSummary(string $table, string $periodColumn, int $period, int $idAnak, int $year, string $assessorType): void
    {
        $rows = $this->db->table('character_assessment_details d')
            ->select('i.id_aspect, AVG(d.score) AS avg_score')
            ->join('character_assessments a', 'a.id_assessment = d.id_assessment')
            ->join('character_indicators i', 'i.id_indicator = d.id_indicator')
            ->where('a.id_anak', $idAnak)
            ->where('a.year', $year)
            ->where('a.' . $periodColumn, $period)
            ->where('a.assessor_type', $assessorType)
            ->groupBy('i.id_aspect')
            ->get()->getResultArray();

        foreach ($rows as $row) {
            $where = [
                'id_anak'       => $idAnak,
                'id_aspect'     => (int) $row['id_aspect'],
                $periodColumn   => $period,
                'year'          => $year,
                'assessor_type' => $assessorType,
            ];

            $existing = $this->db->table($table)->where($where)->get()->getRowArray();
            $payload  = $where + ['avg_score' => round((float) $row['avg_score'], 2)];

            if ($existing) {
                $this->db->table($table)->where($where)->update($payload);
            } else {
                $this->db->table($table)->insert($payload);
            }
        }
    }
}
