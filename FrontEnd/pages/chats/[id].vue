<template>
  <div class="chat-page-wrapper">
    <div class="container">
      <div class="chat-layout">
        <div class="chat-sidebar">
          <div class="chats-list">
            <div 
              v-for="chat in chats" 
              :key="chat.id"
              :class="['chat-item', { active: chat.id === parseInt(route.params.id) }]"
              @click="navigateTo(`/chats/${chat.id}`)"
            >
              <div class="chat-avatar">
                <span>{{ getOtherUser(chat).name.charAt(0).toUpperCase() }}</span>
              </div>
              <div class="chat-info">
                <h4>{{ getOtherUser(chat).name }}</h4>
                <p v-if="chat.messages && chat.messages.length > 0" class="chat-preview">
                  {{ chat.messages[0].message }}
                </p>
                <span class="chat-time">{{ formatTime(chat.last_message_at) }}</span>
              </div>
            </div>
          </div>
        </div>

        <div class="chat-main">
          <div class="chat-header">
            <h2>{{ otherUser?.name || 'Chat' }}</h2>
            <NuxtLink to="/chats" class="back-btn">← Back</NuxtLink>
          </div>
          <div class="messages-container" ref="messagesContainer">
            <div v-if="loading" class="loading">Loading messages...</div>
            <div v-else-if="messages.length > 0" class="messages">
              <div 
                v-for="message in messages" 
                :key="message.id"
                :class="['message', message.sender_id === auth.user.value?.id ? 'sent' : 'received']"
              >
                <div class="message-content">
                  <p>{{ message.message }}</p>
                  <span class="message-time">{{ formatTime(message.created_at) }}</span>
                </div>
              </div>
            </div>
            <div v-else class="no-messages">
              <p>No messages yet. Start the conversation!</p>
            </div>
          </div>
          <div class="message-input">
            <form @submit.prevent="sendMessage">
              <input 
                type="text" 
                v-model="newMessage" 
                placeholder="Type a message..."
                class="message-field"
              />
              <button type="submit" class="btn btn-primary">Send</button>
            </form>
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

const route = useRoute()
const auth = useAuth()
const chat = ref(null)
const chats = ref([])
const messages = ref([])
const otherUser = ref(null)
const newMessage = ref('')
const loading = ref(true)
const messagesContainer = ref(null)

const formatTime = (date) => {
  if (!date) return ''
  const d = new Date(date)
  return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
}

const getOtherUser = (chat) => {
  return chat.user1_id === auth.user.value?.id ? chat.user2 : chat.user1
}

const { apiFetch } = useApi()

const loadChats = async () => {
  try {
    const response = await apiFetch('/chats')
    chats.value = response
  } catch (error) {
    console.error('Error fetching chats:', error)
  }
}

const loadChat = async () => {
  try {
    const response = await apiFetch(`/chats/${route.params.id}`)
    chat.value = response
    otherUser.value = response.user1_id === auth.user.value?.id ? response.user2 : response.user1
    await loadMessages()
  } catch (error) {
    console.error('Error fetching chat:', error)
  } finally {
    loading.value = false
  }
}

const loadMessages = async () => {
  try {
    const response = await apiFetch(`/chats/${route.params.id}/messages`)
    messages.value = response
    scrollToBottom()
  } catch (error) {
    console.error('Error fetching messages:', error)
  }
}

const sendMessage = async () => {
  if (!newMessage.value.trim()) return

  try {
    const response = await apiFetch('/messages', {
      method: 'POST',
      body: {
        chat_id: route.params.id,
        message: newMessage.value,
      },
    })
    messages.value.push(response)
    newMessage.value = ''
    scrollToBottom()
    await loadChats()
  } catch (error) {
    console.error('Error sending message:', error)
    alert('Failed to send message')
  }
}

const scrollToBottom = () => {
  nextTick(() => {
    if (messagesContainer.value) {
      messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
    }
  })
}

onMounted(() => {
  loadChats()
  loadChat()
  setInterval(() => {
    if (chat.value) {
      loadMessages()
      loadChats()
    }
  }, 3000)
})
</script>

<style scoped>
.chat-layout {
  display: grid;
  grid-template-columns: 350px 1fr;
  gap: 0;
  margin: 40px 0;
  background: var(--bg-card);
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.5);
  border: 1px solid var(--border-color);
  overflow: hidden;
  height: 80vh;
}

.chat-sidebar {
  border-right: 1px solid var(--border-color);
  overflow-y: auto;
  background: var(--bg-darker);
}

.chats-list {
  display: flex;
  flex-direction: column;
}

.chat-item {
  display: flex;
  gap: 15px;
  padding: 15px;
  cursor: pointer;
  border-bottom: 1px solid var(--border-color);
  transition: background 0.3s;
}

.chat-item:hover {
  background: var(--bg-card);
}

.chat-item.active {
  background: rgba(102, 126, 234, 0.1);
  border-left: 3px solid var(--accent-color);
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
  font-size: 18px;
  flex-shrink: 0;
}

.chat-info {
  flex: 1;
  min-width: 0;
}

.chat-info h4 {
  margin: 0 0 5px 0;
  color: var(--text-primary);
  font-size: 16px;
}

.chat-preview {
  color: var(--text-secondary);
  font-size: 14px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  margin-bottom: 5px;
}

.chat-time {
  font-size: 12px;
  color: var(--text-muted);
}

.chat-main {
  display: flex;
  flex-direction: column;
  height: 100%;
}

.chat-header {
  padding: 20px;
  border-bottom: 1px solid var(--border-color);
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: var(--bg-darker);
}

.chat-header h2 {
  margin: 0;
  color: var(--text-primary);
  font-size: 20px;
}

.back-btn {
  color: var(--accent-color);
  text-decoration: none;
  font-weight: 500;
}

.messages-container {
  flex: 1;
  overflow-y: auto;
  padding: 20px;
}

.messages {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.message {
  display: flex;
  max-width: 70%;
}

.message.sent {
  align-self: flex-end;
}

.message.received {
  align-self: flex-start;
}

.message-content {
  padding: 12px 16px;
  border-radius: 18px;
  position: relative;
}

.message.sent .message-content {
  background: var(--accent-color);
  color: white;
}

.message.received .message-content {
  background: var(--bg-darker);
  color: var(--text-primary);
  border: 1px solid var(--border-color);
}

.message-content p {
  margin: 0 0 5px 0;
}

.message-time {
  font-size: 11px;
  opacity: 0.7;
  display: block;
  text-align: right;
}

.message-input {
  padding: 20px;
  border-top: 1px solid var(--border-color);
  background: var(--bg-darker);
}

.message-input form {
  display: flex;
  gap: 10px;
}

.message-field {
  flex: 1;
  padding: 12px;
  border: 1px solid var(--border-color);
  border-radius: 25px;
  font-size: 14px;
  background: var(--bg-card);
  color: var(--text-primary);
}

.message-field:focus {
  outline: none;
  border-color: var(--accent-color);
}

.loading,
.no-messages {
  text-align: center;
  padding: 40px 20px;
  color: var(--text-secondary);
}

@media (max-width: 768px) {
  .chat-layout {
    grid-template-columns: 1fr;
    height: auto;
  }
  
  .chat-sidebar {
    display: none;
  }
}
</style>
