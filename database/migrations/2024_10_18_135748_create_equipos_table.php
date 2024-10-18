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
        Schema::create('equipos', function (Blueprint $table) {
            $table->id(); 
            $table->string('nombre', 100);
            $table->string('codigo', 20);
            $table->enum('tipo', ['Reservable', 'No reservable', 'Suministro']);
            $table->text('descripcion')->nullable();
            $table->enum('estado', ['Activo', 'Inactivo', 'Reservado', 'Prestado']);
            $table->boolean('is_active')->default(true);

            $table->foreignId('area_id')->constrained('areas')->onDelete('restrict');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipos');
    }
};
