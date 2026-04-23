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

### Logout

- Method: POST
- URL: /api/logout

## 3. API Endpoints (Detailed)

### POST /api/login

- Description: Login with NIS and password.
- Headers: Accept: application/json
- Request Body (JSON):

```json
{
    "nis": "1001",
    "password": "1001"
}
```

- Example response:

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

- Validation rules:
    - nis: required, string
    - password: required, string

### POST /api/logout

- Description: Revoke current token.
- Headers: Authorization: Bearer <token>
- Request Body: none

Example response:

```json
{
    "message": "Logged out successfully."
}
```

### GET /api/me

- Description: Get current user profile.
- Headers: Authorization: Bearer <token>
- Request Body: none

Example response:

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

- Description: Update current user profile (name/email).
- Headers: Authorization: Bearer <token>
- Request Body (JSON):

```json
{
    "name": "Nama Baru",
    "email": "email_user@gmail.com"
}
```

- Validation rules:
    - name: sometimes, string, max:255
    - email: nullable, email, max:255, unique

### PUT /api/users/{id} (Teacher)

- Description: Update user data (name/email).
- Headers: Authorization: Bearer <token>
- Request Body (JSON):

```json
{
    "name": "Student Updated",
    "email": "email_user@gmail.com"
}
```

- Validation rules:
    - name: sometimes, string, max:255
    - email: nullable, email, max:255, unique

### GET /api/materials

- Description: Student gets materials for their `kelas` and `kelas_index`. Teacher gets all materials.
- Headers: Authorization: Bearer <token>
- Request Body: none

Example response:

```json
{
    "data": [
        {
            "id": 1,
            "title": "Intro",
            "deadline": null,
            "created_at": "2026-04-23T10:00:00.000000Z"
        }
    ]
}
```

- Validation rules: none

### POST /api/materials (Teacher)

- Description: Create material with optional file upload.
- Headers: Authorization: Bearer <token>
- Request Body: form-data
    - title (string, required)
    - content (string, nullable)
    - kelas_target (enum: 10,11,12,13)
    - kelas_index_target (enum: 1,2,3)
    - deadline (datetime, nullable)
    - submission_required (boolean, nullable)
    - file (file, nullable)

Example response:

```json
{
    "data": {
        "id": 1,
        "title": "Intro",
        "content": "Welcome",
        "file_path": "materials/1/uuid.pdf",
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
}
```

- Validation rules:
    - title: required, string, max:255
    - content: nullable, string
    - kelas_target: required, in:10,11,12,13
    - kelas_index_target: required, in:1,2,3
    - deadline: nullable, date
    - submission_required: nullable, boolean
    - file: nullable, file, mimes: pdf, doc, docx, ppt, pptx, max: DEVCLASS_MAX_UPLOAD_KB

### PUT /api/materials/{id} (Teacher)

- Description: Update material details or file.
- Headers: Authorization: Bearer <token>
- Request Body: form-data (same as POST but all optional)

- Validation rules:
    - title: sometimes, string, max:255
    - content: nullable, string
    - kelas_target: sometimes, in:10,11,12,13
    - kelas_index_target: sometimes, in:1,2,3
    - deadline: nullable, date
    - submission_required: nullable, boolean
    - file: nullable, file, mimes: pdf, doc, docx, ppt, pptx, max: DEVCLASS_MAX_UPLOAD_KB

### DELETE /api/materials/{id} (Teacher)

- Description: Delete a material.
- Headers: Authorization: Bearer <token>
- Request Body: none

Example response:

```json
{
    "message": "Material deleted successfully."
}
```

### GET /api/materials/{id}/submissions (Teacher)

- Description: List submissions for a material.
- Headers: Authorization: Bearer <token>
- Request Body: none

Example response:

```json
{
    "data": [
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
            "file_path": "submissions/1/2/uuid.pdf",
            "created_at": "2026-04-23T11:00:00.000000Z"
        }
    ]
}
```

- Validation rules: none

### POST /api/submit/{material_id} (Student)

- Description: Upload assignment submission for a material.
- Headers: Authorization: Bearer <token>
- Request Body: form-data
    - file (file, required)

Example response:

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
        "file_path": "submissions/1/2/uuid.pdf",
        "created_at": "2026-04-23T11:00:00.000000Z"
    }
}
```

- Validation rules:
    - file: required, file, mimes: pdf, doc, docx, ppt, pptx, max: DEVCLASS_MAX_UPLOAD_KB

## 4. Testing Guide

Use Postman or Thunder Client:

1. Login using NIS
2. Copy the `token` from the response
3. Add header: Authorization: Bearer <token>
4. Access protected routes

Sample token usage:

```
Authorization: Bearer <your_token_here>
Accept: application/json
```

## 5. File Upload Guide

- Use `multipart/form-data`
- Field name for file: `file`
- Supported types: pdf, doc, docx, ppt, pptx
- Max size: DEVCLASS_MAX_UPLOAD_KB (default 10240 KB)

## 6. Role-based Access Explanation

- Teacher (admin): create, update, delete materials; view submissions
- Student: list materials for their class group; submit assignments when required

Email notification:

- When teacher creates a material, email is sent to students with matching `kelas_target` and `kelas_index_target`.

## 7. Dummy Accounts

- Teacher
    - nis: teacher@devclass.com
    - password: teacher123

- Student (example)
    - nis: 1001
    - password: 1001
