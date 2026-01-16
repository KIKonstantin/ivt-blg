const protectedMethods = new Set(['POST', 'PUT', 'DELETE'])

export default defineEventHandler(async (event) => {
  const method = event.node.req.method || 'GET'
  if (!protectedMethods.has(method)) {
    return
  }

  const url = event.node.req.url || ''
  if (!url.startsWith('/api/posts')) {
    return
  }
  if (/^\/api\/posts\/\d+\/comments/.test(url)) {
    return
  }

  const { requireAdmin } = await import('../utils/auth')
  await requireAdmin(event)
})
