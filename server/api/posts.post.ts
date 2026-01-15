import { pool } from '../utils/db'

export default defineEventHandler(async (event) => {
    const body = await readBody(event)
    const title = body?.title
    const content = body?.content
    if(!title || !content) {
        throw createError({
            statusCode: 400,
            statusMessage: "Title and content are required"
        })
    }

    const { rows } = await pool.query(
        'INSERT INTO posts (title, content) VALUES ($1, $2) RETURNING *',
        [title, content]
    )
    return rows[0]
})
