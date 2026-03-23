<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta las migraciones.
     */
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('folio')->unique()->comment('Número único de remisión (ej. REM-001)');
            
            // Relaciones Directas a Usuarios/Localidades de Snipe-IT
            $table->integer('admin_id')->unsigned()->index()->comment('Usuario admin que genera la remisión');
            $table->integer('user_id')->unsigned()->nullable()->index()->comment('Empleado que recibe la remisión');
            $table->integer('location_id')->unsigned()->nullable()->index()->comment('Localidad donde se envía la remisión');
            
            // Información General
            $table->string('status')->default('generada');
            $table->text('notes')->nullable();
            
            // Rutas de archivos a Storage
            $table->string('pdf_path')->nullable();
            $table->string('signature_path')->nullable();
            
            // Control de tiempo y eliminación segura
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
