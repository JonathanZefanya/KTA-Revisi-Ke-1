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
                        // Skip jika nama badan usaha kosong
                        if (empty($row['nama_badan_usaha'])) {
                            $this->skipped++;
                            continue;
                        }

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
                        $companyData = [
                            'bentuk' => $row['bentuk'] ?? null,
                            'jenis' => $row['jenis'] ?? $row['jenis_bu'] ?? null,
                            'kualifikasi' => $row['kualifikasi'] ?? null,
                            'penanggung_jawab' => $row['penanggung_jawab'] ?? $row['nama_penanggung_jawab'] ?? null,
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
                                    'name' => $row['penanggung_jawab'] ?? $row['nama_penanggung_jawab'] ?? $row['nama_badan_usaha'],
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

        // Parse tanggal dari Excel jika ada
        $tanggalTerbit = null;
        $tanggalBerlaku = null;

        // Coba parse tanggal terbit
        if (isset($row['tanggal_terbit']) && !empty($row['tanggal_terbit'])) {
            try {
                if (is_numeric($row['tanggal_terbit'])) {
                    // Excel date serial number
                    $tanggalTerbit = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal_terbit']));
                } else {
                    $tanggalTerbit = Carbon::parse($row['tanggal_terbit']);
                }
            } catch (\Exception $e) {
                // Jika gagal parse, gunakan tanggal hari ini
                $tanggalTerbit = now();
            }
        } else {
            $tanggalTerbit = now();
        }

        // Coba parse tanggal berlaku
        if (isset($row['tanggal_berlaku']) && !empty($row['tanggal_berlaku'])) {
            try {
                if (is_numeric($row['tanggal_berlaku'])) {
                    // Excel date serial number
                    $tanggalBerlaku = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal_berlaku']));
                } else {
                    $tanggalBerlaku = Carbon::parse($row['tanggal_berlaku']);
                }
            } catch (\Exception $e) {
                // Jika gagal parse, set 2 tahun dari tanggal terbit
                $tanggalBerlaku = $tanggalTerbit->copy()->addYears(2);
            }
        } else {
            // Default: 2 tahun dari tanggal terbit
            $tanggalBerlaku = $tanggalTerbit->copy()->addYears(2);
        }

        // Generate nomor KTA
        // Format: KTA-{TAHUN}-{URUT}
        $year = $tanggalTerbit->format('Y');
        $lastNumber = User::where('membership_card_number', 'like', "KTA-{$year}-%")
            ->orderByRaw('CAST(SUBSTRING_INDEX(membership_card_number, "-", -1) AS UNSIGNED) DESC')
            ->value('membership_card_number');
        
        if ($lastNumber) {
            $parts = explode('-', $lastNumber);
            $nextNumber = intval(end($parts)) + 1;
        } else {
            $nextNumber = 1;
        }
        
        $ktaNumber = sprintf('KTA-%s-%04d', $year, $nextNumber);

        // Update user dengan data KTA
        $user->update([
            'membership_card_number' => $ktaNumber,
            'membership_card_issued_at' => $tanggalTerbit,
            'membership_card_expires_at' => $tanggalBerlaku,
        ]);
    }
}
