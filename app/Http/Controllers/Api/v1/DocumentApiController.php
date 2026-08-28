<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentApiController extends Controller
{
    /**
     * Display a listing of documents.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Document::with(['categories']);

        // Default to public documents unless explicitly requested
        if ($request->has('is_public')) {
            $query->where('is_public', filter_var($request->is_public, FILTER_VALIDATE_BOOLEAN));
        } else {
            $query->where('is_public', true);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('desc', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $perPage = (int) $request->get('per_page', 15);
        $documents = $query->orderBy('created_at', 'desc')->paginate($perPage);

        $transformedItems = $documents->getCollection()->map(function ($doc) {
            return [
                'id' => $doc->id,
                'title' => $doc->title,
                'category' => $doc->categories?->name ?? null,
                'desc' => $doc->desc,
                'year' => $doc->year,
                'is_public' => (bool) $doc->is_public,
                'file_url' => $doc->file ? asset('storage/' . $doc->file) : null,
                'created_at' => $doc->created_at ? $doc->created_at->toIso8601String() : null,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Daftar dokumen berhasil diambil.',
            'data' => $transformedItems,
            'meta' => [
                'current_page' => $documents->currentPage(),
                'last_page' => $documents->lastPage(),
                'per_page' => $documents->perPage(),
                'total' => $documents->total(),
            ],
        ], 200);
    }
}
