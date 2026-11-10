<?php

namespace App\Controllers;

use App\Models\AnakModel;
use App\Models\CharacterAssessmentModel;
use App\Models\CharacterMasterModel;

class Anak extends BaseController
{
    private AnakModel $anakModel;
    private CharacterAssessmentModel $assessment;
    private CharacterMasterModel $master;

    public function __construct()
    {
        $this->anakModel  = new AnakModel();
        $this->assessment = new CharacterAssessmentModel();
        $this->master     = new CharacterMasterModel();
    }

    public function index()
    {
        $idAnak = (int) $this->session->get('id_anak');
        $anak   = $idAnak > 0 ? $this->anakModel->byId($idAnak) : null;

        if (! $anak) {
            return $this->render('anak/dashboard_unlinked', [
                'title'     => 'Panel Anak - ' . app_name(),
                'pageTitle' => 'Panel Anak',
            ]);
        }

        $month = (int) date('n');
        $year  = (int) date('Y');

        $history        = $this->assessment->getChildAssessmentHistory($idAnak, [], 10);
        $indicators     = $this->assessment->getChildIndicatorScores($idAnak, ['period_type' => 'monthly', 'month' => $month, 'year' => $year]);
        $alreadySubmit  = $this->assessment->hasMonthlyAssessment($idAnak, $month, $year, 'anak_asuh');
        $aspectAvg      = $this->aspectAverages($indicators);

        $latestNote = $this->db->table('character_qualitative_notes n')
            ->select('n.*, a.assessment_date, u.nama AS assessor_name')
            ->join('character_assessments a', 'a.id_assessment = n.id_assessment')
            ->join('users u', 'u.id_user = a.id_assessor', 'left')
            ->where('a.id_anak', $idAnak)
            ->orderBy('a.assessment_date', 'DESC')
            ->orderBy('a.id_assessment', 'DESC')
            ->get()->getRowArray();

        $best    = $aspectAvg !== [] ? array_key_first($aspectAvg) : null;
        $worst   = $aspectAvg !== [] ? array_key_last($aspectAvg) : null;
        $summary = $aspectAvg === []
            ? 'Data penilaian bulan ini belum cukup untuk menyusun ringkasan perkembangan.'
            : 'Perkembangan terkuat ada pada aspek ' . $best . ' (skor ' . number_format((float) $aspectAvg[$best], 2) . '). Aspek yang masih perlu perhatian adalah ' . $worst . ' (skor ' . number_format((float) $aspectAvg[$worst], 2) . ').';

        $improvement = 'Belum ada catatan perbaikan dari guru yang tersimpan pada sistem.';

        if ($latestNote && (! empty($latestNote['areas_to_support']) || ! empty($latestNote['support_strategy']))) {
            $segments = [];

            if (! empty($latestNote['areas_to_support'])) {
                $segments[] = 'Fokus perbaikan berikutnya: ' . trim((string) $latestNote['areas_to_support']);
            }

            if (! empty($latestNote['support_strategy'])) {
                $segments[] = 'Rencana tindakan: ' . trim((string) $latestNote['support_strategy']);
            }

            $improvement = implode(' ', $segments);
        }

        return $this->render('anak/dashboard', [
            'title'         => 'Dashboard Anak - ' . app_name(),
            'pageTitle'     => 'Dashboard Anak',
            'anak'          => $anak,
            'history'       => $history,
            'indicators'    => $indicators,
            'aspectAvg'     => $aspectAvg,
            'alreadySubmit' => $alreadySubmit,
            'latestNote'    => $latestNote,
            'summary'       => $summary,
            'improvement'   => $improvement,
            'month'         => $month,
            'year'          => $year,
        ]);
    }

    public function asesmen_mandiri()
    {
        $idAnak = (int) $this->session->get('id_anak');
        $anak   = $idAnak > 0 ? $this->anakModel->byId($idAnak) : null;

        if (! $anak) {
            return redirect()->to(site_url('anak'))->with('error', 'Akun belum terhubung ke data anak.');
        }

        $month = (int) date('n');
        $year  = (int) date('Y');

        if ($this->request->is('post')) {
            if ($this->assessment->hasMonthlyAssessment($idAnak, $month, $year, 'anak_asuh')) {
                return redirect()->to(site_url('anak/asesmen-mandiri'))->with('error', 'Penilaian mandiri bulan ini sudah pernah dikirim.');
            }

            $scores = array_filter((array) $this->request->getPost('scores'), static fn ($score) => (int) $score >= 1 && (int) $score <= 4);

            if ($scores === []) {
                return redirect()->back()->with('error', 'Silakan isi minimal satu indikator penilaian mandiri.');
            }

            $payload = [
                'id_anak'         => $idAnak,
                'id_assessor'     => (int) $this->session->get('id_user'),
                'assessor_type'   => 'anak_asuh',
                'assessment_date' => date('Y-m-d'),
                'week_number'     => (int) date('W'),
                'month'           => $month,
                'year'            => $year,
                'notes'           => $this->request->getPost('notes') ?: null,
                'status'          => 'completed',
            ];

            if (! $this->assessment->createAssessment($payload, $scores)) {
                return redirect()->back()->with('error', 'Gagal menyimpan penilaian mandiri.');
            }

            log_activity('self_assessment', 'Mengirim penilaian mandiri');

            return redirect()->to(site_url('anak'))->with('success', 'Penilaian mandiri berhasil dikirim.');
        }

        $alreadySubmit = $this->assessment->hasMonthlyAssessment($idAnak, $month, $year, 'anak_asuh');

        return $this->render('anak/asesmen_mandiri', [
            'title'         => 'Asesmen Mandiri - ' . app_name(),
            'pageTitle'     => 'Asesmen Mandiri',
            'anak'          => $anak,
            'aspects'       => $this->master->getAspectsWithIndicators(),
            'scales'        => $this->master->getScales(),
            'alreadySubmit' => $alreadySubmit,
            'month'         => $month,
            'year'          => $year,
        ]);
    }

    public function profil()
    {
        $idAnak = (int) $this->session->get('id_anak');
        $anak   = $idAnak > 0 ? $this->anakModel->byId($idAnak) : null;

        if (! $anak) {
            return redirect()->to(site_url('anak'))->with('error', 'Akun belum terhubung ke data anak.');
        }

        return $this->render('anak/profil', [
            'title'     => 'Profil Saya - ' . app_name(),
            'pageTitle' => 'Profil Saya',
            'anak'      => $anak,
        ]);
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
