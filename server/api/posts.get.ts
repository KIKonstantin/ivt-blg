import { pool } from '../utils/db'

export default defineEventHandler(async () => {
    const { rows } = await pool.query(
        'SELECT * FROM posts ORDER BY created_at DESC'
    )
    return rows
})
