<form action="{{ route('fornecedores.store') }}" method="POST">
    @csrf
    <input type="text" name="nome" placeholder="Nome do fornecedor">
    <button type="submit">Salvar</button>
</form>