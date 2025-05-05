<?php

namespace App\Http\Controllers\RepoCat;

use App\Models\Event;
use App\Models\Tilok;
use App\Models\Report;
use Illuminate\Http\Request;
use App\Models\TilokDocument;
use App\Http\Controllers\Controller;

class RecapController extends Controller
{
    public function index(Request $request)
    {
        $detail_tilok = Report::with(['event', 'tilok'])->get();
        $hadir_tilok = Report::sum('participant_present');
        $tidak_hadir_tilok = Report::sum('participant_absent');
        $tertinggi_tilok = Report::max('highest_score');
        $terendah_tilok = Report::min('lowest_score');
        $sesi = Report::count('session');
        $jumlah = Report::sum('participant_total');

        $eventId = $request->input('event_id'); //ambil request dari form filter
        $tilokId = $request->input('tilok_id'); //ambil request dari form filter


        $event = Event::with(['tiloks'])->orderBy('created_at', 'ASC')->get();
        $tilok = Tilok::with(['events'])->orderBy('created_at', 'ASC')->get();
        $title = 'Halaman Rekapitulasi';
        $reports = Report::with(['event', 'tilok.documents'])
            ->when($eventId, function($query, $eventId){
                $query->where('event_id', $eventId);
            })
            ->when($tilokId, function ($query, $tilokId) {
                $query->where('tilok_id', $tilokId);
            })
            ->selectRaw('event_id, tilok_id, SUM(participant_total) as total_participant, SUM(participant_present) as total_present, SUM(participant_absent) as total_absent, MAX(highest_score) as highest_score, MIN(lowest_score) as lowest_score')
            ->groupBy('event_id', 'tilok_id')
            ->get()
            ->map(function ($report) {
                // Hitung status kelengkapan dokumen
                $requiredDocuments = ['Laporan Pelaksanaan'];

                if(!$report->tilok){
                    $report->is_complete = false;
                    return $report;
                }

                $uploadedDocuments = $report->tilok->documents->pluck('document_name')->toArray();
    
                $report->is_complete = count(array_diff($requiredDocuments, $uploadedDocuments)) === 0;
                return $report;
            });

        // $document = TilokDocument::with(['event', 'tilok'])
        // ->where('tilok_id', $id)
        // // ->where('event_id', $id)
        // ->orderBy('created_at', 'ASC')
        // ->get();

        return view('recap.index', compact(
            'event',
            'title', 
            'reports',
            'detail_tilok', 
            'hadir_tilok', 
            'tidak_hadir_tilok', 
            'jumlah', 
            'tertinggi_tilok', 
            'terendah_tilok', 
            'sesi',
            'eventId',
            'tilok',
            'tilokId',
            // 'document'

        ));
    }

    // public function filter()
    // {

    // }
}
