<?php

namespace App\Http\Controllers;

use App\Models\PatientDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Tambahkan ini

class PatientController extends Controller
{
    public function index(Request $request)
    {
        // Gunakan Eloquent Query
        $query = PatientDatabase::query();

        // SELECT Manual untuk memastikan kolom terambil semua
        // Terkadang Eloquent 'malas' mengambil semua kolom jika ada Cache
        $query->select('*'); 

        // Search Logic
        if ($request->has('search') && $request->search != '') {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                // Cek kedua kemungkinan nama kolom untuk pencarian
                $q->where('name_of_patient', 'like', '%' . $keyword . '%')
                  ->orWhere('no_rm', 'like', '%' . $keyword . '%');
            });
        }

        // Eager Load 'visits' & 'radioConverted' & 'kemoConverted'
        // Kita juga load relasi converted agar query tidak n+1 (lambat)
        $patients = $query->with([
            'visits' => function($q) {
                $q->orderBy('tanggal_kunjungan', 'asc');
            },
            'radioConverted', // Load relasi Radioterapi
            'kemoConverted'   // Load relasi Kemoterapi
        ])
        ->latest()
        ->paginate(15)
        ->appends($request->query());
        
        return view('patients.index', compact('patients'));
    }
}