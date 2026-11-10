<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\ExcelExport;
use App\Libraries\PdfExport;
use App\Models\AnakModel;
use App\Models\CharacterAssessmentModel;
use App\Models\PengurusModel;

class Laporan extends BaseController
{
    public function index(string $jenis = 'data_anak')
    {
        $allowed = ['data_anak', 'pengurus', 'dokumen', 'keuangan', 'statistik', 'eksternal'];

        if (! in_array($jenis, $allowed, true)) {
            $jenis = 'data_anak';
        }

        $filters = $this->filters();
        $data    = $this->reportData($jenis, $filters);

        $pagination = ['page' => 1, 'perPage' => 15, 'total' => 0];
        if ($jenis !== 'statistik') {
            $pagination      = $this->paginate($data['items'] ?? [], 15);
            $data['items']   = $pagination['items'];
        }

        return $this->render('admin/laporan/index', array_merge([
            'title'     => 'Laporan - ' . app_name(),
            'pageTitle' => 'Laporan',
            'jenis'     => $jenis,
            'filters'   => $filters,
            'jenisList' => $allowed,
            'page'      => $pagination['page'],
            'perPage'   => $pagination['perPage'],
            'total'     => $pagination['total'],
        ], $data));
    }

    public function export_pdf_anak()
    {
        $rows = $this->anakRows($this->filters());

        return $this->pdfResponse('laporan_data_anak_' . date('Ymd_His') . '.pdf', 'Laporan Data Anak', [
            'No', 'No. Registrasi', 'Nama', 'L/P', 'Tempat/Tgl Lahir', 'Umur', 'Kategori', 'Pendidikan', 'Status', 'Asal',
        ], $rows, ['Jumlah data' => count($rows)], 'L');
    }

    public function export_excel_anak()
    {
        $rows = $this->anakRows($this->filters());

        return $this->excelResponse('laporan_data_anak_' . date('Ymd_His') . '.xlsx', 'Data Anak', [
            'No', 'No. Registrasi', 'Nama', 'L/P', 'Tempat/Tanggal Lahir', 'Umur', 'Kategori', 'Pendidikan', 'Status', 'Asal',
        ], $rows, ['Laporan' => 'Data Anak', 'Dicetak' => date('d/m/Y H:i')]);
    }

    public function export_pdf_pengurus()
    {
        $rows = $this->pengurusRows();

        return $this->pdfResponse('laporan_pengurus_' . date('Ymd_His') . '.pdf', 'Laporan Data Pengurus', [
            'No', 'Nama', 'Jabatan', 'No. HP', 'Email',
        ], $rows, ['Jumlah data' => count($rows)]);
    }

    public function export_excel_pengurus()
    {
        $rows = $this->pengurusRows();

        return $this->excelResponse('laporan_pengurus_' . date('Ymd_His') . '.xlsx', 'Data Pengurus', [
            'No', 'Nama', 'Jabatan', 'No. HP', 'Email',
        ], $rows, ['Laporan' => 'Data Pengurus', 'Dicetak' => date('d/m/Y H:i')]);
    }

    public function export_pdf_dokumen()
    {
        $rows = $this->dokumenRows($this->filters());

        return $this->pdfResponse('laporan_dokumen_' . date('Ymd_His') . '.pdf', 'Laporan Kelengkapan Dokumen Anak', [
            'No', 'Nama Anak', 'No. KK', 'Kartu Keluarga', 'Akta Lahir', 'Dokumen Pendukung',
        ], $rows, ['Jumlah data' => count($rows)], 'L');
    }

    public function export_excel_dokumen()
    {
        $rows = $this->dokumenRows($this->filters());

        return $this->excelResponse('laporan_dokumen_' . date('Ymd_His') . '.xlsx', 'Dokumen Anak', [
            'No', 'Nama Anak', 'No. KK', 'Kartu Keluarga', 'Akta Lahir', 'Dokumen Pendukung',
        ], $rows, ['Laporan' => 'Kelengkapan Dokumen', 'Dicetak' => date('d/m/Y H:i')]);
    }

