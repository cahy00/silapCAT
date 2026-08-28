<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeApiController extends Controller
{
    /**
     * Display a listing of employees.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Employee::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('nip', 'like', '%' . $search . '%');
            });
        }

        $perPage = (int) $request->get('per_page', 20);
        $employees = $query->orderBy('name', 'asc')->paginate($perPage);

        $transformedItems = $employees->getCollection()->map(function ($employee) {
            return [
                'id' => $employee->id,
                'nip' => $employee->nip,
                'name' => $employee->name,
                'rank_class' => $employee->rank_class ?? null,
                'position' => $employee->position ?? null,
                'status' => $employee->status ?? null,
                'created_at' => $employee->created_at ? $employee->created_at->toIso8601String() : null,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Daftar pegawai berhasil diambil.',
            'data' => $transformedItems,
            'meta' => [
                'current_page' => $employees->currentPage(),
                'last_page' => $employees->lastPage(),
                'per_page' => $employees->perPage(),
                'total' => $employees->total(),
            ],
        ], 200);
    }
}
