import { pool } from '../utils/db'
import { defineEventHandler } from 'h3'

export default defineEventHandler(async (event) => {
  const { rows } = await pool.query('SELECT name, description, content, image_url FROM authors LIMIT 1')
  return rows[0] || {}
})
