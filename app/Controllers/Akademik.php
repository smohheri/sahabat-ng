<?php

namespace App\Controllers;

use App\Libraries\ExcelExport;
use App\Libraries\PdfExport;
use App\Models\AbsensiMapelModel;
use App\Models\AnakModel;
use App\Models\MataPelajaranModel;
use App\Models\RombelModel;
use App\Models\UserModel;

class Akademik extends BaseController
{
    private MataPelajaranModel $mapelModel;
    private RombelModel $rombelModel;
    private AbsensiMapelModel $absensiModel;

    public function __construct()
    {
        $this->mapelModel   = new MataPelajaranModel();
        $this->rombelModel  = new RombelModel();
        $this->absensiModel = new AbsensiMapelModel();
    }

    public function mapel()
    {
        $search  = trim((string) $this->request->getGet('q'));
        $builder = $this->mapelModel->getList(['search' => $search]);
        $rows    = $this->isTeacher() ? $this->scopeMapel($builder) : $builder;
        $pagination = $this->paginate($rows);

        return $this->render('akademik/mapel', [
            'title'       => 'Mata Pelajaran - ' . app_name(),
            'pageTitle'   => 'Mata Pelajaran',
            'items'       => $pagination['items'],
            'search'      => $search,
            'pengajar'    => $this->pengajarOptions(),
            'prefix'      => $this->panelPrefix(),
            'isTeacher'   => $this->isTeacher(),
            'page'        => $pagination['page'],
            'perPage'     => $pagination['perPage'],
            'total'       => $pagination['total'],
        ]);
    }

