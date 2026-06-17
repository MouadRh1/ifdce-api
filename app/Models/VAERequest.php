<?php
// app/Models/VAERequest.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VAERequest extends Model
{
    use HasFactory;

    protected $table = 'vae_requests';

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'city',
        'experience_years',
        'domain',
        'experience',
        'target_diploma',
        'field',
        'message',
        'status',
        'contacted_at',
        'documents_received_at',
        'approved_at',
        'rejected_at',
        'admin_notes',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'contacted_at' => 'datetime',
        'documents_received_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Constantes pour les statuts
    const STATUS = [
        'pending' => 'En attente',
        'reviewing' => 'En cours d\'étude',
        'contacted' => 'Contacté',
        'documents' => 'Documents reçus',
        'approved' => 'Approuvé',
        'rejected' => 'Rejeté',
    ];

    const STATUS_COLORS = [
        'pending' => 'yellow',
        'reviewing' => 'blue',
        'contacted' => 'purple',
        'documents' => 'indigo',
        'approved' => 'green',
        'rejected' => 'red',
    ];

    // Domaines
    const DOMAINS = [
        'commerce' => 'Commerce & Vente',
        'informatique' => 'Informatique & Digital',
        'sante' => 'Santé & Social',
        'btp' => 'BTP & Industrie',
        'management' => 'Management & RH',
        'finance' => 'Finance & Comptabilité',
        'autre' => 'Autre',
    ];

    // Années d'expérience
    const EXPERIENCE_YEARS = [
        'lt3' => 'Moins de 3 ans',
        '3-5' => '3 à 5 ans',
        '5-10' => '5 à 10 ans',
        'gt10' => 'Plus de 10 ans',
    ];

    // Diplômes visés
    const TARGET_DIPLOMAS = [
        'bts' => 'BTS (Bac +2)',
        'licence' => 'Licence Pro (Bac +3)',
        'master' => 'Master (Bac +5)',
        'titre' => 'Titre professionnel',
    ];

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByDomain($query, $domain)
    {
        return $query->where('domain', $domain);
    }

    public function scopeByDiploma($query, $diploma)
    {
        return $query->where('target_diploma', $diploma);
    }

    // Accesseurs
    public function getStatusLabelAttribute()
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        return self::STATUS_COLORS[$this->status] ?? 'gray';
    }

    public function getDomainLabelAttribute()
    {
        return self::DOMAINS[$this->domain] ?? $this->domain;
    }

    public function getExperienceLabelAttribute()
    {
        return self::EXPERIENCE_YEARS[$this->experience_years] ?? $this->experience_years;
    }

    public function getTargetDiplomaLabelAttribute()
    {
        return self::TARGET_DIPLOMAS[$this->target_diploma] ?? $this->target_diploma;
    }

    // Méthodes pour mettre à jour le statut
    public function markAsContacted()
    {
        $this->update([
            'status' => 'contacted',
            'contacted_at' => now(),
        ]);
    }

    public function markAsDocumentsReceived()
    {
        $this->update([
            'status' => 'documents',
            'documents_received_at' => now(),
        ]);
    }

    public function markAsApproved()
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);
    }

    public function markAsRejected()
    {
        $this->update([
            'status' => 'rejected',
            'rejected_at' => now(),
        ]);
    }

    public function markAsReviewing()
    {
        $this->update([
            'status' => 'reviewing',
        ]);
    }
}