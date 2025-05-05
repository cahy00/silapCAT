<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Charts\Chart\SuratMasukChart;
use App\Models\Type;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        return view('admin.dashboard');
    }

		public function index()
		{
				$surat_masuk_bulanan = Document::with(['user', 'type'])->where('jenis_surat', 'surat_masuk')
				->GroupBy(DB::raw("Month(created_at)"))
				->count();
				$bulan = Document::select(DB::raw("MONTHNAME(created_at) as bulan"))
				->GroupBy(DB::raw("MONTHNAME(created_at)"))
				->pluck('bulan');
				
				if(Auth::user()->role == 'inka'){
					$surat_keluar = Document::with(['user', 'type'])
					->where('jenis_surat', 'surat_keluar')
					->where('unit', 'inka')
					->count();
					$surat_masuk = Document::with(['user', 'type'])
					->where('jenis_surat', 'surat_masuk')
					->where('unit', 'inka')
					->count();
					$user = User::where('role', 'inka')->count();
					$type = Type::count();
					// $data ['suratMasukChart'] = $suratMasukChart->build();
				}elseif(Auth::user()->role == 'pdsk'){
					$surat_keluar = Document::with(['user', 'type'])
					->where('jenis_surat', 'surat_keluar')
					->where('unit', 'pdsk')
					->count();
					$surat_masuk = Document::with(['user', 'type'])
					->where('jenis_surat', 'surat_masuk')
					->where('unit', 'pdsk')
					->count();
					$user = User::where('role', 'pdsk')->count();
					$type = Type::count();
				}else{
					$surat_keluar = Document::with(['user', 'type'])
					->where('jenis_surat', 'surat_keluar')
					->count();
					$surat_masuk = Document::with(['user', 'type'])
					->where('jenis_surat', 'surat_masuk')
					->count();
					$user = User::count();
					$type = Type::count();
				}
				return view('admin.dashboard', compact('surat_masuk', 'surat_keluar', 'surat_masuk_bulanan',
				'bulan', 
				'user',
			'type'));
			}
			
		}
		// dd($bulan);
		// $surat_masuk_bulanan = Document::where('jenis_surat', 'surat_masuk_bulanan')
		// ->select(DB::raw("CAST(COUNT(id) as int) as surat_masuk_bulanan"))
		// ->GroupBy(DB::raw("Month(created_at)"))
		// ->pluck('surat_masuk_bulanan');
		// dd($surat_masuk_bulanan);
