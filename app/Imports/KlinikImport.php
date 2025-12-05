<?php

namespace App\Imports;

use App\Models\ClinicVisit;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;

class KlinikImport implements ToModel, WithHeadingRow, SkipsEmptyRows
{
    /**
     * Helper untuk membersihkan tanggal
     */
    private function transformDate($value)
    {
        if (empty($value) || $value === 0 || $value === '0') {
            return null;
        }
        try {
            if (is_numeric($value)) {
                return Date::excelToDateTimeObject($value);
            }
            return Carbon::parse($value);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Helper untuk membersihkan teks (jika 0 atau kosong, jadikan null)
     */
    private function cleanText($value)
    {
        if (empty($value) || $value === 0 || $value === '0') {
            return null;
        }
        return trim($value);
    }

    public function model(array $row)
    {
        // 1. Cek No RM
        if (!isset($row['no_rm']) || empty($row['no_rm'])) {
            return null;
        }

        // 2. Bersihkan Tanggal
        $tglKunjungan = $this->transformDate($row['tanggal'] ?? null);

        // 3. Hitung Umur
        $calculatedAge = null;
        $tglLahirString = $row['ttl'] ?? null;
        if (!empty($tglLahirString) && $tglLahirString !== 0 && $tglLahirString !== '0') {
            $tglLahirObj = $this->transformDate($tglLahirString);
            if ($tglLahirObj) {
                $calculatedAge = Carbon::instance($tglLahirObj)->age;
            }
        }
        // Fallback umur
        if ($calculatedAge === null && isset($row['usia']) && is_numeric($row['usia'])) {
            $calculatedAge = $row['usia'];
        }

        // 4. BERSIHKAN JENIS KELAMIN (Solusi Error Anda)
        $jk = isset($row['jenis_kelamin']) ? strtoupper(trim($row['jenis_kelamin'])) : null;
        if ($jk !== 'L' && $jk !== 'P') {
            $jk = null; // Jika isinya '0', '', atau ngawur, jadikan NULL
        }

        // 5. Simpan ke Database
        return new ClinicVisit([
            'no_rm'             => $row['no_rm'],
            'tanggal_kunjungan' => $tglKunjungan,
            
            // Gunakan cleanText agar angka 0 tidak masuk ke kolom teks
            'nama_pasien'       => $this->cleanText($row['nama_pasien'] ?? null),
            'klinik'            => $this->cleanText($row['klinik'] ?? null),
            'dpjp'              => $this->cleanText($row['dpjp'] ?? null),
            
            'new_patient'       => !empty($row['new_patient']) ? 1 : 0,
            
            'diagnosis'         => $this->cleanText($row['diagnosis'] ?? null),
            'cancer_category'   => $this->cleanText($row['cancer_category'] ?? null),
            'stadium'           => $this->cleanText($row['stadium_stage'] ?? null),
            'program'           => $this->cleanText($row['program'] ?? null),
            'dosis_fraksi'      => $this->cleanText($row['dosis_fraksi'] ?? null),
            'teknik_rt'         => $this->cleanText($row['teknik_rt'] ?? null),
            'surgery_type'      => $this->cleanText($row['surgery_type'] ?? null),
            'chemo_status'      => $this->cleanText($row['chemo_status'] ?? null),
            'sumber_informasi'  => $this->cleanText($row['sumber_informasi'] ?? null),
            
            // Data Demografi
            'ttl'               => $tglLahirString, 
            'jenis_kelamin'     => $jk, // <--- Ini yang sudah diperbaiki
            'alamat'            => $this->cleanText($row['alamat_address'] ?? null),
            'kota_area'         => $this->cleanText($row['kota_area'] ?? null),
            'alamat_domisili'   => $this->cleanText($row['alamat_domisili'] ?? null),
            'telepon'           => $this->cleanText($row['telepon'] ?? null),
            
            'usia'              => $calculatedAge, 
            
            // Admin Info
            'catatan'           => $this->cleanText($row['catatan_remarks'] ?? null),
            'updating_nurse'    => $this->cleanText($row['updating_nurse'] ?? null),
            'hospital'          => $this->cleanText($row['hospital'] ?? null),
            'hospital2'         => $this->cleanText($row['hospital2'] ?? null),
            'checker'           => $this->cleanText($row['checker'] ?? null),
        ]);
    }
}