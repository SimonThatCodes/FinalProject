export const useApi = () => {
  const config = useRuntimeConfig()
  const token = useCookie('auth_token')

  const apiFetch = async (url, options = {}) => {
    const baseURL = config.public.apiBase || 'http://localhost:8000/api'
    
    const headers = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      ...options.headers,
    }

    if (token.value) {
      headers['Authorization'] = `Bearer ${token.value}`
    }

    try {
      const response = await $fetch(url, {
        baseURL,
        ...options,
        headers,
      })
      return response
    } catch (error) {
      console.error('API Error:', error)
      throw error
    }
  }

  const apiFetchFormData = async (url, formData) => {
    const baseURL = config.public.apiBase || 'http://localhost:8000/api'
    const headers = {
      'Accept': 'application/json',
    }

    if (token.value) {
      headers['Authorization'] = `Bearer ${token.value}`
    }

    try {
      const response = await fetch(`${baseURL}${url}`, {
        method: 'POST',
        headers,
        body: formData,
      })
      
      if (!response.ok) {
        const error = await response.json()
        throw error
      }
      
      return await response.json()
    } catch (error) {
      console.error('API Error:', error)
      throw error
    }
  }

  return {
    apiFetch,
    apiFetchFormData,
  }
}

