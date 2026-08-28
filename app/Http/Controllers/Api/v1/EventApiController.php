<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventApiController extends Controller
{
    /**
     * Display a listing of events with search, filter, and pagination.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Event::with([
            'eventLocations.location',
            'eventInstitutions.institution',
            'reports.eventLocation.location',
            'reports.user',
            'examScores',
        ]);

        // Search by event name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by start date range
        if ($request->filled('start_date')) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('end_date', '<=', $request->end_date);
        }

        $perPage = (int) $request->get('per_page', 15);
        $events = $query->orderBy('start_date', 'desc')->paginate($perPage);

        $transformedItems = $events->getCollection()->map(fn ($event) => $this->formatEvent($event));

        return response()->json([
            'success' => true,
            'message' => 'Daftar kegiatan berhasil diambil.',
            'data' => $transformedItems,
            'meta' => [
                'current_page' => $events->currentPage(),
                'last_page' => $events->lastPage(),
                'per_page' => $events->perPage(),
                'total' => $events->total(),
            ],
        ], 200);
    }

    /**
     * Display the specified event.
     *
     * @param  int|string  $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $event = Event::with([
            'eventLocations.location',
            'eventInstitutions.institution',
            'eventEmployees.employee',
            'reports.eventLocation.location',
            'reports.user',
            'examScores',
        ])->find($id);

        if (! $event) {
            return response()->json([
                'success' => false,
                'message' => 'Kegiatan tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail kegiatan berhasil diambil.',
            'data' => $this->formatEventDetail($event),
        ], 200);
    }

    /**
     * Format summary event payload.
     */
    private function formatEvent(Event $event): array
    {
        $reports = $event->reports;
        $scores = $event->examScores;

        if ($reports && $reports->isNotEmpty()) {
            $totalParticipants = (int) $reports->sum('total_participants');
            $presentCount = (int) $reports->sum('present_count');
            $absentCount = (int) $reports->sum('absent_count');
            $highestScore = (float) ($reports->max('highest_score') ?? 0);
            
            $validLowest = $reports->whereNotNull('lowest_score')->where('lowest_score', '>', 0);
            $lowestScore = (float) ($validLowest->isNotEmpty() ? $validLowest->min('lowest_score') : ($reports->min('lowest_score') ?? 0));
        } else {
            $totalParticipants = $scores->count();
            $presentCount = $scores->filter(function ($s) {
                $notes = strtolower($s->notes ?? '');
                return ! str_contains($notes, 'tidak hadir') && ! str_contains($notes, 'th') && $notes !== 'absen';
            })->count();
            $absentCount = $totalParticipants - $presentCount;

            $validScores = $scores->where('total_score', '>', 0);
            $highestScore = (float) ($scores->max('total_score') ?? 0);
            $lowestScore = (float) ($validScores->count() > 0 ? $validScores->min('total_score') : 0);
        }

        $passedCount = $scores->where('status', 'Lulus')->count();
        $failedCount = $scores->where('status', 'Tidak Lulus')->count();

        $validScores = $scores->where('total_score', '>', 0);
        $avgScore = (float) ($validScores->count() > 0 ? round($validScores->avg('total_score'), 2) : 0);

        return [
            'id' => $event->id,
            'name' => $event->name,
            'status' => $event->status,
            'start_date' => $event->start_date ? Carbon::parse($event->start_date)->format('Y-m-d') : null,
            'end_date' => $event->end_date ? Carbon::parse($event->end_date)->format('Y-m-d') : null,
            'statistics' => [
                'total_participants' => $totalParticipants,
                'present' => $presentCount,
                'absent' => $absentCount,
                'passed' => $passedCount,
                'failed' => $failedCount,
                'highest_score' => $highestScore,
                'lowest_score' => $lowestScore,
                'average_score' => $avgScore,
            ],
            'created_at' => $event->created_at ? $event->created_at->toIso8601String() : null,
        ];
    }

    /**
     * Format detailed event payload.
     */
    private function formatEventDetail(Event $event): array
    {
        $base = $this->formatEvent($event);

        $base['locations'] = $event->eventLocations->map(fn ($el) => [
            'id' => $el->id,
            'location_name' => $el->location?->name ?? $el->name,
            'address' => $el->location?->address ?? null,
            'city' => $el->location?->city?->name ?? null,
        ]);

        $base['institutions'] = $event->eventInstitutions->map(fn ($ei) => [
            'id' => $ei->institution?->id ?? $ei->id,
            'name' => $ei->institution?->name ?? null,
            'code' => $ei->institution?->code ?? null,
        ]);

        $base['session_reports'] = $event->reports->map(fn ($rep) => [
            'id' => $rep->id,
            'report_date' => $rep->report_date ? Carbon::parse($rep->report_date)->format('Y-m-d') : null,
            'session_name' => $rep->session_name,
            'location_name' => $rep->eventLocation?->location?->name ?? $rep->eventLocation?->name ?? null,
            'total_participants' => (int) $rep->total_participants,
            'present_count' => (int) $rep->present_count,
            'absent_count' => (int) $rep->absent_count,
            'highest_score' => (float) $rep->highest_score,
            'lowest_score' => (float) $rep->lowest_score,
            'reporter_name' => $rep->user?->name ?? null,
        ]);

        $base['documents'] = [
            'implementation_report' => $event->doc_implementation_report ? asset('storage/' . $event->doc_implementation_report) : null,
            'team_decree' => $event->doc_team_decree ? asset('storage/' . $event->doc_team_decree) : null,
            'ba_catos' => $event->doc_ba_catos ? asset('storage/' . $event->doc_ba_catos) : null,
            'institution_announcement' => $event->doc_institution_announcement ? asset('storage/' . $event->doc_institution_announcement) : null,
        ];

        return $base;
    }
}
