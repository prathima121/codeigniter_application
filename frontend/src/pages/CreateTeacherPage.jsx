import { useState } from 'react'
import { createUserAndTeacher } from '../lib/api'

const currentYear = new Date().getFullYear()

function CreateTeacherPage() {
  const [form, setForm] = useState({
    email: '',
    first_name: '',
    last_name: '',
    password: '',
    university_name: '',
    gender: 'male',
    year_joined: String(currentYear),
  })
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState('')
  const [success, setSuccess] = useState('')

  async function handleSubmit(event) {
    event.preventDefault()
    setError('')
    setSuccess('')
    setLoading(true)

    try {
      const response = await createUserAndTeacher({
        ...form,
        year_joined: Number(form.year_joined),
      })
      setSuccess(response.message)
      setForm({
        email: '',
        first_name: '',
        last_name: '',
        password: '',
        university_name: '',
        gender: 'male',
        year_joined: String(currentYear),
      })
    } catch (err) {
      setError(err.message)
    } finally {
      setLoading(false)
    }
  }

  return (
    <section className="card">
      <h1>Create User + Teacher</h1>
      <p className="subtitle">
        Single protected POST API that inserts into auth_user and teachers.
      </p>
      <form onSubmit={handleSubmit} className="form-grid two-col">
        <label>
          Email
          <input
            type="email"
            value={form.email}
            onChange={(e) => setForm({ ...form, email: e.target.value })}
            required
          />
        </label>
        <label>
          Password
          <input
            type="password"
            minLength={8}
            value={form.password}
            onChange={(e) => setForm({ ...form, password: e.target.value })}
            required
          />
        </label>
        <label>
          First name
          <input
            type="text"
            value={form.first_name}
            onChange={(e) => setForm({ ...form, first_name: e.target.value })}
            required
          />
        </label>
        <label>
          Last name
          <input
            type="text"
            value={form.last_name}
            onChange={(e) => setForm({ ...form, last_name: e.target.value })}
            required
          />
        </label>
        <label>
          University name
          <input
            type="text"
            value={form.university_name}
            onChange={(e) => setForm({ ...form, university_name: e.target.value })}
            required
          />
        </label>
        <label>
          Year joined
          <input
            type="number"
            min={1950}
            max={2099}
            value={form.year_joined}
            onChange={(e) => setForm({ ...form, year_joined: e.target.value })}
            required
          />
        </label>
        <label>
          Gender
          <select
            value={form.gender}
            onChange={(e) => setForm({ ...form, gender: e.target.value })}
          >
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="other">Other</option>
          </select>
        </label>

        <div className="full-row">
          {error && <p className="error">{error}</p>}
          {success && <p className="success">{success}</p>}
          <button className="btn" type="submit" disabled={loading}>
            {loading ? 'Submitting...' : 'Create User + Teacher'}
          </button>
        </div>
      </form>
    </section>
  )
}

export default CreateTeacherPage
