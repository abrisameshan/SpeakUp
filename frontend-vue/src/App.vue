<template>
  <div class="app-container">
    
    <Navbar v-if="!route.meta.hideNavbar" />

    <!-- Konten halaman yang berganti-ganti sesuai URL -->
    <main class="main-content">
      <router-view />
    </main>
  </div>
</template>
<script setup>

import Navbar from './views/Navbar.vue'
import { useRoute } from 'vue-router'

const route = useRoute()
</script>

<style>
/* Reset dasar agar background menyatu mulus ke tepi layar */
html, body {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.app-container {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.main-content {
  flex: 1;
}
</style>