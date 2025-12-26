<template>
  <div class="notifications-page-wrapper">
    <div class="container">
      <div class="notifications-content">
          <h1>Notifications</h1>
          <div v-if="loading" class="loading">Loading notifications...</div>
          <div v-else-if="notifications.length > 0" class="notifications-list">
            <div 
              v-for="notification in notifications" 
              :key="notification.id"
              :class="['notification-item', { unread: !notification.read }]"
            >
              <div class="notification-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                  <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                </svg>
              </div>
              <div class="notification-content">
                <h3>{{ notification.title }}</h3>
                <p>{{ notification.message }}</p>
                <span class="notification-time">{{ formatTime(notification.created_at) }}</span>
              </div>
            </div>
          </div>
          <div v-else class="no-notifications">
            <p>No notifications yet.</p>
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

const notifications = ref([
  {
    id: 1,
    title: 'New Pet Added',
    message: 'A new pet matching your preferences has been added to the platform.',
    read: false,
    created_at: new Date(Date.now() - 3600000),
  },
  {
    id: 2,
    title: 'Adoption Request Approved',
    message: 'Your adoption request for "Max" has been approved by the owner.',
    read: false,
    created_at: new Date(Date.now() - 7200000),
  },
  {
    id: 3,
    title: 'Message from John Doe',
    message: 'You have a new message from John Doe about "Bella".',
    read: true,
    created_at: new Date(Date.now() - 86400000),
  },
])

const loading = ref(false)

const formatTime = (date) => {
  if (!date) return ''
  const d = new Date(date)
  const now = new Date()
  const diff = now - d
  const minutes = Math.floor(diff / 60000)
  const hours = Math.floor(minutes / 60)
  const days = Math.floor(hours / 24)

  if (minutes < 1) return 'Just now'
  if (minutes < 60) return `${minutes}m ago`
  if (hours < 24) return `${hours}h ago`
  if (days < 7) return `${days}d ago`
  return d.toLocaleDateString()
}
</script>

<style scoped>
.notifications-content {
  background: var(--bg-card);
  border-radius: 12px;
  padding: 40px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.5);
  border: 1px solid var(--border-color);
  max-width: 900px;
  margin: 40px auto;
}

.notifications-content h1 {
  font-size: 28px;
  margin-bottom: 30px;
  color: var(--text-primary);
}

.notifications-list {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.notification-item {
  display: flex;
  gap: 15px;
  padding: 20px;
  background: var(--bg-darker);
  border-radius: 8px;
  border-left: 4px solid transparent;
  border: 1px solid var(--border-color);
  transition: all 0.3s;
}

.notification-item.unread {
  background: rgba(102, 126, 234, 0.1);
  border-left-color: var(--accent-color);
}

.notification-icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: var(--accent-color);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.notification-content {
  flex: 1;
}

.notification-content h3 {
  font-size: 16px;
  margin-bottom: 5px;
  color: var(--text-primary);
}

.notification-content p {
  color: var(--text-secondary);
  font-size: 14px;
  margin-bottom: 8px;
}

.notification-time {
  font-size: 12px;
  color: var(--text-muted);
}

.no-notifications {
  text-align: center;
  padding: 60px 20px;
  color: var(--text-secondary);
}

.loading {
  text-align: center;
  padding: 40px;
  color: var(--text-secondary);
}
</style>

