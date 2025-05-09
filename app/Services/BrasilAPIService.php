<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BrasilAPIService
{
    public function consultarCNPJ($cnpj)
    {
        $response = Http::withoutVerifying()->get("https://brasilapi.com.br/api/cnpj/v1/{$cnpj}");
        
        if ($response->successful()) {
            return $response->json();
        }
        
        return null;
    }
}
