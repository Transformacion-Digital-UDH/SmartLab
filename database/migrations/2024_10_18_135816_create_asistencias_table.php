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
    Schema::create('asistencias', function (Blueprint $table) {
        $table->id(); 
        $table->dateTime('hora_entrada');
        $table->dateTime('hora_salida')->nullable();
        $table->string('tarea', 255);
        $table->text('nota')->nullable();
        $table->boolean('is_active')->default(true);

        $table->foreignId('usuario_id')->constrained('users')->onDelete('restrict');
        $table->foreignId('proyecto_id')->nullable()->constrained('proyectos')->onDelete('restrict');

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};
