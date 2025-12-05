<?php

namespace App\Imports;

use App\Models\PatientDatabase;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class DatabaseImport implements WithMultipleSheets 
{
    public function sheets(): array
    {
        return [
            // Baca sheet index 0 (Sheet Pertama)
            0 => new FirstSheetImport(), 
        ];
    }
}

class FirstSheetImport implements ToModel, WithStartRow, SkipsEmptyRows, WithCalculatedFormulas
{
    public function startRow(): int
    {
        return 2; 
    }

    // Helper Aman: Ambil data berdasarkan index, return null jika index tidak ada
    private function getValue($row, $index)
    {
        if (!isset($row[$index])) {
            return null;
        }
        return $this->cleanText($row[$index]);
    }

    private function cleanText($value)
    {
        if (is_null($value) || $value === '') return null;
        $str = trim((string)$value);
        if (in_array($str, ['0', '-', '#N/A', '#REF!', '#VALUE!', '#NAME?'])) {
            return null;
        }
        return $str;
    }

    private function cleanNumber($value)
    {
        return is_numeric($value) ? (int)$value : null;
    }

    public function model(array $row)
    {
        // 1. Ambil No RM (Index 0)
        // Gunakan helper getValue agar tidak error "Undefined array key"
        $noRm = $this->getValue($row, 0);

        if (empty($noRm) || $noRm === 'No. RM') {
            return null;
        }

        return new PatientDatabase([
            'no_rm'           => $noRm,
            'name_of_patient' => $this->getValue($row, 1),
            'diagnosis'       => $this->getValue($row, 2),
            
            // Age (Index 3) - butuh perlakuan khusus (angka)
            'age'             => isset($row[3]) ? $this->cleanNumber($row[3]) : null,
            
            'overseas_hospital' => $this->getValue($row, 4),

            // Radiation Oncology (RO)
            'source_information_ro' => $this->getValue($row, 5),
            'new_ro_clinic'         => $this->getValue($row, 6),
            'new_rt'                => $this->getValue($row, 7),
            'reason'                => $this->getValue($row, 8),

            // Medical Oncology (MO)
            'source_information_mo' => $this->getValue($row, 9),
            'new_mo_clinic'         => $this->getValue($row, 10),
            'new_chemo'             => $this->getValue($row, 11),
            'reason2'               => $this->getValue($row, 12),

            // Breast (BO)
            'source_information_bo' => $this->getValue($row, 13),
            'new_bo_clinic'         => $this->getValue($row, 14),

            // Gyne (GO)
            'source_information_go' => $this->getValue($row, 15),
            'new_go_clinic'         => $this->getValue($row, 16),

            // Pulmo (PO)
            'source_information_po' => $this->getValue($row, 17),
            'new_po_clinic'         => $this->getValue($row, 18),

            // Pediatric (AO)
            'source_information_ao' => $this->getValue($row, 19),
            'new_ao_clinic'         => $this->getValue($row, 20),

            // Notes
            'activities_notes'  => $this->getValue($row, 21),
            'activities_notes2' => $this->getValue($row, 22),
            'activities_notes3' => $this->getValue($row, 23),
            'activities_notes4' => $this->getValue($row, 24),
            'activities_notes5' => $this->getValue($row, 25),
        ]);
    }
}