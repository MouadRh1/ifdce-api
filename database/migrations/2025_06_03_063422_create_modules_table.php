<?php
// filepath: c:\Users\youco\OneDrive\Bureau\IFDCE\backend\database\migrations\2025_06_03_063422_create_modules_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('credits')->default(3);
            $table->integer('semester')->default(1);
            $table->integer('hours_per_week')->default(3);
            $table->enum('type', ['core', 'elective', 'practical']);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};