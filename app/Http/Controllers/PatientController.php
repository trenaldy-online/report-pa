<?php

namespace App\Http\Controllers;

use App\Models\PatientDatabase;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $query = PatientDatabase::query();

        // Search
        if ($request->has('search') && $request->search != '') {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('name_of_patient', 'like', '%' . $keyword . '%')
                  ->orWhere('no_rm', 'like', '%' . $keyword . '%');
            });
        }

        // Query Utama + Load Visits (Diurutkan Tanggal Terlama -> Terbaru)
        $patients = $query->with(['visits' => function($q) {
            $q->orderBy('tanggal_kunjungan', 'asc');
        }])->latest()->paginate(15)->appends($request->query());
        
        return view('patients.index', compact('patients'));
    }
}