<template>
  <div class="verification-page">
    <div class="verification-container">
      <div class="verification-card">
        <h1>Identity Verification</h1>
        <p class="verification-description">
          To ensure the safety and security of our platform, we require identity verification before you can access the dashboard and adopt pets.
        </p>
        
        <div class="verification-steps">
          <div class="step-item">
            <div class="step-number">1</div>
            <div class="step-content">
              <h3>Upload ID (Front)</h3>
              <p>Upload a clear photo of the front of your government-issued ID</p>
              <div class="upload-area" @click="triggerUpload('idFront')">
                <input 
                  type="file" 
                  ref="idFrontInput"
                  @change="handleFileUpload('idFront', $event)"
                  accept="image/*"
                  style="display: none;"
                />
                <img v-if="uploads.idFront" :src="uploads.idFront" alt="ID Front" class="upload-preview" />
                <div v-else class="upload-placeholder">
                  <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="17 8 12 3 7 8"></polyline>
                    <line x1="12" y1="3" x2="12" y2="15"></line>
                  </svg>
                  <span>Click to upload</span>
                </div>
              </div>
            </div>
          </div>

          <div class="step-item">
            <div class="step-number">2</div>
            <div class="step-content">
              <h3>Upload ID (Back)</h3>
              <p>Upload a clear photo of the back of your government-issued ID</p>
              <div class="upload-area" @click="triggerUpload('idBack')">
                <input 
                  type="file" 
                  ref="idBackInput"
                  @change="handleFileUpload('idBack', $event)"
                  accept="image/*"
                  style="display: none;"
                />
                <img v-if="uploads.idBack" :src="uploads.idBack" alt="ID Back" class="upload-preview" />
                <div v-else class="upload-placeholder">
                  <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="17 8 12 3 7 8"></polyline>
                    <line x1="12" y1="3" x2="12" y2="15"></line>
                  </svg>
                  <span>Click to upload</span>
                </div>
              </div>
            </div>
          </div>

          <div class="step-item">
            <div class="step-number">3</div>
            <div class="step-content">
              <h3>Upload Selfie with ID</h3>
              <p>Take a selfie holding your ID next to your face</p>
              <div class="upload-area" @click="triggerUpload('selfie')">
                <input 
                  type="file" 
                  ref="selfieInput"
                  @change="handleFileUpload('selfie', $event)"
                  accept="image/*"
                  style="display: none;"
                />
                <img v-if="uploads.selfie" :src="uploads.selfie" alt="Selfie" class="upload-preview" />
                <div v-else class="upload-placeholder">
                  <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                  </svg>
                  <span>Click to upload</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div v-if="error" class="error">{{ error }}</div>
        <div v-if="success" class="success">{{ success }}</div>

        <div class="verification-actions">
          <button 
            class="btn btn-secondary" 
            @click="verifyImmediately"
            :disabled="submitting"
          >
            {{ submitting ? 'Verifying...' : 'Verify Immediately (Skip Upload)' }}
          </button>
          <button 
            class="btn btn-primary btn-full" 
            @click="submitVerification"
            :disabled="!canSubmit || submitting"
          >
            {{ submitting ? 'Submitting...' : 'Submit for verification' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  middleware: 'auth',
  layout: 'default'
})

const uploads = ref({
  idFront: null,
  idBack: null,
  selfie: null,
})

const files = ref({
  idFront: null,
  idBack: null,
  selfie: null,
})

const error = ref('')
const success = ref('')
const submitting = ref(false)

const idFrontInput = ref(null)
const idBackInput = ref(null)
const selfieInput = ref(null)

const canSubmit = computed(() => {
  return uploads.value.idFront && uploads.value.idBack && uploads.value.selfie
})

const triggerUpload = (type) => {
  if (type === 'idFront') idFrontInput.value?.click()
  if (type === 'idBack') idBackInput.value?.click()
  if (type === 'selfie') selfieInput.value?.click()
}

const handleFileUpload = (type, event) => {
  const file = event.target.files[0]
  if (file) {
    if (file.size > 5 * 1024 * 1024) {
      error.value = 'File size must be less than 5MB'
      return
    }
    files.value[type] = file
    uploads.value[type] = URL.createObjectURL(file)
  }
}

const { apiFetch } = useApi()
const auth = useAuth()

const verifyImmediately = async () => {
  error.value = ''
  success.value = ''
  submitting.value = true

  try {
    const response = await apiFetch('/verify', {
      method: 'POST',
    })
    
    success.value = 'Account verified successfully!'
    
    // Update user in auth state
    if (auth.user.value) {
      auth.user.value.verified = true
    }
    
    // Refresh user data
    await auth.fetchUser()
    
    setTimeout(() => {
      navigateTo('/')
    }, 1500)
  } catch (err) {
    error.value = err.data?.message || 'Failed to verify account. Please try again.'
  } finally {
    submitting.value = false
  }
}

const submitVerification = async () => {
  if (!canSubmit.value) {
    error.value = 'Please upload all required documents'
    return
  }

  error.value = ''
  success.value = ''
  submitting.value = true

  try {
    // For now, just verify immediately after upload
    // In production, you would upload files and wait for admin approval
    await verifyImmediately()
  } catch (err) {
    error.value = 'Failed to submit verification. Please try again.'
  } finally {
    submitting.value = false
  }
}
</script>

<style scoped>
.verification-page {
  min-height: 100vh;
  padding: 40px 20px;
}

.verification-container {
  max-width: 800px;
  margin: 0 auto;
}

.verification-card {
  background: var(--bg-card);
  padding: 40px;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.5);
  border: 1px solid var(--border-color);
}

.verification-card h1 {
  font-size: 28px;
  margin-bottom: 15px;
  color: var(--text-primary);
}

.verification-description {
  color: var(--text-secondary);
  margin-bottom: 40px;
  line-height: 1.6;
}

.verification-steps {
  display: flex;
  flex-direction: column;
  gap: 30px;
  margin-bottom: 40px;
}

.step-item {
  display: flex;
  gap: 20px;
}

.step-number {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: var(--accent-color);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  flex-shrink: 0;
}

.step-content {
  flex: 1;
}

.step-content h3 {
  font-size: 18px;
  margin-bottom: 8px;
  color: var(--text-primary);
}

.step-content p {
  color: var(--text-secondary);
  margin-bottom: 15px;
  font-size: 14px;
}

.upload-area {
  border: 2px dashed var(--border-color);
  border-radius: 8px;
  padding: 30px;
  text-align: center;
  cursor: pointer;
  transition: all 0.3s;
  background: var(--bg-darker);
}

.upload-area:hover {
  border-color: var(--accent-color);
  background: var(--bg-card);
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

.upload-preview {
  max-width: 100%;
  max-height: 200px;
  border-radius: 8px;
}

.verification-actions {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.btn-full {
  width: 100%;
  padding: 14px;
  font-size: 16px;
}
</style>
