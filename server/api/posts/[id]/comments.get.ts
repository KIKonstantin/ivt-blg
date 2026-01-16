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
    `SELECT comments.id, comments.content, comments.created_at, users.email
     FROM comments
     JOIN users ON users.id = comments.user_id
     WHERE comments.post_id = $1
     ORDER BY comments.created_at ASC`,
    [id]
  )

  return rows
})
