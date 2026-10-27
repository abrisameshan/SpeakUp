<template>
  <div class="liquid-scene">
    <!-- Latar Belakang Fluid Mesh (Selaras dengan halaman Login) -->
    <div class="ambient-bg">
      <div class="blob blob-1"></div>
      <div class="blob blob-2"></div>
      <div class="blob blob-3"></div>
    </div>

    <!-- Container Utama -->
    <div class="glass-container">
      <!-- Card Liquid Glass -->
      <div class="liquid-card">
        <!-- Efek Pantulan Cahaya Atas (Specular Highlight) -->
        <div class="glass-glare"></div>

        <!-- Header -->
        <div class="card-header">
          <div class="liquid-logo">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
              <circle cx="9" cy="7" r="4"/>
              <line x1="19" y1="8" x2="19" y2="14"/>
              <line x1="22" y1="11" x2="16" y2="11"/>
            </svg>
          </div>
          <h2>Daftar Akun Baru</h2>
          <p class="subtitle">Lengkapi data di bawah untuk memulai</p>
        </div>

        <!-- Alert Error (Glass Style) -->
        <div v-if="errorMessage" class="glass-error">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
          </svg>
          <span>{{ errorMessage }}</span>
        </div>

        <!-- Form Register -->
        <form @submit.prevent="handleRegister" class="liquid-form">
          <div class="form-group">
            <label for="name">Nama Lengkap</label>
            <div class="liquid-input-wrap">
              <input 
                id="name"
                type="text" 
                v-model="form.name" 
                placeholder="Masukkan nama lengkap..." 
                autocomplete="name"
                required 
              />
            </div>
          </div>

          <div class="form-group">
            <label for="email">Email</label>
            <div class="liquid-input-wrap">
              <input 
                id="email"
                type="email" 
                v-model="form.email" 
                placeholder="contoh@mail.com" 
                autocomplete="email"
                required 
              />
            </div>
          </div>

          <div class="form-group">
            <div class="label-row">
              <label for="password">Password</label>
              <span 
                class="char-hint" 
                :class="{ 'is-valid': form.password.length >= 8 }"
              >
                {{ form.password.length >= 8 ? '✓ 8+ karakter' : 'Min. 8 karakter' }}
              </span>
            </div>
            <div class="liquid-input-wrap">
              <input 
                id="password"
                :type="showPassword ? 'text' : 'password'" 
                v-model="form.password" 
                placeholder="Minimal 8 karakter" 
                autocomplete="new-password"
                minlength="8"
                required 
              />
              <button 
                type="button" 
                class="eye-btn" 
                @click="showPassword = !showPassword"
                :aria-label="showPassword ? 'Sembunyikan' : 'Tampilkan'"
              >
                <svg v-if="!showPassword" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                  <circle cx="12" cy="12" r="3"></circle>
                </svg>
                <svg v-else width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                  <line x1="1" y1="1" x2="23" y2="23"></line>
                </svg>
              </button>
            </div>
          </div>

          <button type="submit" class="liquid-submit-btn" :disabled="loading">
            <span v-if="loading" class="spinner"></span>
            <span>{{ loading ? 'Mendaftarkan...' : 'Daftar Sekarang' }}</span>
          </button>
        </form>
      </div>

      <!-- Switch Link (Kapsul Kaca Kecil di Bawah) -->
      <div class="liquid-pill-footer">
        Sudah punya akun? <router-link to="/login">Login di sini</router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const form = reactive({
  name: '',
  email: '',
  password: ''
})

const loading = ref(false)
const errorMessage = ref('')
const showPassword = ref(false)

