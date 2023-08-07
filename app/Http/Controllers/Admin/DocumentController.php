<?php

namespace App\Http\Controllers\Admin;

use App\Models\Type;
use App\Models\User;
use App\Models\Document;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
					$document = Document::with(['user', 'type'])
					->orderBy('created_at', 'ASC')
					->where('jenis_surat', 'surat_masuk')
					->where('unit', Auth::user()->role)
					->get();
					$user = User::orderBy('name', 'ASC')
					->where('role', Auth::user()->role)
					->get();
					// $document = User::with(['document'])->get();
				}else{
					$title = 'Surat Keluar';
					$document = Document::with(['user', 'type'])
					->orderBy('created_at', 'ASC')
					->where('jenis_surat', 'surat_keluar')
					->get();
				}
			}elseif(Auth::user()->role == 'admin'){
				if(decrypt($id) == 1){
					$title = 'Surat Masuk';
					$document = Document::with(['user', 'type'])
					->orderBy('created_at', 'ASC')
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
        // $validator = $request->validate([
				// 	'type_id' => 'required',
				// 	'number' => 'required'
				// ], [
				// 	'number.required' => 'Nomor Dokumen Masih Kosong'
				// ]);

				// if($validator){
				// 	return redirect()->back()->withErrors($validator)->withInput();
				// }

				if($request->file('file')->isValid())
					{
						$file = $request->file('file');
						$name = $request->name;
						$extension = $file->getClientOriginalExtension();
						$slug = Str::slug($request->name);
						$newName = 'document/' . date('YmdHis').".".$extension;
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
							'date' => $request->date,
							'number' => $request->number,
							'file' => $newName,
							'jenis_surat' => 'surat_masuk',
							'tgl_distribusi' => $request->tgl_distribusi,
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
							'date' => $request->date,
							'number' => $request->number,
							'file' => $newName,
							'jenis_surat' => 'surat_keluar',
							'tgl_distribusi' => $request->tgl_distribusi,
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
							'date' => $request->date,
							'number' => $request->number,
							'file' => $newName,
							'jenis_surat' => 'surat_masuk',
							'tgl_distribusi' => $request->tgl_distribusi,
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
							'date' => $request->date,
							'number' => $request->number,
							'file' => $newName,
							'jenis_surat' => 'surat_keluar',
							'tgl_distribusi' => $request->tgl_distribusi,
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
				return view('document.show', compact('data'));
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
