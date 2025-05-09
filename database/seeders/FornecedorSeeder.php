<?php

namespace Database\Seeders;

use App\Models\Fornecedor;
use Illuminate\Database\Seeder;

class FornecedorSeeder extends Seeder
{
    public function run()
    {
        Fornecedor::create([
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
    }
}