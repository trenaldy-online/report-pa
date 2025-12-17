<?php

namespace App\Imports;

use App\Models\ClinicVisit;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;

class KlinikImport implements ToModel, WithHeadingRow, SkipsEmptyRows, WithCustomCsvSettings
{
    // Settingan agar membaca Koma/Titik Koma dengan benar
    public function getCsvSettings(): array
    {
        return [
            'input_encoding' => 'UTF-8',
            'delimiter' => ',', 
        ];
    }

    // Helper: Ubah Angka Excel (45992) jadi Tanggal (2025-12-01)
    private function transformDate($value)
    {
        if (empty($value) || $value === 0 || $value === '0') return null;
        try {
            // Jika formatnya angka excel (seperti 45992)
            if (is_numeric($value)) {
                return Date::excelToDateTimeObject($value);
            }
            // Jika formatnya teks (2025-12-01)
            return Carbon::parse($value);
        } catch (\Throwable $e) {
            return null;
        }
    }

    // Helper: Jaga angka 0 agar tidak hilang
    private function cleanText($value)
    {
        if ($value === null) return null;
        if (trim((string)$value) === '') return null;
        return trim((string)$value);
    }

    public function model(array $row)
    {
        // Hapus dd($row) agar data bisa masuk ke database!
        
        // 1. Validasi
        if (empty($row['no_rm'])) return null;

        // 2. Olah Tanggal
        $tglKunjungan = $this->transformDate($row['tanggal_kunjungan'] ?? null);
        $tglLahir     = $this->transformDate($row['ttl'] ?? null);
        
        // 3. Simpan
        return new ClinicVisit([
            'no_rm'             => $row['no_rm'],
            'tanggal_kunjungan' => $tglKunjungan, // Hasil konversi 45992 -> Tanggal
            
            'nama_pasien'       => $this->cleanText($row['nama_pasien'] ?? null),
            'klinik'            => $this->cleanText($row['klinik'] ?? null),
            'dpjp'              => $this->cleanText($row['dpjp'] ?? null),
            
            // Perhatikan ini: Data catatan sudah ada di $row['catatan']
            'catatan'           => $this->cleanText($row['catatan'] ?? null),
            
            'ttl'               => $this->cleanText($row['ttl'] ?? null), 
            'jenis_kelamin'     => isset($row['jenis_kelamin']) ? strtoupper(substr($row['jenis_kelamin'], 0, 1)) : null,
            
            'alamat'            => $this->cleanText($row['alamat'] ?? null),
            'kota_area'         => $this->cleanText($row['kota_area'] ?? null),
            'alamat_domisili'   => $this->cleanText($row['alamat_domisili'] ?? null),
            'telepon'           => $this->cleanText($row['telepon'] ?? null),
            'diagnosis'         => $this->cleanText($row['diagnosis'] ?? null),
            'cancer_category'   => $this->cleanText($row['cancer_category'] ?? null),
            'stadium'           => $this->cleanText($row['stadium'] ?? null),
            'program'           => $this->cleanText($row['program'] ?? null),
            'dosis_fraksi'      => $this->cleanText($row['dosis_fraksi'] ?? null),
            'teknik_rt'         => $this->cleanText($row['teknik_rt'] ?? null),
            'surgery_type'      => $this->cleanText($row['surgery_type'] ?? null),
            'chemo_status'      => $this->cleanText($row['chemo_status'] ?? null),
            'surat_rujukan'     => $this->cleanText($row['surat_rujukan'] ?? null),
            'sumber_informasi'  => $this->cleanText($row['sumber_informasi'] ?? null),
            'hospital'          => $this->cleanText($row['hospital'] ?? null),
            'hospital2'         => $this->cleanText($row['hospital2'] ?? null),
            'updating_nurse'    => $this->cleanText($row['updating_nurse'] ?? null),
            'checker'           => $this->cleanText($row['checker'] ?? null),
            
            'new_patient'       => (!empty($row['new_patient']) && $row['new_patient'] == 1) ? 1 : 0,
            'usia'              => is_numeric($row['usia'] ?? null) ? $row['usia'] : null,
        ]);
    }
}