<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'material_id',
        'student_id',
        'file_path',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
