# CopyPasteHouse

A Pastebin clone for sharing text snippets and code.

## Stack

- Laravel 12
- PHP 8.2
- SQLite
- Tailwind CSS v4
- Vite

## Requirements

- PHP 8.2 or higher
- Composer
- Node.js 20+ and npm
- SQLite extension (php8.2-sqlite3)

## Installation

1. Clone the repository:
```bash
git clone https://github.com/Pablo1Gustavo/CopyPasteHouse.git
cd CopyPasteHouse
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install JavaScript dependencies:
```bash
npm install
```

4. Copy environment file:
```bash
cp .env.example .env
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Create database and run migrations:
```bash
touch database/database.sqlite
php artisan migrate
```

7. Seed the database with syntax highlights and expiration times:
```bash
php artisan db:seed
```

## Running the Application

Start the Laravel development server:
```bash
php artisan serve
```

In a separate terminal, start the Vite development server:
```bash
npm run dev
```

Access the application at http://127.0.0.1:8000

## API Documentation

The API is documented using Swagger/OpenAPI. To access the interactive API documentation:

1. Make sure the application is running (`php artisan serve`)
2. Visit: **http://127.0.0.1:8000/api/documentation**

