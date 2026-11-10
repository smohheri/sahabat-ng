<?php

namespace App\Libraries;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelExport
{
    /**
     * Bangun file XLSX dan kembalikan isinya sebagai string biner.
     *
     * @param array<int, array<int, mixed>> $rows
     * @param array<int, array{label: string, file: string}> $charts
     */
    public function build(string $title, array $headers, array $rows, array $meta = [], array $charts = []): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle(mb_substr($title, 0, 31) ?: 'Laporan');

        $rowNumber = 1;

        foreach ($meta as $label => $value) {
            $sheet->setCellValue('A' . $rowNumber, $label);
            $sheet->setCellValue('B' . $rowNumber, $value);
            $sheet->mergeCells('B' . $rowNumber . ':D' . $rowNumber);
            $rowNumber++;
        }

        if ($meta !== []) {
            $rowNumber++;
        }

        // Grafik ringkasan (2 kolom, sebelum tabel).
        if ($charts !== []) {
            $gridTop = $rowNumber;
            $cols    = ['A', 'J'];
            $idx     = 0;

            foreach ($charts as $chart) {
                $col = $cols[$idx % 2];
                $r   = $gridTop + intdiv($idx, 2) * 13;

                $sheet->setCellValue($col . $r, (string) $chart['label']);
                $sheet->getStyle($col . $r)->getFont()->setBold(true)->setSize(11);

                $drawing = new Drawing();
                $drawing->setName((string) $chart['label']);
                $drawing->setDescription((string) $chart['label']);
                $drawing->setPath((string) $chart['file']);
                $drawing->setCoordinates($col . ($r + 1));
                $drawing->setWidth(320);
                $drawing->setHeight(84);
                $drawing->setWorksheet($sheet);

                $idx++;
            }

            $rowNumber = $gridTop + (int) ceil($idx / 2) * 13 + 1;
        }

        $headerRow = $rowNumber;

        // Tabel mulai di halaman berikutnya saat dicetak.
        if ($charts !== []) {
            $sheet->setBreak('A' . $headerRow, Worksheet::BREAK_ROW);
        }
        $col       = 'A';

        foreach ($headers as $header) {
            $sheet->setCellValue($col . $headerRow, $header);
            $col++;
        }

        $lastColumn = chr(ord('A') + count($headers) - 1);

        $sheet->getStyle('A' . $headerRow . ':' . $lastColumn . $headerRow)->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $rowNumber++;

        foreach ($rows as $row) {
            $col = 'A';

            foreach ($row as $value) {
                $sheet->setCellValue($col . $rowNumber, $value);
                $col++;
            }

            $rowNumber++;
        }

        $lastRow = max($rowNumber - 1, $headerRow);
        $sheet->getStyle('A' . $headerRow . ':' . $lastColumn . $lastRow)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
        ]);

        foreach (range('A', $lastColumn) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $sheet->freezePane('A' . ($headerRow + 1));

        $writer = new Xlsx($spreadsheet);

        ob_start();
        $writer->save('php://output');
        $content = (string) ob_get_clean();

        $spreadsheet->disconnectWorksheets();

        return $content;
    }
}
