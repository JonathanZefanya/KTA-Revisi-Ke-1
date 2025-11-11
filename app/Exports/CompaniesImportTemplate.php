<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class CompaniesImportTemplate implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        // Return beberapa baris contoh
        return [
            [
                'Jawa Timur',
                '1',
                'PT. Contoh Perusahaan',
                'Budi Santoso',
                'Jl. Contoh No. 123, Kelurahan ABC, Kecamatan XYZ - 60251',
                '081234567890',
                'KTA-2024-0001',
                '01.234.567.8-901.000',
                'contoh@perusahaan.com',
                'Kecil / Spesialis 1',
                '29 Oktober 2025',
                '28 Oktober 2026',
                'Berlaku',
                'Jl. Lokasi AMP No. 456 (Opsional)',
                'Jl. Lokasi CBP No. 789 (Opsional)',
            ],
            [
                'Jawa Timur',
                '2',
                'CV. Contoh Lainnya',
                'Ani Wijaya',
                'Jl. Alamat Lain No. 88, Kec. Sawahan - 60251',
                '082345678901',
                'KTA-2024-0002',
                '02.345.678.9-012.000',
                'info@contoh.co.id',
                'BUJKA',
                '30 September 2025',
                '29 September 2026',
                'Berlaku',
                '',
                '',
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'Provinsi',
            'Nomor Keanggotaan',
            'Nama Badan Usaha',
            'Nama Pimpinan',
            'Alamat Badan Usaha',
            'No. Telepon',
            'KTA',
            'NPWP',
            'Email',
            'Kualifikasi',
            'Tanggal Registrasi Terakhir',
            'Masa Berlaku',
            'Status',
            'Alamat Lokasi Asphalt Mixing Plant',
            'Alamat Lokasi Concrete Batching Plant',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style untuk header
        $sheet->getStyle('A1:O1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0d479a'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Tinggi baris header
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Style untuk data contoh
        $sheet->getStyle('A2:O3')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP,
                'wrapText' => true,
            ],
        ]);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,  // Provinsi
            'B' => 18,  // Nomor Keanggotaan
            'C' => 35,  // Nama Badan Usaha
            'D' => 30,  // Nama Penanggung Jawab
            'E' => 45,  // Alamat Badan Usaha
            'F' => 18,  // No. Telepon
            'G' => 20,  // KTA
            'H' => 22,  // NPWP
            'I' => 28,  // Email
            'J' => 25,  // Kualifikasi
            'K' => 18,  // Tanggal Terbit/Daftar
            'L' => 18,  // Masa Berlaku
            'M' => 12,  // Status
            'N' => 40,  // Alamat AMP
            'O' => 40,  // Alamat CBP
        ];
    }
}