const handleRegister = async () => {
  loading.value = true
  errorMessage.value = ''

  try {
    const response = await fetch('http://127.0.0.1:8000/api/register', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(form)
    })

    const data = await response.json()

    if (!response.ok) {
      throw new Error(data.message || 'Gagal mendaftar akun.')
    }

    // Simpan token yang dikembalikan dari backend
    localStorage.setItem('token', data.token)

    // Langsung arahkan ke homepage
    router.push('/')
  } catch (error) {
    errorMessage.value = error.message
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
/* 1. SCENE & FLUID BACKGROUND */
.liquid-scene {
  position: relative;
  min-height: 100vh;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px 16px;
  background-color: #eef2f9;
  font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "Segoe UI", Roboto, sans-serif;
  overflow: hidden;
  box-sizing: border-box;
}

.ambient-bg {
  position: absolute;
  inset: 0;
  z-index: 0;
  overflow: hidden;
  filter: blur(70px);
}

.blob {
  position: absolute;
  border-radius: 50%;
  opacity: 0.75;
  animation: floatOrb 18s ease-in-out infinite alternate;
}

.blob-1 {
  width: 420px;
  height: 420px;
  background: radial-gradient(circle, #93c5fd 0%, #bfdbfe 100%);
  top: -8%;
  left: 18%;
}

.blob-2 {
  width: 380px;
  height: 380px;
  background: radial-gradient(circle, #c4b5fd 0%, #e0e7ff 100%);
  bottom: -5%;
  right: 18%;
  animation-delay: -6s;
}

.blob-3 {
  width: 300px;
  height: 300px;
  background: radial-gradient(circle, #a5f3fc 0%, #bae6fd 100%);
  top: 40%;
  left: 50%;
  transform: translate(-50%, -50%);
  animation-delay: -12s;
}

@keyframes floatOrb {
  0% { transform: translate(0, 0) scale(1); }
  50% { transform: translate(35px, -25px) scale(1.08); }
  100% { transform: translate(-25px, 30px) scale(0.95); }
}

/* 2. CONTAINER & LIQUID GLASS CARD */
.glass-container {
  position: relative;
  z-index: 1;
  width: 100%;
  max-width: 390px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 18px;
}

.liquid-card {
  position: relative;
  width: 100%;
  padding: 34px 30px;
  border-radius: 28px;
  box-sizing: border-box;
  overflow: hidden;

  /* Efek Liquid Glass */
  background: linear-gradient(
    145deg,
    rgba(255, 255, 255, 0.65) 0%,
    rgba(255, 255, 255, 0.28) 100%
  );
  backdrop-filter: blur(28px) saturate(190%);
  -webkit-backdrop-filter: blur(28px) saturate(190%);

  border: 1px solid rgba(255, 255, 255, 0.75);

  /* 3D Specular Rim */
  box-shadow:
    0 24px 48px -12px rgba(15, 23, 42, 0.08),
    0 4px 16px -2px rgba(15, 23, 42, 0.04),
    inset 0 2px 3px 0 rgba(255, 255, 255, 0.95),
    inset 0 -2px 6px 0 rgba(255, 255, 255, 0.45),
    inset 0 0 18px 0 rgba(255, 255, 255, 0.25);
}

.glass-glare {
  position: absolute;
  top: -50%;
  left: -20%;
  width: 140%;
  height: 75%;
  background: radial-gradient(
    ellipse at center,
    rgba(255, 255, 255, 0.7) 0%,
    rgba(255, 255, 255, 0) 70%
  );
  transform: rotate(-8deg);
  pointer-events: none;
}

/* 3. HEADER & DROPLET LOGO */
.card-header {
  position: relative;
  text-align: center;
  margin-bottom: 24px;
}

.liquid-logo {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 48px;
  height: 48px;
  border-radius: 16px;
  margin-bottom: 14px;
  color: #1e293b;
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.35));
  border: 1px solid rgba(255, 255, 255, 0.9);
  box-shadow:
    0 8px 16px -4px rgba(15, 23, 42, 0.08),
    inset 0 2px 2px rgba(255, 255, 255, 1),
    inset 0 -2px 4px rgba(148, 163, 184, 0.2);
}

h2 {
  margin: 0 0 6px 0;
  font-size: 22px;
  font-weight: 700;
  color: #0f172a;
  letter-spacing: -0.02em;
}

.subtitle {
  margin: 0;
  font-size: 13.5px;
  color: #475569;
}

/* 4. FORM & LIQUID INPUTS */
.liquid-form {
  position: relative;
}

.form-group {
  margin-bottom: 16px;
}

.label-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 7px;
}

label {
  display: block;
  font-size: 13px;
  font-weight: 600;
  color: #1e293b;
  margin-bottom: 7px;
  padding-left: 2px;
}

.label-row label {
  margin-bottom: 0;
}

.char-hint {
  font-size: 11.5px;
  font-weight: 500;
  color: #64748b;
  transition: color 0.2s ease;
}

.char-hint.is-valid {
  color: #059669;
  font-weight: 600;
}

.liquid-input-wrap {
  position: relative;
  display: flex;
  align-items: center;
}

input {
  width: 100%;
  height: 44px;
  padding: 0 14px;
  font-size: 14px;
  color: #0f172a;
  box-sizing: border-box;
  border-radius: 14px;
  outline: none;

  background: rgba(255, 255, 255, 0.45);
  border: 1px solid rgba(255, 255, 255, 0.75);
  box-shadow:
    inset 0 2px 4px rgba(15, 23, 42, 0.04),
    0 1px 2px rgba(255, 255, 255, 0.7);
  transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

input::placeholder {
  color: #64748b;
  opacity: 0.75;
}

input:focus {
  background: rgba(255, 255, 255, 0.82);
  border-color: #ffffff;
  box-shadow:
    0 4px 16px -2px rgba(59, 130, 246, 0.14),
    0 0 0 3px rgba(255, 255, 255, 0.65),
    inset 0 1px 2px rgba(255, 255, 255, 1);
  transform: translateY(-1px);
}

.liquid-input-wrap input[type="password"],
.liquid-input-wrap input[type="text"]#password {
  padding-right: 42px;
}

.eye-btn {
  position: absolute;
  right: 10px;
  background: none;
  border: none;
  padding: 6px;
  color: #64748b;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  transition: color 0.15s ease, background-color 0.15s ease;
}

.eye-btn:hover {
  color: #0f172a;
  background: rgba(255, 255, 255, 0.5);
}

/* 5. LIQUID BUTTON */
.liquid-submit-btn {
  width: 100%;
  height: 46px;
  margin-top: 8px;
  border-radius: 14px;
  font-size: 14.5px;
  font-weight: 600;
  color: #ffffff;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;

  background: linear-gradient(180deg, rgba(30, 41, 59, 0.9) 0%, rgba(15, 23, 42, 0.96) 100%);
  border: 1px solid rgba(255, 255, 255, 0.2);
  box-shadow:
    0 10px 20px -6px rgba(15, 23, 42, 0.35),
    inset 0 1.5px 1.5px rgba(255, 255, 255, 0.35),
    inset 0 -2px 4px rgba(0, 0, 0, 0.4);
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

.liquid-submit-btn:hover:not(:disabled) {
  transform: translateY(-1.5px);
  background: linear-gradient(180deg, rgba(51, 65, 85, 0.92) 0%, rgba(15, 23, 42, 0.98) 100%);
  box-shadow:
    0 14px 24px -6px rgba(15, 23, 42, 0.4),
    inset 0 1.5px 2px rgba(255, 255, 255, 0.45);
}

.liquid-submit-btn:active:not(:disabled) {
  transform: translateY(0);
}

.liquid-submit-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* 6. ERROR BANNER */
.glass-error {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 11px 14px;
  margin-bottom: 18px;
  border-radius: 12px;
  background: rgba(254, 226, 226, 0.65);
  border: 1px solid rgba(252, 165, 165, 0.8);
  box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.8);
  color: #b91c1c;
  font-size: 13px;
}

/* 7. FOOTER PILL */
.liquid-pill-footer {
  padding: 10px 22px;
  border-radius: 999px;
  font-size: 13px;
  color: #334155;
  background: rgba(255, 255, 255, 0.45);
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  border: 1px solid rgba(255, 255, 255, 0.75);
  box-shadow:
    0 4px 12px rgba(15, 23, 42, 0.04),
    inset 0 1px 1px rgba(255, 255, 255, 0.9);
}

.liquid-pill-footer a {
  color: #0f172a;
  font-weight: 600;
  text-decoration: none;
  margin-left: 4px;
}

.liquid-pill-footer a:hover {
  text-decoration: underline;
}

/* Spinner Loading */
.spinner {
  width: 15px;
  height: 15px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  border-top-color: #ffffff;
  animation: spin 0.6s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>