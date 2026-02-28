import { pool } from '../../utils/db'
import { requireAdmin } from '../../utils/auth'

export default defineEventHandler(async (event) => {
  await requireAdmin(event)

  const { rows } = await pool.query(
    `SELECT c.id, c.content, c.author_name, c.is_approved, c.created_at, p.title as post_title
     FROM comments c
     JOIN posts p ON p.id = c.post_id
     ORDER BY c.created_at DESC`
  )

  return rows
})
