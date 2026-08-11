# Feed Backend

API backend for a social feed application built with Laravel 12 and Sanctum authentication.

## Overview

Esta aplicação fornece endpoints para:
- cadastro e login de usuários
- atualização de perfil e avatar
- publicação de posts com mídia
- curtidas e comentários em posts
- seguir/desseguir usuários
- pesquisa de usuários
- feed paginado de posts

## Requisitos

- PHP 8.2+
- Composer
- Node.js / npm (para frontend e Vite)
- MySQL ou MariaDB
- Docker e Docker Compose (opcional)

## Instalação

1. Clone o repositório

   ```bash
   git clone <repo-url>
   cd Feed_Backend
   ```

2. Copie o arquivo de ambiente

   ```bash
   cp .env.example .env
   ```

3. Configure as variáveis de ambiente no `.env`

   A configuração padrão em `.env.example` já está preparada para rodar com o Docker Compose do projeto:

   ```dotenv
   APP_NAME=Laravel
   APP_ENV=local
   APP_KEY=
   APP_DEBUG=true
   APP_URL=http://localhost:8000

   DB_CONNECTION=mysql
   DB_HOST=mysql
   DB_PORT=3306
   DB_DATABASE=laravel
   DB_USERNAME=laravel
   DB_PASSWORD=laravel
   ```

### Opção 1: rodar com Docker Compose

```bash
docker compose up --build
```

- O serviço `php` expõe a porta `8000`.
- O serviço `mysql` expõe a porta `3306`.

Para executar comandos Artisan no contêiner PHP:

```bash
docker compose exec php php artisan migrate --seed
```

### Opção 2: rodar localmente

```bash
composer install
npm install
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Se for executar localmente sem Docker, ajuste `DB_HOST` para `127.0.0.1` no `.env`.

## Docker / Docker Compose

A aplicação inclui suporte a `docker compose` usando `compose.yaml`.

```bash
docker compose up --build
```

O serviço `php` expõe a porta `8000`.
O serviço `mysql` expõe a porta `3306`.

## Comandos úteis

- `php artisan migrate --seed`
- `php artisan migrate:fresh --seed`
- `php artisan storage:link`
- `npm run dev`
- `npm run build`

## Endpoints principais

### Autenticação
- `POST /api/register` — registrar usuário
- `POST /api/login` — login
- `POST /api/logout` — logout
- `GET /api/me` — dados do usuário autenticado

### Perfil
- `GET /api/profile` — ver perfil próprio
- `PUT /api/profile` — atualizar perfil
- `GET /api/users/{username}` — ver perfil de outro usuário

### Seguidores
- `POST /api/users/{username}/follow` — seguir usuário
- `DELETE /api/users/{username}/follow` — deixar de seguir usuário
- `GET /api/users/{username}/posts` — posts de um usuário

### Posts
- `GET /api/posts` — listar feed paginado
- `POST /api/posts` — criar post com mídia
- `GET /api/posts/{post}` — ver post individual
- `DELETE /api/posts/{post}` — excluir post

### Likes e comentários
- `POST /api/posts/{post}/like` — curtir post
- `DELETE /api/posts/{post}/like` — remover curtida
- `GET /api/posts/{post}/comments` — listar comentários
- `POST /api/posts/{post}/comments` — criar comentário

### Busca
- `GET /api/search?q={termo}` — buscar usuários por nome ou username

## Observações de performance

- O feed de posts usa paginação para limitar a quantidade de resultados.
- A busca atual realiza `LIKE %query%` em nome/username, que pode ficar lenta em tabelas grandes.
- Comentários são retornados sem paginação no endpoint atual.

## Estrutura de diretórios

- `app/Http/Controllers/Api` — controladores de API
- `app/Services` — lógica de negócio e serviço
- `app/Models` — modelos Eloquent
- `routes/api.php` — rotas de API
- `database/migrations` — migrações do banco
- `resources/js` — frontend JS e configuração do Vite

## Dependências

- `laravel/framework` ^12.0
- `laravel/sanctum` ^4.0
- `darkaonline/l5-swagger` para documentação OpenAPI
- `axios` no frontend
- `vite` + `tailwindcss`

## Licença

MIT
