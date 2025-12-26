<template>
  <div class="profile-page-wrapper">
    <div class="container">
      <div class="profile-content">
          <div class="profile-header">
            <div class="profile-avatar">
              <span>{{ auth.user.value?.name?.charAt(0).toUpperCase() || 'U' }}</span>
            </div>
            <div class="profile-info">
              <h1>{{ auth.user.value?.name }}</h1>
              <p v-if="auth.user.value?.email">{{ auth.user.value.email }}</p>
            </div>
          </div>

          <div class="profile-details">
            <div class="detail-card">
              <h2>Contact Information</h2>
              <div class="detail-item">
                <strong>Email:</strong> {{ auth.user.value?.email || 'Not provided' }}
              </div>
              <div class="detail-item" v-if="auth.user.value?.phone">
                <strong>Phone:</strong> {{ auth.user.value.phone }}
              </div>
              <div class="detail-item" v-if="auth.user.value?.address">
                <strong>Address:</strong> {{ auth.user.value.address }}
              </div>
            </div>

            <div class="detail-card">
              <h2>Settings</h2>
              <div class="setting-item">
                <div>
                  <strong>Notifications</strong>
                  <p>Receive notifications about adoption requests and messages</p>
                </div>
                <label class="toggle-switch">
                  <input type="checkbox" v-model="notificationsEnabled" />
                  <span class="slider"></span>
                </label>
              </div>
              <div class="setting-item">
                <div>
                  <strong>Privacy</strong>
                  <p>Control who can see your profile information</p>
                </div>
                <button class="btn btn-secondary">Manage</button>
              </div>
            </div>

            <div class="detail-card">
              <h2>Legal</h2>
              <div class="legal-links">
                <NuxtLink to="/terms">Terms & Conditions</NuxtLink>
                <NuxtLink to="/privacy">Privacy Policy</NuxtLink>
              </div>
            </div>
          </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  middleware: ['auth', 'verified'],
  layout: 'default'
})

const auth = useAuth()
const notificationsEnabled = ref(true)
</script>

<style scoped>
.profile-content {
  background: var(--bg-card);
  border-radius: 12px;
  padding: 40px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.5);
  border: 1px solid var(--border-color);
  max-width: 900px;
  margin: 40px auto;
}

.profile-header {
  display: flex;
  align-items: center;
  gap: 20px;
  margin-bottom: 40px;
  padding-bottom: 30px;
  border-bottom: 1px solid var(--border-color);
}

.profile-avatar {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 32px;
  font-weight: bold;
}

.profile-info h1 {
  font-size: 28px;
  margin-bottom: 5px;
  color: var(--text-primary);
}

.profile-info p {
  color: var(--text-secondary);
}

.profile-details {
  display: flex;
  flex-direction: column;
  gap: 25px;
}

.detail-card {
  padding: 25px;
  background: var(--bg-darker);
  border-radius: 8px;
  border: 1px solid var(--border-color);
}

.detail-card h2 {
  font-size: 20px;
  margin-bottom: 20px;
  color: var(--text-primary);
}

.detail-item {
  margin-bottom: 15px;
  color: var(--text-secondary);
}

.setting-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 0;
  border-bottom: 1px solid var(--border-color);
}

.setting-item:last-child {
  border-bottom: none;
}

.setting-item strong {
  display: block;
  margin-bottom: 5px;
  color: var(--text-primary);
}

.setting-item p {
  font-size: 14px;
  color: var(--text-secondary);
}

.toggle-switch {
  position: relative;
  display: inline-block;
  width: 50px;
  height: 24px;
}

.toggle-switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: var(--border-color);
  transition: 0.4s;
  border-radius: 24px;
}

.slider:before {
  position: absolute;
  content: "";
  height: 18px;
  width: 18px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: 0.4s;
  border-radius: 50%;
}

input:checked + .slider {
  background-color: var(--accent-color);
}

input:checked + .slider:before {
  transform: translateX(26px);
}

.legal-links {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.legal-links a {
  color: var(--accent-color);
  text-decoration: none;
}

.legal-links a:hover {
  text-decoration: underline;
}

</style>
