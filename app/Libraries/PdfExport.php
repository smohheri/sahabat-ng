<?php

namespace App\Libraries;

use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class PdfExport
{
    private array $settings;

    public function __construct(?array $settings = null)
    {
        $this->settings = $settings ?? (array) setting();
    }

    public function render(string $title, string $body, string $filename, string $orientation = 'P', array $meta = []): string
    {
        $tmp = WRITEPATH . 'cache/mpdf';

        if (! is_dir($tmp)) {
            mkdir($tmp, 0775, true);
        }

        $mpdf = new Mpdf([
            'mode'          => 'utf-8',
            'format'        => 'A4',
            'orientation'   => $orientation,
            'tempDir'       => $tmp,
            'margin_left'   => 14,
            'margin_right'  => 14,
            'margin_top'    => 22,
            'margin_bottom' => 18,
        ]);

        $school  = htmlspecialchars((string) ($this->settings['nama_lksa'] ?? 'LKSA Harapan Bangsa'));
        $address = htmlspecialchars((string) ($this->settings['alamat'] ?? ''));

        $header = '<div style="text-align:center;border-bottom:2px solid #1e293b;padding-bottom:6px;margin-bottom:14px">'
            . '<div style="font-size:15px;font-weight:bold">' . $school . '</div>'
            . '<div style="font-size:10px;color:#475569">' . $address . '</div>'
            . '</div>';

        $metaHtml = '';

        foreach ($meta as $label => $value) {
            $metaHtml .= '<tr><td style="width:130px;color:#475569">' . htmlspecialchars((string) $label) . '</td><td>: ' . htmlspecialchars((string) $value) . '</td></tr>';
        }

        $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><style>
            body{font-family:dejavusans,sans-serif;font-size:10.5px;color:#0f172a}
            h1{font-size:16px;margin:0 0 10px}
            h2{font-size:12px;margin:16px 0 6px}
            table.data{width:100%;border-collapse:collapse;margin-top:8px}
            table.data th{background:#e2e8f0;border:1px solid #94a3b8;padding:5px;text-align:left;font-size:9.5px}
            table.data td{border:1px solid #cbd5e1;padding:5px;font-size:9.5px;vertical-align:top}
            .muted{color:#64748b}
            .badge{display:inline-block;padding:2px 6px;border-radius:4px;background:#e2e8f0}
        </style></head><body>'
            . $header
            . '<h1>' . htmlspecialchars($title) . '</h1>'
            . ($metaHtml !== '' ? '<table style="margin-bottom:8px">' . $metaHtml . '</table>' : '')
            . $body
            . '</body></html>';

        $mpdf->WriteHTML($html);
        $mpdf->SetTitle($title);

        $footer = '<table width="100%" style="font-size:8px;color:#64748b;border-top:1px solid #e2e8f0;padding-top:4px;font-family:dejavusans,sans-serif">'
            . '<tr>'
            . '<td width="62%" style="text-align:left">Dicetak dari ' . $school . ' pada ' . date('d/m/Y H:i') . '</td>'
            . '<td width="38%" style="text-align:right">Halaman {PAGENO} dari {nbpg}</td>'
            . '</tr></table>';

        $mpdf->setHTMLFooter($footer);

        return $mpdf->Output($filename, Destination::STRING_RETURN);
    }
}