    public function mapel_store()
    {
        if ($this->isTeacher()) {
            return redirect()->back()->with('error', 'Hanya admin yang dapat menambah mata pelajaran.');
        }

        $data = [
            'kode_mapel'       => strtoupper(trim((string) $this->request->getPost('kode_mapel'))),
            'nama_mapel'       => trim((string) $this->request->getPost('nama_mapel')),
            'id_user_pengampu' => ((int) $this->request->getPost('id_user_pengampu')) ?: null,
            'is_active'        => $this->request->getPost('is_active') ? 1 : 0,
        ];

        if ($data['kode_mapel'] === '' || $data['nama_mapel'] === '') {
            return redirect()->back()->withInput()->with('error', 'Kode dan nama mapel wajib diisi.');
        }

        if ($this->mapelModel->isKodeTaken($data['kode_mapel'])) {
            return redirect()->back()->withInput()->with('error', 'Kode mapel sudah digunakan.');
        }

        $this->mapelModel->insert($data);
        log_activity('add_mapel', 'Menambahkan mapel: ' . $data['nama_mapel']);

        return redirect()->to(site_url($this->panelPrefix() . '/mapel'))->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function mapel_update(int $id)
    {
        if ($this->isTeacher()) {
            return redirect()->back()->with('error', 'Hanya admin yang dapat mengubah mata pelajaran.');
        }

        $item = $this->mapelModel->find($id);

        if (! $item) {
            return redirect()->back()->with('error', 'Mata pelajaran tidak ditemukan.');
        }

        $data = [
            'kode_mapel'       => strtoupper(trim((string) $this->request->getPost('kode_mapel'))),
            'nama_mapel'       => trim((string) $this->request->getPost('nama_mapel')),
            'id_user_pengampu' => ((int) $this->request->getPost('id_user_pengampu')) ?: null,
            'is_active'        => $this->request->getPost('is_active') ? 1 : 0,
        ];

        if ($this->mapelModel->isKodeTaken($data['kode_mapel'], $id)) {
            return redirect()->back()->withInput()->with('error', 'Kode mapel sudah digunakan.');
        }

        $this->mapelModel->update($id, $data);
        log_activity('edit_mapel', 'Memperbarui mapel: ' . $data['nama_mapel']);

        return redirect()->to(site_url($this->panelPrefix() . '/mapel'))->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function mapel_delete(int $id)
    {
        if ($denied = $this->requireAdmin()) {
            return $denied;
        }

        if ($this->isTeacher()) {
            return redirect()->back()->with('error', 'Hanya admin yang dapat menghapus mata pelajaran.');
        }

        $item = $this->mapelModel->find($id);

        if ($item) {
            $this->mapelModel->delete($id);
            log_activity('delete_mapel', 'Menghapus mapel: ' . $item['nama_mapel']);
        }

        return redirect()->back()->with('success', 'Mata pelajaran berhasil dihapus.');
    }

    public function rombel()
    {
        $filters = [
            'search'       => trim((string) $this->request->getGet('q')),
            'tahun_ajaran' => (string) $this->request->getGet('tahun_ajaran'),
            'semester'     => (string) $this->request->getGet('semester'),
        ];

        $items = $this->rombelModel->getList($filters);

        if ($this->isTeacher()) {
            $allowedMapel = $this->teacherMapelIds();
            $items = array_values(array_filter($items, function (array $rombel) use ($allowedMapel): bool {
                $mapelIds = $this->rombelModel->mapelIds((int) $rombel['id_rombel']);

                return array_intersect($allowedMapel, $mapelIds) !== [];
            }));
        }

        $totalRombel = count($items);
        $pagination  = $this->paginate($items, 8);
        $items       = $pagination['items'];

        $relations = [];

        foreach ($items as $item) {
            $relations[(int) $item['id_rombel']] = [
                'anak'  => $this->rombelModel->getChildren((int) $item['id_rombel']),
                'mapel' => $this->rombelModel->getMapel((int) $item['id_rombel']),
            ];
        }

        return $this->render('akademik/rombel', [
            'title'         => 'Rombongan Belajar - ' . app_name(),
            'pageTitle'     => 'Rombongan Belajar',
            'items'         => $items,
            'relations'     => $relations,
            'filters'       => $filters,
            'tahunOptions'  => $this->tahunAjaranOptions(),
            'anakList'      => (new AnakModel())->orderBy('nama_anak', 'ASC')->findAll(),
            'mapelList'     => $this->mapelModel->activeOptions(),
            'prefix'        => $this->panelPrefix(),
            'isTeacher'     => $this->isTeacher(),
            'page'          => $pagination['page'],
            'perPage'       => $pagination['perPage'],
            'total'         => $totalRombel,
        ]);
    }

    public function rombel_store()
    {
        if ($this->isTeacher()) {
            return redirect()->back()->with('error', 'Hanya admin yang dapat menambah rombel.');
        }

        $data = $this->rombelPayload();

        if ($data['kode_rombel'] === '' || $data['nama_rombel'] === '') {
            return redirect()->back()->withInput()->with('error', 'Kode dan nama rombel wajib diisi.');
        }

        if ($this->rombelModel->isKodeTaken($data['kode_rombel'])) {
            return redirect()->back()->withInput()->with('error', 'Kode rombel sudah digunakan.');
        }

        if ($this->rombelModel->isNameTaken($data['nama_rombel'], $data['tahun_ajaran'], (int) $data['semester'])) {
            return redirect()->back()->withInput()->with('error', 'Rombel dengan nama, tahun ajaran, dan semester tersebut sudah ada.');
        }

        $id = $this->rombelModel->saveWithRelations(null, $data, $this->intList($this->request->getPost('anak_ids')), $this->intList($this->request->getPost('mapel_ids')));

        if (! $id) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan rombel.');
        }

        log_activity('add_rombel', 'Menambahkan rombel: ' . $data['nama_rombel']);

        return redirect()->to(site_url($this->panelPrefix() . '/rombel'))->with('success', 'Rombel berhasil ditambahkan.');
    }

    public function rombel_update(int $id)
    {
        if ($this->isTeacher()) {
            return redirect()->back()->with('error', 'Hanya admin yang dapat mengubah rombel.');
        }

        $item = $this->rombelModel->find($id);

        if (! $item) {
            return redirect()->back()->with('error', 'Rombel tidak ditemukan.');
        }

        $data = $this->rombelPayload();

        if ($this->rombelModel->isKodeTaken($data['kode_rombel'], $id)) {
            return redirect()->back()->withInput()->with('error', 'Kode rombel sudah digunakan.');
        }

        if ($this->rombelModel->isNameTaken($data['nama_rombel'], $data['tahun_ajaran'], (int) $data['semester'], $id)) {
            return redirect()->back()->withInput()->with('error', 'Rombel dengan nama, tahun ajaran, dan semester tersebut sudah ada.');
        }

        $this->rombelModel->saveWithRelations($id, $data, $this->intList($this->request->getPost('anak_ids')), $this->intList($this->request->getPost('mapel_ids')));
        log_activity('edit_rombel', 'Memperbarui rombel: ' . $data['nama_rombel']);

        return redirect()->to(site_url($this->panelPrefix() . '/rombel'))->with('success', 'Rombel berhasil diperbarui.');
    }

    public function rombel_delete(int $id)
    {
        if ($denied = $this->requireAdmin()) {
            return $denied;
        }

        if ($this->isTeacher()) {
            return redirect()->back()->with('error', 'Hanya admin yang dapat menghapus rombel.');
        }

        $item = $this->rombelModel->find($id);

        if ($item) {
            $this->rombelModel->delete($id);
            log_activity('delete_rombel', 'Menghapus rombel: ' . $item['nama_rombel']);
        }

        return redirect()->back()->with('success', 'Rombel berhasil dihapus.');
    }

    public function absensi()
    {
        $filters = [
            'id_rombel'         => (int) $this->request->getGet('id_rombel'),
            'id_mata_pelajaran' => (int) $this->request->getGet('id_mata_pelajaran'),
            'tanggal'           => (string) ($this->request->getGet('tanggal') ?: date('Y-m-d')),
            'tahun_ajaran'      => (string) $this->request->getGet('tahun_ajaran'),
            'semester'          => (int) $this->request->getGet('semester'),
        ];

        $rombelOptions = $this->isTeacher() ? $this->teacherRombelOptions() : $this->rombelModel->activeByPeriod();

        $mapelOptions = $this->isTeacher()
            ? $this->scopeMapel($this->mapelModel->getList(['is_active' => 1]))
            : $this->mapelModel->getList(['is_active' => 1]);

        $children     = [];
        $details      = [];
        $session      = null;

        if ($filters['id_rombel'] > 0) {
            $children = $this->rombelModel->getChildren($filters['id_rombel']);
        }

        if ($filters['id_rombel'] > 0 && $filters['id_mata_pelajaran'] > 0) {
            $rombel = $this->rombelModel->find($filters['id_rombel']);
            $tahun  = $filters['tahun_ajaran'] ?: ($rombel['tahun_ajaran'] ?? $this->defaultTahunAjaran());
            $sem    = $filters['semester'] ?: (int) ($rombel['semester'] ?? 1);

            $session = $this->absensiModel->getExistingSession($filters['id_rombel'], $filters['id_mata_pelajaran'], $tahun, $sem, $filters['tanggal']);

            if ($session) {
                $details = $this->absensiModel->detailMap((int) $session['id_session']);
            }
        }

        $recapFilters = [
            'id_rombel'         => $filters['id_rombel'],
            'id_mata_pelajaran' => $filters['id_mata_pelajaran'],
            'tahun_ajaran'      => $filters['tahun_ajaran'],
            'semester'          => $filters['semester'],
        ];

        $recapPagination = $this->paginate($this->absensiModel->recapSessions($recapFilters), 20);

        return $this->render('akademik/absensi', [
            'title'         => 'Absensi Mapel - ' . app_name(),
            'pageTitle'     => 'Absensi Mata Pelajaran',
            'prefix'        => $this->panelPrefix(),
            'isTeacher'     => $this->isTeacher(),
            'filters'       => $filters,
            'rombelOptions' => $rombelOptions,
            'mapelOptions'  => $mapelOptions,
            'children'      => $children,
            'details'       => $details,
            'session'       => $session,
            'recap'         => $recapPagination['items'],
            'summary'       => $this->absensiModel->statusSummary($recapFilters),
            'tahunOptions'  => $this->tahunAjaranOptions(),
            'page'          => $recapPagination['page'],
            'perPage'       => $recapPagination['perPage'],
            'total'         => $recapPagination['total'],
        ]);
    }

    public function absensi_save()
    {
        $idRombel = (int) $this->request->getPost('id_rombel');
        $idMapel  = (int) $this->request->getPost('id_mata_pelajaran');
        $tanggal  = (string) ($this->request->getPost('tanggal') ?: date('Y-m-d'));
        $rombel   = $this->rombelModel->find($idRombel);

        if (! $rombel || ! $idMapel) {
            return redirect()->back()->with('error', 'Pilih rombel dan mata pelajaran terlebih dahulu.');
        }

        if (! $this->rombelModel->isMapelInRombel($idRombel, $idMapel)) {
            return redirect()->back()->with('error', 'Mata pelajaran tersebut tidak terdaftar pada rombel ini.');
        }

        if ($this->isTeacher()) {
            $allowedRombelIds = array_map('intval', array_column($this->teacherRombelOptions(), 'id_rombel'));

            if (! in_array($idRombel, $allowedRombelIds, true)) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses ke rombel tersebut.');
            }
        }

        if ($this->isTeacher() && ! in_array($idMapel, $this->teacherMapelIds(), true)) {
            return redirect()->back()->with('error', 'Anda tidak mengampu mata pelajaran tersebut.');
        }

        $statuses = (array) $this->request->getPost('status');
        $notes    = (array) $this->request->getPost('keterangan');
        $rows     = [];

        foreach ($statuses as $idAnak => $status) {
            $rows[(int) $idAnak] = [
                'status'     => (string) $status,
                'keterangan' => $notes[$idAnak] ?? null,
            ];
        }

        $sessionData = [
            'id_rombel'         => $idRombel,
            'id_mata_pelajaran' => $idMapel,
            'tahun_ajaran'      => (string) $rombel['tahun_ajaran'],
            'semester'          => (int) $rombel['semester'],
            'tanggal_absensi'   => $tanggal,
            'catatan'           => $this->request->getPost('catatan') ?: null,
            'created_by'        => (int) $this->session->get('id_user'),
        ];

        $idSession = $this->absensiModel->saveSession($sessionData, $rows);

        if (! $idSession) {
            return redirect()->back()->with('error', 'Gagal menyimpan absensi.');
        }

        log_activity('save_absensi', 'Menyimpan absensi mapel tanggal ' . $tanggal);

        return redirect()->to(site_url($this->panelPrefix() . '/absensi') . '?' . http_build_query([
            'id_rombel'         => $idRombel,
            'id_mata_pelajaran' => $idMapel,
            'tanggal'           => $tanggal,
            'tahun_ajaran'      => $rombel['tahun_ajaran'],
            'semester'          => $rombel['semester'],
        ]))->with('success', 'Absensi berhasil disimpan.');
    }

