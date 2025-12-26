<template>
  <div class="dashboard-page">
    <div class="container">
      <div class="dashboard-header">
        <h1>Find Your Perfect Pet</h1>
        <div class="search-filters">
          <div class="search-bar">
            <input 
              type="text" 
              v-model="searchQuery" 
              placeholder="Search pets..."
              @input="debounceSearch"
            />
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <circle cx="11" cy="11" r="8"></circle>
              <path d="m21 21-4.35-4.35"></path>
            </svg>
          </div>
          <div class="filter-tabs">
            <button 
              v-for="filter in filters" 
              :key="filter.value"
              :class="['filter-tab', { active: activeFilter === filter.value }]"
              @click="setFilter(filter.value)"
            >
              {{ filter.label }}
            </button>
          </div>
        </div>
      </div>

      <div v-if="loading" class="loading">Loading pets...</div>
      <div v-else-if="pets.length > 0" class="pets-grid">
        <div v-for="pet in pets" :key="pet.id" class="pet-card">
          <NuxtLink :to="`/pets/${pet.id}`">
            <div class="pet-image-container">
              <img 
                v-if="pet.images && pet.images.length > 0" 
                :src="getImageUrl(pet.images[0])" 
                :alt="pet.name"
                class="pet-image"
              />
              <div v-else class="pet-image-placeholder">
                <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                </svg>
              </div>
              <div class="pet-status-badge" :class="pet.status">{{ pet.status }}</div>
            </div>
            <div class="pet-info">
              <h3>{{ pet.name }}</h3>
              <p class="pet-meta">{{ pet.species }} • {{ pet.breed || 'Mixed' }}</p>
              <p class="pet-location">{{ pet.location }}</p>
              <p v-if="pet.price" class="pet-price">${{ pet.price }}</p>
            </div>
          </NuxtLink>
        </div>
      </div>
      <div v-else class="no-pets">
        <p>No pets available at the moment.</p>
      </div>

      <div v-if="pets.length > 0" class="pagination">
        <button 
          v-for="page in totalPages" 
          :key="page"
          :class="['page-btn', { active: currentPage === page }]"
          @click="goToPage(page)"
        >
          {{ page }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
const auth = useAuth()
const pets = ref([])
const loading = ref(true)
const searchQuery = ref('')
const activeFilter = ref('all')
const currentPage = ref(1)
const totalPages = ref(1)

const filters = [
  { label: 'All Pets', value: 'all' },
  { label: 'Available', value: 'available' },
  { label: 'Adopted', value: 'adopted' },
]

const getImageUrl = (path) => {
  if (!path) return '/placeholder-pet.jpg'
  return `http://localhost:8000/storage/${path}`
}

let searchTimeout = null
const debounceSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    loadPets()
  }, 500)
}

const setFilter = (filter) => {
  activeFilter.value = filter
  currentPage.value = 1
  loadPets()
}

const goToPage = (page) => {
  currentPage.value = page
  loadPets()
}

const { apiFetch } = useApi()

const loadPets = async () => {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (activeFilter.value !== 'all') {
      params.append('status', activeFilter.value)
    }
    if (searchQuery.value) {
      params.append('search', searchQuery.value)
    }
    params.append('page', currentPage.value)

    const response = await apiFetch(`/pets?${params.toString()}`)
    pets.value = response.data || response
    if (response.last_page) {
      totalPages.value = response.last_page
    }
  } catch (error) {
    console.error('Error fetching pets:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadPets()
})
</script>

<style scoped>
.dashboard-page {
  min-height: 100vh;
}

.dashboard-header {
  margin: 40px 0;
}

.dashboard-header h1 {
  font-size: 36px;
  color: var(--text-primary);
  margin-bottom: 30px;
  font-weight: 700;
}

.search-filters {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.search-bar {
  position: relative;
  max-width: 600px;
}

.search-bar input {
  width: 100%;
  padding: 12px 45px 12px 15px;
  border: 2px solid var(--border-color);
  border-radius: 25px;
  font-size: 16px;
  transition: border-color 0.3s;
  background: var(--bg-darker);
  color: var(--text-primary);
}

.search-bar input:focus {
  outline: none;
  border-color: var(--accent-color);
}

.search-bar svg {
  position: absolute;
  right: 15px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--text-muted);
}

.filter-tabs {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.filter-tab {
  padding: 10px 20px;
  border: 2px solid var(--border-color);
  background: var(--bg-card);
  border-radius: 25px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  color: var(--text-secondary);
  transition: all 0.3s;
}

.filter-tab:hover {
  border-color: var(--accent-color);
  color: var(--accent-color);
}

.filter-tab.active {
  background: var(--accent-color);
  border-color: var(--accent-color);
  color: white;
}

.pets-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 25px;
  margin: 40px 0;
}

.pet-card {
  background: var(--bg-card);
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0,0,0,0.3);
  border: 1px solid var(--border-color);
  transition: transform 0.3s, box-shadow 0.3s;
}

.pet-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 16px rgba(0,0,0,0.5);
  border-color: var(--accent-color);
}

.pet-card a {
  text-decoration: none;
  color: inherit;
}

.pet-image-container {
  position: relative;
  width: 100%;
  height: 220px;
  overflow: hidden;
}

.pet-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.pet-image-placeholder {
  width: 100%;
  height: 100%;
  background: var(--bg-darker);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--text-muted);
}

.pet-status-badge {
  position: absolute;
  top: 10px;
  right: 10px;
  padding: 5px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  color: white;
}

.pet-status-badge.available {
  background: #4CAF50;
}

.pet-status-badge.pending {
  background: #FF9800;
}

.pet-status-badge.adopted {
  background: #9E9E9E;
}

.pet-info {
  padding: 20px;
}

.pet-info h3 {
  margin-bottom: 8px;
  color: var(--text-primary);
  font-size: 20px;
  font-weight: 600;
}

.pet-meta {
  color: var(--text-secondary);
  font-size: 14px;
  margin-bottom: 5px;
}

.pet-location {
  color: var(--text-muted);
  font-size: 13px;
  margin-bottom: 10px;
}

.pet-price {
  color: var(--accent-color);
  font-size: 18px;
  font-weight: 600;
  margin-top: 10px;
}

.pagination {
  display: flex;
  justify-content: center;
  gap: 10px;
  margin: 40px 0;
}

.page-btn {
  padding: 10px 15px;
  border: 1px solid var(--border-color);
  background: var(--bg-card);
  border-radius: 5px;
  cursor: pointer;
  transition: all 0.3s;
  color: var(--text-primary);
}

.page-btn:hover {
  border-color: var(--accent-color);
  color: var(--accent-color);
}

.page-btn.active {
  background: var(--accent-color);
  border-color: var(--accent-color);
  color: white;
}

.loading,
.no-pets {
  text-align: center;
  padding: 60px 20px;
  color: var(--text-secondary);
  font-size: 18px;
}
</style>
