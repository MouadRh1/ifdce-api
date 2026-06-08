<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    use HasFactory;

    protected $fillable = [
        'diploma_id',
        'name',
        'description',
        'max_students',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // A field belongs to a diploma
    public function diploma()
    {
        return $this->belongsTo(Diploma::class);
    }

    // A field has many modules
    public function modules()
    {
        return $this->hasMany(Module::class);
    }
}