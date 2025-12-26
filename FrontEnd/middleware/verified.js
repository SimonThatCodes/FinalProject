export default defineNuxtRouteMiddleware((to, from) => {
  const auth = useAuth()
  
  if (!auth.isAuthenticated.value) {
    return navigateTo('/login')
  }
  
  // Check if user is verified
  // Uncomment this when verification is implemented
  if (auth.user.value && auth.user.value.verified === false) {
    return navigateTo('/verification')
  }
})