    public function export_mapel_pdf()
    {
        $rows = [];

        foreach ($this->mapelModel->getList([]) as $i => $item) {
            $rows[] = [$i + 1, $item['kode_mapel'], $item['nama_mapel'], $item['pengampu'] ?: '-', $item['is_active'] ? 'Aktif' : 'Nonaktif'];
        }

        return $this->pdf('laporan_mapel.pdf', 'Laporan Mata Pelajaran', ['No', 'Kode', 'Nama Mapel', 'Pengampu', 'Status'], $rows);
    }

    public function export_mapel_excel()
    {
        $rows = [];

        foreach ($this->mapelModel->getList([]) as $i => $item) {
            $rows[] = [$i + 1, $item['kode_mapel'], $item['nama_mapel'], $item['pengampu'] ?: '-', $item['is_active'] ? 'Aktif' : 'Nonaktif'];
        }

        return $this->excel('laporan_mapel.xlsx', 'Mata Pelajaran', ['No', 'Kode', 'Nama Mapel', 'Pengampu', 'Status'], $rows);
    }

    public function export_rombel_pdf()
    {
        $rows = [];

        foreach ($this->rombelModel->getList([]) as $i => $item) {
            $children = $this->rombelModel->getChildren((int) $item['id_rombel']);
            $mapel    = $this->rombelModel->getMapel((int) $item['id_rombel']);
            $rows[] = [
                $i + 1,
                $item['kode_rombel'],
                $item['nama_rombel'],
                $item['tahun_ajaran'] . ' / ' . $item['semester'],
                count($children),
                implode(', ', array_column($mapel, 'nama_mapel')) ?: '-',
                $item['is_active'] ? 'Aktif' : 'Nonaktif',
            ];
        }

        return $this->pdf('laporan_rombel.pdf', 'Laporan Rombongan Belajar', ['No', 'Kode', 'Nama Rombel', 'Tahun/Semester', 'Jumlah Anak', 'Mapel', 'Status'], $rows, 'L');
    }

