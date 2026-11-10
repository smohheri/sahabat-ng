<?php

namespace App\Models;

use CodeIgniter\Model;

class AbsensiMapelModel extends Model
{
    protected $table         = 'absensi_mapel_sessions';
    protected $primaryKey    = 'id_session';
    protected $returnType    = 'array';
    protected $allowedFields = ['id_rombel', 'id_mata_pelajaran', 'tahun_ajaran', 'semester', 'tanggal_absensi', 'catatan', 'created_by'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getSession(int $idSession): ?array
    {
        return $this->select('absensi_mapel_sessions.*, rombel.nama_rombel, rombel.kode_rombel, mata_pelajaran.nama_mapel, mata_pelajaran.kode_mapel')
            ->join('rombel', 'rombel.id_rombel = absensi_mapel_sessions.id_rombel')
            ->join('mata_pelajaran', 'mata_pelajaran.id_mata_pelajaran = absensi_mapel_sessions.id_mata_pelajaran')
            ->where('absensi_mapel_sessions.id_session', $idSession)
            ->first();
    }

    public function getExistingSession(int $idRombel, int $idMapel, string $tahun, int $semester, string $tanggal): ?array
    {
        return $this->where([
            'id_rombel'        => $idRombel,
            'id_mata_pelajaran' => $idMapel,
            'tahun_ajaran'     => $tahun,
            'semester'         => $semester,
            'tanggal_absensi'  => $tanggal,
        ])->first();
    }

    public function saveSession(array $sessionData, array $attendanceRows): int|false
    {
        $db = $this->db;
        $db->transBegin();

        try {
            $existing = $this->getExistingSession(
                (int) $sessionData['id_rombel'],
                (int) $sessionData['id_mata_pelajaran'],
                (string) $sessionData['tahun_ajaran'],
                (int) $sessionData['semester'],
                (string) $sessionData['tanggal_absensi']
            );

            if ($existing) {
                $idSession = (int) $existing['id_session'];
                $this->update($idSession, $sessionData);
            } else {
                $idSession = (int) $this->insert($sessionData, true);
            }

            if ($idSession <= 0) {
                throw new \RuntimeException('Gagal menyimpan sesi absensi.');
            }

            $db->table('absensi_mapel_details')->where('id_session', $idSession)->delete();

            $rows = [];
            foreach ($attendanceRows as $idAnak => $row) {
                $status = $this->normalizeStatus($row['status'] ?? 'Hadir');
                $rows[] = [
                    'id_session'       => $idSession,
                    'id_anak'          => (int) $idAnak,
                    'status_kehadiran' => $status,
                    'keterangan'       => $row['keterangan'] ?? null,
                ];
            }

            if ($rows !== []) {
                $db->table('absensi_mapel_details')->insertBatch($rows);
            }

            $db->transCommit();

            return $idSession;
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Simpan absensi gagal: ' . $e->getMessage());

            return false;
        }
    }

    private function normalizeStatus(string $status): string
    {
        $status = ucfirst(strtolower(trim($status)));

        return in_array($status, ['Hadir', 'Izin', 'Sakit', 'Alpha'], true) ? $status : 'Hadir';
    }

    public function details(int $idSession): array
    {
        return $this->db->table('absensi_mapel_details d')
            ->select('d.*, anak.nama_anak, anak.no_registrasi')
            ->join('anak', 'anak.id_anak = d.id_anak')
            ->where('d.id_session', $idSession)
            ->orderBy('anak.nama_anak', 'ASC')
            ->get()->getResultArray();
    }

    public function detailMap(int $idSession): array
    {
        $out = [];

        foreach ($this->details($idSession) as $row) {
            $out[(int) $row['id_anak']] = $row;
        }

        return $out;
    }

    public function recapSessions(array $filters = [], int $limit = 200): array
    {
        $builder = $this->select('absensi_mapel_sessions.*, rombel.nama_rombel, rombel.kode_rombel, mata_pelajaran.nama_mapel, mata_pelajaran.kode_mapel, users.nama AS created_by_name')
            ->join('rombel', 'rombel.id_rombel = absensi_mapel_sessions.id_rombel')
            ->join('mata_pelajaran', 'mata_pelajaran.id_mata_pelajaran = absensi_mapel_sessions.id_mata_pelajaran')
            ->join('users', 'users.id_user = absensi_mapel_sessions.created_by', 'left');

        $this->applyRecapFilters($builder, $filters);

        return $builder->orderBy('absensi_mapel_sessions.tanggal_absensi', 'DESC')
            ->orderBy('absensi_mapel_sessions.id_session', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    public function recapDetails(array $filters = []): array
    {
        $builder = $this->db->table('absensi_mapel_details d')
            ->select('d.*, a.nama_anak, s.tanggal_absensi, s.tahun_ajaran, s.semester, r.nama_rombel, m.nama_mapel')
            ->join('anak a', 'a.id_anak = d.id_anak')
            ->join('absensi_mapel_sessions s', 's.id_session = d.id_session')
            ->join('rombel r', 'r.id_rombel = s.id_rombel')
            ->join('mata_pelajaran m', 'm.id_mata_pelajaran = s.id_mata_pelajaran');

        if (! empty($filters['id_rombel'])) {
            $builder->where('s.id_rombel', (int) $filters['id_rombel']);
        }

        if (! empty($filters['id_mata_pelajaran'])) {
            $builder->where('s.id_mata_pelajaran', (int) $filters['id_mata_pelajaran']);
        }

        if (! empty($filters['tahun_ajaran'])) {
            $builder->where('s.tahun_ajaran', $filters['tahun_ajaran']);
        }

        if (! empty($filters['semester'])) {
            $builder->where('s.semester', (int) $filters['semester']);
        }

        if (! empty($filters['tanggal_dari'])) {
            $builder->where('s.tanggal_absensi >=', $filters['tanggal_dari']);
        }

        if (! empty($filters['tanggal_sampai'])) {
            $builder->where('s.tanggal_absensi <=', $filters['tanggal_sampai']);
        }

        if (! empty($filters['id_anak'])) {
            $builder->where('d.id_anak', (int) $filters['id_anak']);
        }

        return $builder->orderBy('s.tanggal_absensi', 'DESC')->orderBy('a.nama_anak', 'ASC')->get()->getResultArray();
    }

    public function statusSummary(array $filters = []): array
    {
        $rows = $this->recapDetails($filters);
        $summary = ['Hadir' => 0, 'Izin' => 0, 'Sakit' => 0, 'Alpha' => 0, 'total' => 0];

        foreach ($rows as $row) {
            $status = $row['status_kehadiran'] ?? 'Hadir';
            $summary[$status] = ($summary[$status] ?? 0) + 1;
            $summary['total']++;
        }

        return $summary;
    }

    private function applyRecapFilters($builder, array $filters): void
    {
        if (! empty($filters['id_rombel'])) {
            $builder->where('absensi_mapel_sessions.id_rombel', (int) $filters['id_rombel']);
        }

        if (! empty($filters['id_mata_pelajaran'])) {
            $builder->where('absensi_mapel_sessions.id_mata_pelajaran', (int) $filters['id_mata_pelajaran']);
        }

        if (! empty($filters['tahun_ajaran'])) {
            $builder->where('absensi_mapel_sessions.tahun_ajaran', $filters['tahun_ajaran']);
        }

        if (! empty($filters['semester'])) {
            $builder->where('absensi_mapel_sessions.semester', (int) $filters['semester']);
        }

        if (! empty($filters['tanggal_dari'])) {
            $builder->where('absensi_mapel_sessions.tanggal_absensi >=', $filters['tanggal_dari']);
        }

        if (! empty($filters['tanggal_sampai'])) {
            $builder->where('absensi_mapel_sessions.tanggal_absensi <=', $filters['tanggal_sampai']);
        }
    }
}
