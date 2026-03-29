<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('consumable_transactions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('consumable_id');
            $table->enum('type', ['user', 'location']);

            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('location_id')->nullable();

            $table->unsignedBigInteger('remision_id')->nullable();

            $table->integer('quantity');

            $table->enum('status', ['entregado', 'pendiente', 'anulado'])->default('entregado');

            $table->unsignedBigInteger('assigned_by');

            $table->text('notes')->nullable();

            $table->timestamps();

            // INDEXES
            $table->index('consumable_id');
            $table->index('user_id');
            $table->index('location_id');
            $table->index('remision_id');

            // FOREIGN KEYS
            $table->foreign('consumable_id')->references('id')->on('consumables')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('set null');
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('consumable_transactions');
    }
};