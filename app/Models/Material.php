<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'file_path',
        'kelas_target',
        'kelas_index_target',
        'deadline',
        'submission_required',
        'created_by',
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'submission_required' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'material_id');
    }
}
