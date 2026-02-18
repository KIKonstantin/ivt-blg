import { writeFile } from 'node:fs/promises'
import { join } from 'node:path'
import { randomUUID } from 'node:crypto'

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

  // Basic validation for image types
  const allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif']
  if (file.type && !allowedTypes.includes(file.type)) {
     throw createError({
      statusCode: 400,
      statusMessage: 'Only images are allowed'
    })
  }

  const ext = file.filename.split('.').pop()
  const fileName = `${randomUUID()}.${ext}`
  const filePath = join(process.cwd(), 'public/uploads', fileName)

  await writeFile(filePath, file.data)

  return {
    url: `/uploads/${fileName}`
  }
})