    public function export_pdf_statistik()
    {
        return $this->statistikPdf();
    }

    public function generate_pdf_statistik()
    {
        return $this->statistikPdf();
    }

    public function export_pdf_karakter()
    {
        $rows = $this->karakterRows();

        return $this->pdfResponse('laporan_karakter_' . date('Ymd_His') . '.pdf', 'Laporan Penilaian Karakter', [
            'No', 'Nama Anak', 'Tanggal', 'Aspek', 'Indikator', 'Skor', 'Asesor',
        ], $rows, ['Jumlah data' => count($rows)], 'L');
    }

    public function export_pdf_eksternal()
    {
        $rows = $this->anakRows($this->filters());

        return $this->pdfResponse('laporan_eksternal_' . date('Ymd_His') . '.pdf', 'Laporan Eksternal Data Anak', [
            'No', 'Nama', 'L/P', 'Tempat/Tgl Lahir', 'Pendidikan', 'Status', 'Kategori',
        ], array_map(static fn ($row) => [$row[0], $row[2], $row[3], $row[4], $row[7], $row[8], $row[6]], $rows), ['Jumlah data' => count($rows)], 'L');
    }

    public function export_excel_eksternal()
    {
        $rows = $this->anakRows($this->filters());

        return $this->excelResponse('laporan_eksternal_' . date('Ymd_His') . '.xlsx', 'Laporan Eksternal', [
            'No', 'Nama', 'L/P', 'Tempat/Tanggal Lahir', 'Pendidikan', 'Status', 'Kategori',
        ], array_map(static fn ($row) => [$row[0], $row[2], $row[3], $row[4], $row[7], $row[8], $row[6]], $rows), ['Laporan' => 'Data Anak (Eksternal)', 'Dicetak' => date('d/m/Y H:i')]);
    }

    public function export_anak()
    {
        $statusMode = (string) $this->request->getPost('status_mode');
        $format     = (string) $this->request->getPost('format');
        $columns    = (array) $this->request->getPost('columns');

        $status = $statusMode === 'nonaktif' ? ['Nonaktif', 'Alumni'] : ['Aktif'];

        $filters = [
            'status_anak'   => $status,
            'search'        => trim((string) $this->request->getPost('q')),
            'jenis_kelamin' => (string) $this->request->getPost('jenis_kelamin'),
            'pendidikan'    => (string) $this->request->getPost('pendidikan'),
            'kategori'      => (string) $this->request->getPost('kategori'),
        ];

        $items = (new AnakModel())->getList($filters);

        $colDefs = anak_export_columns();
        $columns = array_values(array_unique(array_filter($columns, static fn ($c) => isset($colDefs[$c]))));

        if ($columns === []) {
            $columns = array_keys($colDefs);
        }

        $headers = ['No'];
        foreach ($columns as $c) {
            $headers[] = $colDefs[$c];
        }

        $rows = [];
        foreach ($items as $i => $item) {
            $row = [$i + 1];
            foreach ($columns as $c) {
                $row[] = $this->displayAnakValue($item, $c);
            }
            $rows[] = $row;
        }

        $statusLabel = $statusMode === 'nonaktif' ? 'Anak Nonaktif' : 'Anak Aktif';
        $title       = 'Laporan Data ' . $statusLabel;

        $charts = [];
        foreach ($columns as $c) {
            if (! in_array($c, anak_graphable_columns(), true)) {
                continue;
            }

            $counts = $this->aggregateCounts($items, $c);
            if ($counts === []) {
                continue;
            }

            $file = bar_chart_file($counts, 'Distribusi ' . $colDefs[$c]);
            if ($file) {
                $charts[] = ['label' => $colDefs[$c], 'file' => $file];
            }
        }

        $meta = [
            'Laporan'     => $title,
            'Jumlah data' => count($items),
            'Dicetak'     => date('d/m/Y H:i'),
        ];

        $filename = 'laporan_' . $statusMode . '_' . date('Ymd_His');

        if ($format === 'pdf') {
            return $this->exportAnakPdf($title, $headers, $rows, $charts, $meta, $filename . '.pdf');
        }

        return $this->exportAnakExcel($title, $headers, $rows, $charts, $meta, $filename . '.xlsx');
    }

