<?php

namespace App\Http\Controllers\RepoCat;

use App\Models\Event;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DetailEvent;
use App\Models\DetailTilok;

class DetailEventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Halaman Detail Event';
        $event = Event::all();
        $detail_event = DetailEvent::with(['event'])->get();
        $tilok = DetailEvent::count('tilok');
        return view('detail_event.index', compact('title', 'event', 'detail_event', 'tilok'));
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
        //     'event_id' => 'required|numeric',
        //     'tilok' => 'required|string|max:100',
        //     'jadwal' => 'required'
        // ]);

        if($request->file('dok_survey')->isValid())
        {
            $file = $request->file('dok_survey');
            $extension = $file->getClientOriginalExtension();
            $slug = Str::slug($request->tilok);
            $newNameSurvey = 'dok-survey/' . $slug . "-" . date('d-m-Y').".".$extension;
            $uploadPath = env('UPLOAD_PATH')."/dok-survey";
            $request->file('dok_survey')->move($uploadPath, $newNameSurvey);
        }

        if($request->file('dok_sktim')->isValid())
        {
            $file = $request->file('dok_sktim');
            $extension = $file->getClientOriginalExtension();
            $slug = Str::slug($request->tilok);
            $newNameSktim = 'dok-sktim/' . $slug . "-" . date('d-m-Y').".".$extension;
            $uploadPath = env('UPLOAD_PATH')."/dok-sktim";
            $request->file('dok_sktim')->move($uploadPath, $newNameSktim);
        }

        if($request->file('dok_laporan')->isValid())
        {
            $file = $request->file('dok_laporan');
            $extension = $file->getClientOriginalExtension();
            $slug = Str::slug($request->tilok);
            $newNameLaporan = 'dok-laporan/' . $slug . "-" . date('d-m-Y').".".$extension;
            $uploadPath = env('UPLOAD_PATH')."/dok-laporan";
            $request->file('dok_laporan')->move($uploadPath, $newNameLaporan);
        }

        $data = DetailEvent::create([
            'event_id' => $request->event_id,
            'tilok' => $request->tilok,
            'jadwal' => $request->jadwal,
            'dok_survey' => $newNameSurvey,
            'dok_sktim' => $newNameSktim,
            'dok_laporan' => $newNameLaporan,
        ]);

        return back()->with('success', 'Data Berhasil Di input');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $title = 'Detail Keseluruhan';
        $data = DetailEvent::findOrFail(decrypt($id));

        $detail_tilok = DetailTilok::with(['detailEvent'])->where('detail_event_id', decrypt($id))->get();
        $hadir_tilok = DetailTilok::where('detail_event_id', decrypt($id))->sum('hadir');
        $tidak_hadir_tilok = DetailTilok::where('detail_event_id', decrypt($id))->sum('tidak_hadir');
        $tertinggi_tilok = DetailTilok::where('detail_event_id', decrypt($id))->max('nilai_tertinggi');
        $terendah_tilok = DetailTilok::where('detail_event_id', decrypt($id))->min('nilai_terendah');
        $sesi = DetailTilok::where('detail_event_id', decrypt($id))->count('sesi');
        $jumlah = DetailTilok::where('detail_event_id', decrypt($id))->sum('jumlah');

        return view('detail_event.create', compact('data', 'title', 'detail_tilok', 'hadir_tilok',
    'tidak_hadir_tilok', 'jumlah',
    'tertinggi_tilok', 'terendah_tilok', 'sesi'));
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
        //
    }
}
