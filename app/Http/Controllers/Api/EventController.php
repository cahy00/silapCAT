<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class EventController extends Controller
{
    /**
     * Get a list of all events with detailed analytical data.
     * 
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        // Fetch events with relations needed for dates and scores
        $events = Event::with(['eventLocations', 'examScores'])->get();

        $data = $events->map(function ($event) {
            
            // Use global dates from event
            $startDate = $event->start_date ? Carbon::parse($event->start_date)->format('Y-m-d') : null;
            $endDate = $event->end_date ? Carbon::parse($event->end_date)->format('Y-m-d') : null;

            // Analytics logic
            $totalParticipants = $event->examScores->count();
            
            // We assume a participant is "Hadir" (present) if they have a cat_score > 0 or total_score > 0 
            // Or if they explicitly do not have a "Tidak Hadir" / "TH" note
            $presentParticipants = $event->examScores->filter(function ($score) {
                $notesLower = strtolower($score->notes ?? '');
                if (str_contains($notesLower, 'tidak hadir') || str_contains($notesLower, 'th') || $notesLower === 'absen') {
                    return false;
                }
                return true;
            })->count();
            
            $absentParticipants = $totalParticipants - $presentParticipants;
            
            // Getting highest and lowest scores among those who are present
            $highestScore = $event->examScores->max('total_score') ?? 0;
            
            // For lowest score, we only consider those who actually have a score > 0
            $validScores = $event->examScores->where('total_score', '>', 0);
            $lowestScore = $validScores->count() > 0 ? $validScores->min('total_score') : 0;

            return [
                'id' => $event->id,
                'name' => $event->name,
                'status' => $event->status,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'statistics' => [
                    'total_participants' => $totalParticipants,
                    'present' => $presentParticipants,
                    'absent' => $absentParticipants,
                    'highest_score' => (float) $highestScore,
                    'lowest_score' => (float) $lowestScore,
                ],
                'created_at' => $event->created_at ? $event->created_at->toIso8601String() : null,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Data events berhasil diambil',
            'data' => $data
        ], 200);
    }
}
