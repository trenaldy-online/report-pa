<?php

namespace App\Imports;

use App\Models\ClinicVisitImport; // PENTING: Gunakan Model Sementara
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth; // <--- TAMBAHAN 1: Import Facade Auth

class KlinikImport implements ToModel, WithHeadingRow, SkipsEmptyRows, WithCustomCsvSettings
{
    /**
     * 1. Settingan CSV: Paksa baca Koma (,)
     */
    public function getCsvSettings(): array
    {
        return [
            'input_encoding' => 'UTF-8',
            'delimiter' => ',', 
        ];
    }

    /**
     * 2. Helper Tanggal (ANTI ERROR & Support Format Indo)
     */
    private function transformDate($value)
    {
        if (empty($value) || $value === 0 || $value === '0') return null;

        try {
            // A. Jika format Excel Serial Number (Contoh: 45992)
            if (is_numeric($value)) {
                return Date::excelToDateTimeObject($value);
            }

            $value = trim($value);

            // B. Jika Format Indonesia Garis Miring (14/08/1974)
            if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $value)) {
                return Carbon::createFromFormat('d/m/Y', $value);
            }

            // C. Jika Format Indonesia Strip (14-08-1974)
            if (preg_match('/^(\d{1,2})-(\d{1,2})-(\d{4})$/', $value)) {
                return Carbon::createFromFormat('d-m-Y', $value);
            }

            // D. Format Standar Internasional (YYYY-MM-DD)
            return Carbon::parse($value);

        } catch (\Throwable $e) {
            // JIKA ERROR: Jangan Crash! Kembalikan NULL saja.
            return null;
        }
    }

    /**
     * 3. Helper Text: Angka 0 jangan dianggap NULL
     */
    private function cleanText($value)
    {
        if ($value === null) return null;
        if (trim((string)$value) === '') return null;
        return trim((string)$value);
    }

    public function model(array $row)
    {
        // Validasi Minimal
        if (empty($row['no_rm'])) return null;

        // Olah Tanggal Kunjungan & TTL menggunakan Helper Baru
        $tglKunjungan = $this->transformDate($row['tanggal_kunjungan'] ?? null);
        $tglLahir     = $this->transformDate($row['ttl'] ?? null);

        // Olah Jenis Kelamin (Ambil huruf depan L/P)
        $jk = isset($row['jenis_kelamin']) ? strtoupper(substr(trim($row['jenis_kelamin']), 0, 1)) : null;

        // MASUKKAN KE TABEL SEMENTARA (ClinicVisitImport)
        return new ClinicVisitImport([
            'uploaded_by'       => Auth::id(), // <--- PERBAIKAN: Ganti auth()->id() dengan Auth::id()
            'no_rm'             => $row['no_rm'],
            'tanggal_kunjungan' => $tglKunjungan,
            
            // Kolom Text
            'nama_pasien'       => $this->cleanText($row['nama_pasien'] ?? null),
            'klinik'            => $this->cleanText($row['klinik'] ?? null),
            'dpjp'              => $this->cleanText($row['dpjp'] ?? null),
            'catatan'           => $this->cleanText($row['catatan'] ?? null),
            
            // Kolom Demografi
            'ttl'               => $tglLahir, // Hasil konversi tanggal yang aman
            'jenis_kelamin'     => $jk,
            'alamat'            => $this->cleanText($row['alamat'] ?? null),
            'kota_area'         => $this->cleanText($row['kota_area'] ?? null),
            'alamat_domisili'   => $this->cleanText($row['alamat_domisili'] ?? null),
            'telepon'           => $this->cleanText($row['telepon'] ?? null),
            'usia'              => is_numeric($row['usia'] ?? null) ? $row['usia'] : null,
            
            // Kolom Medis
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
            
            // Kolom Lain
            'hospital'          => $this->cleanText($row['hospital'] ?? null),
            'hospital2'         => $this->cleanText($row['hospital2'] ?? null),
            'updating_nurse'    => $this->cleanText($row['updating_nurse'] ?? null),
            'checker'           => $this->cleanText($row['checker'] ?? null),
            'new_patient'       => (!empty($row['new_patient']) && $row['new_patient'] == 1) ? 1 : 0,
        ]);
    }
}