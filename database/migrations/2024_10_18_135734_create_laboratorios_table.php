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
        Schema::create('laboratorios', function (Blueprint $table) {
            $table->id(); // PK
            $table->string('nombre', 100);
            $table->string('codigo', 20)->nullable();
            $table->text('descripcion')->nullable();
            $table->integer('aforo')->nullable();
            $table->string('email', 100)->nullable();
            $table->string('inauguracion')->nullable();
            $table->string('ubicacion')->nullable();
            $table->string('google_calendar_id')->nullable();
            $table->boolean('is_active')->default(true);

            $table->foreignId('responsable_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('coordinador_id')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laboratorios');
    }
};
