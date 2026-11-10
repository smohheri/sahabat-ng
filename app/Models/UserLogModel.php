<?php

namespace App\Models;

use CodeIgniter\Model;

class UserLogModel extends Model
{
    protected $table            = 'user_logs';
    protected $primaryKey       = 'id_log';
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_user', 'activity', 'description', 'ip_address', 'user_agent'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = '';

    public function getList(array $filters = [], int $limit = 50, int $offset = 0): array
    {
        $builder = $this->select('user_logs.*, users.nama, users.username')
            ->join('users', 'users.id_user = user_logs.id_user', 'left');

        if (! empty($filters['search'])) {
            $search = trim((string) $filters['search']);
            $builder->groupStart()
                ->like('users.nama', $search)
                ->orLike('users.username', $search)
                ->orLike('user_logs.activity', $search)
                ->orLike('user_logs.description', $search)
                ->orLike('user_logs.ip_address', $search)
                ->groupEnd();
        }

        if (! empty($filters['activity'])) {
            $builder->where('user_logs.activity', $filters['activity']);
        }

        if (! empty($filters['id_user'])) {
            $builder->where('user_logs.id_user', (int) $filters['id_user']);
        }

        return $builder->orderBy('user_logs.created_at', 'DESC')->limit($limit, max(0, $offset))->findAll();
    }

    public function countList(array $filters = []): int
    {
        $builder = $this->builder();

        if (! empty($filters['search'])) {
            $search = trim((string) $filters['search']);
            $builder->groupStart()
                ->like('activity', $search)
                ->orLike('description', $search)
                ->orLike('ip_address', $search)
                ->groupEnd();
        }

        if (! empty($filters['activity'])) {
            $builder->where('activity', $filters['activity']);
        }

        if (! empty($filters['id_user'])) {
            $builder->where('id_user', (int) $filters['id_user']);
        }

        return $builder->countAllResults();
    }

    public function recent(int $limit = 10): array
    {
        return $this->select('user_logs.*, users.nama')
            ->join('users', 'users.id_user = user_logs.id_user', 'left')
            ->orderBy('user_logs.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    public function activityOptions(): array
    {
        $rows = $this->select('activity')->distinct()->orderBy('activity', 'ASC')->findAll();

        return array_column($rows, 'activity');
    }
}
