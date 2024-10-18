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
            $table->date('inauguracion')->nullable();
            $table->boolean('is_active')->default(true);

            $table->foreignId('responsable_id')->constrained('users')->onDelete('restrict');

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
