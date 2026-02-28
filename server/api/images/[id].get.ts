import { pool } from '../../utils/db'

export default defineEventHandler(async (event) => {
  const id = getRouterParam(event, 'id')

  const { rows } = await pool.query(
    'SELECT mime_type, data FROM blobs WHERE id = $1',
    [id]
  )

  if (!rows[0]) {
    throw createError({ statusCode: 404, statusMessage: 'Image not found' })
  }

  const blob = rows[0]

  setResponseHeaders(event, {
    'Content-Type': blob.mime_type,
    'Cache-Control': 'public, max-age=31536000, immutable'
  })

  return blob.data
})
