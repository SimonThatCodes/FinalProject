<template>
  <div class="pets-page">
    <div class="container">
      <h1>All Pets</h1>
      
      <div class="filters">
        <select v-model="filters.species" @change="loadPets">
          <option value="">All Species</option>
          <option value="dog">Dog</option>
          <option value="cat">Cat</option>
          <option value="bird">Bird</option>
          <option value="rabbit">Rabbit</option>
          <option value="other">Other</option>
        </select>
        <select v-model="filters.status" @change="loadPets">
          <option value="">All Status</option>
          <option value="available">Available</option>
          <option value="pending">Pending</option>
        </select>
        <input 
          type="text" 
          v-model="filters.search" 
          placeholder="Search pets..."
          @input="debounceSearch"
        />
      </div>

      <div v-if="loading" class="loading">Loading pets...</div>
      <div v-else-if="pets.length > 0" class="pets-grid">
        <div v-for="pet in pets" :key="pet.id" class="pet-card">
          <NuxtLink :to="`/pets/${pet.id}`">
            <img 
              v-if="pet.images && pet.images.length > 0" 
              :src="getImageUrl(pet.images[0])" 
              :alt="pet.name"
              class="pet-image"
            />
            <div class="pet-info">
              <h3>{{ pet.name }}</h3>
              <p>{{ pet.species }} - {{ pet.breed || 'Mixed' }}</p>
              <p class="location">{{ pet.location }}</p>
              <p v-if="pet.price" class="price">${{ pet.price }}</p>
              <p class="status" :class="pet.status">{{ pet.status }}</p>
            </div>
          </NuxtLink>
        </div>
      </div>
      <div v-else class="no-pets">No pets found.</div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  middleware: ['auth', 'verified'],
  layout: 'default'
})

const pets = ref([])
const loading = ref(true)
const filters = ref({
  species: '',
  status: '',
  search: '',
})

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

const { apiFetch } = useApi()

const loadPets = async () => {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (filters.value.species) params.append('species', filters.value.species)
    if (filters.value.status) params.append('status', filters.value.status)
    if (filters.value.search) params.append('search', filters.value.search)

    const response = await apiFetch(`/pets?${params.toString()}`)
    pets.value = response.data || response
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
h1 {
  margin: 30px 0;
  color: var(--text-primary);
}

.filters {
  display: flex;
  gap: 15px;
  margin-bottom: 30px;
  flex-wrap: wrap;
}

.filters select,
.filters input {
  padding: 10px;
  border: 1px solid var(--border-color);
  border-radius: 5px;
  font-size: 14px;
  background: var(--bg-darker);
  color: var(--text-primary);
}

.filters select:focus,
.filters input:focus {
  outline: none;
  border-color: var(--accent-color);
}

.filters input {
  flex: 1;
  min-width: 200px;
}

.pets-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 20px;
  margin: 40px 0;
}

.pet-card {
  background: var(--bg-card);
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 4px 8px rgba(0,0,0,0.3);
  border: 1px solid var(--border-color);
  transition: transform 0.3s, box-shadow 0.3s, border-color 0.3s;
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

.pet-image {
  width: 100%;
  height: 200px;
  object-fit: cover;
}

.pet-info {
  padding: 15px;
}

.pet-info h3 {
  margin-bottom: 5px;
  color: var(--text-primary);
}

.pet-info p {
  color: var(--text-secondary);
  font-size: 14px;
  margin: 5px 0;
}

.price {
  font-weight: bold;
  color: var(--accent-color);
  font-size: 18px;
}

.status {
  display: inline-block;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: bold;
  text-transform: uppercase;
}

.status.available {
  background-color: var(--success-color);
  color: white;
}

.status.pending {
  background-color: var(--warning-color);
  color: white;
}

.loading,
.no-pets {
  text-align: center;
  padding: 60px 20px;
  color: var(--text-secondary);
}
</style>

