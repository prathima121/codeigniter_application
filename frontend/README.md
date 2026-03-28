# Frontend - React Auth Console

This React (Vite) app demonstrates the required authentication and data table modules for the CodeIgniter API.

## Pages

- `/register` -> Register user and store JWT
- `/login` -> Login and store JWT
- `/create` -> Protected page to call single POST API for `auth_user` + `teachers`
- `/users` -> Protected datatable for `auth_user`
- `/teachers` -> Protected datatable for `teachers`

## Tech Stack

- React
- React Router
- Fetch API
- Vite

## Environment

Create a `.env` file from `.env.example`:

```env
VITE_API_BASE_URL=http://localhost:8080/api
```

## Run Locally

```bash
npm install
npm run dev
```

Open `http://localhost:5173`.

## Build

```bash
npm run build
```
