<?php

namespace App\Imports;

use App\Models\RadioterapiConverted;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;

class RadioterapiImport implements ToModel, WithHeadingRow, SkipsEmptyRows
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

        // Kita pakai updateOrCreate.
        // Artinya: Jika No RM sudah ada di daftar Converted, update datanya.
        // Jika belum ada, buat baru.
        return RadioterapiConverted::updateOrCreate(
            ['no_rm' => $row['no_rm']], // Kunci pencarian
            [
                'date_converted' => $this->transformDate($row['date_converted'] ?? null),
                'nama_pasien'    => $row['nama_pasien'] ?? null,
                'diagnosis'      => $row['diagnosis'] ?? null,
                'cancer_type'    => $row['cancer_type'] ?? null,
                'dpjp'           => $row['dpjp'] ?? null,
                'rt_treatment'   => $row['rt_treatment'] ?? null,
            ]
        );
    }
}