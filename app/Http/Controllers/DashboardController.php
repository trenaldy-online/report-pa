<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClinicVisit;
use App\Models\PatientDatabase;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function setFilter(Request $request)
    {
        session([
            'dashboard_start_date' => $request->input('start_date'),
            'dashboard_end_date'   => $request->input('end_date'),
        ]);

        return redirect()->route('dashboard');
    }

    public function index(Request $request)
    {
        // 1. FILTER TANGGAL
        $startDate = session('dashboard_start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate   = session('dashboard_end_date', Carbon::now()->endOfMonth()->toDateString());

        // 2. STATISTIK UTAMA
        $totalPasienDB = PatientDatabase::count();
        $pasienBaru = ClinicVisit::where('new_patient', 1)
            ->whereBetween('tanggal_kunjungan', [$startDate, $endDate])
            ->count();

        // --- PRE-FETCH DATA CONVERTED (Optimasi Performance) ---
        $kemoConvertedRms = DB::table('kemoterapi_converted')->pluck('no_rm')->toArray();
        $radioConvertedRms = DB::table('radioterapi_converted')->pluck('no_rm')->toArray();
        $kemoLookup = array_flip($kemoConvertedRms); 
        $radioLookup = array_flip($radioConvertedRms);


        // 3. WIDGET ATAS (Logic Baru: Join Table)
        $getWidgetStats = function($klinik, $newClinicCol, $statusCol, $convertedTable) use ($startDate, $endDate) {
            $baseQuery = DB::table('clinic_visits')
                ->join('patient_databases', 'clinic_visits.no_rm', '=', 'patient_databases.no_rm')
                ->leftJoin($convertedTable, 'clinic_visits.no_rm', '=', "$convertedTable.no_rm")
                ->where('clinic_visits.klinik', $klinik)
                ->whereBetween('clinic_visits.tanggal_kunjungan', [$startDate, $endDate])
                ->where(function($q) use ($newClinicCol) {
                    $q->whereNotNull("patient_databases.$newClinicCol")
                      ->where("patient_databases.$newClinicCol", '!=', 'No')
                      ->where("patient_databases.$newClinicCol", '!=', '0');
                });

            $converted = (clone $baseQuery)
                ->where(function($q) use ($statusCol, $convertedTable) {
                    $q->whereRaw("patient_databases.$statusCol REGEXP '^[0-9]+$'") 
                      ->orWhereNotNull("$convertedTable.id");
                })
                ->distinct('clinic_visits.no_rm')->count('clinic_visits.no_rm');

            $total = (clone $baseQuery)->distinct('clinic_visits.no_rm')->count('clinic_visits.no_rm');
            $notConverted = $total - $converted;
            
            return [$converted, $notConverted];
        };

        list($convertedChemo, $notConvertedChemo) = $getWidgetStats('MO', 'new_mo_clinic', 'new_chemo', 'kemoterapi_converted');
        list($convertedRt, $notConvertedRt) = $getWidgetStats('RO', 'new_ro_clinic', 'new_rt', 'radioterapi_converted');


        // 4. DATA KUNJUNGAN KLINIK
        $rawKlinikData = DB::table('clinic_visits')
            ->leftJoin('patient_databases', 'clinic_visits.no_rm', '=', 'patient_databases.no_rm')
            ->whereBetween('clinic_visits.tanggal_kunjungan', [$startDate, $endDate])
            ->select('clinic_visits.klinik', 'patient_databases.source_information_mo', 'patient_databases.source_information_ro', 'patient_databases.source_information_bo', 'patient_databases.source_information_go', 'patient_databases.source_information_po', 'patient_databases.source_information_ao')
            ->get();

        $statsKlinik = $rawKlinikData->groupBy('klinik')->map(function ($rows, $namaKlinik) {
            $colName = match(strtoupper($namaKlinik)) { 
                'MO' => 'source_information_mo', 'RO' => 'source_information_ro', 'BO' => 'source_information_bo', 'GO' => 'source_information_go', 'PO' => 'source_information_po', 'AO' => 'source_information_ao', default => null 
            };
            $sources = $rows->map(function($row) use ($colName) {
                if ($colName && !empty($row->$colName) && $row->$colName !== '-' && $row->$colName !== '0') return $row->$colName;
                return null;
            })->filter();
            return (object) ['klinik' => $namaKlinik, 'total' => $rows->count(), 'details'=> $sources->countBy()->sortDesc()];
        })->sortByDesc('total');


        // 5. SUMBER PASIEN
        $activeNoRm = ClinicVisit::whereBetween('tanggal_kunjungan', [$startDate, $endDate])->pluck('no_rm')->unique()->toArray();
        $rawSources = PatientDatabase::whereIn('no_rm', $activeNoRm)->get();
        
        $mergedSources = [];
        foreach($rawSources as $p) {
            $src = $p->source_information_mo ?? $p->source_information_ro ?? $p->source_information_bo ?? $p->source_information_go ?? $p->source_information_po ?? $p->source_information_ao;
            if (!empty($src) && $src !== '-' && $src !== '0' && strtolower($src) !== 'nan') {
                $mergedSources[] = $src;
            }
        }
        $marketingStats = collect(array_count_values($mergedSources))->map(function($total, $src) {
            return (object) ['sumber_informasi' => $src, 'total' => $total];
        })->sortByDesc('total')->take(10);
        $totalSource = $marketingStats->sum('total');


        // 6. PERFORMA DPJP (PERBAIKAN AMBIGUOUS COLUMN)
        $rawDpjp = ClinicVisit::join('patient_databases', 'clinic_visits.no_rm', '=', 'patient_databases.no_rm')
            ->leftJoin('kemoterapi_converted', 'clinic_visits.no_rm', '=', 'kemoterapi_converted.no_rm')
            ->leftJoin('radioterapi_converted', 'clinic_visits.no_rm', '=', 'radioterapi_converted.no_rm')
            ->whereBetween('clinic_visits.tanggal_kunjungan', [$startDate, $endDate])
            ->whereNotNull('clinic_visits.dpjp') // FIX: Tambahkan 'clinic_visits.'
            ->whereNotIn('clinic_visits.dpjp', ['', '-', '0']) // FIX: Tambahkan 'clinic_visits.'
            ->select(
                'clinic_visits.klinik',
                'clinic_visits.dpjp',
                DB::raw('count(*) as total_visit'),
                DB::raw("SUM(CASE 
                    WHEN clinic_visits.klinik = 'MO' 
                         AND patient_databases.new_mo_clinic NOT IN ('No', '0') 
                         AND (patient_databases.new_chemo REGEXP '^[0-9]+$' OR kemoterapi_converted.id IS NOT NULL) THEN 1
                    WHEN clinic_visits.klinik = 'RO' 
                         AND patient_databases.new_ro_clinic NOT IN ('No', '0') 
                         AND (patient_databases.new_rt REGEXP '^[0-9]+$' OR radioterapi_converted.id IS NOT NULL) THEN 1
                    ELSE 0 
                END) as total_converted")
            )
            ->groupBy('clinic_visits.klinik', 'clinic_visits.dpjp')
            ->orderBy('total_visit', 'desc')
            ->get();

        $dpjpStats = $rawDpjp->groupBy('klinik')->map(function ($doctors, $klinik) {
            $totalClinic = $doctors->sum('total_visit');
            $listDoctors = $doctors->map(function($doc) use ($totalClinic) {
                $percentShare = $totalClinic > 0 ? ($doc->total_visit / $totalClinic) * 100 : 0;
                $convRate = $doc->total_visit > 0 ? ($doc->total_converted / $doc->total_visit) * 100 : 0;
                return (object) [
                    'name' => $doc->dpjp, 
                    'total' => $doc->total_visit,
                    'converted' => $doc->total_converted,
                    'conv_rate' => floor($convRate * 10) / 10,
                    'percent' => $percentShare 
                ];
            });
            return (object) ['klinik' => $klinik, 'total_visit' => $totalClinic, 'doctors' => $listDoctors];
        })->sortByDesc('total_visit');


        // 7. EFEKTIVITAS MARKETING
        $analyzedPatients = ClinicVisit::join('patient_databases', 'clinic_visits.no_rm', '=', 'patient_databases.no_rm')
            ->whereBetween('clinic_visits.tanggal_kunjungan', [$startDate, $endDate])
            ->select(
                'patient_databases.name_of_patient as patient_name',
                'patient_databases.*', 
                'clinic_visits.klinik as visit_klinik', 
                'clinic_visits.tanggal_kunjungan'
            )
            ->get();

        $sourceAnalysisStats = [];

        foreach ($analyzedPatients as $patient) {
            $source = $patient->source_information_mo ?? $patient->source_information_ro ?? $patient->source_information_bo ?? $patient->source_information_go ?? $patient->source_information_po ?? $patient->source_information_ao ?? 'Tidak Diketahui';
            if (empty($source) || $source == '-' || $source == '0') $source = 'Tidak Diketahui';

            $status_mo_text = '-';
            $status_ro_text = '-';

            // LOGIKA MO
            if ($patient->visit_klinik == 'MO' && !empty($patient->new_mo_clinic) && $patient->new_mo_clinic != 'No' && $patient->new_mo_clinic != '0') {
                $isKemoConverted = preg_match('/^[0-9]+$/', (string)$patient->new_chemo) || isset($kemoLookup[$patient->no_rm]);
                if ($isKemoConverted) {
                    $status_mo_text = 'Converted';
                } elseif (str_contains($patient->new_chemo, 'Not Converted')) {
                    $status_mo_text = 'Not Converted';
                }
            }

            // LOGIKA RO
            if ($patient->visit_klinik == 'RO' && !empty($patient->new_ro_clinic) && $patient->new_ro_clinic != 'No' && $patient->new_ro_clinic != '0') {
                $isRadioConverted = preg_match('/^[0-9]+$/', (string)$patient->new_rt) || isset($radioLookup[$patient->no_rm]);
                if ($isRadioConverted) {
                    $status_ro_text = 'Converted';
                } elseif (str_contains($patient->new_rt, 'Not Converted')) {
                    $status_ro_text = 'Not Converted';
                }
            }

            if (!isset($sourceAnalysisStats[$source])) {
                $sourceAnalysisStats[$source] = [
                    'name' => $source, 
                    'patients_list' => []
                ];
            }
            
            $visitKey = $patient->no_rm . '_' . $patient->visit_klinik . '_' . $patient->tanggal_kunjungan;
            $sourceAnalysisStats[$source]['patients_list'][$visitKey] = [
                'tanggal' => $patient->tanggal_kunjungan,
                'no_rm' => $patient->no_rm,
                'nama' => $patient->patient_name ?? 'Tanpa Nama',
                'visit' => $patient->visit_klinik,
                'status_mo' => $status_mo_text,
                'status_ro' => $status_ro_text
            ];
        }

        $finalStats = [];
        foreach($sourceAnalysisStats as $srcKey => $data) {
            $allVisits = collect($data['patients_list']);
            
            $total_lead_unique = $allVisits->unique('no_rm')->count();
            
            $mo_conv_total = $allVisits->where('status_mo', 'Converted')->count();
            $ro_conv_total = $allVisits->where('status_ro', 'Converted')->count();

            $u_conv = $allVisits->filter(function($v){ 
                return $v['status_mo'] == 'Converted' || $v['status_ro'] == 'Converted'; 
            })->unique('no_rm')->count();

            $u_not = $allVisits->filter(function($v){ 
                return $v['status_mo'] == 'Not Converted' || $v['status_ro'] == 'Not Converted'; 
            })->unique('no_rm')->count();
            
            $data['total_lead'] = $total_lead_unique;
            $data['unique_converted'] = $u_conv;
            $data['unique_not_converted'] = $u_not;
            $data['mo_conv_raw'] = $mo_conv_total; 
            $data['ro_conv_raw'] = $ro_conv_total;
            $data['patients_list'] = $allVisits->values()->all();
            
            $finalStats[$srcKey] = $data;
        }

        $conversionAnalysis = collect($finalStats)->map(function($item) {
            $divider = $item['total_lead'] > 0 ? $item['total_lead'] : 1;
            $item['rate_converted'] = floor(($item['unique_converted'] / $divider) * 1000) / 10;
            $item['rate_not_converted'] = floor(($item['unique_not_converted'] / $divider) * 1000) / 10;
            $item['rate_pending'] = 100 - $item['rate_converted'] - $item['rate_not_converted'];
            if($item['rate_pending'] < 0) $item['rate_pending'] = 0;
            
            $item['mo_conv'] = $item['mo_conv_raw'];
            $item['ro_conv'] = $item['ro_conv_raw'];
            return (object) $item;
        })->sortByDesc('total_lead');


        // 8. GRAFIK TREN
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $isWeekly = $start->diffInMonths($end) > 3;
        $chartPeriodLabel = $isWeekly ? 'Mingguan (Weekly)' : 'Harian (Daily)';

        $strictCaseWhen = "SUM(CASE 
            WHEN clinic_visits.klinik = 'MO' 
                 AND patient_databases.new_mo_clinic NOT IN ('No', '0') 
                 AND (patient_databases.new_chemo REGEXP '^[0-9]+$' OR kemoterapi_converted.id IS NOT NULL) THEN 1
            WHEN clinic_visits.klinik = 'RO' 
                 AND patient_databases.new_ro_clinic NOT IN ('No', '0') 
                 AND (patient_databases.new_rt REGEXP '^[0-9]+$' OR radioterapi_converted.id IS NOT NULL) THEN 1
            ELSE 0 
        END) as total_converted";

        $trendQuery = ClinicVisit::join('patient_databases', 'clinic_visits.no_rm', '=', 'patient_databases.no_rm')
                ->leftJoin('kemoterapi_converted', 'clinic_visits.no_rm', '=', 'kemoterapi_converted.no_rm')
                ->leftJoin('radioterapi_converted', 'clinic_visits.no_rm', '=', 'radioterapi_converted.no_rm')
                ->whereBetween('clinic_visits.tanggal_kunjungan', [$startDate, $endDate]);

        if ($isWeekly) {
            $trendData = $trendQuery->select(
                    DB::raw('YEARWEEK(clinic_visits.tanggal_kunjungan, 1) as time_key'),
                    DB::raw($strictCaseWhen),
                    DB::raw("count(*) as total_visit") 
                )->groupBy('time_key')->get()->keyBy('time_key');
            
            $period = \Carbon\CarbonPeriod::create($startDate, '1 week', $endDate);
            $dateFormat = 'oW';
        } else {
            $trendData = $trendQuery->select(
                    DB::raw('DATE(clinic_visits.tanggal_kunjungan) as time_key'),
                    DB::raw($strictCaseWhen),
                    DB::raw("count(*) as total_visit") 
                )->groupBy('time_key')->get()->keyBy('time_key');
            
            $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
            $dateFormat = 'Y-m-d';
        }

        $chartDates = [];
        $chartConverted = [];
        $chartNonConverted = [];

        foreach ($period as $date) {
            $dbKey = $date->format($dateFormat);
            $viewLabel = $isWeekly ? "W" . $date->weekOfYear : $date->format('d/m');
            
            $data = $trendData[$dbKey] ?? null;
            $total = $data ? $data->total_visit : 0;
            $conv = $data ? $data->total_converted : 0;
            $non = $total - $conv;

            $chartDates[] = $viewLabel;
            $chartConverted[] = $conv;
            $chartNonConverted[] = $non;
        }

        return view('dashboard', compact(
            'startDate', 'endDate',
            'totalPasienDB', 'pasienBaru',
            'convertedChemo', 'notConvertedChemo',
            'convertedRt', 'notConvertedRt',
            'statsKlinik', 'marketingStats', 'totalSource', 'dpjpStats',
            'conversionAnalysis',
            'chartDates', 'chartConverted', 'chartNonConverted',
            'chartPeriodLabel'
        ));
    }
}