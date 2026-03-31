<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'class_id',
        'title',
        'content',
        'order',
    ];

    public function class() {
        return $this->belongsTo(Classes::class, 'class_id');
    }
}
