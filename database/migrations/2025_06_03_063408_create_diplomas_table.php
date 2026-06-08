<?php
// filepath: c:\Users\youco\OneDrive\Bureau\IFDCE\backend\database\migrations\2025_06_03_063408_create_diplomas_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diplomas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('duration_years')->default(3);
            $table->enum('level', ['technicien', 'bachelor', 'master', 'doctorate'])->default('technicien');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diplomas');
    }
};