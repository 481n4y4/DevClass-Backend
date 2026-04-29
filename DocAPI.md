# DevClass API Documentation

## 1. Setup Instructions

1. Install dependencies

```
composer install
```

2. Configure environment

Copy `.env.example` to `.env` and update these values:

```
APP_NAME="DevClass API"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=devclass
DB_USERNAME=devclassuser
DB_PASSWORD=secret

FILESYSTEM_DISK=sftp
SFTP_HOST=127.0.0.1
SFTP_USERNAME=devclass
SFTP_PASSWORD=secret
SFTP_PORT=22
SFTP_ROOT=/home/devclass/files

DEVCLASS_FILES_DISK=sftp
DEVCLASS_MATERIALS_DIR=materials
DEVCLASS_SUBMISSIONS_DIR=submissions
DEVCLASS_MAX_UPLOAD_KB=10240
DEVCLASS_FILES_USE_QUEUE=false

MAIL_MAILER=log
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

Gmail SMTP example:

MAIL_MAILER=smtp
MAIL_SCHEME=tls
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_gmail@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_FROM_ADDRESS=your_gmail@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

3. Generate app key

```
php artisan key:generate
```

4. Run migrations and seeders

```
php artisan migrate --seed
```

5. Start server

```
php artisan serve
```

## 2. Authentication Guide

Login uses `nis` as the username. Default student password is the same as `nis`.

### Login

- Method: POST
- URL: /api/login
- Body (JSON):

```json
{
    "nis": "1001",
    "password": "1001"
}
```

Example response:

```json
{
    "user": {
        "id": 2,
        "nis": "1001",
        "email": "email_user@gmail.com",
        "name": "Student 1",
        "no_absen": 1,
        "kelas": "10",
        "kelas_index": "2",
        "role": "student",
        "created_at": "2026-04-23T10:00:00.000000Z"
    },
    "token": "<your_token_here>"
}
```

Use the token on protected endpoints:

```
Authorization: Bearer <your_token_here>
Accept: application/json
```

## 3. API Endpoints (Detailed)

### POST /api/login

- Description: Login with NIS and password.
- Headers: `Accept: application/json`
- Request Body (JSON):

```json
{
    "nis": "1001",
    "password": "1001"
}
```

- Validation:
    - nis: required, string
    - password: required, string

- Response (200):

```json
{
    "user": {
        "id": 2,
        "nis": "1001",
        "email": "email_user@gmail.com",
        "name": "Student 1",
        "no_absen": 1,
        "kelas": "10",
        "kelas_index": "2",
        "role": "student",
        "created_at": "2026-04-23T10:00:00.000000Z"
    },
    "token": "<your_token_here>"
}
```

### POST /api/logout

- Description: Revoke current token.
- Headers: `Authorization: Bearer <token>`, `Accept: application/json`
- Request Body: none

- Response (200):

```json
{
    "message": "Logged out successfully."
}
```

### GET /api/me

- Description: Get current user profile.
- Headers: `Authorization: Bearer <token>`, `Accept: application/json`
- Request Body: none

- Response (200):

```json
{
    "id": 2,
    "nis": "1001",
    "email": "email_user@gmail.com",
    "name": "Student 1",
    "no_absen": 1,
    "kelas": "10",
    "kelas_index": "2",
    "role": "student",
    "created_at": "2026-04-23T10:00:00.000000Z"
}
```

### PUT /api/me

- Description: Update current user profile (name and/or email).
- Headers: `Authorization: Bearer <token>`, `Accept: application/json`, `Content-Type: application/json`
- Request Body (JSON):

```json
{
    "name": "Nama Baru",
    "email": "email_user@gmail.com"
}
```

- Validation:
    - name: sometimes, string, max:255
    - email: sometimes, email, max:255, unique:users,email

- Response (200):

```json
{
    "id": 2,
    "nis": "1001",
    "email": "email_user@gmail.com",
    "name": "Nama Baru",
    "no_absen": 1,
    "kelas": "10",
    "kelas_index": "2",
    "role": "student",
    "created_at": "2026-04-23T10:00:00.000000Z"
}
```

### GET /api/materials

- Description: List materials. Students get materials for their `kelas` and `kelas_index`. Teachers get all materials.
- Headers: `Authorization: Bearer <token>`, `Accept: application/json`
- Request Body: none

- Response (200):

```json
[
    {
        "id": 1,
        "title": "Intro to Programming",
        "deadline": "2026-04-30 23:59:00",
        "created_at": "2026-04-23T10:00:00.000000Z"
    }
]
```

### GET /api/materials/{id}

- Description: Get material detail with creator info. Students can only access materials assigned to their class.
- Headers: `Authorization: Bearer <token>`, `Accept: application/json`
- Request Body: none

- Response (200):

```json
{
    "id": 1,
    "title": "Intro to Programming",
    "content": "Learn the basics of programming with examples.",
    "file_path": "materials/1/550e8400-e29b-41d4-a716-446655440000.pdf",
    "kelas_target": "10",
    "kelas_index_target": "1",
    "deadline": "2026-04-30 23:59:00",
    "submission_required": true,
    "created_by": {
        "id": 1,
        "nis": "teacher@devclass.com",
        "name": "DevClass Teacher",
        "no_absen": 0,
        "kelas": "10",
        "kelas_index": "1",
        "role": "teacher",
        "created_at": "2026-04-23T10:00:00.000000Z"
    },
    "created_at": "2026-04-23T10:00:00.000000Z"
}
```

- Error (403): Student accessing material from different class
- Error (404): Material not found

### POST /api/materials

- Description: Create material with optional file upload. Only teachers.
- Headers: `Authorization: Bearer <token>`, `Accept: application/json`, `Content-Type: multipart/form-data`
- Request Body (form-data):
    - title: string, required, max:255
    - content: string, nullable
    - kelas_target: enum (10, 11, 12, 13), required
    - kelas_index_target: enum (1, 2, 3), required
    - deadline: datetime, nullable, format: YYYY-MM-DD or YYYY-MM-DD HH:mm:ss
    - submission_required: boolean, nullable (default: true)
    - file: file, nullable, mimes: pdf, doc, docx, ppt, pptx, max: 10MB

- Response (201):

```json
{
    "id": 1,
    "title": "Intro to Programming",
    "content": "Learn the basics of programming with examples.",
    "file_path": "materials/1/550e8400-e29b-41d4-a716-446655440000.pdf",
    "kelas_target": "10",
    "kelas_index_target": "1",
    "deadline": "2026-04-30 23:59:00",
    "submission_required": true,
    "created_by": {
        "id": 1,
        "nis": "teacher@devclass.com",
        "name": "DevClass Teacher",
        "no_absen": 0,
        "kelas": "10",
        "kelas_index": "1",
        "role": "teacher",
        "created_at": "2026-04-23T10:00:00.000000Z"
    },
    "created_at": "2026-04-23T10:00:00.000000Z"
}
```

- Error (403): Non-teacher trying to create
- Error (422): Validation failed

### PUT /api/materials/{id}

- Description: Update material (all fields optional). Only the teacher who created it.
- Headers: `Authorization: Bearer <token>`, `Accept: application/json`, `Content-Type: multipart/form-data`
- Request Body (form-data): Same as POST but all fields optional
    - title: sometimes, string, max:255
    - content: nullable, string
    - kelas_target: sometimes, in:10,11,12,13
    - kelas_index_target: sometimes, in:1,2,3
    - deadline: nullable, date
    - submission_required: nullable, boolean
    - file: nullable, file, mimes: pdf, doc, docx, ppt, pptx

- Response (200):

```json
{
    "id": 1,
    "title": "Updated Title",
    "content": "Updated content.",
    "file_path": "materials/1/550e8400-e29b-41d4-a716-446655440000.pdf",
    "kelas_target": "10",
    "kelas_index_target": "1",
    "deadline": "2026-04-30 23:59:00",
    "submission_required": true,
    "created_by": {
        "id": 1,
        "nis": "teacher@devclass.com",
        "name": "DevClass Teacher",
        "no_absen": 0,
        "kelas": "10",
        "kelas_index": "1",
        "role": "teacher",
        "created_at": "2026-04-23T10:00:00.000000Z"
    },
    "created_at": "2026-04-23T10:00:00.000000Z"
}
```

### DELETE /api/materials/{id}

- Description: Delete material and all associated submissions/files. Only the teacher who created it.
- Headers: `Authorization: Bearer <token>`, `Accept: application/json`
- Request Body: none

- Response (200):

```json
{
    "message": "Material deleted successfully."
}
```

- Error (403): Non-teacher or unauthorized teacher
- Error (404): Material not found

### POST /api/submit/{materialId}

- Description: Submit assignment for a material. Only students. Replaces previous submission if exists.
- Headers: `Authorization: Bearer <token>`, `Accept: application/json`, `Content-Type: multipart/form-data`
- Request Body (form-data):
    - file: file, required, mimes: pdf, doc, docx, ppt, pptx, max: 10MB

- Response (201):

```json
{
    "id": 1,
    "material_id": 1,
    "student": {
        "id": 2,
        "nis": "1001",
        "name": "Student 1",
        "no_absen": 1,
        "kelas": "10",
        "kelas_index": "2",
        "role": "student",
        "created_at": "2026-04-23T10:00:00.000000Z"
    },
    "submitted_at": "2026-04-23T11:00:00.000000Z",
    "file_path": "submissions/1/2/550e8400-e29b-41d4-a716-446655440000.pdf",
    "created_at": "2026-04-23T11:00:00.000000Z"
}
```

- Error (403): Non-student
- Error (404): Material not found
- Error (422): Validation failed, deadline passed, or student not in target class
- Possible error messages:
    - `"Submission deadline has passed."` - if material deadline < now
    - `"Submission is not required for this material."` - if submission_required = false
    - `"You are not assigned to this class group."` - if student's class doesn't match

### GET /api/materials/{materialId}/my-submission

- Description: Get current student's submission for a material.
- Headers: `Authorization: Bearer <token>`, `Accept: application/json`
- Request Body: none

- Response (200):

```json
{
    "data": {
        "id": 1,
        "material_id": 1,
        "student": {
            "id": 2,
            "nis": "1001",
            "name": "Student 1",
            "no_absen": 1,
            "kelas": "10",
            "kelas_index": "2",
            "role": "student",
            "created_at": "2026-04-23T10:00:00.000000Z"
        },
        "submitted_at": "2026-04-23T11:00:00.000000Z",
        "file_path": "submissions/1/2/550e8400-e29b-41d4-a716-446655440000.pdf",
        "grade": {
            "id": 1,
            "score": 85,
            "feedback": "Good work!",
            "graded_by": {
                "id": 1,
                "name": "Teacher Name"
            }
        },
        "created_at": "2026-04-23T11:00:00.000000Z"
    }
}
```

- Response (200) if no submission:

```json
{
    "data": null
}
```

- Error (403): Non-student

### GET /api/materials/{materialId}/submissions

- Description: List all submissions for a material. Only teachers who own the material.
- Headers: `Authorization: Bearer <token>`, `Accept: application/json`
- Request Body: none

- Response (200):

```json
[
    {
        "id": 1,
        "material_id": 1,
        "student": {
            "id": 2,
            "nis": "1001",
            "name": "Student 1",
            "no_absen": 1,
            "kelas": "10",
            "kelas_index": "2",
            "role": "student",
            "created_at": "2026-04-23T10:00:00.000000Z"
        },
        "submitted_at": "2026-04-23T11:00:00.000000Z",
        "file_path": "submissions/1/2/550e8400-e29b-41d4-a716-446655440000.pdf",
        "grade": null,
        "created_at": "2026-04-23T11:00:00.000000Z"
    }
]
```

- Error (403): Non-teacher
- Error (404): Material not found

### POST /api/submissions/{submissionId}/grade

- Description: Add or update grade for a submission. Only teachers who own the material.
- Headers: `Authorization: Bearer <token>`, `Accept: application/json`, `Content-Type: application/json`
- Request Body (JSON):
    - score: integer, required, min:0, max:100
    - feedback: string, nullable

```json
{
    "score": 85,
    "feedback": "Great work, well done!"
}
```

- Response (201):

```json
{
    "id": 1,
    "score": 85,
    "feedback": "Great work, well done!",
    "graded_by": {
        "id": 1,
        "nis": "teacher@devclass.com",
        "name": "Teacher Name",
        "no_absen": 0,
        "kelas": "10",
        "kelas_index": "1",
        "role": "teacher",
        "created_at": "2026-04-23T10:00:00.000000Z"
    }
}
```

- Error (403): Non-teacher or unauthorized teacher
- Error (404): Submission not found
- Error (422): Validation failed

### DELETE /api/submissions/{submissionId}

- Description: Delete submission. Student can delete their own submission (if not graded). Teacher can delete any submission for their material.
- Headers: `Authorization: Bearer <token>`, `Accept: application/json`
- Request Body: none

- Response (200):

```json
{
    "message": "Submission deleted successfully."
}
```

- Error (403): Unauthorized
- Error (404): Submission not found
- Error (422): Student trying to delete already graded submission

### GET /api/download/{path}

- Description: Download file from SFTP storage with authentication. Path should be URL encoded.
- Headers: `Authorization: Bearer <token>`, `Accept: application/json`
- Request Body: none
- URL Parameter:
    - path: URL-encoded file path (e.g., `materials/1/uuid.pdf` → `materials%2F1%2Fuuid.pdf`)

- Response (200): Binary file stream with headers:
    - `Content-Type: application/octet-stream`
    - `Content-Disposition: attachment; filename="{filename}"`

- Error (404): File not found
- Error (500): Failed to read file

### GET /api/admin/users

- Description: List all users with optional filters. Only teachers/admins.
- Headers: `Authorization: Bearer <token>`, `Accept: application/json`
- Query Parameters (optional):
    - role: filter by role (student, teacher)
    - kelas: filter by class (10, 11, 12, 13)
    - kelas_index: filter by class index (1, 2, 3)

- Response (200):

```json
[
    {
        "id": 2,
        "nis": "1001",
        "email": "student@example.com",
        "name": "Student 1",
        "no_absen": 1,
        "kelas": "10",
        "kelas_index": "2",
        "role": "student",
        "created_at": "2026-04-23T10:00:00.000000Z"
    }
]
```

- Error (403): Non-teacher/admin

### POST /api/admin/users

- Description: Create new user. Only teachers/admins. Default password is NIS if not provided.
- Headers: `Authorization: Bearer <token>`, `Accept: application/json`, `Content-Type: application/json`
- Request Body (JSON):
    - nis: string, required, unique:users
    - email: email, nullable, unique:users
    - name: string, required
    - no_absen: integer, nullable
    - kelas: enum (10, 11, 12, 13), required
    - kelas_index: enum (1, 2, 3), required
    - role: enum (student, teacher), required
    - password: string, nullable (default: NIS)

```json
{
    "nis": "1002",
    "email": "student2@example.com",
    "name": "Student 2",
    "no_absen": 2,
    "kelas": "10",
    "kelas_index": "2",
    "role": "student"
}
```

- Response (201):

```json
{
    "id": 3,
    "nis": "1002",
    "email": "student2@example.com",
    "name": "Student 2",
    "no_absen": 2,
    "kelas": "10",
    "kelas_index": "2",
    "role": "student",
    "created_at": "2026-04-24T10:00:00.000000Z"
}
```

- Error (403): Non-teacher/admin
- Error (422): Validation failed

### GET /api/admin/users/{id}

- Description: Get specific user detail. Only teachers/admins.
- Headers: `Authorization: Bearer <token>`, `Accept: application/json`
- Request Body: none

- Response (200):

```json
{
    "id": 2,
    "nis": "1001",
    "email": "student@example.com",
    "name": "Student 1",
    "no_absen": 1,
    "kelas": "10",
    "kelas_index": "2",
    "role": "student",
    "created_at": "2026-04-23T10:00:00.000000Z"
}
```

- Error (403): Non-teacher/admin
- Error (404): User not found

### PUT /api/admin/users/{id}

- Description: Update user data. Only teachers/admins.
- Headers: `Authorization: Bearer <token>`, `Accept: application/json`, `Content-Type: application/json`
- Request Body (JSON): All fields optional
    - email: email, nullable, unique:users
    - name: string
    - no_absen: integer
    - kelas: in:10,11,12,13
    - kelas_index: in:1,2,3
    - role: in:student,teacher
    - password: string, nullable (leave empty to keep current)

```json
{
    "name": "Updated Name",
    "no_absen": 3
}
```

- Response (200):

```json
{
    "id": 2,
    "nis": "1001",
    "email": "student@example.com",
    "name": "Updated Name",
    "no_absen": 3,
    "kelas": "10",
    "kelas_index": "2",
    "role": "student",
    "created_at": "2026-04-23T10:00:00.000000Z"
}
```

- Error (403): Non-teacher/admin
- Error (404): User not found
- Error (422): Validation failed

### DELETE /api/admin/users/{id}

- Description: Delete user. Only teachers/admins.
- Headers: `Authorization: Bearer <token>`, `Accept: application/json`
- Request Body: none

- Response (200):

```json
{
    "message": "User deleted successfully."
}
```

- Error (403): Non-teacher/admin
- Error (404): User not found

### PUT /api/users/{id}

- Description: Update user data by teacher (alternate endpoint to admin version).
- Headers: `Authorization: Bearer <token>`, `Accept: application/json`, `Content-Type: application/json`
- Request Body (JSON):
    - name: string, optional
    - email: email, optional, unique:users

```json
{
    "name": "New Name",
    "email": "newemail@example.com"
}
```

- Response (200): Updated user object
- Error (403): Non-teacher

## 4. Testing Guide

Use Postman or Thunder Client:

1. Login using NIS
2. Copy the `token` from the response
3. Add header: `Authorization: Bearer <token>`
4. Add header: `Accept: application/json`
5. Access protected routes

Sample token usage:

```
Authorization: Bearer <your_token_here>
Accept: application/json
```

## 5. File Upload Guide

- Use `multipart/form-data` Content-Type
- Field name for file: `file`
- Supported types: pdf, doc, docx, ppt, pptx
- Max size: 10MB (DEVCLASS_MAX_UPLOAD_KB)
- File paths stored in DB: `materials/{materialId}/{uuid}` or `submissions/{materialId}/{studentId}/{uuid}`

## 6. File Download Guide

To download files from SFTP:

1. Use GET `/api/download/{path}` endpoint
2. URL-encode the path: `materials/1/uuid.pdf` → `materials%2F1%2Fuuid.pdf`
3. Include Authorization header with Bearer token
4. Example: `GET /api/download/materials%2F1%2Fuuid.pdf`

Frontend implementation with Axios:

```javascript
const path = "materials/1/uuid.pdf";
const encodedPath = path
    .split("/")
    .map((segment) => encodeURIComponent(segment))
    .join("/");
const response = await api.get(`/download/${encodedPath}`, {
    responseType: "blob",
});
const url = window.URL.createObjectURL(response.data);
const link = document.createElement("a");
link.href = url;
link.download = "filename.pdf";
link.click();
```

## 7. Role-based Access Explanation

- **Teacher**: Create, update, delete materials; view and grade submissions; manage users; access admin panel
- **Student**: List materials for their class group; submit assignments when required; view their own grade; download materials and feedback

## 8. Dummy Accounts

- **Teacher**
    - nis: teacher@devclass.com
    - password: teacher123

- **Student** (example)
    - nis: 1001
    - password: 1001
