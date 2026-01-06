<?php

namespace App\Imports;

use App\Models\KemoterapiConverted;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;

class KemoterapiImport implements ToModel, WithHeadingRow, SkipsEmptyRows
{
    private function transformDate($value)
    {
        if (empty($value)) return null;
        try {
            if (is_numeric($value)) return Date::excelToDateTimeObject($value);
            return Carbon::parse($value);
        } catch (\Throwable $e) { return null; }
    }

    public function model(array $row)
    {
        if (empty($row['no_rm'])) return null;

        return KemoterapiConverted::updateOrCreate(
            ['no_rm' => $row['no_rm']], 
            [
                'date_converted' => $this->transformDate($row['date_converted'] ?? null),
                'nama_pasien'    => $row['nama_pasien'] ?? null,
                'inpatient'      => $row['inpatient'] ?? null,
                'new_kemo'       => $row['new_kemo'] ?? null,
                'status'         => $row['status'] ?? null,
                'telephone'      => $row['telephone'] ?? null,
                'diagnosis'      => $row['diagnosis'] ?? null,
                'cancer_type'    => $row['cancer_type'] ?? null,
                'dpjp'           => $row['dpjp'] ?? null,
            ]
        );
    }
}