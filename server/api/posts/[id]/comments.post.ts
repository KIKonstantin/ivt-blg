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
  const author_name = typeof body?.author_name === 'string' ? body.author_name.trim() : ''

  if (!content || !author_name) {
    throw createError({
      statusCode: 400,
      statusMessage: 'Content and name are required'
    })
  }

  const { rows } = await pool.query(
    `INSERT INTO comments (post_id, author_name, content, is_approved)
     VALUES ($1, $2, $3, $4)
     RETURNING id, content, author_name, created_at`,
    [id, author_name, content, false]
  )

  return rows[0]
})
