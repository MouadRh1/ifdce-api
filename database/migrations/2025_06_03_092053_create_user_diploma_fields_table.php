<?php
// filepath: c:\Users\youco\OneDrive\Bureau\IFDCE\backend\database\migrations\2025_06_03_092053_create_user_diploma_fields_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_diploma_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('diploma_id')->constrained()->onDelete('cascade');
            $table->foreignId('field_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
            
            // Prevent duplicate entries
            $table->unique(['user_id', 'diploma_id', 'field_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_diploma_fields');
    }
};