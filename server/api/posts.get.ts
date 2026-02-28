import { pool } from '../utils/db'
import { defineEventHandler, getQuery } from 'h3'

export default defineEventHandler(async (event) => {
    const query = getQuery(event)
    const sort = query.sort || 'newest'

    let orderBy = 'created_at DESC'
    if (sort === 'oldest') {
        orderBy = 'created_at ASC'
    } else if (sort === 'most_commented') {
        orderBy = '(SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id AND is_approved = TRUE) DESC, created_at DESC'
    }

    const { rows } = await pool.query(
        `SELECT id, title, excerpt, image_url, has_gallery, created_at,
         (SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id AND is_approved = TRUE) as comment_count
         FROM posts 
         ORDER BY ${orderBy}`
    )
    return rows
})
