<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_TEACHER = 'teacher';
    public const ROLE_STUDENT = 'student';

    protected $fillable = [
        'nis',
        'email',
        'name',
        'password',
        'no_absen',
        'kelas',
        'kelas_index',
        'role',
    ];

    protected $hidden = [
        'password',
    ];

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'student_id');
    }

    public function materialsCreated()
    {
        return $this->hasMany(Material::class, 'created_by');
    }

    protected static function booted(): void
    {
        static::creating(function (User $user): void {
            if (! $user->password && $user->nis) {
                $user->password = Hash::make($user->nis);
            }
        });
    }

    public function isTeacher(): bool
    {
        return $this->role === self::ROLE_TEACHER;
    }

    public function isStudent(): bool
    {
        return $this->role === self::ROLE_STUDENT;
    }
}
