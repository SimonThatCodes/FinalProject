<template>
  <div class="callback-page">
    <div class="loading-message">
      <p>Completing authentication...</p>
    </div>
  </div>
</template>

<script setup>
const route = useRoute()
const auth = useAuth()

onMounted(async () => {
  const token = route.query.token
  const userData = route.query.user

  if (token && userData) {
    try {
      // Set token
      const tokenCookie = useCookie('auth_token')
      tokenCookie.value = token

      // Set user
      const user = JSON.parse(atob(userData))
      auth.user.value = user

      // Navigate to home
      navigateTo('/')
    } catch (error) {
      console.error('Error processing callback:', error)
      navigateTo('/login?error=Authentication failed')
    }
  } else {
    navigateTo('/login?error=Invalid callback')
  }
})
</script>

<style scoped>
.callback-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--bg-dark);
}

.loading-message {
  text-align: center;
  color: var(--text-primary);
}

.loading-message p {
  font-size: 18px;
}
</style>




