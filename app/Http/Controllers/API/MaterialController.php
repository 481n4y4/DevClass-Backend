<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\MaterialStoreRequest;
use App\Http\Resources\MaterialResource;
use App\Models\Material;
use App\Services\ClassroomService;
use App\Services\MaterialService;

class MaterialController extends Controller
{
    public function __construct(
        private readonly MaterialService $materials,
        private readonly ClassroomService $classrooms
    ) {}

    public function getByClass(int $classId)
    {
        $classroom = $this->classrooms->findOrFail($classId);
        $this->authorize('view', $classroom);

        $materials = $this->materials->listByClass($classId);

        return MaterialResource::collection($materials);
    }

    public function store(MaterialStoreRequest $request)
    {
        $classroom = $this->classrooms->findOrFail($request->validated()['class_id']);
        $this->authorize('createForClass', [Material::class, $classroom]);

        $material = $this->materials->create(
            $request->user(),
            $request->validated(),
            $request->file('file')
        );

        return (new MaterialResource($material->load('uploader')))
            ->response()
            ->setStatusCode(201);
    }
}
