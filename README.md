# WakaTrack

Sistema full-stack compatível com ingestão principal da API do WakaTime, construído com Laravel 13 + Inertia.js (Vue 3), MySQL e Docker Compose.

## Stack

- Backend: Laravel 13
- Frontend: Inertia.js + Vue 3
- Banco: MySQL 8.4
- Testes: Pest/PHPUnit
- Infra: Docker multi-stage + Docker Compose

## Compatibilidade WakaTime (core)

Endpoints implementados:

- `POST /api/v1/users/current/heartbeats`
- `POST /api/v1/users/current/heartbeats.bulk`

Autenticação:

- `Authorization: Basic <base64(API_KEY)>`
- ou `?api_key=...`

Payload suportado (core):

- `time` (ou `timestamp`)
- `entity`
- `type` (`file`, `app`, `domain`)
- `project`
- `language`
- `editor`
- `is_write`

## Setup com Docker

```bash
cp .env.example .env
docker compose up -d --build
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --force
```

Aplicação: `http://localhost:8080`

## Desenvolvimento local sem Docker

```bash
cp .env.example .env
composer install
npm install
php artisan key:generate
php artisan migrate
npm run dev
php artisan serve
```

## Testes e cobertura

```bash
php artisan test
php -d xdebug.mode=coverage ./vendor/bin/pest --coverage --min=75
```

## Funcionalidades principais

- Cadastro e login com Breeze
- API key por usuário com armazenamento hash SHA-256
- Ingestão de heartbeats com deduplicação por hash e proximidade temporal
- Agregação por projeto, linguagem, editor e dia
- Dashboard Inertia com filtros (7 dias e intervalo customizado)
- Página de projetos derivados dos dados rastreados
