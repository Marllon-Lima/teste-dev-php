# API de Fornecedores

API para cadastro e gestão de fornecedores com integração à BrasilAPI.

## Requisitos

- PHP 8.0+
- Composer
- MySQL 5.7+
- Laravel 9.x

## Instalação

1. Clone o repositório:
```bash
git clone https://github.com/SEU_USUARIO/fornecedores-api.git
cd fornecedores-api
```

2. Instale as dependências:
```bash
composer install
```

3. Configure o ambiente:
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure o banco de dados no `.env`:
```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fornecedores
DB_USERNAME=root
DB_PASSWORD=
```

5. Execute as migrations:
```bash
php artisan migrate
```

## Uso

Inicie o servidor:
```bash
php artisan serve
```

### Endpoints

- `GET /api/fornecedores` - Lista fornecedores
- `POST /api/fornecedores` - Cria novo fornecedor
- `GET /api/fornecedores/{id}` - Mostra um fornecedor
- `PUT /api/fornecedores/{id}` - Atualiza fornecedor
- `DELETE /api/fornecedores/{id}` - Remove fornecedor

## Testes

Para executar os testes:
```bash
php artisan test
```
