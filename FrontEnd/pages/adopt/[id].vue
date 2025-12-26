<template>
  <div class="adoption-page-wrapper">
    <div class="container">
      <div v-if="loading" class="loading">Loading...</div>
      <div v-else-if="pet" class="adoption-page">
        <h1>Adoption Request Confirmation</h1>
        <div class="adoption-content">
          <div class="adoption-section">
            <h2>Pet Details</h2>
            <div class="pet-summary">
              <img 
                v-if="pet.images && pet.images.length > 0" 
                :src="getImageUrl(pet.images[0])" 
                :alt="pet.name"
                class="pet-thumbnail"
              />
              <div class="pet-summary-info">
                <h3>{{ pet.name }}</h3>
                <p><strong>Breed:</strong> {{ pet.breed || 'Mixed' }}</p>
                <p><strong>Age:</strong> {{ pet.age || 'Unknown' }} years</p>
                <p><strong>Species:</strong> {{ pet.species }}</p>
              </div>
            </div>
          </div>

          <div class="adoption-section">
            <h2>Your Information</h2>
            <div class="user-info">
              <p><strong>Name:</strong> {{ auth.user.value?.name }}</p>
              <p v-if="auth.user.value?.email"><strong>Email:</strong> {{ auth.user.value.email }}</p>
              <p v-if="auth.user.value?.phone"><strong>Phone:</strong> {{ auth.user.value.phone }}</p>
              <p v-if="auth.user.value?.address"><strong>Address:</strong> {{ auth.user.value.address }}</p>
            </div>
          </div>

          <div class="adoption-section">
            <h2>Terms & Conditions</h2>
            <div class="terms-content">
              <p>By confirming this adoption request, you agree to:</p>
              <ul>
                <li>Provide a safe and loving home for the pet</li>
                <li>Ensure the pet receives proper veterinary care</li>
                <li>Keep the pet's vaccinations up to date</li>
                <li>Allow follow-up visits if requested by the previous owner</li>
                <li>Not use the pet for breeding or commercial purposes</li>
              </ul>
            </div>
            <div class="terms-checkbox">
              <input type="checkbox" id="acceptTerms" v-model="acceptedTerms" />
              <label for="acceptTerms">I have read and agree to the Terms & Conditions</label>
            </div>
          </div>

          <div class="adoption-section">
            <h2>Additional Notes (Optional)</h2>
            <textarea 
              v-model="notes" 
              placeholder="Tell the owner about yourself and why you'd like to adopt this pet..."
              rows="5"
            ></textarea>
          </div>

          <div v-if="error" class="error">{{ error }}</div>
          <div v-if="success" class="success">{{ success }}</div>

          <div class="adoption-actions">
            <button class="btn btn-secondary" @click="$router.back()">Cancel</button>
            <button 
              class="btn btn-primary" 
              @click="confirmAdoption"
              :disabled="!acceptedTerms || submitting"
            >
              {{ submitting ? 'Submitting...' : 'Confirm Adoption' }}
            </button>
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
const pet = ref(null)
const notes = ref('')
const acceptedTerms = ref(false)
const error = ref('')
const success = ref('')
const loading = ref(true)
const submitting = ref(false)

const getImageUrl = (path) => {
  if (!path) return '/placeholder-pet.jpg'
  return `http://localhost:8000/storage/${path}`
}

const { apiFetch } = useApi()

const loadPet = async () => {
  try {
    const response = await apiFetch(`/pets/${route.params.id}`)
    pet.value = response
  } catch (error) {
    console.error('Error fetching pet:', error)
  } finally {
    loading.value = false
  }
}

const confirmAdoption = async () => {
  if (!acceptedTerms.value) {
    error.value = 'Please accept the Terms & Conditions'
    return
  }

  error.value = ''
  success.value = ''
  submitting.value = true

  try {
    await apiFetch('/adoptions', {
      method: 'POST',
      body: {
        pet_id: pet.value.id,
        notes: notes.value,
      },
    })
    success.value = 'Adoption request submitted successfully!'
    setTimeout(() => {
      navigateTo('/profile')
    }, 2000)
  } catch (err) {
    error.value = err.data?.message || 'Failed to submit adoption request'
  } finally {
    submitting.value = false
  }
}

onMounted(() => {
  loadPet()
})
</script>

<style scoped>
.adoption-page {
  max-width: 800px;
  margin: 40px auto;
}

.adoption-page h1 {
  font-size: 32px;
  margin-bottom: 30px;
  color: var(--text-primary);
}

.adoption-content {
  background: var(--bg-card);
  padding: 40px;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.5);
  border: 1px solid var(--border-color);
}

.adoption-section {
  margin-bottom: 30px;
  padding-bottom: 30px;
  border-bottom: 1px solid var(--border-color);
}

.adoption-section:last-child {
  border-bottom: none;
}

.adoption-section h2 {
  font-size: 24px;
  margin-bottom: 20px;
  color: var(--text-primary);
}

.pet-summary {
  display: flex;
  gap: 20px;
}

.pet-thumbnail {
  width: 150px;
  height: 150px;
  object-fit: cover;
  border-radius: 8px;
}

.pet-summary-info h3 {
  font-size: 20px;
  margin-bottom: 10px;
  color: var(--text-primary);
}

.pet-summary-info p {
  margin-bottom: 5px;
  color: var(--text-secondary);
}

.user-info p {
  margin-bottom: 10px;
  color: var(--text-secondary);
}

.terms-content {
  background: var(--bg-darker);
  padding: 20px;
  border-radius: 8px;
  margin-bottom: 20px;
  border: 1px solid var(--border-color);
}

.terms-content p {
  margin-bottom: 10px;
  color: var(--text-secondary);
}

.terms-content ul {
  margin-left: 20px;
  color: var(--text-secondary);
}

.terms-content li {
  margin-bottom: 8px;
}

.terms-checkbox {
  display: flex;
  align-items: flex-start;
  gap: 10px;
}

.terms-checkbox input[type="checkbox"] {
  width: auto;
  margin-top: 3px;
}

.terms-checkbox label {
  color: var(--text-secondary);
  cursor: pointer;
}

textarea {
  width: 100%;
  padding: 12px;
  border: 1px solid var(--border-color);
  border-radius: 8px;
  font-size: 14px;
  font-family: inherit;
  resize: vertical;
  background: var(--bg-darker);
  color: var(--text-primary);
}

textarea:focus {
  outline: none;
  border-color: var(--accent-color);
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.adoption-actions {
  display: flex;
  gap: 15px;
  justify-content: flex-end;
  margin-top: 30px;
}

.adoption-actions .btn {
  padding: 12px 30px;
}
</style>

