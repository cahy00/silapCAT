<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportApiController extends Controller
{
    /**
     * Display a listing of reports ordered by latest date with filters and pagination.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Report::with(['event', 'eventLocation.location', 'user']);

        // Filter by Event
        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        // Filter by Location
        if ($request->filled('event_location_id')) {
            $query->where('event_location_id', $request->event_location_id);
        }

        // Filter by Session
        if ($request->filled('session_name')) {
            $query->where('session_name', $request->session_name);
        }

        // Filter by specific report date
        if ($request->filled('report_date')) {
            $query->whereDate('report_date', $request->report_date);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('report_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('report_date', '<=', $request->end_date);
        }

        // Search by event name or location name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('event', fn ($eq) => $eq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('eventLocation.location', fn ($lq) => $lq->where('name', 'like', "%{$search}%"));
            });
        }

        // Default sort: Latest report date first, then session name
        $sortOrder = $request->get('sort', 'desc') === 'asc' ? 'asc' : 'desc';
        $query->orderBy('report_date', $sortOrder)->orderBy('session_name', $sortOrder);

        $perPage = (int) $request->get('per_page', 20);
        $reports = $query->paginate($perPage);

        $transformedItems = $reports->getCollection()->map(fn ($report) => $this->formatReport($report));

        return response()->json([
            'success' => true,
            'message' => 'Daftar laporan harian pelaksanaan berhasil diambil.',
            'data' => $transformedItems,
            'meta' => [
                'current_page' => $reports->currentPage(),
                'last_page' => $reports->lastPage(),
                'per_page' => $reports->perPage(),
                'total' => $reports->total(),
            ],
        ], 200);
    }

    /**
     * Display the specified report detail.
     *
     * @param  int|string  $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $report = Report::with(['event', 'eventLocation.location', 'user'])->find($id);

        if (! $report) {
            return response()->json([
                'success' => false,
                'message' => 'Data laporan tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail laporan berhasil diambil.',
            'data' => $this->formatReport($report),
        ], 200);
    }

    /**
     * Format a report item.
     *
     * @param  Report  $report
     * @return array
     */
    private function formatReport(Report $report): array
    {
        $present = (int) $report->present_count;
        $absent = (int) $report->absent_count;
        $total = (int) ($report->total_participants ?: ($present + $absent));

        return [
            'id' => $report->id,
            'event_id' => $report->event_id,
            'event_name' => $report->event?->name,
            'event_location_id' => $report->event_location_id,
            'location_name' => $report->eventLocation?->location?->name ?? $report->eventLocation?->name ?? null,
            'report_date' => $report->report_date ? Carbon::parse($report->report_date)->format('Y-m-d') : null,
            'session_name' => $report->session_name,
            'total_participants' => $total,
            'present_count' => $present,
            'absent_count' => $absent,
            'highest_score' => (float) $report->highest_score,
            'lowest_score' => (float) $report->lowest_score,
            'reporter_name' => $report->user?->name ?? null,
            'created_at' => $report->created_at ? $report->created_at->toIso8601String() : null,
        ];
    }
}
