<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\EnrollmentRequest;
use App\Http\Resources\ClassroomResource;
use App\Http\Resources\EnrollmentResource;
use App\Services\EnrollmentService;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function __construct(private readonly EnrollmentService $enrollments) {}

    public function enroll(EnrollmentRequest $request)
    {
        $enrollment = $this->enrollments->enroll(
            $request->user(),
            $request->validated()['class_id']
        );

        return (new EnrollmentResource($enrollment))
            ->response()
            ->setStatusCode(201);
    }

    public function getMyClasses(Request $request)
    {
        $classes = $this->enrollments->myClasses($request->user(), $request->query('q'));

        return ClassroomResource::collection($classes);
    }

    public function unenroll(Request $request, int $classId)
    {
        $this->enrollments->unenroll($request->user(), $classId);

        return response()->json([
            'message' => 'Unenrolled successfully.',
        ]);
    }
}
