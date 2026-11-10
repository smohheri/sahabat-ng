<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\PdfExport;
use App\Models\AnakModel;
use App\Models\CharacterAssessmentModel;
use App\Models\CharacterMasterModel;

class PenilaianKarakter extends BaseController
{
    private CharacterMasterModel $master;
    private CharacterAssessmentModel $assessment;

    public function __construct()
    {
        $this->master     = new CharacterMasterModel();
        $this->assessment = new CharacterAssessmentModel();
    }

    public function master()
    {
        return $this->render('admin/penilaian/master', [
            'title'     => 'Master Penilaian Karakter - ' . app_name(),
            'pageTitle' => 'Master Penilaian Karakter',
            'scales'    => $this->master->getScales(),
            'aspects'   => $this->master->getAspectsWithIndicators(),
        ]);
    }

    public function store_scale()
    {
        $this->master->insertScale([
            'score'       => (int) $this->request->getPost('score'),
            'category'    => (string) $this->request->getPost('category'),
            'description' => $this->request->getPost('description') ?: null,
            'created_at'  => date('Y-m-d H:i:s'),
        ]);

        log_activity('add_scale', 'Menambahkan skala penilaian');

        return redirect()->back()->with('success', 'Skala penilaian berhasil ditambahkan.');
    }

    public function update_scale(int $id)
    {
        $this->master->updateScale($id, [
            'score'       => (int) $this->request->getPost('score'),
            'category'    => (string) $this->request->getPost('category'),
            'description' => $this->request->getPost('description') ?: null,
        ]);

        log_activity('edit_scale', 'Memperbarui skala penilaian');

        return redirect()->back()->with('success', 'Skala penilaian berhasil diperbarui.');
    }

    public function delete_scale(int $id)
    {
        if ($denied = $this->requireAdmin()) {
            return $denied;
        }

        $this->master->deleteScale($id);
        log_activity('delete_scale', 'Menghapus skala penilaian');

        return redirect()->back()->with('success', 'Skala penilaian berhasil dihapus.');
    }

    public function store_aspect()
    {
        $code = (string) $this->request->getPost('aspect_code');

        if ($this->master->isAspectCodeTaken($code)) {
            return redirect()->back()->with('error', 'Kode aspek sudah digunakan.');
        }

        $this->master->insert([
            'aspect_name' => (string) $this->request->getPost('aspect_name'),
            'aspect_code' => $code,
            'description' => $this->request->getPost('description') ?: null,
            'sort_order'  => (int) ($this->request->getPost('sort_order') ?: 0),
        ]);

        log_activity('add_aspect', 'Menambahkan aspek penilaian');

        return redirect()->back()->with('success', 'Aspek penilaian berhasil ditambahkan.');
    }

    public function update_aspect(int $id)
    {
        $code = (string) $this->request->getPost('aspect_code');

        if ($this->master->isAspectCodeTaken($code, $id)) {
            return redirect()->back()->with('error', 'Kode aspek sudah digunakan.');
        }

        $this->master->update($id, [
            'aspect_name' => (string) $this->request->getPost('aspect_name'),
            'aspect_code' => $code,
            'description' => $this->request->getPost('description') ?: null,
            'sort_order'  => (int) ($this->request->getPost('sort_order') ?: 0),
        ]);

        log_activity('edit_aspect', 'Memperbarui aspek penilaian');

        return redirect()->back()->with('success', 'Aspek penilaian berhasil diperbarui.');
    }

    public function delete_aspect(int $id)
    {
        if ($denied = $this->requireAdmin()) {
            return $denied;
        }

        $this->master->delete($id);
        log_activity('delete_aspect', 'Menghapus aspek penilaian');

        return redirect()->back()->with('success', 'Aspek penilaian berhasil dihapus.');
    }

    public function store_indicator()
    {
        $this->master->insertIndicator([
            'id_aspect'      => (int) $this->request->getPost('id_aspect'),
            'indicator_name' => (string) $this->request->getPost('indicator_name'),
            'indicator_code' => $this->request->getPost('indicator_code') ?: null,
            'description'    => $this->request->getPost('description') ?: null,
            'sort_order'     => (int) ($this->request->getPost('sort_order') ?: 0),
        ]);

        log_activity('add_indicator', 'Menambahkan indikator penilaian');

        return redirect()->back()->with('success', 'Indikator berhasil ditambahkan.');
    }

    public function update_indicator(int $id)
    {
        $this->master->updateIndicator($id, [
            'id_aspect'      => (int) $this->request->getPost('id_aspect'),
            'indicator_name' => (string) $this->request->getPost('indicator_name'),
            'indicator_code' => $this->request->getPost('indicator_code') ?: null,
            'description'    => $this->request->getPost('description') ?: null,
            'sort_order'     => (int) ($this->request->getPost('sort_order') ?: 0),
        ]);

        log_activity('edit_indicator', 'Memperbarui indikator penilaian');

        return redirect()->back()->with('success', 'Indikator berhasil diperbarui.');
    }

