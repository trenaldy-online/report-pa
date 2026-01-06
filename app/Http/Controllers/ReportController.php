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

        // Parameter: (Klinik, NewClinicCol, StatusCol, ReasonCol, SourceCol, ConvertedTable)
        $moData = $this->getClinicStats('MO', 'new_mo_clinic', 'new_chemo', 'reason2', 'source_information_mo', 'kemoterapi_converted', $startDate, $endDate);
        $roData = $this->getClinicStats('RO', 'new_ro_clinic', 'new_rt', 'reason', 'source_information_ro', 'radioterapi_converted', $startDate, $endDate);
        
        // Klinik lain tidak punya tabel converted khusus, jadi parameter tabel null
        $boData = $this->getClinicStats('BO', 'new_bo_clinic', null, null, 'source_information_bo', null, $startDate, $endDate);
        $goData = $this->getClinicStats('GO', 'new_go_clinic', null, null, 'source_information_go', null, $startDate, $endDate);
        $poData = $this->getClinicStats('PO', 'new_po_clinic', null, null, 'source_information_po', null, $startDate, $endDate);
        $aoData = $this->getClinicStats('AO', 'new_ao_clinic', null, null, 'source_information_ao', null, $startDate, $endDate);

        // --- SUMMARY GLOBAL ---
        $statsKlinik = collect([
            (object)['klinik' => 'MO', 'total' => $moData['total_visit'], 'details' => $moData['sources']],
            (object)['klinik' => 'RO', 'total' => $roData['total_visit'], 'details' => $roData['sources']],
            (object)['klinik' => 'BO', 'total' => $boData['total_visit'], 'details' => $boData['sources']],
            (object)['klinik' => 'GO', 'total' => $goData['total_visit'], 'details' => $goData['sources']],
            (object)['klinik' => 'PO', 'total' => $poData['total_visit'], 'details' => $poData['sources']],
            (object)['klinik' => 'AO', 'total' => $aoData['total_visit'], 'details' => $aoData['sources']],
        ])->sortByDesc('total');

        $allSources = array_merge(
            $moData['raw_sources'], $roData['raw_sources'], $boData['raw_sources'],
            $goData['raw_sources'], $poData['raw_sources'], $aoData['raw_sources']
        );
        $marketingCounts = array_count_values($allSources);
        arsort($marketingCounts);
        
        $marketingStats = collect($marketingCounts)->map(function($total, $src) {
            return (object) ['sumber_informasi' => $src, 'total' => $total];
        });
        $totalSource = $marketingStats->sum('total');

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

    private function getClinicStats($clinicCode, $newClinicCol, $statusCol, $reasonCol, $sourceCol, $convertedTable, $start, $end)
    {
        // 1. QUERY DASAR (JOIN TABLE)
        $query = DB::table('clinic_visits')
            ->leftJoin('patient_databases', 'clinic_visits.no_rm', '=', 'patient_databases.no_rm')
            ->where('clinic_visits.klinik', $clinicCode)
            ->whereBetween('clinic_visits.tanggal_kunjungan', [$start, $end])
            ->where(function($q) use ($newClinicCol) {
                $q->whereNotNull("patient_databases.$newClinicCol")
                  ->where("patient_databases.$newClinicCol", '!=', 'No')
                  ->where("patient_databases.$newClinicCol", '!=', '0');
            });

        // JOIN KE TABEL CONVERTED (Jika ada parameter tabelnya)
        if ($convertedTable) {
            $query->leftJoin($convertedTable, 'clinic_visits.no_rm', '=', "$convertedTable.no_rm");
        }

        // 2. TOTAL VISIT
        $totalVisit = (clone $query)->distinct('clinic_visits.no_rm')->count('clinic_visits.no_rm');
        
        $converted = 0; $notConverted = 0; $reasonsList = [];
        
        // 3. LOGIC CONVERTED (GABUNGAN)
        if ($statusCol) {
            $convertedQuery = (clone $query)->where(function($q) use ($statusCol, $convertedTable) {
                // Logic Lama: Regex Angka
                $q->whereRaw("patient_databases.$statusCol REGEXP '^[0-9]+$'");
                
                // Logic Baru: Jika ada di tabel converted (ID tidak null)
                if ($convertedTable) {
                    $q->orWhereNotNull("$convertedTable.id");
                }
            });

            $converted = $convertedQuery->distinct('clinic_visits.no_rm')->count('clinic_visits.no_rm');
            
            // Not Converted = Total - Converted (Lebih akurat daripada query LIKE)
            $notConverted = $totalVisit - $converted;
            
            // Reasons (Ambil dari pasien yang statusnya 'Not Converted' secara text)
            // Kita tetap pakai text 'Not Converted' dari DB lama untuk alasan, karena tabel baru tidak punya kolom alasan.
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

        // 4. SUMBER INFO
        $rawSourcesData = (clone $query)
            ->select("patient_databases.$sourceCol")
            ->get();
            
        $sourcesClean = [];
        foreach($rawSourcesData as $r) {
            $val = $r->$sourceCol;
            if(!empty($val) && $val != '-' && $val != '0' && strtolower($val) != 'nan') {
                $sourcesClean[] = $val;
            }
        }
        $sourcesCount = collect(array_count_values($sourcesClean))->sortDesc();

        // 5. DETAIL PASIEN (MODAL)
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

        return [
            'total_visit' => $totalVisit,
            'converted' => $converted,
            'not_converted' => $notConverted,
            'reasons' => $reasonsList,
            'sources' => $sourcesCount,
            'raw_sources' => $sourcesClean,
            'overseas' => 0, 
            'monthly_total' => $totalVisit, 
            'details_list' => $patientDetails
        ];
    }

    public function updateNote(Request $request)
    {
        $request->validate([
            'no_rm' => 'required',
            'note_col' => 'required', 
            'note_content' => 'nullable|string'
        ]);

        DB::table('patient_databases')
            ->where('no_rm', $request->no_rm)
            ->update([
                $request->note_col => $request->note_content,
                'updated_at' => now()
            ]);

        return back()->with('success', 'Catatan berhasil diperbarui.');
    }

    public function setFilter(Request $request)
    {
        session([
            'report_start_date' => $request->input('start_date'),
            'report_end_date'   => $request->input('end_date'),
        ]);
        return redirect()->route('report.index');
    }
}