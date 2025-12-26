<template>
  <div class="app-layout">
    <SideNav />
    <div class="main-content">
      <slot />
    </div>
  </div>
</template>

<style scoped>
.app-layout {
  display: flex;
  min-height: 100vh;
  background: var(--bg-dark);
}

.main-content {
  flex: 1;
  margin-left: 250px;
  padding: 20px;
  min-height: 100vh;
}

@media (max-width: 768px) {
  .main-content {
    margin-left: 0;
  }
}
</style>

