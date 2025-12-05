<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = session('report_start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate   = session('report_end_date', Carbon::now()->endOfMonth()->toDateString());

        // --- DATA PER KLINIK (Detail) ---
        // Format: (Klinik, KolomNewClinic, KolomStatus, KolomReason, KolomSource, Start, End)
        
        $moData = $this->getClinicStats('MO', 'new_mo_clinic', 'new_chemo', 'reason2', 'source_information_mo', $startDate, $endDate);
        $roData = $this->getClinicStats('RO', 'new_ro_clinic', 'new_rt', 'reason', 'source_information_ro', $startDate, $endDate);
        
        $boData = $this->getClinicStats('BO', 'new_bo_clinic', null, null, 'source_information_bo', $startDate, $endDate);
        $goData = $this->getClinicStats('GO', 'new_go_clinic', null, null, 'source_information_go', $startDate, $endDate);
        $poData = $this->getClinicStats('PO', 'new_po_clinic', null, null, 'source_information_po', $startDate, $endDate);
        $aoData = $this->getClinicStats('AO', 'new_ao_clinic', null, null, 'source_information_ao', $startDate, $endDate);

        // --- SUMMARY GLOBAL (Untuk Tampilan Bawah) ---
        $statsKlinik = collect([
            (object)['klinik' => 'MO', 'total' => $moData['total_visit'], 'details' => $moData['sources']],
            (object)['klinik' => 'RO', 'total' => $roData['total_visit'], 'details' => $roData['sources']],
            (object)['klinik' => 'BO', 'total' => $boData['total_visit'], 'details' => $boData['sources']],
            (object)['klinik' => 'GO', 'total' => $goData['total_visit'], 'details' => $goData['sources']],
            (object)['klinik' => 'PO', 'total' => $poData['total_visit'], 'details' => $poData['sources']],
            (object)['klinik' => 'AO', 'total' => $aoData['total_visit'], 'details' => $aoData['sources']],
        ])->sortByDesc('total');

        // Hitung Total Marketing Global (Gabungan Semua Klinik)
        // Sekarang error 'Undefined index' akan hilang karena raw_sources sudah ada
        $allSources = array_merge(
            $moData['raw_sources'], 
            $roData['raw_sources'], 
            $boData['raw_sources'],
            $goData['raw_sources'], 
            $poData['raw_sources'], 
            $aoData['raw_sources']
        );
        $marketingCounts = array_count_values($allSources);
        arsort($marketingCounts);
        
        $marketingStats = collect($marketingCounts)->map(function($total, $src) {
            return (object) ['sumber_informasi' => $src, 'total' => $total];
        });
        $totalSource = $marketingStats->sum('total');

        // Data Tambahan
        $totalChemoNew = $moData['converted']; 
        $moConversionRate = $moData['total_visit'] > 0 ? ($moData['converted'] / $moData['total_visit']) * 100 : 0;
        
        $totalRtNew = $roData['converted'];
        $roConversionRate = $roData['total_visit'] > 0 ? ($roData['converted'] / $roData['total_visit']) * 100 : 0;

        return view('report.biweekly', compact(
            'startDate', 'endDate',
            'moData', 'boData', 'goData', 'poData', 'aoData', 'roData',
            'totalChemoNew', 'moConversionRate', 'totalRtNew', 'roConversionRate',
            'statsKlinik', 'marketingStats', 'totalSource'
        ));
    }

    private function getClinicStats($clinicCode, $newClinicCol, $statusCol, $reasonCol, $sourceCol, $start, $end)
    {
        // 1. QUERY DASAR
        $query = DB::table('clinic_visits')
            ->leftJoin('patient_databases', 'clinic_visits.no_rm', '=', 'patient_databases.no_rm')
            ->where('clinic_visits.klinik', $clinicCode)
            ->whereBetween('clinic_visits.tanggal_kunjungan', [$start, $end])
            ->where(function($q) use ($newClinicCol) {
                $q->whereNotNull("patient_databases.$newClinicCol")
                  ->where("patient_databases.$newClinicCol", '!=', 'No')
                  ->where("patient_databases.$newClinicCol", '!=', '0');
            });

        // 2. TOTAL & STATUS
        $totalVisit = (clone $query)->distinct('clinic_visits.no_rm')->count('clinic_visits.no_rm');
        
        $converted = 0; $notConverted = 0; $reasonsList = [];
        if ($statusCol) {
            $converted = (clone $query)->whereRaw("patient_databases.$statusCol REGEXP '^[0-9]+$'")->distinct('clinic_visits.no_rm')->count('clinic_visits.no_rm');
            $notConverted = (clone $query)->where("patient_databases.$statusCol", 'LIKE', '%Not Converted%')->distinct('clinic_visits.no_rm')->count('clinic_visits.no_rm');
            
            if ($reasonCol) {
                $reasonsList = (clone $query)
                    ->where("patient_databases.$statusCol", 'LIKE', '%Not Converted%')
                    ->whereNotNull("patient_databases.$reasonCol")
                    ->where("patient_databases.$reasonCol", '!=', '')
                    ->select("patient_databases.$reasonCol as reason_text", DB::raw('count(*) as total'))
                    ->groupBy("patient_databases.$reasonCol")
                    ->orderBy('total', 'desc')->get();
            }
        }

        // 3. DETAIL PASIEN (MODAL)
        $rawPatients = (clone $query)
            ->select(
                'clinic_visits.no_rm',
                'clinic_visits.tanggal_kunjungan',
                'patient_databases.name_of_patient',
                'patient_databases.age',
                'patient_databases.diagnosis',
                "patient_databases.$sourceCol as source_info",
                'patient_databases.activities_notes',
                'patient_databases.activities_notes2',
                'patient_databases.activities_notes3',
                'patient_databases.activities_notes4',
                'patient_databases.activities_notes5'
            )
            ->get();

        $patientDetails = $rawPatients->map(function($patient) {
            // Hitung urutan kunjungan untuk ambil note yang sesuai
            $previousVisits = DB::table('clinic_visits')
                ->where('no_rm', $patient->no_rm)
                ->where('tanggal_kunjungan', '<', $patient->tanggal_kunjungan)
                ->count();
            
            $rank = $previousVisits + 1;
            $noteKey = 'activities_notes' . ($rank > 1 ? $rank : '');
            $noteContent = $patient->$noteKey ?? '-';

            return (object) [
                'no_rm' => $patient->no_rm,
                'name'  => $patient->name_of_patient,
                'age'   => $patient->age,
                'diagnosis' => $patient->diagnosis,
                'source' => $patient->source_info,
                'note'   => $noteContent,
                'visit_rank' => $rank,
                'note_col' => $noteKey,
            ];
        });

        // 4. SOURCES & OVERSEAS
        $rawSources = (clone $query)
            ->whereNotNull("patient_databases.$sourceCol")
            ->where("patient_databases.$sourceCol", '!=', '')
            ->where("patient_databases.$sourceCol", '!=', '-')
            ->where("patient_databases.$sourceCol", '!=', '0')
            ->pluck("patient_databases.$sourceCol")
            ->toArray();
        $sourcesCount = array_count_values($rawSources); arsort($sourcesCount);
        
        $overseas = (clone $query)
            ->whereNotNull('patient_databases.overseas_hospital')
            ->where('patient_databases.overseas_hospital', '!=', '')
            ->distinct('clinic_visits.no_rm')->count('clinic_visits.no_rm');
        
        // 5. MONTHLY CONTEXT
        $monthStart = Carbon::parse($start)->startOfMonth()->toDateString();
        $monthEnd   = Carbon::parse($start)->endOfMonth()->toDateString();
        $monthQuery = DB::table('clinic_visits')
            ->leftJoin('patient_databases', 'clinic_visits.no_rm', '=', 'patient_databases.no_rm')
            ->where('clinic_visits.klinik', $clinicCode)
            ->whereBetween('clinic_visits.tanggal_kunjungan', [$monthStart, $monthEnd])
            ->where(function($q) use ($newClinicCol) {
                $q->whereNotNull("patient_databases.$newClinicCol")->where("patient_databases.$newClinicCol", '!=', 'No')->where("patient_databases.$newClinicCol", '!=', '0');
            });
        
        $monthlyTotal = (clone $monthQuery)->distinct('clinic_visits.no_rm')->count('clinic_visits.no_rm');
        $monthlyConv = $statusCol ? (clone $monthQuery)->whereRaw("patient_databases.$statusCol REGEXP '^[0-9]+$'")->distinct('clinic_visits.no_rm')->count('clinic_visits.no_rm') : 0;

        return [
            'total_visit' => $totalVisit,
            'converted' => $converted,
            'not_converted' => $notConverted,
            'reasons' => $reasonsList,
            'sources' => $sourcesCount,
            'raw_sources' => $rawSources, // <--- INI DIA PENYEBABNYA (Sudah Ditambahkan)
            'overseas' => $overseas,
            'monthly_total' => $monthlyTotal,
            'monthly_conv' => $monthlyConv,
            'details_list' => $patientDetails
        ];
    }

    // Fungsi untuk menyimpan edit note dari Modal
    public function updateNote(Request $request)
    {
        // Validasi input
        $request->validate([
            'no_rm' => 'required',
            'note_col' => 'required', // Nama kolom (activities_notes, activities_notes2, dst)
            'note_content' => 'nullable|string'
        ]);

        // Cari Pasien di tabel patient_databases berdasarkan No RM
        // Kita gunakan update query builder agar cepat
        DB::table('patient_databases')
            ->where('no_rm', $request->no_rm)
            ->update([
                $request->note_col => $request->note_content,
                'updated_at' => now()
            ]);

        // Kembali ke halaman sebelumnya dengan pesan sukses (opsional)
        return back()->with('success', 'Catatan berhasil diperbarui.');
    }

    // Fungsi untuk menyimpan filter ke Session lalu Redirect
    public function setFilter(Request $request)
    {
        // Simpan tanggal ke session
        session([
            'report_start_date' => $request->input('start_date'),
            'report_end_date'   => $request->input('end_date'),
        ]);

        // Kembali ke halaman report dengan URL bersih
        return redirect()->route('report.index');
    }
}