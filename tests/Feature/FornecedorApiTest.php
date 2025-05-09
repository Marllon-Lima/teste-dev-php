<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FornecedorApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_criar_fornecedor()
    {
        $response = $this->postJson('/api/fornecedores', [
            'tipo' => 'PJ',
            'documento' => '33014556000196',
            'nome_razao_social' => 'EMPRESA TESTE LTDA',
            'telefone' => '(11) 9999-8888',
            'email' => 'teste@empresa.com',
            'cep' => '01311000',
            'logradouro' => 'Av. Paulista',
            'numero' => '1000',
            'bairro' => 'Bela Vista',
            'cidade' => 'São Paulo',
            'uf' => 'SP'
        ]);

        $response->assertStatus(201);
    }

    public function test_listar_fornecedores()
    {
        $response = $this->getJson('/api/fornecedores');
        $response->assertStatus(200);
    }
}