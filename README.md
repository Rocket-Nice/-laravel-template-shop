# Le Mousse

<p align="center">
    <a href="https://lemousse.shop" target="_blank">
        <img src="https://lemousse.shop/img/season/logo-winter.png" alt="Le Mousse" width="220">
    </a>
</p>

<h1 align="center">Le Mousse</h1>

<p align="center">
  Веб-приложение на <strong>Laravel Framework 9.52.10</strong>
</p>

---

## Технологический стек

- **Backend:** PHP 8.0+  
- **Framework:** Laravel 9.52.10  
- **Database:** MySQL / MariaDB
- **Frontend:** Blade / Bootstrap / Vite / Alpine.js
- **Package manager:** Composer  
- **Web server:** Nginx / Apache  
- **Environment:** `.env`

---

## Структура проекта

Проект использует стандартную структуру Laravel:

```bash
app/ — бизнес-логика приложения
bootstrap/ — загрузка и инициализация фреймворка
config/ — конфигурационные файлы
database/ — миграции, сиды, фабрики
public/ — публичная директория (index.php, assets)
resources/ — Blade-шаблоны, стили, скрипты
routes/ — маршруты (web.php, api.php)
storage/ — логи, кэш, загруженные файлы
tests/ — тесты
.env.example — пример конфигурации окружения
```

---

## Требования к окружению

Перед установкой убедитесь, что на сервере установлены:

- PHP **>= 8.0**
- Node.js (для сборки фронта)

---

## Инструкция по сборке и запуску

### 1. Клонирование репозитория

```bash
git clone <repository_url>
cd lemus
```

### 2. Установка зависимостей

```bash
composer install
```

### 3. Установка npm-зависимостей

```bash
npm install
```

### 4. Настройка окружения

```bash
cp .env.example .env
```

### 5. Генерация ключа приложения

```bash
php artisan key:generate
```

### 6. Миграции базы данных

```bash
php artisan migrate
```

### 7. Сборка фронтенда

```bash
npm run build

# Для разработки можно использовать:
# npm run dev
```

## API документация

В проекте реализована интерактивная документация API с использованием **Swagger (OpenAPI)**.

Документация доступна по маршруту: /api-documentation
