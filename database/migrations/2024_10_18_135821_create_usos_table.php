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
        Schema::create('usos', function (Blueprint $table) {
            $table->id();
            $table->dateTime('hora_entrada')->nullable();
            $table->dateTime('hora_salida')->nullable();
            $table->boolean('is_active')->default(true);

            $table->foreignId('recurso_id')->constrained('recursos')->onDelete('restrict');
            $table->foreignId('asistencia_id')->nullable()->constrained('asistencias')->onDelete('restrict');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usos');
    }
};
