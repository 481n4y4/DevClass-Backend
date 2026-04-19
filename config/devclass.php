<?php

return [
    'pagination' => [
        'per_page' => env('DEVCLASS_PER_PAGE', 15),
    ],
    'files' => [
        'disk' => env('DEVCLASS_FILES_DISK', 'sftp'),
        'materials_dir' => env('DEVCLASS_MATERIALS_DIR', 'materials'),
        'submissions_dir' => env('DEVCLASS_SUBMISSIONS_DIR', 'submissions'),
        'max_upload_kb' => env('DEVCLASS_MAX_UPLOAD_KB', 10240),
        'allowed_mimes' => ['pdf', 'docx', 'pptx', 'jpg', 'jpeg', 'png'],
        'use_queue' => env('DEVCLASS_FILES_USE_QUEUE', false),
    ],
];