    private function displayAnakValue(array $anak, string $key): string
    {
        switch ($key) {
            case 'jenis_kelamin':
                return ($anak['jenis_kelamin'] ?? '') === 'L' ? 'Laki-laki' : 'Perempuan';
            case 'tanggal_lahir':
                return tanggal_id($anak['tanggal_lahir'] ?? null);
            case 'umur':
                return umur($anak['tanggal_lahir'] ?? null);
            case 'biaya_spp':
                $spp = $anak['biaya_spp'] ?? null;

                return ($spp !== null && $spp !== '' && (float) $spp > 0) ? rupiah($spp) : '-';
            default:
                $val = $anak[$key] ?? null;

                return ($val === null || $val === '') ? '-' : (string) $val;
        }
    }

    private function aggregateCounts(array $items, string $key): array
    {
        $counts = [];
        foreach ($items as $item) {
            $v = $this->displayAnakValue($item, $key);
            if ($v === '-' || $v === '') {
                continue;
            }
            $counts[$v] = ($counts[$v] ?? 0) + 1;
        }

        return $counts;
    }

    private function exportAnakPdf(string $title, array $headers, array $rows, array $charts, array $meta, string $filename)
    {
        $chartHtml = '';

        if ($charts !== []) {
            $chartHtml .= '<h2 style="margin:0 0 6px;font-size:12px">Grafik Ringkasan</h2>';
            $chartHtml .= '<table width="100%" cellpadding="0" cellspacing="0">';
            $items = array_values($charts);
            $total = count($items);

            for ($i = 0; $i < $total; $i += 2) {
                $chartHtml .= '<tr>';

                for ($j = 0; $j < 2; $j++) {
                    $chartHtml .= '<td width="50%" valign="top" style="padding:4px">';

                    if (isset($items[$i + $j])) {
                        $c = $items[$i + $j];
                        $chartHtml .= '<div style="font-size:10px;font-weight:bold;color:#334155">' . htmlspecialchars((string) $c['label']) . '</div>'
                            . '<img src="' . htmlspecialchars((string) $c['file']) . '" style="width:100%;max-width:340px">';
                    }

                    $chartHtml .= '</td>';
                }

                $chartHtml .= '</tr>';
            }

            $chartHtml .= '</table>';
            $chartHtml .= '<pagebreak />';
        }

        $body = $chartHtml . view('pdf/table', ['headers' => $headers, 'rows' => $rows]);

        $content = (new PdfExport((array) setting()))->render($title, $body, $filename, 'L', $meta);

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
            ->setBody($content);
    }

    private function exportAnakExcel(string $title, array $headers, array $rows, array $charts, array $meta, string $filename)
    {
        $content = (new ExcelExport())->build($title, $headers, $rows, $meta, $charts);

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($content);
    }

    private function statistikPdf()
    {
        $stats = (new AnakModel())->statistics();

        $body = view('pdf/statistik', ['stats' => $stats]);

        $content = (new PdfExport((array) setting()))->render('Laporan Statistik Anak', $body, 'laporan_statistik_' . date('Ymd_His') . '.pdf', 'P', [
            'Total anak' => $stats['total'],
            'Anak aktif' => $stats['aktif'],
            'Laki-laki'  => $stats['laki'],
            'Perempuan'  => $stats['perempuan'],
        ]);

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="laporan_statistik.pdf"')
            ->setBody($content);
    }

