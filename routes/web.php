<?php

use Illuminate\Support\Facades\Route;

// Rotas de testes
Route::get('/', function () {
    return view('welcome');
});

Route::get('/teste', function() {
    return 'Funcionando!';
});