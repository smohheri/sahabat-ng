<?php

namespace App\Controllers;

use App\Models\AnakModel;
use App\Models\CarouselModel;
use App\Models\FasilitasModel;
use App\Models\GuruModel;
use App\Models\HeroModel;
use App\Models\PengurusModel;

class Landing extends BaseController
{
    public function index()
    {
        $anakModel      = new AnakModel();
        $carouselModel  = new CarouselModel();
        $heroModel      = new HeroModel();
        $fasilitasModel = new FasilitasModel();

        $stats = $anakModel->statistics();

        $stats['pengurus'] = (new PengurusModel())->countAllResults();
        $stats['pengajar'] = (new GuruModel())->countAllResults();

        return $this->renderLanding('landing/home', [
            'title'       => app_name() . ' - Beranda',
            'heroes'      => $heroModel->active(),
            'carousel'    => $carouselModel->active(),
            'facilities'  => $fasilitasModel->active(),
            'stats'       => $stats,
            'settings'    => $this->settings,
        ]);
    }

    public function license()
    {
        return $this->renderLanding('landing/license', [
            'title'    => 'Lisensi - ' . app_name(),
            'settings' => $this->settings,
        ]);
    }

    public function donasi()
    {
        return $this->renderLanding('landing/donasi', [
            'title'    => 'Donasi - ' . app_name(),
            'settings' => $this->settings,
        ]);
    }
}
