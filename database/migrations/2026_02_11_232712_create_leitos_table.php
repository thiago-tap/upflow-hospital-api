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
    Schema::create('leitos', function (Blueprint $table) {
        $table->id();
        $table->string('codigo')->unique(); // Ex: 'UTI-01'
        $table->string('tipo')->default('ENFERMARIA'); // UTI, QUARTO, ENFERMARIA
        $table->string('status')->default('LIVRE'); // LIVRE, OCUPADO, MANUTENCAO

        // Chave estrangeira para a tabela 'pacientes'.
        // 'nullable' significa que pode ficar vazio (livre).
        // 'unique' garante que um paciente só ocupe UM leito por vez.
        $table->foreignId('paciente_id')->nullable()->unique()->constrained('pacientes');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leitos');
    }
};
