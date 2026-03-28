import { useEffect, useState } from 'react'
import { getAuthUsers } from '../lib/api'

function UsersTablePage() {
  const [rows, setRows] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')

  useEffect(() => {
    async function load() {
      try {
        const response = await getAuthUsers()
        setRows(response.data)
      } catch (err) {
        setError(err.message)
      } finally {
        setLoading(false)
      }
    }

    load()
  }, [])

  if (loading) return <p>Loading auth_user table...</p>
  if (error) return <p className="error">{error}</p>

  return (
    <section className="card">
      <h1>auth_user Table</h1>
      <div className="table-wrap">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Email</th>
              <th>First Name</th>
              <th>Last Name</th>
              <th>Created</th>
            </tr>
          </thead>
          <tbody>
            {rows.map((row) => (
              <tr key={row.id}>
                <td>{row.id}</td>
                <td>{row.email}</td>
                <td>{row.first_name}</td>
                <td>{row.last_name}</td>
                <td>{row.created_at ?? '-'}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </section>
  )
}

export default UsersTablePage
