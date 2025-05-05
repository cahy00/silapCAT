<?php

namespace App\Http\Controllers\RepoCat;

use App\Models\Event;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Validated;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Halaman Event';
        $data = Event::all();
        return view('event.index', compact('title', 'data'));
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
            'name' => 'required|string|unique:events',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            // 'year' => 'required|numeric'
        ]);

        $data = Event::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
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
        $data_event = Event::findOrFail(decrypt($id));
        $title = 'Halaman Update Event';
        $data = Event::all();
        return view('event.edit', compact('title', 'data', 'data_event'));
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
        $data_event = Event::findOrFail(decrypt($id));
        $data_event->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'year' => $request->year
        ]);

        return redirect('/event')->with('success', 'Event Berhasil Diinput');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Event::findOrFail(decrypt($id));
        $data->delete();

        return redirect('/event')->with('success', 'Data Berhasil Dihapus');
    }
}
