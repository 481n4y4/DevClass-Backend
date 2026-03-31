<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use App\Models\Submission;

class Assignment extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'class_id',
        'title',
        'description',
        'due_date'
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'datetime',
        ];
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }
}
