<template>
  <nav class="navbar">
    <div class="container">
      <div class="nav-content">
        <NuxtLink to="/" class="logo">Adopet</NuxtLink>
        <div class="nav-links">
          <NuxtLink to="/">Home</NuxtLink>
          <NuxtLink to="/pets">All Pets</NuxtLink>
          <template v-if="auth.isAuthenticated.value">
            <NuxtLink to="/pets/create">Post Pet</NuxtLink>
            <NuxtLink to="/chats">Messages</NuxtLink>
            <NuxtLink to="/profile">Profile</NuxtLink>
            <button @click="handleLogout" class="btn-logout">Logout</button>
          </template>
          <template v-else>
            <NuxtLink to="/login">Login</NuxtLink>
            <NuxtLink to="/register">Register</NuxtLink>
          </template>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
const auth = useAuth()

const handleLogout = async () => {
  await auth.logout()
}
</script>

<style scoped>
.navbar {
  background-color: #fff;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  padding: 15px 0;
  position: sticky;
  top: 0;
  z-index: 100;
}

.nav-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.logo {
  font-size: 24px;
  font-weight: bold;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  text-decoration: none;
}

.nav-links {
  display: flex;
  gap: 20px;
  align-items: center;
}

.nav-links a {
  text-decoration: none;
  color: #333;
  font-weight: 500;
  transition: color 0.3s;
}

.nav-links a:hover {
  color: #667eea;
}

.btn-logout {
  background-color: #f44336;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 5px;
  cursor: pointer;
  font-size: 14px;
}

.btn-logout:hover {
  background-color: #da190b;
}
</style>

