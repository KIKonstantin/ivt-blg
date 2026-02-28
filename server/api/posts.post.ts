import { pool } from '../utils/db'

export default defineEventHandler(async (event) => {
    const body = await readBody(event)
    const title = body?.title
    const content = body?.content
    const excerpt = body?.excerpt
    const image_url = body?.image_url
    const gallery = body?.gallery || [] // Array of image URLs

    if(!title || !content) {
        throw createError({
            statusCode: 400,
            statusMessage: "Title and content are required"
        })
    }

    const has_gallery = gallery.length > 0

    const { rows } = await pool.query(
        'INSERT INTO posts (title, content, excerpt, image_url, has_gallery) VALUES ($1, $2, $3, $4, $5) RETURNING *',
        [title, content, excerpt, image_url, has_gallery]
    )
    const post = rows[0]

    // Insert gallery images if any
    if (gallery.length > 0) {
        for (const imgUrl of gallery) {
            await pool.query(
                'INSERT INTO post_galleries (post_id, image_url) VALUES ($1, $2)',
                [post.id, imgUrl]
            )
        }
    }

    return post
})
