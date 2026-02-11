<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
// ESTAS SÃO AS LINHAS QUE FALTAM OU ESTÃO INCORRETAS:
use App\Models\Leito;
use App\Models\Paciente;

class HospitalSeeder extends Seeder
{
    public function run()
    {
        // Limpa a tabela antes de criar (opcional, evita erro de duplicidade se rodar 2x)
        // Leito::truncate();
        // Paciente::truncate();

        // Criar leitos
        Leito::create(['codigo' => 'UTI-01']);
        Leito::create(['codigo' => 'ENFERMARIA-10']);

        // Criar pacientes
        Paciente::create(['nome' => 'João da Silva', 'cpf' => '12345678900']);
        Paciente::create(['nome' => 'Maria Oliveira', 'cpf' => '98765432100']);
    }
}