    private function reportData(string $jenis, array $filters): array
    {
        return match ($jenis) {
            'pengurus'  => ['items' => (new PengurusModel())->orderBy('nama_pengurus', 'ASC')->findAll()],
            'dokumen'   => ['items' => $this->dokumenRows($filters)],
            'keuangan'  => ['items' => $this->anakRows($filters), 'keuangan' => $this->keuanganSummary()],
            'statistik' => ['stats' => (new AnakModel())->statistics()],
            'eksternal' => ['items' => $this->anakRows($filters)],
            default     => ['items' => $this->anakRows($filters)],
        };
    }

    private function filters(): array
    {
        return [
            'search'        => trim((string) $this->request->getGet('q')),
            'status_anak'   => (string) $this->request->getGet('status_anak'),
            'jenis_kelamin' => (string) $this->request->getGet('jenis_kelamin'),
            'pendidikan'    => (string) $this->request->getGet('pendidikan'),
        ];
    }

    private function anakRows(array $filters): array
    {
        $items = (new AnakModel())->getList($filters);
        $rows  = [];

        foreach ($items as $i => $item) {
            $rows[] = [
                $i + 1,
                $item['no_registrasi'] ?: '-',
                $item['nama_anak'],
                $item['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan',
                trim(($item['tempat_lahir'] ?: '-') . ', ' . tanggal_id($item['tanggal_lahir'])),
                umur($item['tanggal_lahir'] ?? null),
                $item['kategori'] ?: '-',
                $item['pendidikan'] ?: '-',
                $item['status_anak'] ?: '-',
                $item['nama_kabupaten'] ?: ($item['nama_provinsi'] ?: '-'),
            ];
        }

        return $rows;
    }

    private function pengurusRows(): array
    {
        $rows = [];

        foreach ((new PengurusModel())->orderBy('nama_pengurus', 'ASC')->findAll() as $i => $item) {
            $rows[] = [$i + 1, $item['nama_pengurus'], $item['jabatan'] ?: '-', $item['no_hp'] ?: '-', $item['email'] ?: '-'];
        }

        return $rows;
    }

    private function dokumenRows(array $filters): array
    {
        $rows = [];

        foreach ((new AnakModel())->getList($filters) as $i => $item) {
            $rows[] = [
                $i + 1,
                $item['nama_anak'],
                $item['no_kk'] ?: '-',
                $item['file_kk'] ? 'Lengkap' : 'Belum ada',
                $item['file_akta'] ? 'Lengkap' : 'Belum ada',
                $item['file_pendukung'] ? 'Lengkap' : 'Belum ada',
            ];
        }

        return $rows;
    }

    private function keuanganSummary(): array
    {
        $items = (new AnakModel())->findAll();
        $total = 0.0;
        $perPendidikan = [];

        foreach ($items as $item) {
            $spp = (float) ($item['biaya_spp'] ?? 0);
            $total += $spp;
            $pend = $item['pendidikan'] ?: 'Lainnya';
            $perPendidikan[$pend] = ($perPendidikan[$pend] ?? 0) + $spp;
        }

        arsort($perPendidikan);

        return ['total' => $total, 'per_pendidikan' => $perPendidikan, 'jumlah_anak' => count($items)];
    }

    private function karakterRows(): array
    {
        $rows = [];

        foreach ((new CharacterAssessmentModel())->getReportRows(['id_anak' => (int) $this->request->getGet('id_anak') ?: null]) as $i => $item) {
            $rows[] = [
                $i + 1,
                $item['nama_anak'],
                tanggal_id($item['assessment_date']),
                $item['aspect_name'],
                $item['indicator_name'],
                $item['score'],
                $item['assessor_name'] ?: '-',
            ];
        }

        return $rows;
    }

    private function pdfResponse(string $filename, string $title, array $headers, array $rows, array $meta = [], string $orientation = 'P')
    {
        $body    = view('pdf/table', ['headers' => $headers, 'rows' => $rows]);
        $content = (new PdfExport((array) setting()))->render($title, $body, $filename, $orientation, $meta);

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
            ->setBody($content);
    }

    private function excelResponse(string $filename, string $title, array $headers, array $rows, array $meta = [])
    {
        $content = (new ExcelExport())->build($title, $headers, $rows, $meta);

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($content);
    }
}
