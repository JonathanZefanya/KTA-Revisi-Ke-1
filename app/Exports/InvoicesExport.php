<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class InvoicesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $query;

    public function __construct($query = null)
    {
        $this->query = $query;
    }

    public function collection()
    {
        if ($this->query) {
            return $this->query->with(['user', 'user.companies'])->get();
        }
        return Invoice::with(['user', 'user.companies'])->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Invoice ID',
            'Nomor Invoice',
            'Tipe',
            'Nama User',
            'Email User',
            'Nama Perusahaan',
            'Jumlah (Rp)',
            'Status',
            'Bank Tujuan',
            'Bukti Transfer',
            'Tanggal Dibuat',
            'Tanggal Jatuh Tempo',
            'Tanggal Dibayar',
            'Catatan Admin',
        ];
    }

    public function map($invoice): array
    {
        $company = $invoice->user->companies->first();
        
        // Format status
        $statusMap = [
            'pending' => 'Pending',
            'paid' => 'Dibayar',
            'verified' => 'Terverifikasi',
            'cancelled' => 'Dibatalkan',
            'expired' => 'Kedaluwarsa',
        ];
        
        $status = $statusMap[$invoice->status] ?? $invoice->status ?? 'N/A';
        
        // Format tipe
        $typeMap = [
            'registration' => 'Registrasi',
            'renewal' => 'Perpanjangan',
        ];
        
        $type = $typeMap[$invoice->type] ?? $invoice->type ?? 'N/A';

        return [
            $invoice->id ?? 'N/A',
            $invoice->invoice_number ?? 'N/A',
            $type,
            $invoice->user->name ?? 'N/A',
            $invoice->user->email ?? 'N/A',
            $company->name ?? 'N/A',
            $invoice->amount ? 'Rp ' . number_format($invoice->amount, 0, ',', '.') : 'N/A',
            $status,
            $invoice->bank_name ?? 'N/A',
            $invoice->payment_proof_path ? 'Ada' : 'Belum',
            $invoice->created_at ? $invoice->created_at->format('d/m/Y H:i') : 'N/A',
            $invoice->due_date ? $invoice->due_date->format('d/m/Y') : 'N/A',
            $invoice->paid_at ? $invoice->paid_at->format('d/m/Y H:i') : 'N/A',
            $invoice->admin_notes ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style untuk header
        $sheet->getStyle('A1:N1')->applyFromArray([
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
        $sheet->getStyle('A1:N' . $highestRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ]);

        // Alignment untuk semua data
        $sheet->getStyle('A2:N' . $highestRow)->applyFromArray([
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
            'A' => 12,  // Invoice ID
            'B' => 20,  // Nomor Invoice
            'C' => 15,  // Tipe
            'D' => 25,  // Nama User
            'E' => 28,  // Email User
            'F' => 30,  // Nama Perusahaan
            'G' => 18,  // Jumlah
            'H' => 15,  // Status
            'I' => 18,  // Bank Tujuan
            'J' => 15,  // Bukti Transfer
            'K' => 18,  // Tanggal Dibuat
            'L' => 18,  // Tanggal Jatuh Tempo
            'M' => 18,  // Tanggal Dibayar
            'N' => 30,  // Catatan Admin
        ];
    }
}
