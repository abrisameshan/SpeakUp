<template>
  <div class="home-container">
    <h1>Selamat Datang di Homepage!</h1>
    <p>Kamu berhasil login.</p>
    <button @click="handleLogout">Logout</button>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()

const handleLogout = () => {
  localStorage.removeItem('token')
  router.push('/login')
}
</script>

<style scoped>
.home-container {
  max-width: 500px;
  margin: 50px auto;
  text-align: center;
  font-family: sans-serif;
}
button {
  padding: 8px 16px;
  background-color: #dc3545;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
</style>