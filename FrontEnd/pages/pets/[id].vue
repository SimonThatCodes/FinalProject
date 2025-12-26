<template>
  <div class="pet-detail-page-wrapper">
    <div class="container">
      <div v-if="loading" class="loading">Loading...</div>
      <div v-else-if="pet" class="pet-detail-page">
        <div class="pet-main">
          <div class="pet-image-section">
            <img 
              v-if="pet.images && pet.images.length > 0" 
              :src="getImageUrl(pet.images[0])" 
              :alt="pet.name"
              class="main-pet-image"
            />
            <div v-else class="no-image">No image available</div>
            <div class="pet-actions">
              <button class="btn-action" @click="sharePet">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <circle cx="18" cy="5" r="3"></circle>
                  <circle cx="6" cy="12" r="3"></circle>
                  <circle cx="18" cy="19" r="3"></circle>
                  <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                  <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
                </svg>
                Share
              </button>
              <button class="btn-action" @click="savePet">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                </svg>
                Save
              </button>
              <button 
                class="btn-action btn-adopt" 
                @click="requestAdoption"
                :disabled="pet.status !== 'available'"
              >
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                </svg>
                Adopt
              </button>
            </div>
          </div>
          <div class="pet-info-section">
            <h1>{{ pet.name }}</h1>
            <div class="pet-basic-info">
              <div class="info-item">
                <strong>Breed:</strong> {{ pet.breed || 'Mixed' }}
              </div>
              <div class="info-item">
                <strong>Age:</strong> {{ pet.age || 'Unknown' }} years
              </div>
              <div class="info-item">
                <strong>Gender:</strong> {{ pet.gender || 'Unknown' }}
              </div>
              <div class="info-item">
                <strong>Location:</strong> {{ pet.location }}
              </div>
            </div>
            <div class="pet-description">
              <h3>Description</h3>
              <p>{{ pet.description }}</p>
            </div>
          </div>
        </div>

        <div class="pet-details-sections">
          <div class="detail-section">
            <h3>Doctor Details</h3>
            <div class="detail-content">
              <p><strong>Veterinarian:</strong> Dr. Smith</p>
              <p><strong>Clinic:</strong> Happy Paws Veterinary</p>
              <p><strong>Contact:</strong> (555) 123-4567</p>
            </div>
          </div>

          <div class="detail-section">
            <h3>Medical History</h3>
            <div class="detail-content">
              <p>Last checkup: 2 months ago</p>
              <p>Health status: Excellent</p>
              <p>Previous conditions: None</p>
            </div>
          </div>

          <div class="detail-section">
            <h3>Vaccinations</h3>
            <div class="detail-content">
              <div class="vaccination-item">
                <span>Rabies</span>
                <span class="vaccination-status">✓ Up to date</span>
              </div>
              <div class="vaccination-item">
                <span>DHPP</span>
                <span class="vaccination-status">✓ Up to date</span>
              </div>
              <div class="vaccination-item">
                <span>Bordetella</span>
                <span class="vaccination-status">✓ Up to date</span>
              </div>
            </div>
          </div>

          <div class="detail-section">
            <h3>Owner Details</h3>
            <div class="detail-content">
              <p><strong>Name:</strong> {{ pet.user?.name }}</p>
              <p v-if="pet.user?.email"><strong>Email:</strong> {{ pet.user.email }}</p>
              <p v-if="pet.user?.phone"><strong>Phone:</strong> {{ pet.user.phone }}</p>
              <button 
                class="btn btn-secondary" 
                @click="startChat"
                style="margin-top: 15px;"
              >
                Message Owner
              </button>
            </div>
          </div>
        </div>

        <div class="related-pets" v-if="relatedPets.length > 0">
          <h2>Related Pets</h2>
          <div class="related-pets-grid">
            <div v-for="relatedPet in relatedPets" :key="relatedPet.id" class="related-pet-card">
              <NuxtLink :to="`/pets/${relatedPet.id}`">
                <img 
                  v-if="relatedPet.images && relatedPet.images.length > 0" 
                  :src="getImageUrl(relatedPet.images[0])" 
                  :alt="relatedPet.name"
                />
                <h4>{{ relatedPet.name }}</h4>
              </NuxtLink>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const route = useRoute()
const auth = useAuth()
const pet = ref(null)
const relatedPets = ref([])
const loading = ref(true)

const getImageUrl = (path) => {
  if (!path) return '/placeholder-pet.jpg'
  return `http://localhost:8000/storage/${path}`
}

const { apiFetch } = useApi()

const loadPet = async () => {
  try {
    const response = await apiFetch(`/pets/${route.params.id}`)
    pet.value = response
    await loadRelatedPets()
  } catch (error) {
    console.error('Error fetching pet:', error)
  } finally {
    loading.value = false
  }
}

