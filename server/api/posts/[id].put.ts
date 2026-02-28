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
  const excerpt = body?.excerpt
  const image_url = body?.image_url
  const gallery = body?.gallery || [] // Array of image URLs

  if (!title || !content) {
    throw createError({
      statusCode: 400,
      statusMessage: 'Title and content are required'
    })
  }

  const has_gallery = gallery.length > 0

  const { rows } = await pool.query(
    'UPDATE posts SET title = $1, content = $2, excerpt = $3, image_url = $4, has_gallery = $5 WHERE id = $6 RETURNING *',
    [title, content, excerpt, image_url, has_gallery, id]
  )

  if (rows.length === 0) {
    throw createError({
      statusCode: 404,
      statusMessage: 'Post not found'
    })
  }

  // Update gallery: simplify by deleting old and inserting new
  await pool.query('DELETE FROM post_galleries WHERE post_id = $1', [id])
  if (gallery.length > 0) {
    for (const imgUrl of gallery) {
      await pool.query(
        'INSERT INTO post_galleries (post_id, image_url) VALUES ($1, $2)',
        [id, imgUrl]
      )
    }
  }

  return rows[0]

  return rows[0]
})
