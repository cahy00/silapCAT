<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InstitutionApiController extends Controller
{
    /**
     * Display a listing of institutions.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Institution::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('code', 'like', '%' . $search . '%');
            });
        }

        $perPage = (int) $request->get('per_page', 20);
        $institutions = $query->orderBy('name', 'asc')->paginate($perPage);

        $transformedItems = $institutions->getCollection()->map(function ($inst) {
            return [
                'id' => $inst->id,
                'name' => $inst->name,
                'code' => $inst->code ?? null,
                'created_at' => $inst->created_at ? $inst->created_at->toIso8601String() : null,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Daftar instansi berhasil diambil.',
            'data' => $transformedItems,
            'meta' => [
                'current_page' => $institutions->currentPage(),
                'last_page' => $institutions->lastPage(),
                'per_page' => $institutions->perPage(),
                'total' => $institutions->total(),
            ],
        ], 200);
    }
}
