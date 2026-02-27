<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Submissions extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'assignment_id',
        'user_id',
        'file_path',
        'score',
        'feedback',
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
