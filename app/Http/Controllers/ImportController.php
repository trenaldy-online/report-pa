<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\KlinikImport;
use App\Imports\DatabaseImport;
// use App\Imports\DatabaseImport; (Nanti dibuat)

class ImportController extends Controller
{
    public function showForm()
    {
        return view('import');
    }

    public function process(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
            'type' => 'required'
        ]);

        $file = $request->file('file');
        $type = $request->type;

        if ($type == 'KLINIK') {
            Excel::import(new KlinikImport, $file);
        } 
        else if ($type == 'DATABASE') {
            Excel::import(new DatabaseImport, $file);
        }
        // else if ($type == 'DATABASE') { ... }

        return back()->with('success', 'Data berhasil diimport!');
    }
}