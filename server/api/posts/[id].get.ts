import { pool } from '../../utils/db'

export default defineEventHandler(async (event) => {
  const id = Number(getRouterParam(event, 'id'))
  if (!Number.isFinite(id)) {
    throw createError({
      statusCode: 400,
      statusMessage: 'Invalid post id'
    })
  }

  const { rows } = await pool.query('SELECT * FROM posts WHERE id = $1', [id])
  if (rows.length === 0) {
    throw createError({
      statusCode: 404,
      statusMessage: 'Post not found'
    })
  }

  const post = rows[0]
  
  // Fetch gallery images
  const { rows: galleryRows } = await pool.query(
    'SELECT image_url FROM post_galleries WHERE post_id = $1 ORDER BY created_at ASC',
    [id]
  )
  post.gallery = galleryRows.map(r => r.image_url)

  return post
})
