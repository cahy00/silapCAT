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
        // Fetch events with relations needed for dates, reports and scores
        $events = Event::with(['eventLocations', 'examScores', 'reports'])->get();

        $data = $events->map(function ($event) {
            
            // Use global dates from event
            $startDate = $event->start_date ? Carbon::parse($event->start_date)->format('Y-m-d') : null;
            $endDate = $event->end_date ? Carbon::parse($event->end_date)->format('Y-m-d') : null;

            $reports = $event->reports;

            if ($reports && $reports->isNotEmpty()) {
                $presentParticipants = (int) $reports->sum('present_count');
                $absentParticipants = (int) $reports->sum('absent_count');
                $totalParticipants = (int) ($reports->sum('total_participants') ?: ($presentParticipants + $absentParticipants));
                $highestScore = (float) ($reports->max('highest_score') ?? 0);
                $validLowest = $reports->whereNotNull('lowest_score')->where('lowest_score', '>', 0);
                $lowestScore = (float) ($validLowest->isNotEmpty() ? $validLowest->min('lowest_score') : ($reports->min('lowest_score') ?? 0));
            } else {
                // Analytics logic fallback to examScores
                $totalParticipants = $event->examScores->count();
                
                $presentParticipants = $event->examScores->filter(function ($score) {
                    $notesLower = strtolower($score->notes ?? '');
                    if (str_contains($notesLower, 'tidak hadir') || str_contains($notesLower, 'th') || $notesLower === 'absen') {
                        return false;
                    }
                    return true;
                })->count();
                
                $absentParticipants = $totalParticipants - $presentParticipants;
                $highestScore = $event->examScores->max('total_score') ?? 0;
                $validScores = $event->examScores->where('total_score', '>', 0);
                $lowestScore = $validScores->count() > 0 ? $validScores->min('total_score') : 0;
            }

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
