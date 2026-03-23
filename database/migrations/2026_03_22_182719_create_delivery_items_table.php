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
        Schema::create('delivery_items', function (Blueprint $table) {
            $table->id();
            
            // Relación con la remisión
            $table->unsignedBigInteger('delivery_id');
            $table->foreign('delivery_id')->references('id')->on('deliveries')->onDelete('cascade');
            
            // Relación Polimórfica: Crea las columnas 'item_type' (ej. App\Models\Asset) e 'item_id'
            $table->morphs('item');
            
            $table->string('notes')->nullable()->comment('Condición específica o nota sobre el equipo al ser entregado');
            
            $table->timestamps();
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_items');
    }
};
