<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diploma extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'duration_years',
        'level',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // A diploma has many fields
    public function fields()
    {
        return $this->hasMany(Field::class);
    }

    // Get all modules through fields
    public function modules()
    {
        return $this->hasManyThrough(Module::class, Field::class);
    }
}