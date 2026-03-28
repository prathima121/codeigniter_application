import { Link, Navigate, Route, Routes, useNavigate } from 'react-router-dom'
import ProtectedRoute from './components/ProtectedRoute'
import CreateTeacherPage from './pages/CreateTeacherPage'
import LoginPage from './pages/LoginPage'
import RegisterPage from './pages/RegisterPage'
import TeachersTablePage from './pages/TeachersTablePage'
import UsersTablePage from './pages/UsersTablePage'
import './App.css'

function App() {
  const navigate = useNavigate()
  const token = localStorage.getItem('auth_token')

  function logout() {
    localStorage.removeItem('auth_token')
    localStorage.removeItem('auth_user')
    navigate('/login')
  }

  return (
    <div className="app-shell">
      <header className="top-bar">
        <div>
          <p className="brand-kicker">CodeIgniter + React</p>
          <h2>Teacher Auth Console</h2>
        </div>
        <nav className="nav-links">
          <Link to="/users">Users</Link>
          <Link to="/teachers">Teachers</Link>
          <Link to="/create">Create Pair</Link>
          {!token && <Link to="/login">Login</Link>}
          {!token && <Link to="/register">Register</Link>}
          {token && (
            <button className="btn ghost" onClick={logout}>
              Logout
            </button>
          )}
        </nav>
      </header>

      <main className="content">
        <Routes>
          <Route path="/" element={<Navigate to={token ? '/users' : '/login'} replace />} />
          <Route path="/login" element={<LoginPage />} />
          <Route path="/register" element={<RegisterPage />} />

          <Route
            path="/create"
            element={
              <ProtectedRoute>
                <CreateTeacherPage />
              </ProtectedRoute>
            }
          />
          <Route
            path="/users"
            element={
              <ProtectedRoute>
                <UsersTablePage />
              </ProtectedRoute>
            }
          />
          <Route
            path="/teachers"
            element={
              <ProtectedRoute>
                <TeachersTablePage />
              </ProtectedRoute>
            }
          />
          <Route path="*" element={<Navigate to="/" replace />} />
        </Routes>
      </main>
    </div>
  )
}

export default App
