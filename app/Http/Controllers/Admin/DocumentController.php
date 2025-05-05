<?php

namespace App\Http\Controllers\Admin;

use App\Models\Type;
use App\Models\User;
use App\Models\Document;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Models\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
				
			$inka = Auth::user()->role == 'inka';
			$type = Type::with(['user'])->orderBy('created_at', 'ASC')->get();
			if(Auth::user()->role == 'inka'){
				if(decrypt($id) == 1){
					$title = 'Surat Masuk';
					$document = Document::with(['user', 'type', 'action'])
					->orderBy('created_at', 'DESC')
					// ->selectRaw('DATE_FORMAT(created_at, "%Y-%m-%d %H:%i:00")')
					// ->orderBy('tgl_distribusi', [
					// 	Carbon::now()->startOfMonth(),
					// 	Carbon::now()->endOfMonth()
					// ])
					->where('jenis_surat', 'surat_masuk')
					->where('unit', Auth::user()->role)
					->get();

				// 	$document = Document::orderBy('tgl_distribusi')
				// 	->get()
				// 	->groupBy(function($data) {
        //     return \Carbon\Carbon::parse($data->created_at)->format('F');
        // });
					
					$user = User::orderBy('name', 'ASC')
					->where('role', Auth::user()->role)
					->get();
					// $document = User::with(['document'])->get();
				}else{
					$title = 'Surat Keluar';
					$document = Document::with(['user', 'type', 'action'])
					->orderBy('created_at', 'ASC')
					->where('jenis_surat', 'surat_keluar')
					->get();
				}
			}elseif(Auth::user()->hasRole('admin')){
				if(decrypt($id) == 1){
					$title = 'Surat Masuk';
					$document = Document::with(['user', 'type'])
					->orderByRaw('SUBSTRING(tgl_distribusi, 4, 5) DESC')
					->orderByRaw('SUBSTRING(tgl_distribusi, 1, 2) DESC')
					// orderByRaw('DAY(tgl_distribusi) ASC')
					->where('jenis_surat', 'surat_masuk')
					->get();
					$user = User::orderBy('name', 'ASC')
					->get();
					// $document = User::with(['document'])->get();
				}else{
					$title = 'Surat Keluar';
					$document = Document::with(['user', 'type'])
					->orderBy('created_at', 'ASC')
					->where('jenis_surat', 'surat_keluar')
					->get();
				}
			}
				$person = User::orderBy('name', 'DESC')->where('id', Auth::user()->role == 'inka')->get();
				return view('document.index', compact('type', 'document', 'title', 'id'));
    }

		

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
					'type_id' => 'required',
					'number' => 'required|unique:documents',
					'name' => 'required',
					'date' => 'required|date',
					'tgl_distribusi' => 'required|date',
					'sifat' => 'required', 
				], [
					'number.required' => 'Nomor Dokumen Masih Kosong',
					'number.unique' => 'Nomor Dokumen sudah ada'
				]);

				if($validator->fails()){
					return redirect()->back()->withErrors($validator)->withInput();
				}

				if($request->file('file')->isValid())
					{
						$file = $request->file('file');
						$name = $request->name;
						$extension = $file->getClientOriginalExtension();
						$slug = Str::slug($request->name);
						$newName = 'document/' . $slug . "-" . date('d-m-Y').".".$extension;
						$uploadPath = env('UPLOAD_PATH')."/document";
						$request->file('file')->move($uploadPath, $newName);
						// $data['file'] = $newName;
						// $newName = $name . "." . $extension;
						// $uploads = Storage::putFileAs('public/file', $request->file('file'), $newName);
					}

				if(Auth::user()->role = 'inka'){
					if($request->id == 1){
						$data = Document::create([
							'user_id' => Auth::user()->id,
							'type_id' => $request->type_id,
							'name' => $request->name,
							'date' => Carbon::createFromFormat('m/d/Y', $request->date)
							->format('d-m-Y'),
							'number' => $request->number,
							'file' => $newName,
							'jenis_surat' => 'surat_masuk',
							'tgl_distribusi' => Carbon::createFromFormat('m/d/Y', $request->tgl_distribusi)
							->format('d-m-Y'),
							'asal' => $request->asal,
							'disposisi' => $request->disposisi,
							'unit' => 'inka',
							'sifat' => $request->sifat
						]);
					}else{
						$data = Document::create([
							'user_id' => Auth::user()->id,
							'type_id' => $request->type_id,
							'name' => $request->name,
							'date' => Carbon::createFromFormat('m/d/Y', $request->date)
							->format('d-m-Y'),
							'number' => $request->number,
							'file' => $newName,
							'jenis_surat' => 'surat_keluar',
							'tgl_distribusi' => Carbon::createFromFormat('m/d/Y', $request->tgl_distribusi)
							->format('d-m-Y'),
							'asal' => 'surat_keluar',
							'disposisi' => 'surat_keluar',
							'unit' => 'inka',
							'sifat' => $request->sifat
						]);
					}
				}elseif(Auth::user()->role = 'admin'){
					if($request->id == 1){
						$data = Document::create([
							'user_id' => Auth::user()->id,
							'type_id' => $request->type_id,
							'name' => $request->name,
							'date' => Carbon::parse($request->date),
							'number' => $request->number,
							'file' => $newName,
							'jenis_surat' => 'surat_masuk',
							'tgl_distribusi' => Carbon::parse($request->tgl_distribusi),
							'asal' => $request->asal,
							'disposisi' => $request->disposisi,
							'unit' => 'admin',
							'sifat' => $request->sifat
						]);
					}else{
						$data = Document::create([
							'user_id' => Auth::user()->id,
							'type_id' => $request->type_id,
							'name' => $request->name,
							'date' => Carbon::createFromFormat('m/d/Y', $request->date)
							->format('d-m-Y'),
							'number' => $request->number,
							'file' => $newName,
							'jenis_surat' => 'surat_keluar',
							'tgl_distribusi' => Carbon::createFromFormat('m/d/Y', $request->tgl_distribusi)
							->format('d-m-Y'),
							'asal' => 'surat_keluar',
							'disposisi' => 'surat_keluar',
							'unit' => 'admin',
							'sifat' => $request->sifat
						]);
					}
				}

				return back()->with('success', 'Data Berhasil Diinput');
    
			}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Document::findOrFail(decrypt($id));
				$action = Action::orderBy('created_at', 'DESC')->where('document_id', decrypt($id))
				->get();
				return view('document.show', compact('data', 'action'));
    }

		public function action(Request $request)
		{
				Action::create([
					'document_id' => $request->document_id,
					'followup' => $request->followup,
					'description' => $request->description
				]);

				return back()->with('success', 'Data Berhasil Diinput');

		}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $document = Document::findOrFail(decrypt($id));
				$document->delete();
				Storage::disk('local')->delete(storage_path('../../' . $document->file));

				return back()->with('success', 'Data Berhasil Dihapus');

    }
}
