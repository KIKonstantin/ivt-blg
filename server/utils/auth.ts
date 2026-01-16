import type { H3Event } from 'h3'
import { getCookie, setCookie } from 'h3'
import { pool } from './db'

const SESSION_COOKIE = 'session_token'
const SESSION_TTL_HOURS = 8

export type SessionUser = {
  id: number
  email: string
  role: string
}

export const getSessionUser = async (event: H3Event) => {
  const token = getCookie(event, SESSION_COOKIE)
  if (!token) {
    return null
  }

  const { rows } = await pool.query(
    `SELECT users.id, users.email, users.role
     FROM sessions
     JOIN users ON users.id = sessions.user_id
     WHERE sessions.token = $1 AND sessions.expires_at > NOW()`,
    [token]
  )

  return rows[0] ?? null
}

export const isAdmin = async (event: H3Event) => {
  const user = await getSessionUser(event)
  return user?.role === 'admin'
}

export const requireAdmin = async (event: H3Event) => {
  const user = await getSessionUser(event)
  if (!user || user.role !== 'admin') {
    throw createError({
      statusCode: 403,
      statusMessage: 'Admin access required'
    })
  }
  return user
}

export const createSession = async (event: H3Event, userId: number) => {
  const token = crypto.randomUUID()
  const { rows } = await pool.query(
    `INSERT INTO sessions (token, user_id, expires_at)
     VALUES ($1, $2, NOW() + INTERVAL '${SESSION_TTL_HOURS} hours')
     RETURNING token`,
    [token, userId]
  )

  setCookie(event, SESSION_COOKIE, rows[0].token, {
    httpOnly: true,
    sameSite: 'lax',
    secure: process.env.NODE_ENV === 'production',
    path: '/',
    maxAge: SESSION_TTL_HOURS * 60 * 60
  })
}

export const clearSession = async (event: H3Event) => {
  const token = getCookie(event, SESSION_COOKIE)
  if (token) {
    await pool.query('DELETE FROM sessions WHERE token = $1', [token])
  }

  setCookie(event, SESSION_COOKIE, '', {
    httpOnly: true,
    sameSite: 'lax',
    secure: process.env.NODE_ENV === 'production',
    path: '/',
    maxAge: 0
  })
}
