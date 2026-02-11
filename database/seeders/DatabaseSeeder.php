<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Paciente;
use App\Models\Leito;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Criar alguns pacientes de teste
        $pacientes = [
            ['nome' => 'João Silva', 'cpf' => '123.456.789-00'],
            ['nome' => 'Maria Santos', 'cpf' => '234.567.890-11'],
            ['nome' => 'Pedro Oliveira', 'cpf' => '345.678.901-22'],
            ['nome' => 'Ana Costa', 'cpf' => '456.789.012-33'],
            ['nome' => 'Carlos Souza', 'cpf' => '567.890.123-44'],
            ['nome' => 'Juliana Ferreira', 'cpf' => '678.901.234-55'],
            ['nome' => 'Roberto Lima', 'cpf' => '789.012.345-66'],
            ['nome' => 'Fernanda Alves', 'cpf' => '890.123.456-77'],
            ['nome' => 'Lucas Pereira', 'cpf' => '901.234.567-88'],
            ['nome' => 'Patrícia Rodrigues', 'cpf' => '012.345.678-99'],
            ['nome' => 'Ricardo Martins', 'cpf' => '111.222.333-44'],
            ['nome' => 'Camila Souza', 'cpf' => '222.333.444-55'],
            ['nome' => 'Bruno Costa', 'cpf' => '333.444.555-66'],
            ['nome' => 'Amanda Oliveira', 'cpf' => '444.555.666-77'],
            ['nome' => 'Felipe Santos', 'cpf' => '555.666.777-88'],
        ];

        foreach ($pacientes as $paciente) {
            Paciente::create($paciente);
        }

        // Criar leitos de teste
        $leitos = [
            // UTI
            ['codigo' => 'UTI-01', 'paciente_id' => null],
            ['codigo' => 'UTI-02', 'paciente_id' => null],
            ['codigo' => 'UTI-03', 'paciente_id' => null],
            ['codigo' => 'UTI-04', 'paciente_id' => null],
            ['codigo' => 'UTI-05', 'paciente_id' => null],
            // Enfermaria
            ['codigo' => 'ENFERMARIA-01', 'paciente_id' => null],
            ['codigo' => 'ENFERMARIA-02', 'paciente_id' => null],
            ['codigo' => 'ENFERMARIA-03', 'paciente_id' => null],
            ['codigo' => 'ENFERMARIA-04', 'paciente_id' => null],
            ['codigo' => 'ENFERMARIA-05', 'paciente_id' => null],
            // Quartos
            ['codigo' => 'QUARTO-01', 'paciente_id' => null],
            ['codigo' => 'QUARTO-02', 'paciente_id' => null],
            ['codigo' => 'QUARTO-03', 'paciente_id' => null],
            ['codigo' => 'QUARTO-04', 'paciente_id' => null],
            ['codigo' => 'QUARTO-05', 'paciente_id' => null],
        ];

        foreach ($leitos as $leito) {
            Leito::create($leito);
        }
    }
}
