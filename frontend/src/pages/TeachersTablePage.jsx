import { useEffect, useState } from 'react'
import { getTeachers } from '../lib/api'

function TeachersTablePage() {
  const [rows, setRows] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')

  useEffect(() => {
    async function load() {
      try {
        const response = await getTeachers()
        setRows(response.data)
      } catch (err) {
        setError(err.message)
      } finally {
        setLoading(false)
      }
    }

    load()
  }, [])

  if (loading) return <p>Loading teachers table...</p>
  if (error) return <p className="error">{error}</p>

  return (
    <section className="card">
      <h1>teachers Table</h1>
      <div className="table-wrap">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>User ID</th>
              <th>University</th>
              <th>Gender</th>
              <th>Year Joined</th>
            </tr>
          </thead>
          <tbody>
            {rows.map((row) => (
              <tr key={row.id}>
                <td>{row.id}</td>
                <td>{row.user_id}</td>
                <td>{row.university_name}</td>
                <td>{row.gender}</td>
                <td>{row.year_joined}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </section>
  )
}

export default TeachersTablePage
