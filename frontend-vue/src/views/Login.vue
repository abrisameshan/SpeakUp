<template>
  <div class="login-card">
    <h2>Login</h2>
    <form @submit.prevent="handleLogin">
      <div class="form-group">
        <label>Email:</label>
        <input 
          type="email" 
          v-model="form.email" 
          placeholder="Masukkan email..." 
          required 
        />
      </div>

      <div class="form-group">
        <label>Password:</label>
        <input 
          type="password" 
          v-model="form.password" 
          placeholder="Masukkan password..." 
          required 
        />
      </div>

      <button type="submit" :disabled="loading">
        {{ loading ? 'Memproses...' : 'Login' }}
      </button>

      <p v-if="errorMessage" class="error-text">{{ errorMessage }}</p>
    </form>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const form = reactive({
  email: '',
  password: ''
})

const loading = ref(false)
const errorMessage = ref('')

const handleLogin = async () => {
  loading.value = true
  errorMessage.value = ''

  try {
    const response = await fetch('http://127.0.0.1:8000/api/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(form)
    })

    const data = await response.json()

    if (!response.ok) {
      throw new Error(data.message || 'Login gagal, periksa email atau password.')
    }

    // 1. Simpan token ke localStorage
    localStorage.setItem('token', data.token)

    // 2. Arahkan langsung ke homepage
    router.push('/')
  } catch (error) {
    errorMessage.value = error.message
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.login-card {
  max-width: 360px;
  margin: 80px auto;
  padding: 24px;
  border: 1px solid #ddd;
  border-radius: 8px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
  font-family: sans-serif;
}

h2 {
  text-align: center;
  margin-bottom: 20px;
}

.form-group {
  margin-bottom: 16px;
}

label {
  display: block;
  margin-bottom: 6px;
  font-size: 14px;
  font-weight: 600;
}

input {
  width: 100%;
  padding: 10px;
  box-sizing: border-box;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 14px;
}

button {
  width: 100%;
  padding: 10px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 16px;
  cursor: pointer;
}

button:disabled {
  background-color: #a0c4ff;
  cursor: not-allowed;
}

.error-text {
  color: #dc3545;
  font-size: 13px;
  margin-top: 12px;
  text-align: center;
}
</style>