    public function export_rombel_excel()
    {
        $rows = [];

        foreach ($this->rombelModel->getList([]) as $i => $item) {
            $children = $this->rombelModel->getChildren((int) $item['id_rombel']);
            $mapel    = $this->rombelModel->getMapel((int) $item['id_rombel']);
            $rows[] = [
                $i + 1,
                $item['kode_rombel'],
                $item['nama_rombel'],
                $item['tahun_ajaran'] . ' / ' . $item['semester'],
                count($children),
                implode(', ', array_column($mapel, 'nama_mapel')) ?: '-',
                $item['is_active'] ? 'Aktif' : 'Nonaktif',
            ];
        }

        return $this->excel('laporan_rombel.xlsx', 'Rombongan Belajar', ['No', 'Kode', 'Nama Rombel', 'Tahun/Semester', 'Jumlah Anak', 'Mapel', 'Status'], $rows);
    }

    public function export_absensi_pdf()
    {
        $filters = [
            'id_rombel'         => (int) $this->request->getGet('id_rombel'),
            'id_mata_pelajaran' => (int) $this->request->getGet('id_mata_pelajaran'),
            'tahun_ajaran'      => (string) $this->request->getGet('tahun_ajaran'),
            'semester'          => (int) $this->request->getGet('semester'),
            'tanggal_dari'      => (string) $this->request->getGet('tanggal_dari'),
            'tanggal_sampai'    => (string) $this->request->getGet('tanggal_sampai'),
        ];

        $rows = [];

        foreach ($this->absensiModel->recapDetails($filters) as $i => $item) {
            $rows[] = [
                $i + 1,
                tanggal_id($item['tanggal_absensi']),
                $item['nama_rombel'],
                $item['nama_mapel'],
                $item['nama_anak'],
                $item['status_kehadiran'],
                $item['keterangan'] ?: '-',
            ];
        }

        return $this->pdf('laporan_absensi.pdf', 'Laporan Absensi Mata Pelajaran', ['No', 'Tanggal', 'Rombel', 'Mapel', 'Nama Anak', 'Status', 'Keterangan'], $rows, 'L');
    }

