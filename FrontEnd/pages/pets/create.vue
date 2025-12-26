<template>
  <div class="add-pet-page-wrapper">
    <div class="container">
      <div class="add-pet-page">
        <h1>Add Pet</h1>
        <form @submit.prevent="handleSubmit" class="pet-form">
          <div class="form-section">
            <h2>Basic Information</h2>
            <div class="form-grid">
              <div class="form-group">
                <label>Pet Name *</label>
                <input type="text" v-model="form.name" required />
              </div>
              <div class="form-group">
                <label>Breed *</label>
                <input type="text" v-model="form.breed" required />
              </div>
              <div class="form-group">
                <label>Age</label>
                <input type="number" v-model.number="form.age" min="0" />
              </div>
              <div class="form-group">
                <label>Gender</label>
                <select v-model="form.gender">
                  <option value="">Select Gender</option>
                  <option value="male">Male</option>
                  <option value="female">Female</option>
                </select>
              </div>
              <div class="form-group">
                <label>Species *</label>
                <select v-model="form.species" required>
                  <option value="">Select Species</option>
                  <option value="dog">Dog</option>
                  <option value="cat">Cat</option>
                  <option value="bird">Bird</option>
                  <option value="rabbit">Rabbit</option>
                  <option value="other">Other</option>
                </select>
              </div>
              <div class="form-group">
                <label>Size</label>
                <select v-model="form.size">
                  <option value="">Select Size</option>
                  <option value="small">Small</option>
                  <option value="medium">Medium</option>
                  <option value="large">Large</option>
                </select>
              </div>
            </div>
          </div>

          <div class="form-section">
            <h2>Description</h2>
            <div class="form-group">
              <textarea 
                v-model="form.description" 
                required
                rows="6"
                placeholder="Describe the pet's personality, behavior, and any special needs..."
              ></textarea>
            </div>
          </div>

          <div class="form-section">
            <h2>Photos</h2>
            <div 
              class="photo-upload-area"
              @drop="handleDrop"
              @dragover.prevent
              @dragenter.prevent
              :class="{ 'drag-over': isDragging }"
            >
              <input 
                type="file" 
                ref="fileInput"
                @change="handleImageChange" 
                multiple 
                accept="image/*"
                style="display: none;"
              />
              <div v-if="imagePreviews.length === 0" class="upload-placeholder">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                  <polyline points="17 8 12 3 7 8"></polyline>
                  <line x1="12" y1="3" x2="12" y2="15"></line>
                </svg>
                <p>Drag and drop images here, or <button type="button" @click="$refs.fileInput.click()" class="link-btn">browse</button></p>
                <span>Upload up to 5 images (max 2MB each)</span>
              </div>
              <div v-else class="image-previews">
                <div v-for="(preview, index) in imagePreviews" :key="index" class="image-preview">
                  <img :src="preview" alt="Preview" />
                  <button type="button" @click="removeImage(index)" class="remove-image">×</button>
                </div>
                <div v-if="imagePreviews.length < 5" class="add-more" @click="$refs.fileInput.click()">
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                  </svg>
                </div>
              </div>
            </div>
          </div>

          <div class="form-section">
            <h2>Medical Information</h2>
            <div class="form-grid">
              <div class="form-group">
                <label>Veterinarian Name</label>
                <input type="text" v-model="form.vet_name" />
              </div>
              <div class="form-group">
                <label>Clinic Name</label>
                <input type="text" v-model="form.clinic_name" />
              </div>
              <div class="form-group">
                <label>Last Checkup Date</label>
                <input type="date" v-model="form.last_checkup" />
              </div>
              <div class="form-group">
                <label>Health Status</label>
                <select v-model="form.health_status">
                  <option value="">Select Status</option>
                  <option value="excellent">Excellent</option>
                  <option value="good">Good</option>
                  <option value="fair">Fair</option>
                  <option value="needs_attention">Needs Attention</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label>Medical Notes</label>
              <textarea 
                v-model="form.medical_notes" 
                rows="4"
                placeholder="Any medical conditions, medications, or special care requirements..."
              ></textarea>
            </div>
          </div>

          <div class="form-section">
            <h2>Location & Pricing</h2>
            <div class="form-grid">
              <div class="form-group">
                <label>Location *</label>
                <input type="text" v-model="form.location" required placeholder="City, State" />
              </div>
              <div class="form-group">
                <label>Adoption Fee</label>
                <input type="number" v-model.number="form.price" min="0" step="0.01" placeholder="0.00" />
              </div>
            </div>
          </div>

          <div v-if="error" class="error">{{ error }}</div>
          <div v-if="success" class="success">{{ success }}</div>

          <div class="form-actions">
            <button type="button" class="btn btn-secondary" @click="$router.back()">Cancel</button>
            <button type="submit" class="btn btn-primary" :disabled="loading">
              {{ loading ? 'Saving...' : 'Save' }}
            </button>
          </div>
        </form>
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
const form = ref({
  name: '',
  species: '',
  breed: '',
  age: null,
  gender: '',
  size: '',
  description: '',
  location: '',
  price: null,
  vet_name: '',
  clinic_name: '',
  last_checkup: '',
  health_status: '',
  medical_notes: '',
})
const images = ref([])
const imagePreviews = ref([])
const isDragging = ref(false)
const error = ref('')
const success = ref('')
const loading = ref(false)

