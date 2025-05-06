<?php

namespace App\Http\Controllers\RepoCat;

use App\Models\Report;
use App\Models\EventTilok;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TilokDocument;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('report.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $title = 'Halaman Report';
        $data = EventTilok::findOrFail($id);

        $document = TilokDocument::with(['event', 'tilok'])
        ->where('tilok_id', $id)
        // ->where('event_id', $id)
        ->orderBy('created_at', 'ASC')
        ->get();

        $user = Auth::user();


        $detail_tilok = Report::with(['event', 'tilok'])->where('tilok_id', $data->tilok_id)->where('event_id', $data->event_id)->get();
        // $detail_tilok = Report::with(['event', 'tilok'])->whereIn('tilok_id', $user->eventTiloks->pluck('tilok_id'))->get();
        // $detail_tilok = Report::whereHas('eventTilok', function ($query) use ($user) {
        //     $query->whereIn('id', $user->eventTiloks->pluck('id'));
        // })->with(['eventTilok.event', 'eventTilok.tilok'])->get();
        $hadir_tilok = Report::where('tilok_id', $data->tilok_id)->where('event_id', $data->event_id)->sum('participant_present');
        $tidak_hadir_tilok = Report::where('tilok_id', $data->tilok_id)->where('event_id', $data->event_id)->sum('participant_absent');
        $tertinggi_tilok = Report::where('tilok_id', $data->tilok_id)->where('event_id', $data->event_id)->max('highest_score');
        $terendah_tilok = Report::where('tilok_id', $data->tilok_id)->where('event_id', $data->event_id)->min('lowest_score');
        $sesi = Report::where('tilok_id', $data->tilok_id)->where('event_id', $data->event_id)->count('session');
        $jumlah = Report::where('tilok_id', $data->tilok_id)->where('event_id', $data->event_id)->sum('participant_total');
        return view('report.create', 
        compact('title', 'data','detail_tilok', 
        'hadir_tilok', 'tidak_hadir_tilok', 'jumlah', 
        'tertinggi_tilok', 'terendah_tilok', 'sesi', 
        'document'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'tilok_id' => 'required|exists:tiloks,id',
        ]);

        // $data = Report::create([
        //     'event_id' => $request->event_id,
        //     'tilok_id' => $request->tilok_id,
        //     'instansi_name' => $request->instansi_name,
        //     'exam_date' => $request->exam_date,
        //     'session' => $request->session,
        //     'participant_total' => $request->participant_total,
        //     'participant_present' => $request->participant_present,
        //     'participant_absent' => $request->participant_absent,
        //     'highest_score' => $request->highest_score,
        //     'lowest_score' => $request->lowest_score,
        //     'average_score' => $request->lowest_score + $request->lowest_score/2,
        // ]);

        $common = [
            'event_id'   => $request->event_id,
            'tilok_id'   => $request->tilok_id,
            'instansi_name'=> $request->instansi_name,
            'exam_date'  => $request->exam_date,
        ];

        foreach ($request->reports as $data) {
            // gabungkan exam_date dari atas form
            $data['exam_date'] = $request->exam_date;
            Report::create(array_merge($common, $data));
        }

        return back()->with('success', 'Data Berhasil Di input');
    }

    public function uploadDokumen(Request $request)
    {
        if($request->file('document_path')->isValid())
        {
            $file = $request->file('document_path');
            $extension = $file->getClientOriginalExtension();
            $slug = Str::slug($request->document_name);
            $newNameDocumentTilok = 'dok-laporan-tilok/' . $slug . "-" . date('d-m-Y').".".$extension;
            $uploadPath = env('UPLOAD_PATH')."/dok-laporan-tilok";
            $request->file('document_path')->move($uploadPath, $newNameDocumentTilok);
        }

        $data = TilokDocument::create([
            'event_id' => $request->event_id,
            'tilok_id' => $request->tilok_id,
            'document_name' => $request->document_name,
            'document_path' => $newNameDocumentTilok
        ]);

        return back()->with('success', 'Data Berhasil Di input');
        
    }

    public function showDocument($id)
    {
        return view('report.create', compact('data'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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

    public function createOperator($id)
    {
        $title = 'Halaman Report';
        $data = EventTilok::findOrFail($id);

        $document = TilokDocument::with(['event', 'tilok'])
        ->where('tilok_id', $id)
        // ->where('event_id', $id)
        ->orderBy('created_at', 'ASC')
        ->get();


        $detail_tilok = Report::with(['event', 'tilok'])->where('tilok_id', $data->tilok_id)->where('event_id', $data->event_id)->get();
        $hadir_tilok = Report::where('tilok_id', $data->tilok_id)->where('event_id', $data->event_id)->sum('participant_present');
        $tidak_hadir_tilok = Report::where('tilok_id', $data->tilok_id)->where('event_id', $data->event_id)->sum('participant_absent');
        $tertinggi_tilok = Report::where('tilok_id', $data->tilok_id)->where('event_id', $data->event_id)->max('highest_score');
        $terendah_tilok = Report::where('tilok_id', $data->tilok_id)->where('event_id', $data->event_id)->min('lowest_score');
        $sesi = Report::where('tilok_id', $data->tilok_id)->where('event_id', $data->event_id)->count('session');
        $jumlah = Report::where('tilok_id', $data->tilok_id)->where('event_id', $data->event_id)->sum('participant_total');
        return view('report.create', 
        compact('title', 'data','detail_tilok', 
        'hadir_tilok', 'tidak_hadir_tilok', 'jumlah', 
        'tertinggi_tilok', 'terendah_tilok', 'sesi', 
        'document'));
    }

    public function storeOperator(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'tilok_id' => 'required|exists:tiloks,id',
        ]);

        $data = Report::create([
            'event_id' => $request->event_id,
            'tilok_id' => $request->tilok_id,
            'instansi_name' => $request->instansi_name,
            'exam_date' => $request->exam_date,
            'session' => $request->session,
            'participant_total' => $request->participant_total,
            'participant_present' => $request->participant_present,
            'participant_absent' => $request->participant_absent,
            'highest_score' => $request->highest_score,
            'lowest_score' => $request->lowest_score,
            'average_score' => $request->lowest_score + $request->lowest_score/2,
        ]);

        return back()->with('success', 'Data Berhasil Di input');
    }
}
