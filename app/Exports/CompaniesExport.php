<?php

namespace App\Exports;

use App\Models\Company;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class CompaniesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $query;

    public function __construct($query = null)
    {
        $this->query = $query;
    }

    public function collection()
    {
        if ($this->query) {
            return $this->query->with(['users'])->get();
        }
        return Company::with(['users'])->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Badan Usaha',
            'Bentuk',
            'Jenis',
            'Kualifikasi',
            'Penanggung Jawab',
            'NPWP',
            'Email',
            'Telepon',
            'Alamat',
            'Alamat Lokasi Asphalt Mixing Plant',
            'Alamat Lokasi Concrete Batching Plant',
            'Provinsi',
            'Kota/Kabupaten',
            'Kode Pos',
            'Jumlah User Terkait',
            'Tanggal Dibuat',
        ];
    }

    public function map($company): array
    {
        return [
            $company->id ?? 'N/A',
            $company->name ?? 'N/A',
            $company->bentuk ?? 'N/A',
            $company->jenis ?? 'N/A',
            $company->kualifikasi ?? 'N/A',
            $company->penanggung_jawab ?? 'N/A',
            $company->npwp ?? 'N/A',
            $company->email ?? 'N/A',
            $company->phone ?? 'N/A',
            $company->address ?? 'N/A',
            $company->asphalt_mixing_plant_address ?? 'N/A',
            $company->concrete_batching_plant_address ?? 'N/A',
            $company->province_name ?? 'N/A',
            $company->city_name ?? 'N/A',
            $company->postal_code ?? 'N/A',
            $company->users->count(),
            $company->created_at ? $company->created_at->format('d/m/Y H:i') : 'N/A',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style untuk header
        $sheet->getStyle('A1:Q1')->applyFromArray([
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

        // Border untuk semua data
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('A1:Q' . $highestRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ]);

        // Alignment untuk semua data
        $sheet->getStyle('A2:Q' . $highestRow)->applyFromArray([
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
            'A' => 8,   // ID
            'B' => 35,  // Nama Badan Usaha
            'C' => 12,  // Bentuk
            'D' => 12,  // Jenis
            'E' => 25,  // Kualifikasi
            'F' => 30,  // Penanggung Jawab
            'G' => 20,  // NPWP
            'H' => 28,  // Email
            'I' => 18,  // Telepon
            'J' => 40,  // Alamat
            'K' => 40,  // Alamat AMP
            'L' => 40,  // Alamat CBP
            'M' => 20,  // Provinsi
            'N' => 20,  // Kota/Kabupaten
            'O' => 12,  // Kode Pos
            'P' => 18,  // Jumlah User
            'Q' => 18,  // Tanggal Dibuat
        ];
    }
}
