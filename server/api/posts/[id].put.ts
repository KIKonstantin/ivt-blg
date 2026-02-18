import { pool } from '../../utils/db'

export default defineEventHandler(async (event) => {
  const id = Number(getRouterParam(event, 'id'))
  if (!Number.isFinite(id)) {
    throw createError({
      statusCode: 400,
      statusMessage: 'Invalid post id'
    })
  }

  const body = await readBody(event)
  const title = body?.title
  const content = body?.content
  const image_url = body?.image_url

  if (!title || !content) {
    throw createError({
      statusCode: 400,
      statusMessage: 'Title and content are required'
    })
  }

  const { rows } = await pool.query(
    'UPDATE posts SET title = $1, content = $2, image_url = $3 WHERE id = $4 RETURNING *',
    [title, content, image_url, id]
  )

  if (rows.length === 0) {
    throw createError({
      statusCode: 404,
      statusMessage: 'Post not found'
    })
  }

  return rows[0]
})