const handleImageChange = (event) => {
  const files = Array.from(event.target.files)
  if (images.value.length + files.length > 5) {
    error.value = 'Maximum 5 images allowed'
    return
  }
  files.forEach(file => {
    if (file.size > 2 * 1024 * 1024) {
      error.value = 'Each image must be less than 2MB'
      return
    }
    images.value.push(file)
    imagePreviews.value.push(URL.createObjectURL(file))
  })
}

const handleDrop = (event) => {
  isDragging.value = false
  const files = Array.from(event.dataTransfer.files).filter(file => file.type.startsWith('image/'))
  if (files.length > 0) {
    const input = { target: { files } }
    handleImageChange(input)
  }
}

const removeImage = (index) => {
  images.value.splice(index, 1)
  imagePreviews.value.splice(index, 1)
}

const { apiFetchFormData } = useApi()

const handleSubmit = async () => {
  error.value = ''
  success.value = ''
  loading.value = true

  try {
    const formData = new FormData()
    Object.keys(form.value).forEach(key => {
      if (form.value[key] !== null && form.value[key] !== '') {
        formData.append(key, form.value[key])
      }
    })
    
    images.value.forEach(image => {
      formData.append('images[]', image)
    })

    const data = await apiFetchFormData('/pets', formData)

    success.value = 'Pet posted successfully!'
    setTimeout(() => {
      navigateTo(`/pets/${data.id}`)
    }, 1500)
  } catch (err) {
    error.value = err.message || 'Failed to post pet'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.add-pet-page {
  max-width: 900px;
  margin: 40px auto;
}

.add-pet-page h1 {
  font-size: 32px;
  margin-bottom: 30px;
  color: var(--text-primary);
}

.pet-form {
  background: var(--bg-card);
  padding: 40px;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.5);
  border: 1px solid var(--border-color);
}

.form-section {
  margin-bottom: 40px;
  padding-bottom: 30px;
  border-bottom: 1px solid var(--border-color);
}

.form-section:last-of-type {
  border-bottom: none;
}

.form-section h2 {
  font-size: 24px;
  margin-bottom: 20px;
  color: var(--text-primary);
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 500;
  color: var(--text-primary);
}

.form-group input,
.form-group select,
.form-group textarea {
  width: 100%;
  padding: 12px;
  border: 1px solid var(--border-color);
  border-radius: 8px;
  font-size: 16px;
  font-family: inherit;
  background: var(--bg-darker);
  color: var(--text-primary);
  transition: border-color 0.3s;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: var(--accent-color);
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.photo-upload-area {
  border: 2px dashed var(--border-color);
  border-radius: 8px;
  padding: 40px;
  text-align: center;
  transition: all 0.3s;
  background: var(--bg-darker);
}

.photo-upload-area.drag-over {
  border-color: var(--accent-color);
  background: rgba(102, 126, 234, 0.1);
}

.upload-placeholder {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
  color: var(--text-muted);
}

.upload-placeholder svg {
  color: var(--text-muted);
}

.link-btn {
  background: none;
  border: none;
  color: var(--accent-color);
  cursor: pointer;
  text-decoration: underline;
  font-size: inherit;
}

.image-previews {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 15px;
}

.image-preview {
  position: relative;
  width: 100%;
  height: 150px;
  border-radius: 8px;
  overflow: hidden;
}

.image-preview img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.remove-image {
  position: absolute;
  top: 5px;
  right: 5px;
  background: rgba(244, 67, 54, 0.9);
  color: white;
  border: none;
  border-radius: 50%;
  width: 30px;
  height: 30px;
  cursor: pointer;
  font-size: 20px;
  line-height: 1;
  display: flex;
  align-items: center;
  justify-content: center;
}

.add-more {
  width: 100%;
  height: 150px;
  border: 2px dashed var(--border-color);
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  color: var(--text-muted);
  background: var(--bg-darker);
  transition: all 0.3s;
}

.add-more:hover {
  border-color: var(--accent-color);
  color: var(--accent-color);
  background: rgba(102, 126, 234, 0.1);
}

.form-actions {
  display: flex;
  gap: 15px;
  justify-content: flex-end;
  margin-top: 30px;
}

.form-actions .btn {
  padding: 12px 30px;
}

@media (max-width: 768px) {
  .form-grid {
    grid-template-columns: 1fr;
  }
}
</style>
