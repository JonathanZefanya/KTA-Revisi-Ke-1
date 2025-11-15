<?php

namespace App\Imports;

use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Carbon\Carbon;

class CompaniesImport implements ToCollection, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    protected $errors = [];
    protected $imported = 0;
    protected $skipped = 0;

    public function batchSize(): int
    {
        return 100; 
    }

    public function chunkSize(): int
    {
        return 100; 
    }

    public function collection(Collection $rows)
    {
        // Disable model events untuk speed up
        Company::withoutEvents(function () use ($rows) {
            User::withoutEvents(function () use ($rows) {
                foreach ($rows as $index => $row) {
                    try {
                        // Skip jika nama badan usaha atau email kosong
                        // Email WAJIB untuk mencegah orphan companies
                        if (empty($row['nama_badan_usaha'])) {
                            $this->skipped++;
                            $this->errors[] = "Baris " . ($index + 2) . ": Nama Badan Usaha kosong";
                            continue;
                        }
                        
                        // if (empty($row['email'])) {
                        //     $this->skipped++;
                        //     $this->errors[] = "Baris " . ($index + 2) . ": Email kosong (Email wajib diisi)";
                        //     continue;
                        // }

                        // Pisahkan alamat dan kode pos (pattern: alamat - kodepos)
                        $alamat = $row['alamat_badan_usaha'] ?? $row['alamat'] ?? '';
                        $kodePos = null;
                        
                        if (!empty($alamat) && strpos($alamat, ' - ') !== false) {
                            $parts = explode(' - ', $alamat);
                            if (count($parts) >= 2) {
                                // Ambil part terakhir sebagai kode pos
                                $lastPart = trim(array_pop($parts));
                                
                                // Validasi kode pos (harus angka dan 5 digit)
                                if (is_numeric($lastPart) && strlen($lastPart) === 5) {
                                    $kodePos = $lastPart;
                                    $alamat = trim(implode(' - ', $parts));
                                } else {
                                    // Jika bukan kode pos valid, kembalikan
                                    $alamat = $row['alamat_badan_usaha'] ?? $row['alamat'] ?? '';
                                }
                            }
                        }
                        
                        // Jika ada kolom kode_pos terpisah, gunakan itu
                        if (isset($row['kode_pos']) && !empty($row['kode_pos'])) {
                            $kodePos = $row['kode_pos'];
                        }

                        // Data company
                        // Support multiple column names untuk Nama Pimpinan / Penanggung Jawab
                        $companyData = [
                            'bentuk' => $row['bentuk'] ?? null,
                            'jenis' => $row['jenis'] ?? $row['jenis_bu'] ?? null,
                            'kualifikasi' => $row['kualifikasi'] ?? null,
                            'penanggung_jawab' => $row['nama_pimpinan'] ?? $row['penanggung_jawab'] ?? $row['nama_penanggung_jawab'] ?? null,
                            'npwp' => $row['npwp'] ?? null,
                            'email' => $row['email'] ?? null,
                            'phone' => $row['telepon'] ?? $row['nomor_telepon'] ?? $row['no_telepon'] ?? null,
                            'address' => $alamat,
                            'asphalt_mixing_plant_address' => $row['alamat_lokasi_asphalt_mixing_plant'] ?? null,
                            'concrete_batching_plant_address' => $row['alamat_lokasi_concrete_batching_plant'] ?? null,
                            'province_name' => $row['provinsi'] ?? null,
                            'city_name' => $row['kotakabupaten'] ?? $row['kota'] ?? null,
                            'postal_code' => $kodePos,
                        ];

                        // Update or Create company (lebih efisien)
                        $company = Company::updateOrCreate(
                            ['name' => $row['nama_badan_usaha']],
                            $companyData
                        );

                        $this->imported++;

                        // Handle user creation/update jika ada email
                        if (!empty($row['email'])) {
                            $user = User::updateOrCreate(
                                ['email' => $row['email']],
                                [
                                    'name' => $row['nama_pimpinan'] ?? $row['penanggung_jawab'] ?? $row['nama_penanggung_jawab'] ?? $row['nama_badan_usaha'],
                                    'password' => Hash::make('password123'),
                                    'approved_at' => now(),
                                    'email_verified_at' => now(),
                                ]
                            );

                            // Attach user ke company jika belum
                            if (!$company->users()->where('user_id', $user->id)->exists()) {
                                $company->users()->attach($user->id);
                            }
                            
                            // Generate KTA jika belum ada
                            $this->generateKtaForUser($user, $row);
                        }

                    } catch (\Exception $e) {
                        $this->errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                        $this->skipped++;
                    }
                }
            });
        });
    }

    public function getImported()
    {
        return $this->imported;
    }

    public function getSkipped()
    {
        return $this->skipped;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    protected function generateKtaForUser(User $user, $row)
    {
        // Skip jika user sudah punya KTA
        if ($user->membership_card_number) {
            return;
        }

        // Parse tanggal dari Excel
        // Tanggal Registrasi Terakhir = Tanggal TERBIT KTA (membership_card_issued_at)
        // Masa Berlaku = Tanggal EXPIRED KTA (membership_card_expires_at)
        $tanggalTerbitKta = null;
        $tanggalExpiredKta = null;

        // Parse TANGGAL TERBIT KTA dari kolom "Tanggal Registrasi Terakhir"
        // Support multiple column names untuk backward compatibility
        $tanggalTerbitField = $row['tanggal_registrasi_terakhir'] 
            ?? $row['tanggal_terbit'] 
            ?? $row['tanggal_terbitdaftar'] 
            ?? $row['tanggal_daftar'] 
            ?? null;

        if (!empty($tanggalTerbitField)) {
            try {
                if (is_numeric($tanggalTerbitField)) {
                    // Excel date serial number (contoh: 45205 = 29 Oktober 2025)
                    $tanggalTerbitKta = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggalTerbitField));
                } else {
                    // Text date format (contoh: "29 Oktober 2025" atau "2025-10-29")
                    $tanggalTerbitKta = Carbon::parse($tanggalTerbitField);
                }
            } catch (\Exception $e) {
                // Jika gagal parse, gunakan tanggal hari ini
                $tanggalTerbitKta = now();
            }
        } else {
            $tanggalTerbitKta = now();
        }

        // Parse TANGGAL EXPIRED KTA dari kolom "Masa Berlaku"
        // Support multiple column names untuk backward compatibility
        $tanggalExpiredField = $row['masa_berlaku'] 
            ?? $row['tanggal_berlaku'] 
            ?? $row['tanggal_expired'] 
            ?? null;

        if (!empty($tanggalExpiredField)) {
            try {
                if (is_numeric($tanggalExpiredField)) {
                    // Excel date serial number (contoh: 45569 = 28 Oktober 2026)
                    $tanggalExpiredKta = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggalExpiredField));
                } else {
                    // Text date format (contoh: "28 Oktober 2026" atau "2026-10-28")
                    $tanggalExpiredKta = Carbon::parse($tanggalExpiredField);
                }
            } catch (\Exception $e) {
                // Jika gagal parse, set 2 tahun dari tanggal terbit
                $tanggalExpiredKta = $tanggalTerbitKta->copy()->addYears(2);
            }
        } else {
            // Default: 2 tahun dari tanggal terbit
            $tanggalExpiredKta = $tanggalTerbitKta->copy()->addYears(2);
        }

        // Ambil nomor KTA dari Excel jika ada
        // Support multiple column names untuk "Nomor Keanggotaan"
        $nomorKtaFromExcel = $row['nomor_keanggotaan'] 
            ?? $row['nomor_kta'] 
            ?? $row['no_kta'] 
            ?? $row['kta_number'] 
            ?? null;

        // Cek apakah nomor KTA dari Excel sudah digunakan
        if (!empty($nomorKtaFromExcel)) {
            // Bersihkan nomor KTA (hapus spasi, tab, newline)
            $nomorKtaFromExcel = trim($nomorKtaFromExcel);
            
            // Cek apakah nomor sudah ada di database
            $existingKta = User::where('membership_card_number', $nomorKtaFromExcel)
                ->where('id', '!=', $user->id)
                ->exists();
            
            if (!$existingKta) {
                // Gunakan nomor dari Excel
                $ktaNumber = $nomorKtaFromExcel;
            } else {
                // Jika sudah ada, generate nomor baru dengan suffix
                $ktaNumber = $this->generateUniqueKtaNumber($tanggalTerbitKta);
            }
        } else {
            // Generate nomor KTA otomatis jika tidak ada di Excel
            $ktaNumber = $this->generateUniqueKtaNumber($tanggalTerbitKta);
        }

        // Update user dengan data KTA
        // membership_card_issued_at = Tanggal Terbit KTA (dari Tanggal Registrasi Terakhir)
        // membership_card_expires_at = Tanggal Expired KTA (dari Masa Berlaku)
        $user->update([
            'membership_card_number' => $ktaNumber,
            'membership_card_issued_at' => $tanggalTerbitKta,
            'membership_card_expires_at' => $tanggalExpiredKta,
        ]);
    }

    /**
     * Generate nomor KTA unik dengan format sama seperti di Excel
     * Format dari Excel: 16/012/AB, 13/014/AB, dst
     * Format generate: {URUT}/{BULAN_TAHUN}/AB
     * Contoh: 01/0125/AB (urut 01, bulan 01, tahun 25, suffix AB)
     */
    protected function generateUniqueKtaNumber($tanggalTerbit)
    {
        // Parse format dari contoh: 16/012/AB
        // Pattern: {2digit urut}/{3digit kode}/AB
        // Kode 012 = bulan 01, tahun 2 (2012? atau 012?)
        
        // Ambil bulan dan 2 digit tahun terakhir dari tanggal terbit
        $month = $tanggalTerbit->format('m'); // 01-12
        $yearShort = $tanggalTerbit->format('y'); // 25 untuk 2025
        $kodeWaktu = $month . $yearShort; // Contoh: 0125 untuk Januari 2025
        
        // Cari nomor urut terakhir dengan format yang sama
        // Pattern: __/0125/AB (__ = 2 digit urut)
        $lastKta = User::where('membership_card_number', 'like', "%/{$kodeWaktu}/AB")
            ->orderByRaw('CAST(SUBSTRING_INDEX(membership_card_number, "/", 1) AS UNSIGNED) DESC')
            ->value('membership_card_number');
        
        if ($lastKta) {
            // Ambil nomor urut dari format: 16/012/AB -> 16
            $parts = explode('/', $lastKta);
            $nextNumber = intval($parts[0]) + 1;
        } else {
            // Mulai dari 01
            $nextNumber = 1;
        }
        
        // Format: 01/0125/AB, 02/0125/AB, dst
        return sprintf('%02d/%s/AB', $nextNumber, $kodeWaktu);
    }
}
