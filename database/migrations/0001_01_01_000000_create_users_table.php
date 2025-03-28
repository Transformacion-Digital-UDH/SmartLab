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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nombres')->nullable();
            $table->string('apellidos')->nullable();
            $table->string('dni', 8)->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('password')->nullable();
            $table->string('celular')->nullable();
            $table->string('codigo', 20)->nullable();
            $table->enum('estado_cuenta', ['En revisión', 'Aprobada', 'Desaprobada', 'Suspendida'])->nullable();
            $table->integer('se_registro')->default(0);
            $table->enum('rol', ['Libre', 'Invitado', 'Admin']);
            $table->string('profile_photo_path', 2048)->nullable();
            $table->text('razon_registro')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('google_token_json')->nullable();

            $table->foreignId('laboratorio_seleccionado')->nullable()->constrained('laboratorios')->onDelete('set null');

            $table->rememberToken();

            $table->foreignId('current_team_id')->nullable();

            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
