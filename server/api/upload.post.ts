import { pool } from '../utils/db'

export default defineEventHandler(async (event) => {
  const formData = await readMultipartFormData(event)
  if (!formData || formData.length === 0) {
    throw createError({
      statusCode: 400,
      statusMessage: 'No file uploaded'
    })
  }

  const file = formData.find(item => item.name === 'file')
  if (!file || !file.filename) {
    throw createError({
      statusCode: 400,
      statusMessage: 'Invalid file upload'
    })
  }

  const allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif']
  if (file.type && !allowedTypes.includes(file.type)) {
    throw createError({
      statusCode: 400,
      statusMessage: 'Only images are allowed'
    })
  }

  const { rows } = await pool.query(
    'INSERT INTO blobs (filename, mime_type, data) VALUES ($1, $2, $3) RETURNING id',
    [file.filename, file.type || 'image/jpeg', file.data]
  )

  return {
    url: `/api/images/${rows[0].id}`
  }
})
