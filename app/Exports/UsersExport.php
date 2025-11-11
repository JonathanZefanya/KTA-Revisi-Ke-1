<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class UsersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $query;

    public function __construct($query = null)
    {
        $this->query = $query;
    }

    public function collection()
    {
        if ($this->query) {
            return $this->query->with(['companies'])->get();
        }
        return User::with(['companies'])->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Provinsi',
            'Nomor Keanggotaan',
            'Nama Badan Usaha',
            'Nama Pimpinan',
            'Alamat Badan Usaha',
            'Alamat Lokasi Asphalt Mixing Plant',
            'Alamat Lokasi Concrete Batching Plant',
            'Nomor Telepon',
            'Email',
            'NPWP',
            'Bentuk BU',
            'Jenis BU',
            'Kualifikasi',
            'No. KTA',
            'Tanggal Terbit',
            'Tanggal Berlaku',
            'Status KTA',
        ];
    }

    public function map($user): array
    {
        $company = $user->companies->first();
        
        // Tentukan status KTA
        $status = 'N/A';
        if ($user->membership_card_expires_at) {
            $status = now()->lte($user->membership_card_expires_at) ? 'Berlaku' : 'Expired';
        }

        return [
            $company->province_name ?? 'N/A',
            $user->id ?? 'N/A',
            $company->name ?? 'N/A',
            $company->penanggung_jawab ?? $user->name ?? 'N/A',
            $company->address ?? 'N/A',
            $company->asphalt_mixing_plant_address ?? 'N/A',
            $company->concrete_batching_plant_address ?? 'N/A',
            $company->phone ?? $user->phone ?? 'N/A',
            $user->email ?? 'N/A',
            $company->npwp ?? 'N/A',
            $company->bentuk ?? 'N/A',
            $company->jenis ?? 'N/A',
            $company->kualifikasi ?? 'N/A',
            $user->membership_card_number ?? 'N/A',
            $user->membership_card_issued_at ? $user->membership_card_issued_at->format('d/m/Y') : 'N/A',
            $user->membership_card_expires_at ? $user->membership_card_expires_at->format('d/m/Y') : 'N/A',
            $status,
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
            'A' => 20,  // Provinsi
            'B' => 18,  // Nomor Keanggotaan
            'C' => 35,  // Nama Badan Usaha
            'D' => 30,  // Nama Penanggung Jawab
            'E' => 40,  // Alamat Badan Usaha
            'F' => 40,  // Alamat AMP
            'G' => 40,  // Alamat CBP
            'H' => 18,  // Nomor Telepon
            'I' => 30,  // Email
            'J' => 20,  // NPWP
            'K' => 15,  // Bentuk BU
            'L' => 15,  // Jenis BU
            'M' => 25,  // Kualifikasi
            'N' => 20,  // No. KTA
            'O' => 15,  // Tanggal Terbit
            'P' => 15,  // Tanggal Berlaku
            'Q' => 12,  // Status KTA
        ];
    }
}
