<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClassStoreRequest;
use App\Http\Requests\ClassUpdateRequest;
use App\Http\Resources\ClassroomResource;
use App\Models\Classroom;
use App\Services\ClassroomService;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function __construct(private readonly ClassroomService $classrooms) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Classroom::class);

        $classes = $this->classrooms->listForUser(
            $request->user(),
            $request->query('q')
        );

        return ClassroomResource::collection($classes);
    }

    public function store(ClassStoreRequest $request)
    {
        $this->authorize('create', Classroom::class);

        $classroom = $this->classrooms->create($request->user(), $request->validated());

        return (new ClassroomResource($classroom->load('teacher')))
            ->response()
            ->setStatusCode(201);
    }

    public function show(int $id)
    {
        $classroom = $this->classrooms->findOrFail($id);
        $this->authorize('view', $classroom);

        return new ClassroomResource($classroom->load('teacher'));
    }

    public function update(ClassUpdateRequest $request, int $id)
    {
        $classroom = $this->classrooms->findOrFail($id);
        $this->authorize('update', $classroom);

        $classroom = $this->classrooms->update($classroom, $request->validated());

        return new ClassroomResource($classroom->load('teacher'));
    }

    public function destroy(int $id)
    {
        $classroom = $this->classrooms->findOrFail($id);
        $this->authorize('delete', $classroom);

        $this->classrooms->delete($classroom);

        return response()->json([
            'message' => 'Class deleted successfully.',
        ]);
    }
}
