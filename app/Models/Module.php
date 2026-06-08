<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'field_id',
        'name',
        'description',
        'credits',
        'semester',
        'hours_per_week',
        'type',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // A module belongs to a field
    public function field()
    {
        return $this->belongsTo(Field::class);
    }

    // Get the diploma through field
    public function diploma()
    {
        return $this->field->diploma;
    }
}