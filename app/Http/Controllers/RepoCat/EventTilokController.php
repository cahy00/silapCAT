<?php

namespace App\Http\Controllers\RepoCat;

use App\Models\Event;
use App\Models\Tilok;
use App\Models\EventTilok;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class EventTilokController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Test';
        if(Auth::user()->hasRole('admin')){
            $event_tilok = EventTilok::with(['event', 'tilok'])->get();
            
        }else{
            $event_tilok = Auth::user()->eventTiloks()->with(['event', 'tilok'])->get();

        }
        return view('event_tilok.index', compact('title', 'event_tilok'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Halaman Event';
        $event = Event::all();
        $tilok = Tilok::all();


        if(Auth::user()->hasRole('admin')){
            $event_tilok = EventTilok::with(['event', 'tilok'])->get();
            
        }else{
            $event_tilok = Auth::user()->eventTiloks()->with(['event', 'tilok'])->get();

        }
        return view('event_tilok.index', compact('event', 'tilok', 'title', 'event_tilok'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = EventTilok::create([
            'event_id' => $request->event_id,
            'tilok_id' => $request->tilok_id,
        ]);

        return back()->with('success', 'Event Berhasil Diinput');

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
