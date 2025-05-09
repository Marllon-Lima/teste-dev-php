<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fornecedor extends Model
{
    protected $table = 'fornecedores';
    
    protected $fillable = [
        'tipo',
        'documento',
        'nome_razao_social',
        'nome_fantasia',
        'telefone',
        'email',
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'uf'
    ];

    protected $casts = [
        'documento' => 'string',
    ];
}