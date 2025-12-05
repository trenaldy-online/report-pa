<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ClinicVisit;
use App\Models\PatientDatabase;
use Carbon\Carbon;

class DashboardController extends Controller
{
public function index(Request $request)
    {
        $startDate = session('dashboard_start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate   = session('dashboard_end_date', Carbon::now()->endOfMonth()->toDateString());

        // 1. TOTAL DATABASE & PASIEN BARU
        $totalPasienDB = PatientDatabase::count();
        $pasienBaru = ClinicVisit::where('new_patient', true)
                                ->whereBetween('tanggal_kunjungan', [$startDate, $endDate])
                                ->count();

        // ==========================================
        // LOGIKA STATUS KEMO (MO) - REVISI
        // ==========================================
        $baseMoQuery = DB::table('clinic_visits')
            ->join('patient_databases', 'clinic_visits.no_rm', '=', 'patient_databases.no_rm')
            ->where('clinic_visits.klinik', 'MO')
            ->whereBetween('clinic_visits.tanggal_kunjungan', [$startDate, $endDate])
            ->where(function($q) {
                $q->whereNotNull('patient_databases.new_mo_clinic')
                  ->where('patient_databases.new_mo_clinic', '!=', 'No')
                  ->where('patient_databases.new_mo_clinic', '!=', '0');
            });

        // A. CONVERTED (Angka)
        $convertedChemo = (clone $baseMoQuery)
            ->whereRaw("patient_databases.new_chemo REGEXP '^[0-9]+$'")
            ->distinct('clinic_visits.no_rm')->count('clinic_visits.no_rm');

        // B. NOT CONVERTED (Cari teks "Not Converted" saja)
        $notConvertedChemo = (clone $baseMoQuery)
            ->where('patient_databases.new_chemo', 'LIKE', '%Not Converted%')
            ->distinct('clinic_visits.no_rm')->count('clinic_visits.no_rm');


        // ==========================================
        // LOGIKA STATUS RADIOTERAPI (RO) - REVISI
        // ==========================================
        $baseRoQuery = DB::table('clinic_visits')
            ->join('patient_databases', 'clinic_visits.no_rm', '=', 'patient_databases.no_rm')
            ->where('clinic_visits.klinik', 'RO')
            ->whereBetween('clinic_visits.tanggal_kunjungan', [$startDate, $endDate])
            ->where(function($q) {
                $q->whereNotNull('patient_databases.new_ro_clinic')
                  ->where('patient_databases.new_ro_clinic', '!=', 'No')
                  ->where('patient_databases.new_ro_clinic', '!=', '0');
            });

        // A. CONVERTED (Angka)
        $convertedRt = (clone $baseRoQuery)
            ->whereRaw("patient_databases.new_rt REGEXP '^[0-9]+$'")
            ->distinct('clinic_visits.no_rm')->count('clinic_visits.no_rm');

        // B. NOT CONVERTED (Cari teks "Not Converted" saja)
        $notConvertedRt = (clone $baseRoQuery)
            ->where('patient_databases.new_rt', 'LIKE', '%Not Converted%')
            ->distinct('clinic_visits.no_rm')->count('clinic_visits.no_rm');


        // --- DATA LAINNYA (Summary Klinik & Marketing) ---
        // (Bagian ini tidak berubah, sama seperti sebelumnya)
        $rawKlinikData = DB::table('clinic_visits')
            ->leftJoin('patient_databases', 'clinic_visits.no_rm', '=', 'patient_databases.no_rm')
            ->whereBetween('clinic_visits.tanggal_kunjungan', [$startDate, $endDate])
            ->select('clinic_visits.klinik', 'patient_databases.source_information_mo', 'patient_databases.source_information_ro', 'patient_databases.source_information_bo', 'patient_databases.source_information_go', 'patient_databases.source_information_po', 'patient_databases.source_information_ao')
            ->get();

        $statsKlinik = $rawKlinikData->groupBy('klinik')->map(function ($rows, $namaKlinik) {
            $colName = match(strtoupper($namaKlinik)) { 'MO' => 'source_information_mo', 'RO' => 'source_information_ro', 'BO' => 'source_information_bo', 'GO' => 'source_information_go', 'PO' => 'source_information_po', 'AO' => 'source_information_ao', default => null };
            $sources = $rows->map(function($row) use ($colName) {
                if ($colName && !empty($row->$colName) && $row->$colName !== '-' && $row->$colName !== '0') return $row->$colName;
                return null;
            })->filter();
            return (object) ['klinik' => $namaKlinik, 'total' => $rows->count(), 'details'=> $sources->countBy()->sortDesc()];
        })->sortByDesc('total');

        $activeNoRm = ClinicVisit::whereBetween('tanggal_kunjungan', [$startDate, $endDate])->pluck('no_rm')->unique()->toArray();
        $rawSources = PatientDatabase::whereIn('no_rm', $activeNoRm)->select('source_information_mo', 'source_information_ro', 'source_information_bo', 'source_information_go', 'source_information_po', 'source_information_ao')->get();
        $mergedSources = [];
        $isValid = function($val) { return !empty($val) && $val !== '-' && $val !== '0' && strtolower($val) !== 'nan'; };
        foreach($rawSources as $item) {
            if ($isValid($item->source_information_mo)) $mergedSources[] = $item->source_information_mo;
            if ($isValid($item->source_information_ro)) $mergedSources[] = $item->source_information_ro;
            if ($isValid($item->source_information_bo)) $mergedSources[] = $item->source_information_bo;
            if ($isValid($item->source_information_go)) $mergedSources[] = $item->source_information_go;
            if ($isValid($item->source_information_po)) $mergedSources[] = $item->source_information_po;
            if ($isValid($item->source_information_ao)) $mergedSources[] = $item->source_information_ao;
        }
        $marketingStats = collect(array_count_values($mergedSources))->map(function($total, $src) {
            return (object) ['sumber_informasi' => $src, 'total' => $total];
        })->sortByDesc('total');
        $totalSource = $marketingStats->sum('total');

        // DPJP Stats
        $rawDpjp = ClinicVisit::select('klinik', 'dpjp', DB::raw('count(*) as total'))->whereBetween('tanggal_kunjungan', [$startDate, $endDate])->whereNotNull('dpjp')->where('dpjp', '!=', '')->where('dpjp', '!=', '-')->where('dpjp', '!=', '0')->groupBy('klinik', 'dpjp')->orderBy('total', 'desc')->get();
        $dpjpStats = $rawDpjp->groupBy('klinik')->map(function ($doctors, $klinik) {
            $totalClinic = $doctors->sum('total');
            $listDoctors = $doctors->map(function($doc) use ($totalClinic) {
                return (object) ['name' => $doc->dpjp, 'total' => $doc->total, 'percent' => $totalClinic > 0 ? ($doc->total / $totalClinic) * 100 : 0];
            });
            return (object) ['klinik' => $klinik, 'total_visit' => $totalClinic, 'doctors' => $listDoctors];
        })->sortByDesc('total_visit');

        return view('dashboard', compact(
            'totalPasienDB', 'pasienBaru', 
            'convertedChemo', 'notConvertedChemo', // Hapus notSuggestedChemo
            'convertedRt', 'notConvertedRt',       // Hapus notSuggestedRt
            'statsKlinik', 'startDate', 'endDate', 'marketingStats', 'totalSource', 'dpjpStats'
        ));
    }

        // Fungsi untuk menyimpan filter Dashboard ke Session
        public function setFilter(Request $request)
        {
            // Simpan tanggal ke session dengan nama unik (beda dengan report)
            session([
                'dashboard_start_date' => $request->input('start_date'),
                'dashboard_end_date'   => $request->input('end_date'),
            ]);

            // Redirect kembali ke dashboard (URL jadi bersih)
            return redirect()->route('dashboard');
        }
}