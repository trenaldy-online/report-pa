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

        // 3. WIDGET ATAS (Logic Acuan)
        $getWidgetStats = function($klinik, $newClinicCol, $statusCol) use ($startDate, $endDate) {
            $baseQuery = DB::table('clinic_visits')
                ->join('patient_databases', 'clinic_visits.no_rm', '=', 'patient_databases.no_rm')
                ->where('clinic_visits.klinik', $klinik)
                ->whereBetween('clinic_visits.tanggal_kunjungan', [$startDate, $endDate])
                ->where(function($q) use ($newClinicCol) {
                    $q->whereNotNull("patient_databases.$newClinicCol")
                      ->where("patient_databases.$newClinicCol", '!=', 'No')
                      ->where("patient_databases.$newClinicCol", '!=', '0');
                });

            $converted = (clone $baseQuery)
                ->whereRaw("patient_databases.$statusCol REGEXP '^[0-9]+$'")
                ->distinct('clinic_visits.no_rm')->count('clinic_visits.no_rm');

            $notConverted = (clone $baseQuery)
                ->where('patient_databases.' . $statusCol, 'LIKE', '%Not Converted%')
                ->distinct('clinic_visits.no_rm')->count('clinic_visits.no_rm');
            
            return [$converted, $notConverted];
        };

        list($convertedChemo, $notConvertedChemo) = $getWidgetStats('MO', 'new_mo_clinic', 'new_chemo');
        list($convertedRt, $notConvertedRt) = $getWidgetStats('RO', 'new_ro_clinic', 'new_rt');


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


        // 6. PERFORMA DPJP (LOGIKA STRICT CONVERTED)
        // Kita join table agar bisa cek status converted di patient_databases
        $rawDpjp = ClinicVisit::join('patient_databases', 'clinic_visits.no_rm', '=', 'patient_databases.no_rm')
            ->whereBetween('clinic_visits.tanggal_kunjungan', [$startDate, $endDate])
            ->whereNotNull('dpjp')
            ->whereNotIn('dpjp', ['', '-', '0']) // Filter DPJP kosong
            ->select(
                'clinic_visits.klinik',
                'clinic_visits.dpjp',
                // Hitung Total Kunjungan
                DB::raw('count(*) as total_visit'),
                
                // Hitung Converted (Strict sesuai Klinik Fisik)
                DB::raw("SUM(CASE 
                    WHEN clinic_visits.klinik = 'MO' 
                         AND patient_databases.new_mo_clinic NOT IN ('No', '0') 
                         AND patient_databases.new_chemo REGEXP '^[0-9]+$' THEN 1
                    WHEN clinic_visits.klinik = 'RO' 
                         AND patient_databases.new_ro_clinic NOT IN ('No', '0') 
                         AND patient_databases.new_rt REGEXP '^[0-9]+$' THEN 1
                    ELSE 0 
                END) as total_converted")
            )
            ->groupBy('clinic_visits.klinik', 'clinic_visits.dpjp')
            ->orderBy('total_visit', 'desc')
            ->get();

        $dpjpStats = $rawDpjp->groupBy('klinik')->map(function ($doctors, $klinik) {
            $totalClinic = $doctors->sum('total_visit');
            
            $listDoctors = $doctors->map(function($doc) use ($totalClinic) {
                // Share: Seberapa dominan dokter ini di kliniknya (Total Visit Dokter / Total Visit Klinik)
                $percentShare = $totalClinic > 0 ? ($doc->total_visit / $totalClinic) * 100 : 0;
                
                // Rate: Seberapa sukses dokter ini mengonversi pasien (Converted / Total Visit Dokter)
                $convRate = $doc->total_visit > 0 ? ($doc->total_converted / $doc->total_visit) * 100 : 0;

                return (object) [
                    'name' => $doc->dpjp, 
                    'total' => $doc->total_visit,
                    'converted' => $doc->total_converted, // <-- Data Baru
                    'conv_rate' => floor($convRate * 10) / 10, // Floor 1 decimal
                    'percent' => $percentShare 
                ];
            });
            return (object) ['klinik' => $klinik, 'total_visit' => $totalClinic, 'doctors' => $listDoctors];
        })->sortByDesc('total_visit');

        // 7. EFEKTIVITAS MARKETING (LOGIKA DISAMAKAN DENGAN WIDGET ATAS)
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

            // Variable Status PER KUNJUNGAN (Reset setiap loop)
            $status_mo_text = '-';
            $status_ro_text = '-';
            
            $mo_c = 0; $mo_nc = 0; 
            $ro_c = 0; $ro_nc = 0;

            // LOGIKA MO (SAMA PERSIS WIDGET ATAS)
            // Syarat: Harus visit Klinik MO DAN kolom validasi terisi
            if ($patient->visit_klinik == 'MO' && !empty($patient->new_mo_clinic) && $patient->new_mo_clinic != 'No' && $patient->new_mo_clinic != '0') {
                if (preg_match('/^[0-9]+$/', (string)$patient->new_chemo)) {
                    $mo_c = 1; 
                    $status_mo_text = 'Converted';
                } elseif (str_contains($patient->new_chemo, 'Not Converted')) {
                    $mo_nc = 1;
                    $status_mo_text = 'Not Converted';
                }
            }

            // LOGIKA RO (SAMA PERSIS WIDGET ATAS)
            // Syarat: Harus visit Klinik RO DAN kolom validasi terisi
            if ($patient->visit_klinik == 'RO' && !empty($patient->new_ro_clinic) && $patient->new_ro_clinic != 'No' && $patient->new_ro_clinic != '0') {
                if (preg_match('/^[0-9]+$/', (string)$patient->new_rt)) {
                    $ro_c = 1;
                    $status_ro_text = 'Converted';
                } elseif (str_contains($patient->new_rt, 'Not Converted')) {
                    $ro_nc = 1;
                    $status_ro_text = 'Not Converted';
                }
            }

            if (!isset($sourceAnalysisStats[$source])) {
                $sourceAnalysisStats[$source] = [
                    'name' => $source, 
                    'total_lead' => 0, 
                    'unique_converted' => 0, 
                    'unique_not_converted' => 0,
                    'patients_list' => []
                ];
            }
            
            // Simpan data (Gunakan kombinasi RM + Klinik agar unik per kunjungan fisik)
            // KITA HAPUS LOGIKA DISTINCT RM DISINI untuk menghitung per kejadian kunjungan (agar match widget atas)
            // ATAU tetap gunakan NO_RM jika ingin total leadnya unik.
            // Untuk amannya, kita pakai key unik per visit agar listnya lengkap.
            $visitKey = $patient->no_rm . '_' . $patient->visit_klinik . '_' . $patient->tanggal_kunjungan;

            $sourceAnalysisStats[$source]['patients_list'][$visitKey] = [
                'tanggal' => $patient->tanggal_kunjungan,
                'no_rm' => $patient->no_rm,
                'nama' => $patient->patient_name ?? 'Tanpa Nama',
                'visit' => $patient->visit_klinik,
                'status_mo' => $status_mo_text, // Hanya terisi jika visit MO
                'status_ro' => $status_ro_text  // Hanya terisi jika visit RO
            ];
        }

        // Final Calculation
        $finalStats = [];
        foreach($sourceAnalysisStats as $srcKey => $data) {
            $allVisits = collect($data['patients_list']);
            
            // Total Lead = Total Orang Unik (tetap kita hitung orang unik utk Lead)
            $total_lead_unique = $allVisits->unique('no_rm')->count();
            
            // Hitung Converted/Not Converted berdasarkan STATUS yang sudah difilter di atas
            // Kita sum langsung dari list karena listnya sudah strict logic
            $mo_conv_total = $allVisits->where('status_mo', 'Converted')->count();
            $mo_not_total  = $allVisits->where('status_mo', 'Not Converted')->count();
            $ro_conv_total = $allVisits->where('status_ro', 'Converted')->count();
            $ro_not_total  = $allVisits->where('status_ro', 'Not Converted')->count();

            // Unique Converted untuk Progress Bar
            // Pasien dianggap converted jika di salah satu kunjungannya dia converted
            $u_conv = $allVisits->filter(function($v){ 
                return $v['status_mo'] == 'Converted' || $v['status_ro'] == 'Converted'; 
            })->unique('no_rm')->count();

            $u_not = $allVisits->filter(function($v){ 
                return $v['status_mo'] == 'Not Converted' || $v['status_ro'] == 'Not Converted'; 
            })->unique('no_rm')->count();
            
            $data['total_lead'] = $total_lead_unique;
            $data['unique_converted'] = $u_conv;
            $data['unique_not_converted'] = $u_not;
            
            // Data Raw untuk kotak kecil
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
            
            // Mapping untuk view
            $item['mo_conv'] = $item['mo_conv_raw'];
            $item['ro_conv'] = $item['ro_conv_raw'];

            return (object) $item;
        })->sortByDesc('total_lead');

        // 8. GRAFIK TREN KUNJUNGAN (LOGIKA STRICT / KETAT)
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $diffInMonths = $start->diffInMonths($end);
        
        $isWeekly = $diffInMonths > 3;
        $chartPeriodLabel = $isWeekly ? 'Mingguan (Weekly)' : 'Harian (Daily)';

        // --- RUMUS STRICT (Sama persis dengan Widget Atas) ---
        // Hanya hitung Converted jika:
        // 1. Visit klinik 'MO' DAN status Chemo sukses.
        // 2. ATAU Visit klinik 'RO' DAN status RT sukses.
        // Selain itu (misal visit BO/Bedah), dianggap 0 (Kuning).
        $strictCaseWhen = "SUM(CASE 
            WHEN clinic_visits.klinik = 'MO' 
                 AND patient_databases.new_mo_clinic NOT IN ('No', '0') 
                 AND patient_databases.new_chemo REGEXP '^[0-9]+$' THEN 1
            WHEN clinic_visits.klinik = 'RO' 
                 AND patient_databases.new_ro_clinic NOT IN ('No', '0') 
                 AND patient_databases.new_rt REGEXP '^[0-9]+$' THEN 1
            ELSE 0 
        END) as total_converted";

        if ($isWeekly) {
            // --- MINGGUAN ---
            $trendData = ClinicVisit::join('patient_databases', 'clinic_visits.no_rm', '=', 'patient_databases.no_rm')
                ->whereBetween('clinic_visits.tanggal_kunjungan', [$startDate, $endDate])
                ->select(
                    DB::raw('YEARWEEK(clinic_visits.tanggal_kunjungan, 1) as time_key'),
                    DB::raw($strictCaseWhen), // Gunakan Rumus Strict
                    DB::raw("count(*) as total_visit") 
                )
                ->groupBy('time_key')
                ->get()
                ->keyBy('time_key');

            $chartDates = [];
            $chartConverted = [];
            $chartNonConverted = [];
            
            $period = \Carbon\CarbonPeriod::create($startDate, '1 week', $endDate);

            foreach ($period as $date) {
                $dbKey = $date->format('oW'); 
                $viewLabel = "W" . $date->weekOfYear . " (" . $date->format('M') . ")";
                
                $data = $trendData[$dbKey] ?? null;
                $total = $data ? $data->total_visit : 0;
                $conv = $data ? $data->total_converted : 0;
                $non = $total - $conv;

                $chartDates[] = $viewLabel;
                $chartConverted[] = $conv;
                $chartNonConverted[] = $non;
            }

        } else {
            // --- HARIAN ---
            $trendData = ClinicVisit::join('patient_databases', 'clinic_visits.no_rm', '=', 'patient_databases.no_rm')
                ->whereBetween('clinic_visits.tanggal_kunjungan', [$startDate, $endDate])
                ->select(
                    DB::raw('DATE(clinic_visits.tanggal_kunjungan) as time_key'),
                    DB::raw($strictCaseWhen), // Gunakan Rumus Strict
                    DB::raw("count(*) as total_visit") 
                )
                ->groupBy('time_key')
                ->get()
                ->keyBy('time_key');

            $chartDates = [];
            $chartConverted = [];
            $chartNonConverted = [];
            
            $period = \Carbon\CarbonPeriod::create($startDate, $endDate);

            foreach ($period as $date) {
                $dbKey = $date->format('Y-m-d');
                $viewLabel = $date->format('d/m');
                
                $data = $trendData[$dbKey] ?? null;
                $total = $data ? $data->total_visit : 0;
                $conv = $data ? $data->total_converted : 0;
                $non = $total - $conv;

                $chartDates[] = $viewLabel;
                $chartConverted[] = $conv;
                $chartNonConverted[] = $non;
            }
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