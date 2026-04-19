# DevClass API - Postman Guide

Dokumen ini menjelaskan cara menggunakan API DevClass di Postman (Laravel Sanctum, token-based).

## Base URL

Sesuaikan dengan environment Anda:

- Local: http://127.0.0.1:8000/api
- Prod: https://your-domain.com/api

## Auth Flow (Sanctum Token)

1. Register

- Method: POST
- URL: /register
- Body (JSON):

```json
{
    "name": "Teacher One",
    "email": "teacher1@devclass.test",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "teacher"
}
```

2. Login

- Method: POST
- URL: /login
- Body (JSON):

```json
{
    "email": "teacher1@devclass.test",
    "password": "password123"
}
```

Response akan berisi token:

```json
{
    "user": {
        "id": 1,
        "name": "Teacher One",
        "email": "teacher1@devclass.test",
        "role": "teacher",
        "created_at": "2026-04-19T00:00:00.000000Z"
    },
    "token": "<your_token_here>"
}
```

3. Set Authorization Header di Postman

- Type: Bearer Token
- Token: <your_token_here>

Semua endpoint protected membutuhkan header:

```
Authorization: Bearer <your_token_here>
Accept: application/json
```

4. Logout

- Method: POST
- URL: /logout

5. Get Current User

- Method: GET
- URL: /me

## Postman Collection Setup

- Buat Environment baru (misal: DevClass Local)
- Tambahkan variable:
    - base_url = http://127.0.0.1:8000/api
    - token = (isi setelah login)
- Gunakan di request: {{base_url}}/login dan Authorization Bearer {{token}}

## Endpoints

### Auth

- POST /register
- POST /login
- POST /logout
- GET /me

### Classes

- POST /classes
- GET /classes
- GET /classes/{id}
- PUT /classes/{id}
- DELETE /classes/{id}

### Enrollment

- POST /enroll
- GET /my-classes
- DELETE /enroll/{classId}

### Materials

- POST /materials (multipart/form-data, upload ke SFTP)
- GET /classes/{id}/materials

### Assignments

- POST /assignments
- GET /classes/{id}/assignments

### Submissions

- POST /submissions (multipart/form-data, upload ke SFTP)
- GET /assignments/{id}/submissions
- GET /my-submissions

### Grades

- POST /grades
- GET /my-grades

### Announcements

- POST /announcements
- GET /classes/{id}/announcements

### File Access (Proxy)

- GET /files/material/{id}
- GET /files/submission/{id}

## Request Examples

### Create Class

- Method: POST
- URL: {{base_url}}/classes
- Body (JSON):

```json
{
    "name": "Backend Laravel",
    "description": "Kelas Laravel advanced"
}
```

### Enroll ke Class

- Method: POST
- URL: {{base_url}}/enroll
- Body (JSON):

```json
{
    "class_id": 1
}
```

### Upload Material (Teacher/Admin)

- Method: POST
- URL: {{base_url}}/materials
- Body: form-data
    - class_id: 1
    - title: "Slide 1"
    - description: "Intro"
    - file: (choose file)

Allowed mimes: pdf, docx, pptx, jpg, jpeg, png
Max size: 10MB (default)

### Create Assignment

- Method: POST
- URL: {{base_url}}/assignments
- Body (JSON):

```json
{
    "class_id": 1,
    "title": "Tugas 1",
    "description": "Buat API sederhana",
    "deadline": "2026-04-30 23:59:00"
}
```

### Submit Assignment (Student)

- Method: POST
- URL: {{base_url}}/submissions
- Body: form-data
    - assignment_id: 5
    - file: (choose file)

### Give Grade (Teacher/Admin)

- Method: POST
- URL: {{base_url}}/grades
- Body (JSON):

```json
{
    "submission_id": 12,
    "score": 90,
    "feedback": "Good job"
}
```

### Create Announcement

- Method: POST
- URL: {{base_url}}/announcements
- Body (JSON):

```json
{
    "class_id": 1,
    "title": "Jadwal berubah",
    "content": "Pertemuan pindah ke Jumat"
}
```

## Notes

- Semua upload file disimpan ke SFTP, tidak ada local storage.
- File download harus lewat endpoint /files/\*, tidak expose path langsung.
- Query search untuk classes dan my-classes: gunakan ?q=keyword
- Pagination default 15, bisa diubah via DEVCLASS_PER_PAGE
