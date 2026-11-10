<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id_user';
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'nama', 'username', 'password', 'id_role', 'id_anak', 'id_guru',
        'id_pengurus', 'is_active', 'last_login_at',
    ];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $validationRules  = [
        'nama'     => 'required|min_length[2]|max_length[100]',
        'username' => 'required|alpha_numeric_punct|min_length[3]|max_length[50]',
    ];

    public function login(string $username, string $password): ?array
    {
        $user = $this->select('users.*, roles.name AS role, roles.label AS role_label')
            ->join('roles', 'roles.id_role = users.id_role', 'left')
            ->where('users.username', $username)
            ->first();

        if (! $user) {
            return null;
        }

        if ((int) ($user['is_active'] ?? 1) !== 1) {
            return null;
        }

        $hash = (string) $user['password'];

        if (! password_verify($password, $hash)) {
            return null;
        }

        return $user;
    }

    public function withRole(int $id): ?array
    {
        return $this->select('users.*, roles.name AS role, roles.label AS role_label')
            ->join('roles', 'roles.id_role = users.id_role', 'left')
            ->where('users.id_user', $id)
            ->first();
    }

    public function allWithRole(): array
    {
        return $this->select('users.*, roles.name AS role, roles.label AS role_label, anak.nama_anak, guru.nama_guru')
            ->join('roles', 'roles.id_role = users.id_role', 'left')
            ->join('anak', 'anak.id_anak = users.id_anak', 'left')
            ->join('guru', 'guru.id_guru = users.id_guru', 'left')
            ->orderBy('users.nama', 'ASC')
            ->findAll();
    }

    public function byAnak(int $idAnak): ?array
    {
        return $this->where('id_anak', $idAnak)->first();
    }

    public function byGuru(int $idGuru): ?array
    {
        return $this->where('id_guru', $idGuru)->first();
    }

    public function byPengurus(int $idPengurus): ?array
    {
        return $this->where('id_pengurus', $idPengurus)->first();
    }

    public function saveUser(array $data, ?int $id = null): int|false
    {
        if (! empty($data['password'])) {
            $data['password'] = password_hash((string) $data['password'], PASSWORD_BCRYPT);
        } else {
            unset($data['password']);
        }

        if ($id !== null) {
            return $this->update($id, $data) ? $id : false;
        }

        return $this->insert($data, true);
    }

    public function isUsernameTaken(string $username, ?int $exceptId = null): bool
    {
        $builder = $this->where('username', $username);

        if ($exceptId !== null) {
            $builder->where('id_user !=', $exceptId);
        }

        return $builder->countAllResults() > 0;
    }
}
