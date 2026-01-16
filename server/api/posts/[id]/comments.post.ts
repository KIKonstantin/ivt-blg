import { pool } from '../../../utils/db'
import { requireUser } from '../../../utils/auth'

export default defineEventHandler(async (event) => {
  const id = Number(getRouterParam(event, 'id'))
  if (!Number.isFinite(id)) {
    throw createError({
      statusCode: 400,
      statusMessage: 'Invalid post id'
    })
  }

  const body = await readBody(event)
  const content = typeof body?.content === 'string' ? body.content.trim() : ''
  if (!content) {
    throw createError({
      statusCode: 400,
      statusMessage: 'Content is required'
    })
  }

  const user = await requireUser(event)
  const { rows } = await pool.query(
    `INSERT INTO comments (post_id, user_id, content)
     VALUES ($1, $2, $3)
     RETURNING id, content, created_at`,
    [id, user.id, content]
  )

  return {
    ...rows[0],
    email: user.email
  }
})