    public function delete_indicator(int $id)
    {
        if ($denied = $this->requireAdmin()) {
            return $denied;
        }

        $this->master->deleteIndicator($id);
        log_activity('delete_indicator', 'Menghapus indikator penilaian');

        return redirect()->back()->with('success', 'Indikator berhasil dihapus.');
    }

    public function data()
    {
        $filters = [
            'id_anak'       => (int) $this->request->getGet('id_anak'),
            'assessor_type' => (string) $this->request->getGet('assessor_type'),
            'month'         => (int) $this->request->getGet('month'),
            'year'          => (int) $this->request->getGet('year'),
        ];

        $perPage = 25;
        $page    = max(1, (int) $this->request->getGet('page'));
        $builder = $this->db->table('character_assessments ca')
            ->select('ca.*, anak.nama_anak, users.nama AS assessor_name, AVG(d.score) AS avg_score, COUNT(d.id_detail) AS indicator_count')
            ->join('anak', 'anak.id_anak = ca.id_anak', 'left')
            ->join('users', 'users.id_user = ca.id_assessor', 'left')
            ->join('character_assessment_details d', 'd.id_assessment = ca.id_assessment', 'left')
            ->groupBy('ca.id_assessment, anak.nama_anak, users.nama');

        if ($filters['id_anak'] > 0) {
            $builder->where('ca.id_anak', $filters['id_anak']);
        }

        if ($filters['assessor_type'] !== '') {
            $builder->where('ca.assessor_type', $filters['assessor_type']);
        }

        if ($filters['month'] > 0) {
            $builder->where('ca.month', $filters['month']);
        }

        if ($filters['year'] > 0) {
            $builder->where('ca.year', $filters['year']);
        }

        $all   = $builder->orderBy('ca.assessment_date', 'DESC')->get()->getResultArray();
        $total = count($all);
        $items = array_slice($all, ($page - 1) * $perPage, $perPage);

        return $this->render('admin/penilaian/data', [
            'title'     => 'Data Penilaian Karakter - ' . app_name(),
            'pageTitle' => 'Data Penilaian Karakter',
            'items'     => $items,
            'filters'   => $filters,
            'anakList'  => (new AnakModel())->orderBy('nama_anak', 'ASC')->findAll(),
            'page'      => $page,
            'perPage'   => $perPage,
            'total'     => $total,
        ]);
    }

    public function detail(int $id = 0)
    {
        $assessment = $this->db->table('character_assessments ca')
            ->select('ca.*, anak.nama_anak, users.nama AS assessor_name')
            ->join('anak', 'anak.id_anak = ca.id_anak', 'left')
            ->join('users', 'users.id_user = ca.id_assessor', 'left')
            ->where('ca.id_assessment', $id)
            ->get()->getRowArray();

        if (! $assessment) {
            return redirect()->to(site_url('admin/penilaian-karakter/data-penilaian'))->with('error', 'Data penilaian tidak ditemukan.');
        }

        $details = $this->db->table('character_assessment_details d')
            ->select('d.*, i.indicator_name, i.indicator_code, asp.aspect_name')
            ->join('character_indicators i', 'i.id_indicator = d.id_indicator')
            ->join('character_aspects asp', 'asp.id_aspect = i.id_aspect')
            ->where('d.id_assessment', $id)
            ->orderBy('asp.sort_order', 'ASC')->orderBy('i.sort_order', 'ASC')
            ->get()->getResultArray();

        $note = $this->db->table('character_qualitative_notes')->where('id_assessment', $id)->get()->getRowArray();

        return $this->render('admin/penilaian/detail', [
            'title'      => 'Detail Penilaian - ' . app_name(),
            'pageTitle'  => 'Detail Penilaian Karakter',
            'assessment' => $assessment,
            'details'    => $details,
            'note'       => $note,
        ]);
    }

    public function catatan()
    {
        $pagination = $this->paginate($this->assessment->getQualitativeNotes(0), 12);

        return $this->render('admin/penilaian/catatan', [
            'title'     => 'Catatan Kualitatif - ' . app_name(),
            'pageTitle' => 'Catatan Kualitatif',
            'items'     => $pagination['items'],
            'page'      => $pagination['page'],
            'perPage'   => $pagination['perPage'],
            'total'     => $pagination['total'],
        ]);
    }

    public function ringkasan_mingguan()
    {
        $pagination = $this->paginate($this->groupSummaries($this->assessment->getWeeklySummary(0)), 10);

        return $this->render('admin/penilaian/ringkasan_mingguan', [
            'title'     => 'Ringkasan Mingguan - ' . app_name(),
            'pageTitle' => 'Ringkasan Mingguan',
            'groups'    => $pagination['items'],
            'page'      => $pagination['page'],
            'perPage'   => $pagination['perPage'],
            'total'     => $pagination['total'],
        ]);
    }

