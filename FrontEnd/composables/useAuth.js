export const useAuth = () => {
  const user = useState('user', () => null)
  const token = useCookie('auth_token', { maxAge: 60 * 60 * 24 * 7 }) // 7 days
  const { apiFetch } = useApi()

  const login = async (email, password) => {
    try {
      const response = await apiFetch('/login', {
        method: 'POST',
        body: { email, password },
      })

      token.value = response.token
      user.value = response.user
      
      return response
    } catch (error) {
      throw error
    }
  }

  const register = async (userData) => {
    try {
      const response = await apiFetch('/register', {
        method: 'POST',
        body: userData,
      })

      token.value = response.token
      user.value = response.user
      
      return response
    } catch (error) {
      throw error
    }
  }

  const logout = async () => {
    try {
      if (token.value) {
        await apiFetch('/logout', {
          method: 'POST',
        })
      }
    } catch (error) {
      console.error('Logout error:', error)
    } finally {
      token.value = null
      user.value = null
      navigateTo('/login')
    }
  }

  const fetchUser = async () => {
    if (!token.value) return null

    try {
      const response = await apiFetch('/user')
      user.value = response
      return response
    } catch (error) {
      token.value = null
      user.value = null
      return null
    }
  }

  const verifyAccount = async () => {
    try {
      const response = await apiFetch('/verify', {
        method: 'POST',
      })
      if (user.value) {
        user.value.verified = true
      }
      return response
    } catch (error) {
      throw error
    }
  }

  const isAuthenticated = computed(() => !!token.value && !!user.value)

  return {
    user: readonly(user),
    token: readonly(token),
    login,
    register,
    logout,
    fetchUser,
    verifyAccount,
    isAuthenticated,
  }
}

