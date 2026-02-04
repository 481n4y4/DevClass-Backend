<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasUuids, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status'
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'string',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

     public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Relasi: user -> submissions
     * (murid submit banyak tugas)
     */
    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    /**
     * Helper: cek apakah user guru
     */
    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    /**
     * Helper: cek apakah user murid
     */
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }
}
