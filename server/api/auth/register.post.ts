import { hash } from 'bcryptjs'
import { pool } from '../../utils/db'
import { requireAdmin } from '../../utils/auth'

const MIN_PASSWORD_LENGTH = 6

export default defineEventHandler(async (event) => {
  const body = await readBody(event)
  const email = typeof body?.email === 'string' ? body.email.trim().toLowerCase() : ''
  const password = body?.password
  const role = body?.role === 'admin' ? 'admin' : 'editor'

  if (!email || !password) {
    throw createError({
      statusCode: 400,
      statusMessage: 'Email and password are required'
    })
  }

  if (String(password).length < MIN_PASSWORD_LENGTH) {
    throw createError({
      statusCode: 400,
      statusMessage: `Password must be at least ${MIN_PASSWORD_LENGTH} characters`
    })
  }

  const { rows: existing } = await pool.query('SELECT id FROM users WHERE email = $1', [email])
  if (existing.length > 0) {
    throw createError({
      statusCode: 409,
      statusMessage: 'User already exists'
    })
  }

  const { rows: countRows } = await pool.query('SELECT COUNT(*)::int AS count FROM users')
  const totalUsers = countRows[0]?.count ?? 0

  if (totalUsers > 0) {
    await requireAdmin(event)
  }

  const passwordHash = await hash(password, 10)
  const userRole = totalUsers === 0 ? 'admin' : role

  const { rows } = await pool.query(
    'INSERT INTO users (email, password_hash, role) VALUES ($1, $2, $3) RETURNING id, email, role',
    [email, passwordHash, userRole]
  )

  return { user: rows[0] }
})
