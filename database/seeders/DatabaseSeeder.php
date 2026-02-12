<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Paciente;
use App\Models\Leito;
use App\Enums\StatusLeito;
use App\Enums\TipoLeito;
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
        // CPFs apenas numéricos (11 dígitos, válidos pelo algoritmo oficial)
        $pacientes = [
            ['nome' => 'João Silva', 'cpf' => '52998224725'],
            ['nome' => 'Maria Santos', 'cpf' => '11144477735'],
            ['nome' => 'Pedro Oliveira', 'cpf' => '22255588846'],
            ['nome' => 'Ana Costa', 'cpf' => '33366699957'],
            ['nome' => 'Carlos Souza', 'cpf' => '44477711107'],
            ['nome' => 'Juliana Ferreira', 'cpf' => '55588822200'],
            ['nome' => 'Roberto Lima', 'cpf' => '66699933310'],
            ['nome' => 'Fernanda Alves', 'cpf' => '77711144407'],
            ['nome' => 'Lucas Pereira', 'cpf' => '88822255500'],
            ['nome' => 'Patrícia Rodrigues', 'cpf' => '99933366610'],
            ['nome' => 'Ricardo Martins', 'cpf' => '12345678909'],
            ['nome' => 'Camila Souza', 'cpf' => '98765432100'],
            ['nome' => 'Bruno Costa', 'cpf' => '14725836982'],
            ['nome' => 'Amanda Oliveira', 'cpf' => '25836914737'],
            ['nome' => 'Felipe Santos', 'cpf' => '36914725837'],
        ];

        foreach ($pacientes as $paciente) {
            Paciente::create($paciente);
        }

        // Criar leitos de teste
        $leitos = [
            // UTI
            ['codigo' => 'UTI-01', 'tipo' => TipoLeito::UTI->value, 'status' => StatusLeito::LIVRE->value],
            ['codigo' => 'UTI-02', 'tipo' => TipoLeito::UTI->value, 'status' => StatusLeito::LIVRE->value],
            ['codigo' => 'UTI-03', 'tipo' => TipoLeito::UTI->value, 'status' => StatusLeito::LIVRE->value],
            ['codigo' => 'UTI-04', 'tipo' => TipoLeito::UTI->value, 'status' => StatusLeito::LIVRE->value],
            ['codigo' => 'UTI-05', 'tipo' => TipoLeito::UTI->value, 'status' => StatusLeito::LIVRE->value],
            // Enfermaria
            ['codigo' => 'ENFERMARIA-01', 'tipo' => TipoLeito::ENFERMARIA->value, 'status' => StatusLeito::LIVRE->value],
            ['codigo' => 'ENFERMARIA-02', 'tipo' => TipoLeito::ENFERMARIA->value, 'status' => StatusLeito::LIVRE->value],
            ['codigo' => 'ENFERMARIA-03', 'tipo' => TipoLeito::ENFERMARIA->value, 'status' => StatusLeito::LIVRE->value],
            ['codigo' => 'ENFERMARIA-04', 'tipo' => TipoLeito::ENFERMARIA->value, 'status' => StatusLeito::LIVRE->value],
            ['codigo' => 'ENFERMARIA-05', 'tipo' => TipoLeito::ENFERMARIA->value, 'status' => StatusLeito::LIVRE->value],
            // Quartos
            ['codigo' => 'QUARTO-01', 'tipo' => TipoLeito::QUARTO->value, 'status' => StatusLeito::LIVRE->value],
            ['codigo' => 'QUARTO-02', 'tipo' => TipoLeito::QUARTO->value, 'status' => StatusLeito::LIVRE->value],
            ['codigo' => 'QUARTO-03', 'tipo' => TipoLeito::QUARTO->value, 'status' => StatusLeito::LIVRE->value],
            ['codigo' => 'QUARTO-04', 'tipo' => TipoLeito::QUARTO->value, 'status' => StatusLeito::LIVRE->value],
            ['codigo' => 'QUARTO-05', 'tipo' => TipoLeito::QUARTO->value, 'status' => StatusLeito::LIVRE->value],
        ];

        foreach ($leitos as $leito) {
            Leito::create($leito);
        }
    }
}
