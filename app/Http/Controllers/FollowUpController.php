<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FollowUpController extends Controller
{
    public function index(Request $request)
    {
        // 1. Setup Filter Default (Hari Ini jika kosong)
        $startDate = $request->input('start_date', Carbon::now()->toDateString());
        $endDate   = $request->input('end_date', Carbon::now()->toDateString());
        $searchRm  = $request->input('no_rm');

        // 2. Query Builder (JOIN Table)
        $query = DB::table('clinic_visits')
            ->join('patient_databases', 'clinic_visits.no_rm', '=', 'patient_databases.no_rm')
            ->select(
                // Data Clinic Visits
                'clinic_visits.no_rm',
                'clinic_visits.tanggal_kunjungan',
                'clinic_visits.nama_pasien', // Nama saat visit
                'clinic_visits.catatan',
                'clinic_visits.jenis_kelamin',
                'clinic_visits.telepon',
                'clinic_visits.diagnosis as diagnosis_visit', // Alias biar gak bentrok

                // Data Patient Databases
                'patient_databases.name_of_patient', // Nama di master
                'patient_databases.diagnosis as diagnosis_master', // Alias
                'patient_databases.activities_notes',
                'patient_databases.activities_notes2',
                'patient_databases.activities_notes3'
            )
            ->whereBetween('clinic_visits.tanggal_kunjungan', [$startDate, $endDate]);

        // 3. Filter Tambahan: No RM (Jika diisi)
        if (!empty($searchRm)) {
            $query->where('clinic_visits.no_rm', 'like', '%' . $searchRm . '%');
        }

        // 4. Ambil Data (Pagination)
        $data = $query->orderBy('clinic_visits.tanggal_kunjungan', 'desc')->paginate(20);

        // Append query string agar saat pindah halaman filter tidak hilang
        $data->appends($request->all());

        return view('followup.index', compact('data', 'startDate', 'endDate', 'searchRm'));
    }
}