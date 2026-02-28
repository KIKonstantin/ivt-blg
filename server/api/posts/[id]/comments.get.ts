import { pool } from '../../../utils/db'

export default defineEventHandler(async (event) => {
  const id = Number(getRouterParam(event, 'id'))
  if (!Number.isFinite(id)) {
    throw createError({
      statusCode: 400,
      statusMessage: 'Invalid post id'
    })
  }

  const { rows } = await pool.query(
    `SELECT id, content, author_name, created_at
     FROM comments
     WHERE post_id = $1 AND is_approved = TRUE
     ORDER BY created_at DESC`,
    [id]
  )

  return rows
})
