<?php

namespace App\Http\Controllers\RepoCat;

use App\Http\Controllers\Controller;
use App\Models\DetailTilok;
use Illuminate\Http\Request;

class DetailTilokController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $data = DetailTilok::create([
            'detail_event_id' => $request->detail_event_id,
            'tilok' => $request->tilok,
            'tanggal' => $request->tanggal,
            'instansi' => $request->instansi,
            'sesi' => $request->sesi,
            'hadir' => $request->hadir,
            'tidak_hadir' => $request->tidak_hadir,
            'nilai_tertinggi' => $request->nilai_tertinggi,
            'nilai_terendah' => $request->nilai_terendah,
            'jumlah' => $request->hadir + $request->tidak_hadir,
        ]);

        return back()->with('success', 'Berhasil Input Data');
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
}
