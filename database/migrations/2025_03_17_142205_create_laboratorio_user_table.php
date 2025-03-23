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
        Schema::create('laboratorio_user', function (Blueprint $table) {
            $table->id();
            $table->enum('rol', ['Miembro', 'Coordinador', 'Responsable']);
            $table->boolean('is_active')->default(true);

            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('laboratorio_id')->constrained()->onDelete('cascade');

            $table->unique(['user_id', 'laboratorio_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laboratorio_user');
    }
};
