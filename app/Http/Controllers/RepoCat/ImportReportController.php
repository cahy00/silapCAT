<?php

namespace App\Http\Controllers\RepoCat;

use Illuminate\Http\Request;
use App\Imports\ReportImport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class ImportReportController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);
    
        Excel::import(new ReportImport, $request->file('file'));
    return back()->with('success', 'Data laporan berhasil diimpor!');
    }
}
