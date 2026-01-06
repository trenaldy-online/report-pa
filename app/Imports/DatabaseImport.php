<?php

namespace App\Imports;

use App\Models\PatientDatabaseImport; // Model Sementara
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Illuminate\Support\Facades\Auth; // <--- TAMBAHAN PENTING 1

class DatabaseImport implements ToModel, WithHeadingRow, SkipsEmptyRows, WithCustomCsvSettings
{
    public function getCsvSettings(): array
    {
        return [
            'input_encoding' => 'UTF-8',
            'delimiter' => ',', 
        ];
    }

    /**
     * Helper: Bersihkan Teks
     */
    private function cleanText($value)
    {
        if ($value === null) return null;
        if (trim((string)$value) === '') return null;
        return trim((string)$value);
    }

    public function model(array $row)
    {
        // 1. Validasi No RM (Wajib Ada)
        if (empty($row['no_rm']) || $row['no_rm'] == 'No. RM') {
            return null;
        }

        // 2. Mapping Data (Sesuai Header Excel di atas)
        return new PatientDatabaseImport([
            'uploaded_by'           => Auth::id(), // <--- PERBAIKAN DI SINI (Ganti auth()->id())
            'no_rm'                 => $this->cleanText($row['no_rm']),
            'name_of_patient'       => $this->cleanText($row['name_of_patient']),
            'diagnosis'             => $this->cleanText($row['diagnosis']),
            
            // Pastikan Age masuk sebagai angka
            'age'                   => is_numeric($row['age']) ? $row['age'] : null,
            
            'overseas_hospital'     => $this->cleanText($row['overseas_hospital']),

            // Radiation Oncology (RO)
            'source_information_ro' => $this->cleanText($row['source_information_ro']),
            'new_ro_clinic'         => $this->cleanText($row['new_ro_clinic']),
            'new_rt'                => $this->cleanText($row['new_rt']),
            'reason'                => $this->cleanText($row['reason']),

            // Medical Oncology (MO)
            'source_information_mo' => $this->cleanText($row['source_information_mo']),
            'new_mo_clinic'         => $this->cleanText($row['new_mo_clinic']),
            'new_chemo'             => $this->cleanText($row['new_chemo']),
            'reason2'               => $this->cleanText($row['reason2']),

            // Breast (BO)
            'source_information_bo' => $this->cleanText($row['source_information_bo']),
            'new_bo_clinic'         => $this->cleanText($row['new_bo_clinic']),

            // Gyne (GO)
            'source_information_go' => $this->cleanText($row['source_information_go']),
            'new_go_clinic'         => $this->cleanText($row['new_go_clinic']),

            // Pulmo (PO)
            'source_information_po' => $this->cleanText($row['source_information_po']),
            'new_po_clinic'         => $this->cleanText($row['new_po_clinic']),

            // Pediatric (AO)
            'source_information_ao' => $this->cleanText($row['source_information_ao']),
            'new_ao_clinic'         => $this->cleanText($row['new_ao_clinic']),

            // Notes (Aktivitas)
            'activities_notes'      => $this->cleanText($row['activities_notes']),
            'activities_notes2'     => $this->cleanText($row['activities_notes2']),
            'activities_notes3'     => $this->cleanText($row['activities_notes3']),
            'activities_notes4'     => $this->cleanText($row['activities_notes4']),
            'activities_notes5'     => $this->cleanText($row['activities_notes5']),
        ]);
    }
}