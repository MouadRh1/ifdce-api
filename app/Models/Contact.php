<?php
// app/Models/Contact.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'type',
        'message',
        'status',
        'read_at',
        'replied_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'replied_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    // Types de contacts disponibles
    public const TYPES = [
        'general' => 'Demande générale',
        'vae' => 'Validation des acquis (VAE)',
        'formation' => 'Formations diplômantes',
        'conseil' => 'Conseil en formation',
        'support' => 'Support technique',
    ];

    // Statuts disponibles
    public const STATUS = [
        'pending' => 'En attente',
        'read' => 'Lu',
        'replied' => 'Répondu',
        'archived' => 'Archivé',
    ];

    // Scopes pour faciliter les requêtes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Marquer comme lu
    public function markAsRead()
    {
        $this->update([
            'status' => 'read',
            'read_at' => now(),
        ]);
    }

    // Marquer comme répondu
    public function markAsReplied()
    {
        $this->update([
            'status' => 'replied',
            'replied_at' => now(),
        ]);
    }

    // Accesseurs
    public function getTypeLabelAttribute()
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getStatusLabelAttribute()
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'yellow',
            'read' => 'blue',
            'replied' => 'green',
            'archived' => 'gray',
            default => 'gray',
        };
    }
}