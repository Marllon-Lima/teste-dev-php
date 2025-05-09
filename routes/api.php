<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FornecedorController;

Route::prefix('fornecedores')->name('api.fornecedores.')->group(function() {
    Route::get('/', [FornecedorController::class, 'index'])->name('index');
    Route::post('/', [FornecedorController::class, 'store'])->name('store');
    Route::get('/{id}', [FornecedorController::class, 'show'])->name('show');
    Route::put('/{id}', [FornecedorController::class, 'update'])->name('update');
    Route::delete('/{id}', [FornecedorController::class, 'destroy'])->name('destroy');
});
