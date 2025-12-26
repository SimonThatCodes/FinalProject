<template>
  <div class="chats-page">
    <div class="container">
      <h1>Messages</h1>
      <div v-if="loading" class="loading">Loading chats...</div>
      <div v-else-if="chats.length > 0" class="chats-list">
        <div 
          v-for="chat in chats" 
          :key="chat.id" 
          class="chat-item"
          @click="navigateTo(`/chats/${chat.id}`)"
        >
          <div class="chat-avatar">
            <span>{{ getOtherUser(chat).name.charAt(0).toUpperCase() }}</span>
          </div>
          <div class="chat-info">
            <div class="chat-header">
              <h3>{{ getOtherUser(chat).name }}</h3>
              <span class="chat-time">{{ formatTime(chat.last_message_at) }}</span>
            </div>
            <p class="chat-preview" v-if="chat.messages && chat.messages.length > 0">
              {{ chat.messages[0].message }}
            </p>
            <p v-if="chat.pet" class="chat-pet">About: {{ chat.pet.name }}</p>
          </div>
        </div>
      </div>
      <div v-else class="no-chats">
        <p>No messages yet. Start a conversation by messaging a pet owner!</p>
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
const chats = ref([])
const loading = ref(true)

const getOtherUser = (chat) => {
  return chat.user1_id === auth.user.value?.id ? chat.user2 : chat.user1
}

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

const { apiFetch } = useApi()

const loadChats = async () => {
  try {
    const response = await apiFetch('/chats')
    chats.value = response
  } catch (error) {
    console.error('Error fetching chats:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadChats()
})
</script>

<style scoped>
h1 {
  margin: 30px 0;
  color: var(--text-primary);
}

.chats-list {
  display: flex;
  flex-direction: column;
  gap: 15px;
  margin: 30px 0;
}

.chat-item {
  background: var(--bg-card);
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.3);
  border: 1px solid var(--border-color);
  display: flex;
  gap: 15px;
  cursor: pointer;
  transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
}

.chat-item:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.4);
  border-color: var(--accent-color);
}

.chat-avatar {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background: var(--accent-color);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  font-size: 20px;
  flex-shrink: 0;
}

.chat-info {
  flex: 1;
}

.chat-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 5px;
}

.chat-header h3 {
  margin: 0;
  color: var(--text-primary);
  font-size: 18px;
}

.chat-time {
  color: var(--text-muted);
  font-size: 12px;
}

.chat-preview {
  color: var(--text-secondary);
  font-size: 14px;
  margin: 5px 0;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.chat-pet {
  color: var(--accent-color);
  font-size: 12px;
  margin-top: 5px;
}

.loading,
.no-chats {
  text-align: center;
  padding: 60px 20px;
  color: var(--text-secondary);
}
</style>

