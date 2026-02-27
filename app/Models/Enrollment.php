<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'class_id',
    ];

    public function user() 
    {
        return $this->belongsTo(User::class);
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }
}
