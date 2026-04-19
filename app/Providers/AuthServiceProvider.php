<?php

namespace App\Providers;

use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\Classroom;
use App\Models\Grade;
use App\Models\Material;
use App\Models\Submission;
use App\Policies\AnnouncementPolicy;
use App\Policies\AssignmentPolicy;
use App\Policies\ClassroomPolicy;
use App\Policies\GradePolicy;
use App\Policies\MaterialPolicy;
use App\Policies\SubmissionPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Classroom::class => ClassroomPolicy::class,
        Material::class => MaterialPolicy::class,
        Assignment::class => AssignmentPolicy::class,
        Submission::class => SubmissionPolicy::class,
        Grade::class => GradePolicy::class,
        Announcement::class => AnnouncementPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
