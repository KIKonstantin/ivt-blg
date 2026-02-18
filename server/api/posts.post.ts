import { pool } from '../utils/db'

export default defineEventHandler(async (event) => {
    const body = await readBody(event)
    const title = body?.title
    const content = body?.content
    const image_url = body?.image_url
    if(!title || !content) {
        throw createError({
            statusCode: 400,
            statusMessage: "Title and content are required"
        })
    }

    const { rows } = await pool.query(
        'INSERT INTO posts (title, content, image_url) VALUES ($1, $2, $3) RETURNING *',
        [title, content, image_url]
    )
    return rows[0]
})
