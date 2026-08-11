# PHP Feed

Projeto de feed com backend em Laravel e frontend em Vite/Nginx.

## Estrutura do projeto

- `Backend/Feed_Backend` - API Laravel e backend PHP
- `Frontend/Feed_frontend` - cliente frontend com Vite e Nginx
- `compose.yaml` - configuração Docker Compose para executar backend, frontend e banco de dados juntos

## Requisitos

- Docker
- Docker Compose

## Como usar

1. Abra o terminal na raiz do projeto:
   ```bash
   cd c:/Users/DELL/Documents/GitHub/PHP_Feed
   ```

2. Inicie os containers:
   ```bash
   docker compose up --build
   ```

3. Acesse:
   - Backend: `http://localhost:8000`
   - Frontend: `http://localhost:5173`

## Configurações

- O backend usa `mysql` como serviço de banco de dados
- O frontend consome o backend em `http://localhost:8000/api` por padrão

## Observações

- O backend usa `Dockerfile.dev` para montar volume e permitir alterações em tempo real.
- Se for necessário rodar migrations, abra outro terminal e execute no container backend:
  ```bash
  docker compose exec backend php artisan migrate
  ```