    public function export_absensi_excel()
    {
        $filters = [
            'id_rombel'         => (int) $this->request->getGet('id_rombel'),
            'id_mata_pelajaran' => (int) $this->request->getGet('id_mata_pelajaran'),
            'tahun_ajaran'      => (string) $this->request->getGet('tahun_ajaran'),
            'semester'          => (int) $this->request->getGet('semester'),
            'tanggal_dari'      => (string) $this->request->getGet('tanggal_dari'),
            'tanggal_sampai'    => (string) $this->request->getGet('tanggal_sampai'),
        ];

        $rows = [];

        foreach ($this->absensiModel->recapDetails($filters) as $i => $item) {
            $rows[] = [
                $i + 1,
                tanggal_id($item['tanggal_absensi']),
                $item['nama_rombel'],
                $item['nama_mapel'],
                $item['nama_anak'],
                $item['status_kehadiran'],
                $item['keterangan'] ?: '-',
            ];
        }

        return $this->excel('laporan_absensi.xlsx', 'Absensi Mata Pelajaran', ['No', 'Tanggal', 'Rombel', 'Mapel', 'Nama Anak', 'Status', 'Keterangan'], $rows);
    }

    private function panelPrefix(): string
    {
        return $this->isTeacher() ? 'guru/akademik' : 'admin/akademik';
    }

