<template>
  <div class="app-wrapper">
    <!-- Fluid Mesh Blobs (Sekarang membungkus seluruh aplikasi dari atas) -->
    <div class="ambient-bg">
      <div class="blob blob-1"></div>
      <div class="blob blob-2"></div>
      <div class="blob blob-3"></div>
    </div>

    <!-- Navbar sekarang berada tepat di atas background cair -->
    <Navbar v-if="!route.meta.hideNavbar" />

    <!-- Konten halaman -->
    <main class="main-content">
      <router-view />
    </main>
  </div>
</template>
<script setup>
import { useRoute } from 'vue-router'
import Navbar from './views/navbar.vue'

const route = useRoute()
</script>

<style>
/* Reset dasar browser */
html, body {
  margin: 0;
  padding: 0;
  background-color: #eef2f9;
  font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "Segoe UI", Roboto, sans-serif;
}

.app-wrapper {
  position: relative;
  min-height: 100vh;
  width: 100%;
}

/* Fluid Blobs Global */
.ambient-bg {
  position: fixed; /* Gunakan fixed agar background tidak terpotong saat scroll */
  inset: 0;
  z-index: 0;
  overflow: hidden;
  filter: blur(80px);
  pointer-events: none;
}

.blob {
  position: absolute;
  border-radius: 50%;
  opacity: 0.65;
  animation: floatOrb 18s ease-in-out infinite alternate;
}

.blob-1 {
  width: 500px;
  height: 500px;
  background: radial-gradient(circle, #93c5fd 0%, #bfdbfe 100%);
  top: -10%;
  left: 15%;
}

.blob-2 {
  width: 450px;
  height: 450px;
  background: radial-gradient(circle, #c4b5fd 0%, #e0e7ff 100%);
  top: 35%;
  right: 10%;
  animation-delay: -6s;
}

.blob-3 {
  width: 350px;
  height: 350px;
  background: radial-gradient(circle, #a5f3fc 0%, #bae6fd 100%);
  bottom: 5%;
  left: 30%;
  animation-delay: -12s;
}

@keyframes floatOrb {
  0% { transform: translate(0, 0) scale(1); }
  50% { transform: translate(30px, -20px) scale(1.05); }
  100% { transform: translate(-20px, 25px) scale(0.95); }
}

.main-content {
  position: relative;
  z-index: 1;
}
</style>