# 📧 1. Setup Mail (Laravel + Gmail SMTP)

Daripada bangun mail server sendiri, cukup:

**`.env` Laravel:**

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@gmail.com
MAIL_FROM_NAME="DevClass"
```

⚠️ Penting:

* Gunakan **App Password**, bukan password biasa
* Aktifkan 2FA di Gmail

---

# 🧠 2. Core System Design DevClass (LMS-style)

Karena kamu mau mirip Google Classroom, berarti domain utamanya:

### Entity utama:

* User (admin, teacher, student)
* Class
* Enrollment
* Material
* Assignment
* Submission
* Grade
* Announcement

---

# 🔥 3. LIST API WAJIB (Clean & Realistic)

## 🔐 AUTH

```http
[ ] POST   /api/register
[ ] POST   /api/login
[ ] POST   /api/logout
[ ] GET    /api/me
```

---

## 👤 USER

```http
[ ] GET    /api/users
[ ] GET    /api/users/{id}
[ ] PUT    /api/users/{id}
[ ] DELETE /api/users/{id}
```

Tambahan:

* role-based (admin / teacher / student)

---

## 🏫 CLASS

```http
[ ] GET    /api/classes
[ ] POST   /api/classes
[ ] GET    /api/classes/{id}
[ ] PUT    /api/classes/{id}
[ ] DELETE /api/classes/{id}
```

Tambahan:

```http
[ ] POST   /api/classes/{id}/join   // student join
[ ] POST   /api/classes/{id}/leave
```

---

## 👥 ENROLLMENT

(Relasi user ↔ class)

```http
[ ] GET    /api/classes/{id}/students
[ ] POST   /api/classes/{id}/students
[ ] DELETE /api/classes/{id}/students/{user_id}
```

---

## 📚 MATERIAL (Materi)

```http
[ ] GET    /api/classes/{id}/materials
[ ] POST   /api/materials
[ ] GET    /api/materials/{id}
[ ] PUT    /api/materials/{id}
[ ] DELETE /api/materials/{id}
```

---

## 📝 ASSIGNMENT

```http
[ ] GET    /api/classes/{id}/assignments
[ ] POST   /api/assignments
[ ] GET    /api/assignments/{id}
[ ] PUT    /api/assignments/{id}
[ ] DELETE /api/assignments/{id}
```

---

## 📤 SUBMISSION (Upload via SFTP)

```http
[ ] POST   /api/assignments/{id}/submit
[ ] GET    /api/assignments/{id}/submissions
[ ] GET    /api/submissions/{id}
```

---

## 🎯 GRADING

```http
[ ] POST   /api/submissions/{id}/grade
[ ] GET    /api/submissions/{id}/grade
```

---

## 📢 ANNOUNCEMENT

```http
[ ] GET    /api/classes/{id}/announcements
[ ] POST   /api/announcements
[ ] DELETE /api/announcements/{id}
```

---

## 📁 FILE HANDLING (SFTP)

Karena kamu pakai SFTP:

```http
[ ] POST   /api/upload
[ ] GET    /api/files/{id}
[ ] DELETE /api/files/{id}
```

Laravel bisa pakai:

```php
Storage::disk('sftp')
```

---

## 📧 EMAIL (Trigger dari backend)

Tidak perlu API khusus, tapi trigger dari:

* Register → kirim welcome email
* Assignment → notify student
* Deadline → reminder

---

# 🧱 4. Struktur Database (Minimal)

Biar API kamu clean:

* users
* classes
* enrollments
* materials
* assignments
* submissions
* grades
* announcements
* files

