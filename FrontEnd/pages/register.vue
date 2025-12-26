<template>
  <div class="register-page">
    <div class="auth-container">
      <div class="auth-card">
        <h2>Create Account</h2>
        <form @submit.prevent="handleRegister">
          <div class="form-group">
            <label>Name</label>
            <input type="text" v-model="form.name" required />
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" v-model="form.email" required />
          </div>
          <div class="form-group">
            <label>Password</label>
            <input type="password" v-model="form.password" required />
          </div>
          <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" v-model="form.password_confirmation" required />
          </div>
          <div v-if="error" class="error">{{ error }}</div>
          <button type="submit" class="btn btn-primary btn-full" :disabled="loading">
            {{ loading ? 'Creating account...' : 'Create Account' }}
          </button>
          <div class="divider">
            <span>Or continue with</span>
          </div>
          <div class="social-login">
            <button type="button" class="btn-social btn-google" @click="registerWithGoogle">
              <svg width="20" height="20" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
              </svg>
              Sign up with Google
            </button>
          </div>
          <p class="auth-link">
            Already have an account? <NuxtLink to="/login">Login</NuxtLink>
          </p>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  middleware: 'guest',
  layout: 'default'
})

const auth = useAuth()
const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})
const error = ref('')
const loading = ref(false)

const handleRegister = async () => {
  error.value = ''
  loading.value = true
  
  try {
    await auth.register(form.value)
    navigateTo('/verification')
  } catch (err) {
    error.value = err.data?.message || 'Registration failed'
  } finally {
    loading.value = false
  }
}

const registerWithGoogle = async () => {
  error.value = 'Google registration is coming soon!'
  
  // Show message for 3 seconds
  setTimeout(() => {
    error.value = ''
  }, 3000)
}
</script>

<style scoped>
.register-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

.auth-container {
  width: 100%;
  max-width: 450px;
}

.auth-card {
  background: var(--bg-card);
  padding: 40px;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.5);
  border: 1px solid var(--border-color);
}

.auth-card h2 {
  margin-bottom: 30px;
  text-align: center;
  color: var(--text-primary);
  font-size: 24px;
  font-weight: 600;
}

.btn-full {
  width: 100%;
  padding: 12px;
  margin-top: 10px;
}

.divider {
  text-align: center;
  margin: 25px 0;
  position: relative;
  color: var(--text-muted);
  font-size: 14px;
}

.divider::before,
.divider::after {
  content: '';
  position: absolute;
  top: 50%;
  width: 40%;
  height: 1px;
  background: var(--border-color);
}

.divider::before {
  left: 0;
}

.divider::after {
  right: 0;
}

.social-login {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-bottom: 20px;
}

.btn-social {
  width: 100%;
  padding: 12px;
  border: 1px solid var(--border-color);
  border-radius: 8px;
  background: var(--bg-darker);
  color: var(--text-primary);
  font-size: 14px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  transition: all 0.3s;
}

.btn-social:hover:not(:disabled) {
  background: var(--bg-card);
  border-color: var(--accent-color);
}

.btn-social:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-google {
  border-color: #4285F4;
}

.btn-google:hover:not(:disabled) {
  border-color: #4285F4;
  background: rgba(66, 133, 244, 0.1);
}

.auth-link {
  text-align: center;
  margin-top: 20px;
  color: var(--text-secondary);
  font-size: 14px;
}

.auth-link a {
  color: var(--accent-color);
  text-decoration: none;
  font-weight: 500;
}

.auth-link a:hover {
  text-decoration: underline;
}
</style>
