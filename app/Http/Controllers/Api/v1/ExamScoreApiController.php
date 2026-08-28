<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\ExamScore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExamScoreApiController extends Controller
{
    /**
     * Display a listing of exam scores with search, filter, and pagination.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = ExamScore::with(['event']);

        // Filter by Event ID
        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        // Search by Participant Name or NIP
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('nip', 'like', '%' . $search . '%');
            });
        }

        // Filter by Status (Lulus / Tidak Lulus)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by Exam Type (UD_I, UD_II, UPKP)
        if ($request->filled('exam_type')) {
            $query->where('exam_type', $request->exam_type);
        }

        $perPage = (int) $request->get('per_page', 20);
        $scores = $query->orderBy('id', 'desc')->paginate($perPage);

        $transformedItems = $scores->getCollection()->map(function ($score) {
            return [
                'id' => $score->id,
                'event_id' => $score->event_id,
                'event_name' => $score->event?->name ?? null,
                'nip' => $score->nip,
                'name' => $score->name,
                'institution' => $score->institution,
                'rank_class' => $score->rank_class,
                'position' => $score->position,
                'exam_type' => $score->exam_type,
                'exam_date' => $score->exam_date ? $score->exam_date->format('Y-m-d') : null,
                'scores' => [
                    'cat_score' => $score->cat_score,
                    'interview_score' => $score->interview_score,
                    'total_score' => $score->total_score,
                ],
                'status' => $score->status,
                'notes' => $score->notes,
                'created_at' => $score->created_at ? $score->created_at->toIso8601String() : null,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Daftar nilai ujian berhasil diambil.',
            'data' => $transformedItems,
            'meta' => [
                'current_page' => $scores->currentPage(),
                'last_page' => $scores->lastPage(),
                'per_page' => $scores->perPage(),
                'total' => $scores->total(),
            ],
        ], 200);
    }
}
