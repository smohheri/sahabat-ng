<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AnakModel;
use App\Models\CharacterAssessmentModel;
use App\Models\GuruModel;
use App\Models\PengurusModel;
use App\Models\UserLogModel;
use App\Models\UserModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $anakModel   = new AnakModel();
        $stats       = $anakModel->statistics();
        $assessment  = new CharacterAssessmentModel();
        $logModel    = new UserLogModel();

        $recentAnak = $anakModel->orderBy('created_at', 'DESC')->limit(5)->findAll();

        $monthlyAssessments = [];
        if ($assessment->isSchemaReady()) {
            $rows = $this->db->table('character_assessments')
                ->select('month, COUNT(*) AS total')
                ->where('year', (int) date('Y'))
                ->groupBy('month')
                ->orderBy('month', 'ASC')
                ->get()->getResultArray();

            foreach ($rows as $row) {
                $monthlyAssessments[(int) $row['month']] = (int) $row['total'];
            }
        }

        return $this->render('admin/dashboard', [
            // data dashboard
            'title'              => 'Dashboard Admin',
            'pageTitle'          => 'Dashboard',
            'stats'              => $stats,
            'totalPengurus'      => (new PengurusModel())->countAllResults(),
            'totalGuru'          => (new GuruModel())->countAllResults(),
            'totalUsers'         => (new UserModel())->countAllResults(),
            'totalAssessments'   => $assessment->countAssessments(),
            'recentAnak'         => $recentAnak,
            'recentLogs'         => $logModel->recent(8),
            'monthlyAssessments' => $monthlyAssessments,
        ]);
    }

    public function dukung_kami()
    {
        return $this->render('admin/dukung_kami', [
            'title'     => 'Dukung Kami - ' . app_name(),
            'pageTitle' => 'Dukung Kami',
        ]);
    }

    public function kontak()
    {
        return $this->render('admin/kontak', [
            'title'     => 'Kontak - ' . app_name(),
            'pageTitle' => 'Kontak',
        ]);
    }

    public function changelog()
    {
        $changelog = is_file(ROOTPATH . 'CHANGELOG.md') ? (string) file_get_contents(ROOTPATH . 'CHANGELOG.md') : 'Belum ada changelog.';

        return $this->render('admin/changelog', [
            'title'     => 'Changelog - ' . app_name(),
            'pageTitle' => 'Changelog',
            'changelog' => $changelog,
        ]);
    }
}
