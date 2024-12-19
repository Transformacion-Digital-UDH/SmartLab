<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            $table->dateTime('hora_inicio');
            $table->dateTime('hora_fin');
            $table->enum('estado', allowed: ['Por aprobar', 'Aprobada', 'No aprobada', 'Finalizada'])->default('Por aprobar');
            $table->boolean('is_active')->default(true);

            $table->foreignId('usuario_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('equipo_id')->nullable()->constrained('equipos')->onDelete('restrict');
            $table->foreignId('recurso_id')->nullable()->constrained('recursos')->onDelete('restrict');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
