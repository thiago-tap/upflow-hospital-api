<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auditoria_leitos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('leito_id')->nullable()->constrained('leitos')->nullOnDelete();
            $table->foreignId('paciente_id')->nullable()->constrained('pacientes')->nullOnDelete();
            $table->string('acao'); // OCUPAR, LIBERAR, TRANSFERIR
            $table->text('detalhes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditoria_leitos');
    }
};
