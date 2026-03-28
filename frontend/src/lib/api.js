const API_BASE = import.meta.env.VITE_API_BASE_URL ?? 'http://localhost:8080/api'

function getToken() {
  return localStorage.getItem('auth_token')
}

async function request(path, { method = 'GET', body, auth = true } = {}) {
  const headers = {
    'Content-Type': 'application/json',
  }

  if (auth) {
    const token = getToken()
    if (token) {
      headers.Authorization = `Bearer ${token}`
    }
  }

  let response
  try {
    response = await fetch(`${API_BASE}${path}`, {
      method,
      headers,
      body: body ? JSON.stringify(body) : undefined,
    })
  } catch {
    throw new Error(
      `Cannot reach API at ${API_BASE}. Start backend server and verify CORS/URL configuration.`
    )
  }

  const data = await response.json().catch(() => ({}))

  if (!response.ok) {
    const message = data.message ?? 'Request failed'
    const errors = data.errors ? Object.values(data.errors).join(' | ') : ''
    throw new Error(errors || message)
  }

  return data
}

export function register(payload) {
  return request('/register', { method: 'POST', body: payload, auth: false })
}

export function login(payload) {
  return request('/login', { method: 'POST', body: payload, auth: false })
}

export function profile() {
  return request('/profile')
}

export function createUserAndTeacher(payload) {
  return request('/teachers/create-with-user', { method: 'POST', body: payload })
}

export function getAuthUsers() {
  return request('/auth-users')
}

export function getTeachers() {
  return request('/teachers')
}