const loadRelatedPets = async () => {
  try {
    const response = await apiFetch(`/pets?species=${pet.value.species}&limit=4`)
    relatedPets.value = (response.data || response).filter(p => p.id !== pet.value.id).slice(0, 4)
  } catch (error) {
    console.error('Error fetching related pets:', error)
  }
}

const requestAdoption = async () => {
  if (!confirm('Are you sure you want to request adoption for this pet?')) return
  navigateTo(`/adopt/${pet.value.id}`)
}

const startChat = async () => {
  try {
    const response = await apiFetch('/chats', {
      method: 'POST',
      body: {
        user2_id: pet.value.user_id,
        pet_id: pet.value.id,
      },
    })
    navigateTo(`/chats/${response.id}`)
  } catch (error) {
    console.error('Error starting chat:', error)
  }
}

const sharePet = () => {
  if (navigator.share) {
    navigator.share({
      title: pet.value.name,
      text: `Check out ${pet.value.name} on Adopet!`,
      url: window.location.href,
    })
  } else {
    navigator.clipboard.writeText(window.location.href)
    alert('Link copied to clipboard!')
  }
}

const savePet = () => {
  // TODO: Implement save/favorite functionality
  alert('Save functionality coming soon!')
}

onMounted(() => {
  loadPet()
})
</script>

<style scoped>
.pet-detail-page {
  margin: 40px 0;
}

.pet-main {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 40px;
  margin-bottom: 40px;
}

.pet-image-section {
  position: relative;
}

.main-pet-image {
  width: 100%;
  height: 500px;
  object-fit: cover;
  border-radius: 12px;
}

.no-image {
  width: 100%;
  height: 500px;
  background: var(--bg-darker);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--text-muted);
}

.pet-actions {
  display: flex;
  gap: 10px;
  margin-top: 15px;
}

.btn-action {
  flex: 1;
  padding: 12px;
  border: 2px solid var(--border-color);
  background: var(--bg-card);
  color: var(--text-primary);
  border-radius: 8px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  font-weight: 500;
  transition: all 0.3s;
}

.btn-action:hover {
  border-color: var(--accent-color);
  color: var(--accent-color);
  background: var(--bg-card-hover);
}

.btn-adopt {
  background: var(--accent-color);
  border-color: var(--accent-color);
  color: white;
}

.btn-adopt:hover {
  background: var(--accent-hover);
  border-color: var(--accent-hover);
}

.btn-adopt:disabled {
  background: var(--text-muted);
  border-color: var(--text-muted);
  cursor: not-allowed;
  opacity: 0.5;
}

.pet-info-section h1 {
  font-size: 32px;
  margin-bottom: 20px;
  color: var(--text-primary);
}

.pet-basic-info {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 15px;
  margin-bottom: 30px;
  padding: 20px;
  background: var(--bg-darker);
  border-radius: 8px;
  border: 1px solid var(--border-color);
}

.info-item {
  color: var(--text-secondary);
}

.pet-description {
  margin-top: 20px;
}

.pet-description h3 {
  margin-bottom: 10px;
  color: var(--text-primary);
}

.pet-description p {
  color: var(--text-secondary);
  line-height: 1.6;
}

.pet-details-sections {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 25px;
  margin-bottom: 40px;
}

.detail-section {
  background: var(--bg-card);
  padding: 25px;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.3);
  border: 1px solid var(--border-color);
}

.detail-section h3 {
  margin-bottom: 15px;
  color: var(--text-primary);
  font-size: 20px;
}

.detail-content p {
  margin-bottom: 10px;
  color: var(--text-secondary);
}

.vaccination-item {
  display: flex;
  justify-content: space-between;
  padding: 10px 0;
  border-bottom: 1px solid var(--border-color);
}

.vaccination-item:last-child {
  border-bottom: none;
}

.vaccination-status {
  color: var(--success-color);
  font-weight: 500;
}

.related-pets {
  margin-top: 40px;
}

.related-pets h2 {
  margin-bottom: 20px;
  color: var(--text-primary);
}

.related-pets-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 20px;
}

.related-pet-card {
  background: var(--bg-card);
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0,0,0,0.3);
  border: 1px solid var(--border-color);
  transition: transform 0.3s;
}

.related-pet-card:hover {
  transform: translateY(-3px);
  border-color: var(--accent-color);
}

.related-pet-card a {
  text-decoration: none;
  color: inherit;
}

.related-pet-card img {
  width: 100%;
  height: 150px;
  object-fit: cover;
}

.related-pet-card h4 {
  padding: 15px;
  color: var(--text-primary);
  font-size: 16px;
}

@media (max-width: 768px) {
  .pet-main {
    grid-template-columns: 1fr;
  }
  
  .pet-basic-info {
    grid-template-columns: 1fr;
  }
}
</style>
