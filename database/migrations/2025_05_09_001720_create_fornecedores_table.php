<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fornecedores', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['PF', 'PJ']);
            $table->string('documento', 18)->unique();
            $table->string('nome_razao_social');
            $table->string('nome_fantasia')->nullable();
            $table->string('telefone', 20);
            $table->string('email');
            $table->string('cep', 9);
            $table->string('logradouro');
            $table->string('numero', 10);
            $table->string('complemento')->nullable();
            $table->string('bairro');
            $table->string('cidade');
            $table->string('uf', 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fornecedores');
    }
};