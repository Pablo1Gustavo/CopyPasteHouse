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

## Features

- User authentication (register, login, logout)
- Create, view, edit, and delete pastes
- Syntax highlighting support
- Paste expiration options
- Password-protected pastes
- Public and unlisted pastes
- Tags for organizing pastes
- Destroy on first view option

## License

Open-source software licensed under the MIT license.

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
