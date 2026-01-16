type AuthPayload = {
  user: {
    id: number
    email: string
    role: string
  } | null
  isAdmin: boolean
}

const emptyAuth: AuthPayload = { user: null, isAdmin: false }

export const useAuth = () => {
  const auth = useState<AuthPayload>('auth', () => emptyAuth)
  const loaded = useState<boolean>('authLoaded', () => false)
  const pending = useState<boolean>('authPending', () => false)

  const refresh = async () => {
    pending.value = true
    try {
      auth.value = await $fetch<AuthPayload>('/api/auth/me')
    } catch (error) {
      auth.value = emptyAuth
    } finally {
      pending.value = false
    }
  }

  if (!loaded.value) {
    loaded.value = true
    refresh()
  }

  return {
    auth,
    pending,
    refresh
  }
}
