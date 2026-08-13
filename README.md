# Микро-CRM

Тестовое задание: микросервис учёта товаров, заказов, поставок и истории движения остатков.

- **Backend:** PHP 8.4 / Laravel 12, REST API (`/api/v1`), MariaDB (совместима с MySQL).
- **Frontend:** Vue 3 + Vite + TypeScript, стили — Tailwind CSS.

## Структура

```
backend/       Laravel-приложение (REST API, миграции, фабрики, feature-тесты)
frontend/      Vue 3 + Vite + TypeScript (UI: заказы, движения товаров)
docker/        Dockerfile для PHP и Node.js, конфиги nginx/php
docker-compose.yml
```

## Требования

- Docker с Docker Compose v2 (`docker compose`).

## Запуск

Все команды выполняются из корня проекта.

### 1. Сборка и запуск контейнеров

```bash
docker compose up -d --build
```

Поднимаются 4 сервиса:

| Сервис    | Порт                | Что это                            |
|-----------|---------------------|------------------------------------|
| `db`      | `3306`              | MariaDB (БД `ocrm`)                |
| `backend` | `9000` (внутри)     | PHP-FPM + Laravel                  |
| `web`     | `8080 -> 80`        | Nginx (REST API)                   |
| `frontend`| `5173`              | Vite dev-сервер (UI)               |

### 2. Установка зависимостей и подготовка БД

```bash
# Композер-зависимости backend
docker compose exec backend composer install

# Node-зависимости frontend (устанавливаются внутрь volume контейнера)
docker compose exec frontend sh -c 'cd /app && npm install'

# Применить миграции и наполнить тестовыми данными
docker compose exec backend php artisan migrate:fresh --seed
```

### 3. Доступ к приложению

- **UI:** http://localhost:5173
- **REST API:** http://localhost:8080/api/v1

## Наполнение тестовыми данными

```bash
docker compose exec backend php artisan ocrm:seed-test-data
```

Создаёт склады, товары, остатки и клиентов.

## Тесты

Запуск feature-тестов REST API внутри контейнера backend (там есть `dom`, `xmlwriter`, `sqlite`):

```bash
docker compose exec backend vendor/bin/phpunit
```

Проверка TypeScript и продакшен-сборки фронтенда:

```bash
docker compose exec frontend sh -c 'cd /app && npm run build'
```

## Основные эндпоинты API (v1)

| Метод | URL                             | Описание                        |
|-------|---------------------------------|---------------------------------|
| GET   | `/api/v1/warehouses`            | Список складов                  |
| GET   | `/api/v1/products`              | Товары с остатками по складам   |
| GET   | `/api/v1/customers`             | Клиенты (фильтры, пагинация)    |
| GET   | `/api/v1/orders`                | Заказы (фильтры, пагинация)     |
| POST  | `/api/v1/orders`                | Создать заказ (списание остатка)|
| PUT   | `/api/v1/orders/{id}`           | Обновить заказ                  |
| POST  | `/api/v1/orders/{id}/complete`  | Завершить заказ                 |
| POST  | `/api/v1/orders/{id}/cancel`    | Отменить заказ (возврат остатка)|
| POST  | `/api/v1/orders/{id}/revive`    | Возобновить заказ               |
| GET   | `/api/v1/supplies`              | Поставки (пагинация)            |
| POST  | `/api/v1/supplies`              | Создать поставку (+ остаток)    |
| GET   | `/api/v1/movements`             | История движений (фильтры)      |
| POST  | `/api/v1/transfers`             | Создать перемещение             |
| POST  | `/api/v1/transfers/{id}/complete` | Провести перемещение          |
| POST  | `/api/v1/transfers/{id}/cancel` | Отменить перемещение            |

## Остановка

```bash
docker compose down
```

Полная пересборка с очисткой данных (сброс томов):

```bash
docker compose down -v && docker compose up -d --build
```