    public function ringkasan_bulanan()
    {
        $pagination = $this->paginate($this->groupSummaries($this->assessment->getMonthlySummary(0)), 10);

        return $this->render('admin/penilaian/ringkasan_bulanan', [
            'title'     => 'Ringkasan Bulanan - ' . app_name(),
            'pageTitle' => 'Ringkasan Bulanan',
            'groups'    => $pagination['items'],
            'page'      => $pagination['page'],
            'perPage'   => $pagination['perPage'],
            'total'     => $pagination['total'],
        ]);
    }

    public function laporan()
    {
        $filters = [
            'id_anak'       => (int) $this->request->getGet('id_anak'),
            'assessor_type' => (string) $this->request->getGet('assessor_type'),
            'month'         => (int) $this->request->getGet('month'),
            'year'          => (int) $this->request->getGet('year'),
        ];

        $summary = $this->assessment->getChildProgressSummary(array_filter($filters));

        $trend = $this->assessment->getOverallAspectTrend(array_filter($filters));

        $rekapPagination = $this->paginate($summary, 15);

        return $this->render('admin/penilaian/laporan', [
            'title'     => 'Laporan Penilaian Karakter - ' . app_name(),
            'pageTitle' => 'Laporan Penilaian Karakter',
            'summary'   => $summary,
            'rekap'     => $rekapPagination['items'],
            'trend'     => $trend,
            'filters'   => $filters,
            'anakList'  => (new AnakModel())->orderBy('nama_anak', 'ASC')->findAll(),
            'page'      => $rekapPagination['page'],
            'perPage'   => $rekapPagination['perPage'],
            'total'     => $rekapPagination['total'],
        ]);
    }

    public function laporan_detail(int $idAnak = 0)
    {
        $anak = (new AnakModel())->byId($idAnak);

        if (! $anak) {
            return redirect()->to(site_url('admin/penilaian-karakter/laporan'))->with('error', 'Data anak tidak ditemukan.');
        }

        $filters = [
            'id_anak' => $idAnak,
            'month'   => (int) $this->request->getGet('month'),
            'year'    => (int) $this->request->getGet('year'),
        ];

        $history     = $this->assessment->getChildAssessmentHistory($idAnak, [], 20);
        $indicators  = $this->assessment->getChildIndicatorScores($idAnak, array_filter($filters));
        $aspectAvg   = $this->aspectAverages($indicators);

        return $this->render('admin/penilaian/laporan_detail', [
            'title'      => 'Detail Laporan Karakter - ' . app_name(),
            'pageTitle'  => 'Detail Laporan Karakter',
            'anak'       => $anak,
            'history'    => $history,
            'indicators' => $indicators,
            'aspectAvg'  => $aspectAvg,
            'filters'    => $filters,
        ]);
    }

    public function laporan_detail_export_pdf(int $idAnak = 0)
    {
        $anak = (new AnakModel())->byId($idAnak);

        if (! $anak) {
            return redirect()->to(site_url('admin/penilaian-karakter/laporan'))->with('error', 'Data anak tidak ditemukan.');
        }

        $history    = $this->assessment->getChildAssessmentHistory($idAnak, [], 20);
        $indicators = $this->assessment->getChildIndicatorScores($idAnak);
        $aspectAvg  = $this->aspectAverages($indicators);

        $body = view('pdf/karakter_detail', [
            'anak'       => $anak,
            'history'    => $history,
            'indicators' => $indicators,
            'aspectAvg'  => $aspectAvg,
        ]);

        $content = (new PdfExport((array) setting()))->render(
            'Laporan Perkembangan Karakter',
            $body,
            'karakter_' . $idAnak . '.pdf',
            'P',
            ['Nama anak' => $anak['nama_anak'], 'No. Registrasi' => $anak['no_registrasi'] ?: '-']
        );

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="karakter_' . $idAnak . '.pdf"')
            ->setBody($content);
    }

    private function groupSummaries(array $items): array
    {
        $groups = [];
        $index  = [];

        foreach ($items as $row) {
            $key = (int) $row['id_anak'];

            if (! isset($index[$key])) {
                $index[$key] = count($groups);
                $groups[]    = ['nama_anak' => $row['nama_anak'], 'rows' => []];
            }

            $groups[$index[$key]]['rows'][] = $row;
        }

        return $groups;
    }

    private function aspectAverages(array $indicators): array
    {
        $acc = [];

        foreach ($indicators as $row) {
            $name = $row['aspect_name'] ?? 'Aspek';
            $acc[$name]['total'] = ($acc[$name]['total'] ?? 0) + (float) $row['avg_score'];
            $acc[$name]['count'] = ($acc[$name]['count'] ?? 0) + 1;
        }

        $out = [];

        foreach ($acc as $name => $item) {
            $out[$name] = $item['count'] > 0 ? round($item['total'] / $item['count'], 2) : 0;
        }

        arsort($out);

        return $out;
    }
}
