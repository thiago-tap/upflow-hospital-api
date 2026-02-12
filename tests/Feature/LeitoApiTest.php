<?php

namespace Tests\Feature;

use App\Enums\StatusLeito;
use App\Enums\TipoLeito;
use App\Models\Leito;
use App\Models\Paciente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeitoApiTest extends TestCase
{
    use RefreshDatabase;

    private function criarLeito(string $codigo = 'UTI-01', string $tipo = 'UTI', string $status = 'LIVRE'): Leito
    {
        return Leito::create([
            'codigo' => $codigo,
            'tipo' => $tipo,
            'status' => $status,
        ]);
    }

    private function criarPaciente(string $nome = 'João Silva', string $cpf = '52998224725'): Paciente
    {
        return Paciente::create([
            'nome' => $nome,
            'cpf' => $cpf,
        ]);
    }

    // ==================== LISTAR LEITOS ====================

    public function test_listar_leitos_retorna_paginado(): void
    {
        $this->criarLeito('UTI-01');
        $this->criarLeito('UTI-02');

        $response = $this->getJson('/api/leitos');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id_leito', 'codigo', 'tipo', 'status'],
                ],
                'links',
                'meta',
            ]);
    }

    public function test_listar_leitos_com_per_page_customizado(): void
    {
        $this->criarLeito('UTI-01');
        $this->criarLeito('UTI-02');
        $this->criarLeito('UTI-03');

        $response = $this->getJson('/api/leitos?per_page=2');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.per_page', 2);
    }

    public function test_listar_leitos_com_paciente(): void
    {
        $paciente = $this->criarPaciente();
        $leito = $this->criarLeito();
        $leito->update(['paciente_id' => $paciente->id, 'status' => StatusLeito::OCUPADO->value]);

        $response = $this->getJson('/api/leitos');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'nome' => 'João Silva',
                'cpf' => '52998224725',
            ]);
    }

    // ==================== OCUPAR LEITO ====================

    public function test_ocupar_leito_sucesso(): void
    {
        $leito = $this->criarLeito();
        $paciente = $this->criarPaciente();

        $response = $this->postJson('/api/leitos/ocupar', [
            'id_leito' => $leito->id,
            'id_paciente' => $paciente->id,
        ]);

        $response->assertStatus(200)
            ->assertJson(['mensagem' => 'Paciente internado com sucesso.']);

        $this->assertDatabaseHas('leitos', [
            'id' => $leito->id,
            'paciente_id' => $paciente->id,
            'status' => StatusLeito::OCUPADO->value,
        ]);

        $this->assertDatabaseHas('auditoria_leitos', [
            'leito_id' => $leito->id,
            'paciente_id' => $paciente->id,
            'acao' => 'OCUPAR',
        ]);
    }

    public function test_ocupar_leito_ja_ocupado(): void
    {
        $paciente1 = $this->criarPaciente('Paciente 1', '52998224725');
        $paciente2 = $this->criarPaciente('Paciente 2', '11144477735');
        $leito = $this->criarLeito();
        $leito->update(['paciente_id' => $paciente1->id, 'status' => StatusLeito::OCUPADO->value]);

        $response = $this->postJson('/api/leitos/ocupar', [
            'id_leito' => $leito->id,
            'id_paciente' => $paciente2->id,
        ]);

        $response->assertStatus(400)
            ->assertJson(['erro' => 'Este leito não está disponível (Ocupado ou em Manutenção).']);
    }

    public function test_ocupar_paciente_ja_internado(): void
    {
        $paciente = $this->criarPaciente();
        $leito1 = $this->criarLeito('UTI-01');
        $leito2 = $this->criarLeito('UTI-02');
        $leito1->update(['paciente_id' => $paciente->id, 'status' => StatusLeito::OCUPADO->value]);

        $response = $this->postJson('/api/leitos/ocupar', [
            'id_leito' => $leito2->id,
            'id_paciente' => $paciente->id,
        ]);

        $response->assertStatus(400)
            ->assertJson(['erro' => 'Este paciente já está ocupando outro leito.']);
    }

    public function test_ocupar_leito_validacao_campos_obrigatorios(): void
    {
        $response = $this->postJson('/api/leitos/ocupar', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['id_leito', 'id_paciente']);
    }

    // ==================== LIBERAR LEITO ====================

    public function test_liberar_leito_sucesso(): void
    {
        $paciente = $this->criarPaciente();
        $leito = $this->criarLeito();
        $leito->update(['paciente_id' => $paciente->id, 'status' => StatusLeito::OCUPADO->value]);

        $response = $this->postJson('/api/leitos/liberar', [
            'id_leito' => $leito->id,
        ]);

        $response->assertStatus(200)
            ->assertJson(['mensagem' => 'Leito liberado com sucesso.']);

        $this->assertDatabaseHas('leitos', [
            'id' => $leito->id,
            'paciente_id' => null,
            'status' => StatusLeito::LIVRE->value,
        ]);
    }

    public function test_liberar_leito_validacao(): void
    {
        $response = $this->postJson('/api/leitos/liberar', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['id_leito']);
    }

    // ==================== TRANSFERIR ====================

    public function test_transferir_sucesso(): void
    {
        $paciente = $this->criarPaciente();
        $leitoOrigem = $this->criarLeito('UTI-01');
        $leitoDestino = $this->criarLeito('UTI-02');
        $leitoOrigem->update(['paciente_id' => $paciente->id, 'status' => StatusLeito::OCUPADO->value]);

        $response = $this->postJson('/api/leitos/transferir', [
            'id_leito_atual' => $leitoOrigem->id,
            'id_leito_destino' => $leitoDestino->id,
        ]);

        $response->assertStatus(200)
            ->assertJson(['mensagem' => 'Transferência realizada com sucesso.']);

        $this->assertDatabaseHas('leitos', [
            'id' => $leitoOrigem->id,
            'paciente_id' => null,
            'status' => StatusLeito::LIVRE->value,
        ]);

        $this->assertDatabaseHas('leitos', [
            'id' => $leitoDestino->id,
            'paciente_id' => $paciente->id,
            'status' => StatusLeito::OCUPADO->value,
        ]);

        $this->assertDatabaseHas('auditoria_leitos', [
            'acao' => 'TRANSFERIR',
            'paciente_id' => $paciente->id,
        ]);
    }

    public function test_transferir_sem_paciente_na_origem(): void
    {
        $leitoOrigem = $this->criarLeito('UTI-01');
        $leitoDestino = $this->criarLeito('UTI-02');

        $response = $this->postJson('/api/leitos/transferir', [
            'id_leito_atual' => $leitoOrigem->id,
            'id_leito_destino' => $leitoDestino->id,
        ]);

        $response->assertStatus(400)
            ->assertJson(['erro' => 'Não há paciente no leito de origem para transferir.']);
    }

    public function test_transferir_destino_ocupado(): void
    {
        $paciente1 = $this->criarPaciente('Paciente 1', '52998224725');
        $paciente2 = $this->criarPaciente('Paciente 2', '11144477735');
        $leitoOrigem = $this->criarLeito('UTI-01');
        $leitoDestino = $this->criarLeito('UTI-02');
        $leitoOrigem->update(['paciente_id' => $paciente1->id, 'status' => StatusLeito::OCUPADO->value]);
        $leitoDestino->update(['paciente_id' => $paciente2->id, 'status' => StatusLeito::OCUPADO->value]);

        $response = $this->postJson('/api/leitos/transferir', [
            'id_leito_atual' => $leitoOrigem->id,
            'id_leito_destino' => $leitoDestino->id,
        ]);

        $response->assertStatus(400)
            ->assertJson(['erro' => 'O leito de destino já está ocupado.']);
    }

    // ==================== BUSCAR POR CPF ====================

    public function test_buscar_por_cpf_encontrado(): void
    {
        $paciente = $this->criarPaciente();
        $leito = $this->criarLeito();
        $leito->update(['paciente_id' => $paciente->id, 'status' => StatusLeito::OCUPADO->value]);

        $response = $this->getJson('/api/pacientes/52998224725/leito');

        $response->assertStatus(200)
            ->assertJson([
                'paciente' => 'João Silva',
                'leito' => 'UTI-01',
            ]);
    }

    public function test_buscar_por_cpf_nao_encontrado(): void
    {
        // CPF válido mas que não existe no banco
        $response = $this->getJson('/api/pacientes/52998224725/leito');

        $response->assertStatus(404)
            ->assertJson(['erro' => 'Paciente não encontrado.']);
    }

    public function test_buscar_por_cpf_invalido(): void
    {
        $response = $this->getJson('/api/pacientes/00000000000/leito');

        $response->assertStatus(422)
            ->assertJson(['erro' => 'O cpf informado é inválido.']);
    }

    public function test_buscar_por_cpf_paciente_nao_internado(): void
    {
        $this->criarPaciente();

        $response = $this->getJson('/api/pacientes/52998224725/leito');

        $response->assertStatus(200)
            ->assertJson(['mensagem' => 'O paciente não está internado no momento.']);
    }

    // ==================== LISTAR PACIENTES ====================

    public function test_listar_pacientes_retorna_paginado(): void
    {
        $this->criarPaciente('Paciente 1', '52998224725');
        $this->criarPaciente('Paciente 2', '11144477735');

        $response = $this->getJson('/api/pacientes');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'nome', 'cpf'],
                ],
                'links',
                'meta',
            ]);
    }

    // ==================== RATE LIMITING ====================

    public function test_rate_limiting(): void
    {
        $this->criarLeito();

        // Faz muitas requisições para atingir o limite
        for ($i = 0; $i < 61; $i++) {
            $response = $this->getJson('/api/leitos');
        }

        $response->assertStatus(429);
    }
}
