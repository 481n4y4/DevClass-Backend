<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\MaterialStoreRequest;
use App\Http\Requests\MaterialUpdateRequest;
use App\Http\Resources\MaterialResource;
use App\Services\MaterialService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MaterialController extends Controller
{
    public function __construct(private readonly MaterialService $materials) {}

    public function index(Request $request)
    {
        try {
            $materials = $this->materials->listForUser($request->user());

            return MaterialResource::collection($materials);
        } catch (\Throwable $exception) {
            Log::error('Failed to list materials.', ['error' => $exception->getMessage()]);

            return response()->json([
                'message' => 'Unable to fetch materials.',
            ], 500);
        }
    }

    public function store(MaterialStoreRequest $request)
    {
        if (! $request->user()->isTeacher()) {
            return response()->json([
                'message' => 'Only teachers can create materials.',
            ], 403);
        }

        try {
            $material = $this->materials->create(
                $request->user(),
                $request->validated(),
                $request->file('file')
            );

            return (new MaterialResource($material->load('creator')))
                ->response()
                ->setStatusCode(201);
        } catch (\Throwable $exception) {
            Log::error('Failed to create material.', ['error' => $exception->getMessage()]);

            return response()->json([
                'message' => 'Unable to create material.',
            ], 500);
        }
    }

    public function update(MaterialUpdateRequest $request, int $id)
    {
        if (! $request->user()->isTeacher()) {
            return response()->json([
                'message' => 'Only teachers can update materials.',
            ], 403);
        }

        try {
            $material = $this->materials->findOrFail($id);
            $material = $this->materials->update(
                $material,
                $request->validated(),
                $request->file('file')
            );

            return new MaterialResource($material->load('creator'));
        } catch (\Throwable $exception) {
            Log::error('Failed to update material.', ['error' => $exception->getMessage()]);

            return response()->json([
                'message' => 'Unable to update material.',
            ], 500);
        }
    }

    public function destroy(Request $request, int $id)
    {
        if (! $request->user()->isTeacher()) {
            return response()->json([
                'message' => 'Only teachers can delete materials.',
            ], 403);
        }

        try {
            $material = $this->materials->findOrFail($id);
            $this->materials->delete($material);

            return response()->json([
                'message' => 'Material deleted successfully.',
            ]);
        } catch (\Throwable $exception) {
            Log::error('Failed to delete material.', ['error' => $exception->getMessage()]);

            return response()->json([
                'message' => 'Unable to delete material.',
            ], 500);
        }
    }
}
