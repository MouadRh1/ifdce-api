<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDiplomaField extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'diploma_id',
        'field_id',
        'status'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function diploma()
    {
        return $this->belongsTo(Diploma::class);
    }

    public function field()
    {
        return $this->belongsTo(Field::class);
    }
}