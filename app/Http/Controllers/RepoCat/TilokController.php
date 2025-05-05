<?php

namespace App\Http\Controllers\RepoCat;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventTilok;
use App\Models\Tilok;
use Illuminate\Http\Request;

class TilokController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Halaman Tilok';
        $data = Tilok::all();
        $event = Event::all();
        $eventTilok = EventTilok::all();
        return view('tilok.index', compact('title', 'data', 'eventTilok', 'event'));
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
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'name' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            // 'year' => 'required|numeric'
        ]);

        $data = Tilok::create([
            'name' => $request->name,
            'address' => $request->address,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        $data->events()->attach($request->event_id);

        

        // $event_tilok = EventTilok::create([
        //     'event_id' => $request->event
        // ])

        return back()->with('success', 'Tilok Berhasil Diinput');

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
