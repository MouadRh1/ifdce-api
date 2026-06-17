<?php
// database/migrations/2024_06_17_000000_create_vae_requests_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vae_requests', function (Blueprint $table) {
            $table->id();
            
            // Informations personnelles
            $table->string('full_name');
            $table->string('email');
            $table->string('phone');
            $table->string('city')->nullable();
            
            // Expérience professionnelle
            $table->string('experience_years'); // lt3, 3-5, 5-10, gt10
            $table->string('domain'); // commerce, informatique, sante, btp, management, finance, autre
            $table->text('experience');
            
            // Diplôme visé
            $table->string('target_diploma'); // bts, licence, master, titre
            $table->string('field'); // Spécialité / Filière
            $table->text('message')->nullable();
            
            // Statut de la demande
            $table->enum('status', [
                'pending',      // En attente
                'reviewing',    // En cours d'étude
                'contacted',    // Contacté
                'documents',    // Documents reçus
                'approved',     // Approuvé
                'rejected'      // Rejeté
            ])->default('pending');
            
            // Suivi
            $table->timestamp('contacted_at')->nullable();
            $table->timestamp('documents_received_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('admin_notes')->nullable();
            
            // IP et User Agent pour traçabilité
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            
            $table->timestamps();
            
            // Index pour les recherches
            $table->index(['email', 'status']);
            $table->index('created_at');
            $table->index('domain');
            $table->index('target_diploma');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vae_requests');
    }
};