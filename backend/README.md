# Backend - CodeIgniter JWT API

This backend provides:

- Basic authentication APIs (`register`, `login`)
- JWT Bearer token protection for secured APIs
- MySQL migration for `auth_user` and `teachers` with a 1-1 relationship
- Single protected POST API that inserts into both tables in one transaction

## Prerequisites

- PHP 8.2+
- Composer
- MySQL 8+

## Setup

1. Install dependencies:

```bash
composer install
```

Windows fallback (if `php`/`composer` are not in PATH):

```powershell
cd backend
Invoke-WebRequest https://getcomposer.org/download/latest-stable/composer.phar -OutFile composer.phar
C:\xampp\php\php.exe composer.phar install
```

2. Configure environment in `.env`:

- `app.baseURL = 'http://localhost:8080/'`
- MySQL connection values
- `auth.jwtSecret` (set a strong value)
- `auth.jwtExpiry` (seconds)

3. Create database:

```sql
CREATE DATABASE codeigniter_auth_demo;
```

4. Run migration:

```bash
php spark migrate
```

5. Start server:

```bash
php spark serve --host=localhost --port=8080
```

## APIs

Public:

- `POST /api/register`
- `POST /api/login`

Protected (Bearer token required):

- `GET /api/profile`
- `POST /api/teachers/create-with-user`
- `GET /api/auth-users`
- `GET /api/teachers`
- `GET /api/teachers-with-users`

## Notes

- Passwords are hashed with PHP `password_hash`.
- JWT is validated by `JwtAuthFilter` for protected routes.
- CORS is configured for frontend local dev origin `http://localhost:5173`.
