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
        // $detail_tilok = Report::with(['event', 'tilok'])->get();
        
        $hadir_tilok = Report::sum('participant_present');
        $tidak_hadir_tilok = Report::sum('participant_absent');
        $tertinggi_tilok = Report::max('highest_score');
        $terendah_tilok = Report::min('lowest_score');
        $sesi = Report::count('session');
        $jumlah = Report::sum('participant_total');

        $eventId = $request->input('event_id'); //ambil request dari form filter
        $tilokId = $request->input('tilok_id'); //ambil request dari form filter
        $fromDate = $request->input('from_date'); //ambil request dari form filter
        $toDate = $request->input('to_date'); //ambil request dari form filter
        
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
            ->when($fromDate && $toDate, function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('exam_date', [$fromDate, $toDate]);
            })
            ->when($fromDate && !$toDate, function ($query, $fromDate) {
                $query->whereDate('exam_date','>=', $fromDate);
            })
            ->when($toDate && !$fromDate, function ($query, $toDate) {
                $query->whereDate('exam_date','>=', $toDate);
            })
            ->selectRaw(
                <<<SQL
                    event_id,
                    tilok_id,
                    GROUP_CONCAT(DISTINCT instansi_name SEPARATOR ', ') AS instansi_list,
                    SUM(participant_total)   AS total_participant,
                    SUM(participant_present) AS total_present,
                    SUM(participant_absent)  AS total_absent,
                    MAX(highest_score)       AS highest_score,
                    MIN(lowest_score)        AS lowest_score
                SQL)
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
            // 'detail_tilok', 
            'hadir_tilok', 
            'tidak_hadir_tilok', 
            'jumlah', 
            'tertinggi_tilok', 
            'terendah_tilok', 
            'sesi',
            'eventId',
            'tilok',
            'tilokId',
            // 'query'
            // 'document'

        ));
    }

    // public function filter()
    // {

    // }
}