    private function rombelPayload(): array
    {
        return [
            'kode_rombel'  => strtoupper(trim((string) $this->request->getPost('kode_rombel'))),
            'nama_rombel'  => trim((string) $this->request->getPost('nama_rombel')),
            'tahun_ajaran' => trim((string) ($this->request->getPost('tahun_ajaran') ?: $this->defaultTahunAjaran())),
            'semester'     => (int) ($this->request->getPost('semester') ?: 1),
            'is_active'    => $this->request->getPost('is_active') ? 1 : 0,
            'keterangan'   => $this->request->getPost('keterangan') ?: null,
        ];
    }

    private function intList(mixed $value): array
    {
        return array_values(array_filter(array_map('intval', (array) $value)));
    }

    private function teacherMapelIds(): array
    {
        $idUser = (int) $this->session->get('id_user');

        if ($idUser <= 0) {
            return [];
        }

        $rows = $this->db->table('mata_pelajaran')->select('id_mata_pelajaran')->where('id_user_pengampu', $idUser)->get()->getResultArray();

        return array_map('intval', array_column($rows, 'id_mata_pelajaran'));
    }

    private function scopeMapel(array $rows): array
    {
        $allowed = $this->teacherMapelIds();

        return array_values(array_filter($rows, static fn (array $row): bool => in_array((int) $row['id_mata_pelajaran'], $allowed, true)));
    }

    private function teacherRombelOptions(): array
    {
        $allowed = $this->teacherMapelIds();

        if ($allowed === []) {
            return [];
        }

        $ids = $this->db->table('rombel_mata_pelajaran')
            ->select('DISTINCT id_rombel')
            ->whereIn('id_mata_pelajaran', $allowed)
            ->get()->getResultArray();

        $idList = array_map('intval', array_column($ids, 'id_rombel'));

        if ($idList === []) {
            return [];
        }

        return $this->rombelModel->where('is_active', 1)->whereIn('id_rombel', $idList)->orderBy('nama_rombel', 'ASC')->findAll();
    }

    private function pengajarOptions(): array
    {
        $rows = $this->db->table('users u')
            ->select('u.id_user, u.nama')
            ->join('roles r', 'r.id_role = u.id_role')
            ->whereIn('r.name', ['pengajar', 'guru'])
            ->orderBy('u.nama', 'ASC')
            ->get()->getResultArray();

        $out = [];
        foreach ($rows as $row) {
            $out[(int) $row['id_user']] = $row['nama'];
        }

        return $out;
    }

    private function tahunAjaranOptions(): array
    {
        $rows = $this->rombelModel->select('tahun_ajaran')->distinct()->orderBy('tahun_ajaran', 'DESC')->findAll();
        $years = array_column($rows, 'tahun_ajaran');
        $current = $this->defaultTahunAjaran();

        if (! in_array($current, $years, true)) {
            array_unshift($years, $current);
        }

        return array_values(array_unique(array_filter($years)));
    }

    private function defaultTahunAjaran(): string
    {
        $month = (int) date('n');
        $year  = (int) date('Y');

        return $month >= 7 ? $year . '/' . ($year + 1) : ($year - 1) . '/' . $year;
    }

    private function pdf(string $filename, string $title, array $headers, array $rows, string $orientation = 'P')
    {
        $body    = view('pdf/table', ['headers' => $headers, 'rows' => $rows]);
        $content = (new PdfExport((array) setting()))->render($title, $body, $filename, $orientation);

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
            ->setBody($content);
    }

    private function excel(string $filename, string $title, array $headers, array $rows)
    {
        $content = (new ExcelExport())->build($title, $headers, $rows, ['Laporan' => $title, 'Dicetak' => date('d/m/Y H:i')]);

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($content);
    }
}
