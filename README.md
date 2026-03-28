# CodeIgniter + React Auth and Teachers Demo

This repository contains:
- `backend` -> CodeIgniter 4 API (JWT auth + MySQL schema)
- `frontend` -> React (Vite) UI for Auth modules and datatables
- `database_exports/mysql_schema.sql` -> MySQL export script
- `database_exports/codeigniter_auth_demo_postman_collection.json` -> Postman collection for API testing

## Features Delivered

1. CodeIgniter application created (`backend`)
2. Basic Auth APIs:
   - `POST /api/register`
   - `POST /api/login`
3. Token-based auth (JWT Bearer) for protected APIs:
   - `GET /api/profile`
   - `POST /api/teachers/create-with-user`
   - `GET /api/auth-users`
   - `GET /api/teachers`
   - `GET /api/teachers-with-users`
4. MySQL configuration included (`backend/.env`)
5. Two tables with 1-1 relationship:
   - `auth_user`
   - `teachers` with `user_id` foreign key and unique constraint
6. Single protected POST API to insert both records in one transaction:
   - `POST /api/teachers/create-with-user`
7. React UI with separate pages:
   - Register
   - Login
   - Create User + Teacher (single POST)
   - `auth_user` datatable page
   - `teachers` datatable page

## Backend Setup (CodeIgniter)

Prerequisites:
- PHP 8.1+
- Composer
- MySQL 8+

Steps:
1. Go to backend folder:
   - `cd backend`
2. Install dependencies:
   - `composer install`
3. Configure environment:
   - `.env` is already provided with sample values.
   - Update DB credentials and JWT secret in `.env`.
4. Create database:
   - `codeigniter_auth_demo` (or update `.env` DB name)
5. Run migrations:
   - `php spark migrate`
6. Start API server:
   - `php spark serve --host=localhost --port=8080`

## Frontend Setup (React)

Prerequisites:
- Node.js 18+

Steps:
1. Go to frontend folder:
   - `cd frontend`
2. Configure API URL:
   - Copy `.env.example` to `.env`
   - Ensure `VITE_API_BASE_URL=http://localhost:8080/api`
3. Install dependencies:
   - `npm install`
4. Run frontend:
   - `npm run dev`
5. Open:
   - `http://localhost:5173`

## API Contract (Quick)

### Register
`POST /api/register`
```json
{
  "email": "john@demo.com",
  "first_name": "John",
  "last_name": "Doe",
  "password": "password123"
}
```

### Login
`POST /api/login`
```json
{
  "email": "john@demo.com",
  "password": "password123"
}
```

### Create both auth_user + teachers (Protected)
`POST /api/teachers/create-with-user`
Headers:
- `Authorization: Bearer <token>`

```json
{
  "email": "teacher1@demo.com",
  "first_name": "Ava",
  "last_name": "Stone",
  "password": "password123",
  "university_name": "Stanford",
  "gender": "female",
  "year_joined": 2022
}
```

## Database Export
- MySQL SQL file: `database_exports/mysql_schema.sql`

## Postman Collection
- Import `database_exports/codeigniter_auth_demo_postman_collection.json`
- Collection variable `baseUrl` default: `http://localhost:8080/api`
- Run `Auth - Login` or `Auth - Register` first to auto-save `token`
- Then run protected requests (profile/create/list endpoints)


