<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserLogModel;

class Logs extends BaseController
{
    public function index()
    {
        $model   = new UserLogModel();
        $filters = [
            'search'   => trim((string) $this->request->getGet('q')),
            'activity' => (string) $this->request->getGet('activity'),
        ];

        $perPage = 25;
        $page    = max(1, (int) $this->request->getGet('page'));
        $total   = $model->countList($filters);
        $items   = $model->getList($filters, $perPage, ($page - 1) * $perPage);

        return $this->render('admin/logs', [
            'title'     => 'Log Aktivitas - ' . app_name(),
            'pageTitle' => 'Log Aktivitas',
            'items'     => $items,
            'filters'   => $filters,
            'page'      => $page,
            'perPage'   => $perPage,
            'total'     => $total,
            'activities' => $model->activityOptions(),
        ]);
    }

    public function ajax()
    {
        $model   = new UserLogModel();
        $filters = [
            'search'   => trim((string) $this->request->getGet('search')),
            'activity' => (string) $this->request->getGet('activity'),
        ];

        $start  = max(0, (int) $this->request->getGet('start'));
        $length = min(200, max(1, (int) ($this->request->getGet('length') ?: 25)));

        return $this->response->setJSON([
            'draw'            => (int) $this->request->getGet('draw'),
            'recordsTotal'    => $model->countList([]),
            'recordsFiltered' => $model->countList($filters),
            'data'            => $model->getList($filters, $length, $start),
        ]);
    }
}
