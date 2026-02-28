import { pool } from '../../../utils/db'
import { requireAdmin } from '../../../utils/auth'

export default defineEventHandler(async (event) => {
  await requireAdmin(event)
  const id = Number(getRouterParam(event, 'id'))
  if (!Number.isFinite(id)) {
    throw createError({
      statusCode: 400,
      statusMessage: 'Invalid comment id'
    })
  }

  const body = await readBody(event)
  const action = body?.action // 'approve' or 'delete'

  if (action === 'approve') {
    const { rows } = await pool.query(
      'UPDATE comments SET is_approved = TRUE WHERE id = $1 RETURNING *',
      [id]
    )
    if (rows.length === 0) {
      throw createError({ statusCode: 404, statusMessage: 'Comment not found' })
    }
    return rows[0]
  } else if (action === 'delete') {
    const { rowCount } = await pool.query('DELETE FROM comments WHERE id = $1', [id])
    if (rowCount === 0) {
      throw createError({ statusCode: 404, statusMessage: 'Comment not found' })
    }
    return { success: true }
  } else {
    throw createError({
      statusCode: 400,
      statusMessage: 'Invalid action'
    })
  }
})
