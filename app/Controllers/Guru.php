<?php

namespace App\Controllers;

use App\Libraries\ExcelExport;
use App\Libraries\PdfExport;
use App\Models\AnakModel;
use App\Models\CharacterAssessmentModel;
use App\Models\CharacterMasterModel;
use App\Models\GuruModel;
use App\Models\RombelModel;
use App\Models\UserModel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Guru extends BaseController
{
    private GuruModel $guruModel;
    private AnakModel $anakModel;
    private CharacterAssessmentModel $assessment;
    private CharacterMasterModel $master;

    public function __construct()
    {
        $this->guruModel  = new GuruModel();
        $this->anakModel  = new AnakModel();
        $this->assessment = new CharacterAssessmentModel();
        $this->master     = new CharacterMasterModel();
    }

    public function index()
    {
        $guru       = $this->currentGuru();
        $rombelIds  = $this->teacherRombelIds();
        $anakCount  = count($this->teacherChildren());

        $recent = $guru
            ? $this->assessment->getAssessmentsByAssessor((int) $this->session->get('id_user'), 8)
            : [];

        $progress = $guru
            ? $this->assessment->getAssessorProgressSummary((int) $this->session->get('id_user'))
            : [];

        return $this->render('guru/dashboard', [
            'title'        => 'Dashboard Pengajar - ' . app_name(),
            'pageTitle'    => 'Dashboard Pengajar',
            'guru'         => $guru,
            'totalAnak'    => $anakCount,
            'totalRombel'  => count($rombelIds),
            'totalAssess'  => count($recent),
            'recent'       => $recent,
            'progress'     => $progress,
            'rombelList'   => $rombelIds,
        ]);
    }

    public function anak()
    {
        $pagination = $this->paginate($this->teacherChildren());

        return $this->render('guru/anak', [
            'title'     => 'Data Anak - ' . app_name(),
            'pageTitle' => 'Data Anak',
            'items'     => $pagination['items'],
            'page'      => $pagination['page'],
            'perPage'   => $pagination['perPage'],
            'total'     => $pagination['total'],
        ]);
    }

    public function profil_pengajar()
    {
        return $this->render('guru/profil_pengajar', [
            'title'     => 'Profil Pengajar - ' . app_name(),
            'pageTitle' => 'Profil Pengajar',
            'guru'      => $this->currentGuru(),
        ]);
    }

    public function edit_profil_pengajar()
    {
        return $this->render('guru/edit_profil_pengajar', [
            'title'     => 'Edit Profil Pengajar - ' . app_name(),
            'pageTitle' => 'Edit Profil Pengajar',
            'guru'      => $this->currentGuru(),
        ]);
    }

    public function update_profil_pengajar()
    {
        $idGuru = (int) $this->session->get('id_guru');
        $guru   = $this->currentGuru();
        $data   = [
            'nama_guru'           => trim((string) $this->request->getPost('nama_guru')),
            'nip'                 => $this->request->getPost('nip') ?: null,
            'jabatan'             => $this->request->getPost('jabatan') ?: null,
            'pendidikan_terakhir' => $this->request->getPost('pendidikan_terakhir') ?: null,
            'bidang_keahlian'     => $this->request->getPost('bidang_keahlian') ?: null,
            'sertifikasi'         => $this->request->getPost('sertifikasi') ?: null,
            'pengalaman_tahun'    => $this->request->getPost('pengalaman_tahun') !== null ? (int) $this->request->getPost('pengalaman_tahun') : null,
            'status_kepegawaian'  => $this->request->getPost('status_kepegawaian') ?: null,
            'no_hp'               => $this->request->getPost('no_hp') ?: null,
            'email'               => $this->request->getPost('email') ?: null,
            'alamat'              => $this->request->getPost('alamat') ?: null,
        ];

        if ($data['nama_guru'] === '') {
            return redirect()->back()->withInput()->with('error', 'Nama pengajar wajib diisi.');
        }

        if ($guru) {
            $idGuru = (int) $guru['id_guru'];
            $this->guruModel->update($idGuru, $data);
        } else {
            $data['is_active'] = 1;
            $idGuru = (int) $this->guruModel->insert($data, true);
            $this->db->table('users')->where('id_user', (int) $this->session->get('id_user'))->update(['id_guru' => $idGuru]);
            $this->session->set('id_guru', $idGuru);
        }

        $foto = store_upload($this->request->getFile('foto'), 'foto_guru', 'guru', ['jpg', 'jpeg', 'png', 'webp']);
        if ($foto !== null) {
            $this->guruModel->update($idGuru, ['foto' => $foto]);
        }

        $ijazah = store_upload($this->request->getFile('file_ijazah_terakhir'), 'file_ijazah', 'ijazah');
        if ($ijazah !== null) {
            $this->guruModel->update($idGuru, ['file_ijazah_terakhir' => $ijazah]);
        }

        log_activity('edit_profil_pengajar', 'Memperbarui profil pengajar');

        return redirect()->to(site_url('guru/profil-pengajar'))->with('success', 'Profil pengajar berhasil disimpan.');
    }

    public function penilaian_karakter()
    {
        $children   = $this->teacherChildren();
        $idAnak     = (int) $this->request->getGet('id_anak');
        $selected   = ($idAnak > 0 && $this->isChildInScope($idAnak))
            ? $this->anakModel->byId($idAnak)
            : ($children[0] ?? null);
        $aspects    = $this->master->getAspectsWithIndicators();
        $scales     = $this->master->getScales();
        $history    = [];
        $monthly    = [];

        if ($selected) {
            $history = $this->assessment->getChildAssessmentHistory((int) $selected['id_anak'], [], 10);
            $monthly = $this->assessment->getChildIndicatorScores((int) $selected['id_anak'], [
                'period_type' => 'monthly',
                'month'       => (int) date('n'),
                'year'        => (int) date('Y'),
            ]);
        }

        return $this->render('guru/penilaian', [
            'title'     => 'Penilaian Karakter - ' . app_name(),
            'pageTitle' => 'Penilaian Karakter',
            'children'  => $children,
            'selected'  => $selected,
            'aspects'   => $aspects,
            'scales'    => $scales,
            'history'   => $history,
            'monthly'   => $monthly,
        ]);
    }

    public function penilaian_karakter_save()
    {
        $idAnak = (int) $this->request->getPost('id_anak');

        if (! $this->isChildInScope($idAnak)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses terhadap data anak tersebut.');
        }

        $anak   = $this->anakModel->find($idAnak);

        if (! $anak) {
            return redirect()->back()->with('error', 'Data anak tidak ditemukan.');
        }

        $scores = array_filter((array) $this->request->getPost('scores'), static fn ($score) => (int) $score >= 1 && (int) $score <= 4);

        if ($scores === []) {
            return redirect()->back()->with('error', 'Isi minimal satu indikator penilaian.');
        }

        $now = date('Y-m-d');

        $payload = [
            'id_anak'         => $idAnak,
            'id_assessor'     => (int) $this->session->get('id_user'),
            'assessor_type'   => 'guru',
            'assessment_date' => $now,
            'week_number'     => (int) date('W'),
            'month'           => (int) date('n'),
            'year'            => (int) date('Y'),
            'notes'           => $this->request->getPost('notes') ?: null,
            'status'          => 'completed',
        ];

        $note = [
            'strengths'            => $this->request->getPost('strengths') ?: null,
            'development_observed' => $this->request->getPost('development_observed') ?: null,
            'areas_to_support'     => $this->request->getPost('areas_to_support') ?: null,
            'support_strategy'     => $this->request->getPost('support_strategy') ?: null,
        ];

        $id = $this->assessment->createAssessment($payload, $scores, $note);

        if (! $id) {
            return redirect()->back()->with('error', 'Gagal menyimpan penilaian karakter.');
        }

        log_activity('add_assessment', 'Menilai karakter anak: ' . $anak['nama_anak']);

        return redirect()->to(site_url('guru/penilaian-karakter') . '?id_anak=' . $idAnak)->with('success', 'Penilaian karakter berhasil disimpan.');
    }

    public function penilaian_karakter_template()
    {
        $children   = $this->teacherChildren();
        $indicators = $this->master->getIndicators();
        $headers    = ['Nama Anak'];

        foreach ($indicators as $indicator) {
            $headers[] = $indicator['indicator_code'] ?: $indicator['indicator_name'];
        }

        $rows = [];

        foreach ($children as $child) {
            $rows[] = array_merge([$child['nama_anak']], array_fill(0, count($indicators), ''));
        }

        $content = (new ExcelExport())->build('Template Penilaian Karakter', $headers, $rows, [
            'Petunjuk' => 'Isi skor 1-4 pada kolom indikator. Jangan mengubah nama anak maupun kode indikator.',
        ]);

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="template_penilaian_karakter.xlsx"')
            ->setBody($content);
    }

    public function penilaian_karakter_import()
    {
        $file = $this->request->getFile('file');

        if ($file === null || ! $file->isValid()) {
            return redirect()->back()->with('error', 'Pilih file Excel template penilaian.');
        }

        try {
            $sheet = IOFactory::load($file->getTempName())->getActiveSheet();
            $rows  = $sheet->toArray(null, true, true, true);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'File Excel tidak dapat dibaca: ' . $e->getMessage());
        }

        $headerRow = array_shift($rows);

        if (! $headerRow) {
            return redirect()->back()->with('error', 'Template tidak memiliki header.');
        }

        $indicatorByCode = [];

        foreach ($this->master->getIndicators() as $indicator) {
            $code = $indicator['indicator_code'] ?: $indicator['indicator_name'];
            $indicatorByCode[$code] = (int) $indicator['id_indicator'];
        }

        $childrenByName = [];

        foreach ($this->teacherChildren() as $child) {
            $childrenByName[strtolower(trim($child['nama_anak']))] = $child;
        }

        $imported = 0;
        $skipped  = 0;
        $now      = date('Y-m-d');

        foreach ($rows as $row) {
            $name = trim((string) ($row['A'] ?? ''));

            if ($name === '') {
                continue;
            }

            $child = $childrenByName[strtolower($name)] ?? null;

            if (! $child) {
                $skipped++;
                continue;
            }

            $scores = [];

            foreach ($headerRow as $column => $code) {
                if ($column === 'A' || empty($code)) {
                    continue;
                }

                $code = trim((string) $code);
                $id   = $indicatorByCode[$code] ?? null;
                $val  = (int) ($row[$column] ?? 0);

                if ($id !== null && $val >= 1 && $val <= 4) {
                    $scores[$id] = $val;
                }
            }

            if ($scores === []) {
                $skipped++;
                continue;
            }

            $payload = [
                'id_anak'         => (int) $child['id_anak'],
                'id_assessor'     => (int) $this->session->get('id_user'),
                'assessor_type'   => 'guru',
                'assessment_date' => $now,
                'week_number'     => (int) date('W'),
                'month'           => (int) date('n'),
                'year'            => (int) date('Y'),
                'status'          => 'completed',
            ];

            if ($this->assessment->createAssessment($payload, $scores)) {
                $imported++;
            } else {
                $skipped++;
            }
        }

        log_activity('import_assessment', 'Import penilaian karakter: ' . $imported . ' berhasil, ' . $skipped . ' dilewati');

        return redirect()->to(site_url('guru/penilaian-karakter'))->with('success', 'Import selesai: ' . $imported . ' data tersimpan, ' . $skipped . ' dilewati.');
    }

    public function perkembangan_anak()
    {
        $children = $this->teacherChildren();
        $summary  = $this->assessment->getChildProgressSummary();

        $byChild = [];

        foreach ($summary as $row) {
            $byChild[(int) $row['id_anak']] = $row;
        }

        $assessed = 0;
        $weighted = 0;
        $totalAssess = 0;

        foreach ($children as &$child) {
            $child['progress'] = $byChild[(int) $child['id_anak']] ?? null;

            $progress = $child['progress'];
            if ($progress && (int) $progress['total_assessment'] > 0) {
                $assessed++;
                $weighted += (float) $progress['avg_score'] * (int) $progress['total_assessment'];
                $totalAssess += (int) $progress['total_assessment'];
            }
        }
        unset($child);

        $pagination = $this->paginate($children);

        return $this->render('guru/perkembangan', [
            'title'      => 'Perkembangan Anak - ' . app_name(),
            'pageTitle'  => 'Perkembangan Anak',
            'items'      => $pagination['items'],
            'totalAnak'  => count($children),
            'assessed'   => $assessed,
            'avgAll'     => $totalAssess > 0 ? $weighted / $totalAssess : 0,
            'page'       => $pagination['page'],
            'perPage'    => $pagination['perPage'],
            'total'      => $pagination['total'],
        ]);
    }

    public function perkembangan_anak_detail(int $idAnak = 0)
    {
        $anak = $this->anakModel->byId($idAnak);

        if (! $anak) {
            return redirect()->to(site_url('guru/perkembangan-anak'))->with('error', 'Data anak tidak ditemukan.');
        }

        if (! $this->isChildInScope($idAnak)) {
            return redirect()->to(site_url('guru/perkembangan-anak'))->with('error', 'Anda tidak memiliki akses terhadap data anak tersebut.');
        }

        return $this->render('guru/perkembangan_detail', [
            'title'      => 'Detail Perkembangan - ' . app_name(),
            'pageTitle'  => 'Detail Perkembangan Anak',
            'anak'       => $anak,
            'history'    => $this->assessment->getChildAssessmentHistory($idAnak, [], 20),
            'indicators' => $this->assessment->getChildIndicatorScores($idAnak, [
                'period_type' => 'monthly',
                'month'       => (int) date('n'),
                'year'        => (int) date('Y'),
            ]),
            'notes'      => $this->assessment->getQualitativeNotes(100),
        ]);
    }

    public function perkembangan_anak_detail_export_pdf(int $idAnak = 0)
    {
        $anak = $this->anakModel->byId($idAnak);

        if (! $anak) {
            return redirect()->to(site_url('guru/perkembangan-anak'))->with('error', 'Data anak tidak ditemukan.');
        }

        if (! $this->isChildInScope($idAnak)) {
            return redirect()->to(site_url('guru/perkembangan-anak'))->with('error', 'Anda tidak memiliki akses terhadap data anak tersebut.');
        }

        $body = view('pdf/karakter_detail', [
            'anak'       => $anak,
            'history'    => $this->assessment->getChildAssessmentHistory($idAnak, [], 20),
            'indicators' => $this->assessment->getChildIndicatorScores($idAnak),
            'aspectAvg'  => [],
        ]);

        $content = (new PdfExport((array) setting()))->render('Perkembangan Anak', $body, 'perkembangan_' . $idAnak . '.pdf', 'P', [
            'Nama anak' => $anak['nama_anak'],
        ]);

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="perkembangan_' . $idAnak . '.pdf"')
            ->setBody($content);
    }

    private function currentGuru(): ?array
    {
        $idGuru = (int) $this->session->get('id_guru');

        if ($idGuru > 0) {
            return $this->guruModel->find($idGuru);
        }

        $user = (new UserModel())->find((int) $this->session->get('id_user'));

        if ($user && ! empty($user['id_guru'])) {
            return $this->guruModel->find((int) $user['id_guru']);
        }

        $byName = $this->guruModel->byName((string) $this->session->get('nama'));

        if ($byName) {
            $this->session->set('id_guru', (int) $byName['id_guru']);
        }

        return $byName;
    }

    private function teacherMapelIds(): array
    {
        $idUser = (int) $this->session->get('id_user');

        return array_map('intval', array_column(
            $this->db->table('mata_pelajaran')->select('id_mata_pelajaran')->where('id_user_pengampu', $idUser)->get()->getResultArray(),
            'id_mata_pelajaran'
        ));
    }

    private function teacherRombelIds(): array
    {
        $mapelIds = $this->teacherMapelIds();

        if ($mapelIds === []) {
            return [];
        }

        $rows = $this->db->table('rombel_mata_pelajaran')
            ->select('DISTINCT id_rombel')
            ->whereIn('id_mata_pelajaran', $mapelIds)
            ->get()->getResultArray();

        return array_map('intval', array_column($rows, 'id_rombel'));
    }

    private function teacherChildren(): array
    {
        $rombelIds = $this->teacherRombelIds();

        if ($rombelIds === []) {
            return $this->anakModel->allAktif();
        }

        $rows = $this->db->table('rombel_anak ra')
            ->select('anak.*')
            ->join('anak', 'anak.id_anak = ra.id_anak')
            ->whereIn('ra.id_rombel', $rombelIds)
            ->where('anak.status_anak', 'Aktif')
            ->orderBy('anak.nama_anak', 'ASC')
            ->get()->getResultArray();

        return $rows ?: $this->anakModel->allAktif();
    }

    private function teacherChildIds(): array
    {
        return array_map('intval', array_column($this->teacherChildren(), 'id_anak'));
    }

    private function isChildInScope(int $idAnak): bool
    {
        return $idAnak > 0 && in_array($idAnak, $this->teacherChildIds(), true);
    }
}
