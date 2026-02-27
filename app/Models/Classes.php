<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    protected $table = 'classes';

    protected $fillable = [
        'title',
        'desc',
        'status'
    ];

    public function materials()
    {
        return $this->hasMany(Material::class);
    }
    
    public function enrollments() 
    {
        return $this->hasMany(Enrollment::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }
}
