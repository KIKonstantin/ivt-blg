import { pool } from '../utils/db'
import { requireAdmin } from '../utils/auth'
import { defineEventHandler, readBody } from 'h3'

export default defineEventHandler(async (event) => {
  await requireAdmin(event)
  const body = await readBody(event)
  const { name, description, content, image_url } = body

  // Check if author exists
  const { rowCount } = await pool.query('SELECT id FROM authors LIMIT 1')

  if (rowCount === 0) {
    await pool.query(
      'INSERT INTO authors (name, description, content, image_url) VALUES ($1, $2, $3, $4)',
      [name, description, content, image_url]
    )
  } else {
    await pool.query(
      'UPDATE authors SET name = $1, description = $2, content = $3, image_url = $4, updated_at = NOW()',
      [name, description, content, image_url]
    )
  }

  return { success: true }
})
