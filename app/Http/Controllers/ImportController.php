<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth; // <--- TAMBAHAN PENTING 1

// Import Class Excel
use App\Imports\KlinikImport;
use App\Imports\DatabaseImport;

// Model Utama
use App\Models\ClinicVisit;
use App\Models\PatientDatabase;

// Model Sementara (Temporary)
use App\Models\ClinicVisitImport;
use App\Models\PatientDatabaseImport;

// Import Class Radioterapi & Kemoterapi
use App\Imports\RadioterapiImport;
use App\Imports\KemoterapiImport;

class ImportController extends Controller
{
    /**
     * 1. TAMPILKAN FORM UPLOAD
     */
    public function showForm()
    {
        return view('import');
    }

    /**
     * 2. PROSES UPLOAD (Excel -> Tabel Sementara)
     */
    public function process(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'type' => 'required'
        ]);

        $file = $request->file('file');
        $type = $request->type;

        // 1. IMPORT KLINIK (Pakai Review)
        if ($type == 'KLINIK') {
            ClinicVisitImport::where('uploaded_by', \Illuminate\Support\Facades\Auth::id())->delete();
            Excel::import(new KlinikImport, $file);
            session(['import_type' => 'KLINIK']);
            return redirect()->route('import.review');
        } 
        // 2. IMPORT DATABASE PASIEN (Pakai Review)
        else if ($type == 'DATABASE') {
            PatientDatabaseImport::where('uploaded_by', \Illuminate\Support\Facades\Auth::id())->delete();
            Excel::import(new DatabaseImport, $file);
            session(['import_type' => 'DATABASE']);
            return redirect()->route('import.review');
        }
        // 3. IMPORT RADIOTERAPI CONVERTED (Langsung Simpan)
        else if ($type == 'RADIO_CONVERTED') {
            Excel::import(new RadioterapiImport, $file);
            return back()->with('success', 'Data Radioterapi Converted berhasil diperbarui!');
        }
        // 4. IMPORT KEMOTERAPI CONVERTED (Langsung Simpan)
        else if ($type == 'KEMO_CONVERTED') {
            Excel::import(new KemoterapiImport, $file);
            return back()->with('success', 'Data Kemoterapi Converted berhasil diperbarui!');
        }

        return back()->with('error', 'Tipe import tidak dikenali.');
    }

    /**
     * 3. HALAMAN REVIEW (Smart Checking)
     */
    public function review()
    {
        $type = session('import_type');

        if ($type == 'KLINIK') {
            return $this->reviewKlinik();
        } elseif ($type == 'DATABASE') {
            return $this->reviewDatabase();
        }

        return redirect()->route('import.form')->with('error', 'Sesi import telah berakhir atau tidak valid.');
    }

    // --- Sub-Fungsi: Review Klinik ---
    private function reviewKlinik()
    {
        // PERBAIKAN: Ganti auth()->id() menjadi Auth::id()
        $previewData = ClinicVisitImport::where('uploaded_by', Auth::id())->get();
        
        if ($previewData->isEmpty()) {
            return redirect()->route('import.form')->with('error', 'Tidak ada data klinik yang terbaca.');
        }

        foreach ($previewData as $row) {
            $existing = ClinicVisit::where('no_rm', $row->no_rm)
                        ->where('tanggal_kunjungan', $row->tanggal_kunjungan)
                        ->where('klinik', $row->klinik)
                        ->first();

            if (!$existing) {
                $row->status_import = 'NEW';
                $row->class_row = 'bg-green-50';
            } else {
                $isDifferent = (
                    trim((string)$row->catatan) !== trim((string)$existing->catatan) ||
                    trim((string)$row->diagnosis) !== trim((string)$existing->diagnosis) ||
                    trim((string)$row->stadium) !== trim((string)$existing->stadium)
                );

                if ($isDifferent) {
                    $row->status_import = 'UPDATE';
                    $row->class_row = 'bg-yellow-50';
                    $row->old_data = $existing; 
                } else {
                    $row->status_import = 'SAME';
                    $row->class_row = 'bg-gray-100 text-gray-400';
                }
            }
        }
        
        $previewData = $previewData->sortBy(fn($row) => match($row->status_import) {'UPDATE'=>1, 'NEW'=>2, 'SAME'=>3});

        return view('import_review', ['previewData' => $previewData, 'type' => 'KLINIK']);
    }

    // --- Sub-Fungsi: Review Database Pasien ---
    private function reviewDatabase()
    {
        // PERBAIKAN: Ganti auth()->id() menjadi Auth::id()
        $previewData = PatientDatabaseImport::where('uploaded_by', Auth::id())->get();

        if ($previewData->isEmpty()) {
            return redirect()->route('import.form')->with('error', 'Tidak ada data pasien yang terbaca.');
        }

        foreach ($previewData as $row) {
            $existing = PatientDatabase::where('no_rm', $row->no_rm)->first();

            if (!$existing) {
                $row->status_import = 'NEW';
                $row->class_row = 'bg-green-50';
            } else {
                $isDifferent = (
                    trim((string)$row->nama_pasien) !== trim((string)$existing->nama_pasien) ||
                    trim((string)$row->alamat) !== trim((string)$existing->alamat) ||
                    trim((string)$row->telepon) !== trim((string)$existing->telepon)
                );

                if ($isDifferent) {
                    $row->status_import = 'UPDATE';
                    $row->class_row = 'bg-yellow-50';
                    $row->old_data = $existing;
                } else {
                    $row->status_import = 'SAME';
                    $row->class_row = 'bg-gray-100 text-gray-400';
                }
            }
        }

        $previewData = $previewData->sortBy(fn($row) => match($row->status_import) {'UPDATE'=>1, 'NEW'=>2, 'SAME'=>3});

        return view('import_review', ['previewData' => $previewData, 'type' => 'DATABASE']);
    }

    /**
     * 4. HAPUS BARIS SEMENTARA
     */
    public function destroyTemp($id)
    {
        $type = session('import_type');
        $userId = Auth::id(); // PERBAIKAN: Gunakan Auth::id()

        if ($type == 'KLINIK') {
            ClinicVisitImport::where('id', $id)->where('uploaded_by', $userId)->delete();
        } elseif ($type == 'DATABASE') {
            PatientDatabaseImport::where('id', $id)->where('uploaded_by', $userId)->delete();
        }

        return back()->with('success', 'Baris data berhasil dihapus dari daftar import.');
    }

    /**
     * 5. COMMIT (Simpan Permanen ke Database Utama)
     */
    public function commit()
    {
        $type = session('import_type');
        $userId = Auth::id(); // PERBAIKAN: Gunakan Auth::id()

        if ($type == 'KLINIK') {
            $this->commitKlinik($userId);
        } elseif ($type == 'DATABASE') {
            $this->commitDatabase($userId);
        } else {
            return redirect()->route('import.form')->with('error', 'Tipe import hilang. Silakan upload ulang.');
        }

        session()->forget('import_type');

        return redirect()->route('import.form')->with('success', 'Import Data Berhasil Disimpan!');
    }

    // --- Sub-Fungsi: Simpan Klinik ---
    private function commitKlinik($userId)
    {
        $tempData = ClinicVisitImport::where('uploaded_by', $userId)->get();

        foreach ($tempData as $row) {
            ClinicVisit::updateOrCreate(
                [
                    'no_rm'             => $row->no_rm,
                    'tanggal_kunjungan' => $row->tanggal_kunjungan,
                    'klinik'            => $row->klinik,
                ],
                [
                    'nama_pasien'       => $row->nama_pasien,
                    'dpjp'              => $row->dpjp,
                    'new_patient'       => $row->new_patient,
                    'catatan'           => $row->catatan,
                    'updating_nurse'    => $row->updating_nurse,
                    'program'           => $row->program,
                    'surat_rujukan'     => $row->surat_rujukan,
                    'sumber_informasi'  => $row->sumber_informasi,
                    'ttl'               => $row->ttl,
                    'jenis_kelamin'     => $row->jenis_kelamin ?: null,
                    'alamat'            => $row->alamat ?? '-',
                    'kota_area'         => $row->kota_area ?? '-',
                    'alamat_domisili'   => $row->alamat_domisili ?? '-',
                    'telepon'           => $row->telepon ?? '-',
                    'diagnosis'         => $row->diagnosis,
                    'cancer_category'   => $row->cancer_category,
                    'stadium'           => $row->stadium,
                    'dosis_fraksi'      => $row->dosis_fraksi,
                    'teknik_rt'         => $row->teknik_rt,
                    'surgery_type'      => $row->surgery_type,
                    'hospital'          => $row->hospital,
                    'chemo_status'      => $row->chemo_status,
                    'hospital2'         => $row->hospital2,
                    'checker'           => $row->checker,
                    'usia'              => $row->usia,
                ]
            );
        }
        ClinicVisitImport::where('uploaded_by', $userId)->delete();
    }

    // --- Sub-Fungsi: Simpan Database Pasien ---
    private function commitDatabase($userId)
    {
        $tempData = PatientDatabaseImport::where('uploaded_by', $userId)->get();

        foreach ($tempData as $row) {
            PatientDatabase::updateOrCreate(
                ['no_rm' => $row->no_rm], 
                [
                    'name_of_patient'       => $row->name_of_patient,
                    'diagnosis'             => $row->diagnosis,
                    'age'                   => $row->age,
                    'overseas_hospital'     => $row->overseas_hospital,
                    'source_information_ro' => $row->source_information_ro,
                    'new_ro_clinic'         => $row->new_ro_clinic,
                    'new_rt'                => $row->new_rt,
                    'reason'                => $row->reason,
                    'source_information_mo' => $row->source_information_mo,
                    'new_mo_clinic'         => $row->new_mo_clinic,
                    'new_chemo'             => $row->new_chemo,
                    'reason2'               => $row->reason2,
                    'source_information_bo' => $row->source_information_bo,
                    'new_bo_clinic'         => $row->new_bo_clinic,
                    'source_information_go' => $row->source_information_go,
                    'new_go_clinic'         => $row->new_go_clinic,
                    'source_information_po' => $row->source_information_po,
                    'new_po_clinic'         => $row->new_po_clinic,
                    'source_information_ao' => $row->source_information_ao,
                    'new_ao_clinic'         => $row->new_ao_clinic,
                    'activities_notes'      => $row->activities_notes,
                    'activities_notes2'     => $row->activities_notes2,
                    'activities_notes3'     => $row->activities_notes3,
                    'activities_notes4'     => $row->activities_notes4,
                    'activities_notes5'     => $row->activities_notes5,
                ]
            );
        }
        PatientDatabaseImport::where('uploaded_by', $userId)->delete();
    }
}