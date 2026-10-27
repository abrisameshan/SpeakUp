<template>
  <header class="navbar-wrapper">
    <nav class="liquid-navbar">
      <!-- Kilauan Cahaya Atas (Specular Glare) -->
      <div class="nav-glare"></div>

      <!-- 1. Brand / Logo Kiri -->
      <router-link to="/" class="brand">
        <div class="brand-droplet">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/>
          </svg>
        </div>
        <span class="brand-name">AeroSpace</span>
      </router-link>

      <!-- 2. Menu Navigasi Tengah (Desktop) -->
      <div class="nav-links">
        <router-link to="/" class="nav-item" exact-active-class="active">
          Beranda
        </router-link>
        <router-link to="/explore" class="nav-item" active-class="active">
          Eksplorasi
        </router-link>
        <router-link to="/projects" class="nav-item" active-class="active">
          Proyek
        </router-link>
        <router-link to="/about" class="nav-item" active-class="active">
          Tentang
        </router-link>
      </div>

      <!-- 3. Aksi Kanan (Auth / Profile) -->
      <div class="nav-actions">
        <template v-if="isLoggedIn">
          <!-- Pill User Status -->
          <div class="user-pill">
            <span class="status-dot"></span>
            <span class="user-text">Aktif</span>
          </div>

          <!-- Tombol Logout -->
          <button @click="handleLogout" class="liquid-btn-outline" title="Keluar">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
              <polyline points="16 17 21 12 16 7"/>
              <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
            <span>Logout</span>
          </button>
        </template>

        <template v-else>
          <router-link to="/login" class="nav-item login-link">
            Masuk
          </router-link>
          <router-link to="/register" class="liquid-btn-dark">
            Daftar
          </router-link>
        </template>

        <!-- Tombol Hamburger (Mobile) -->
        <button 
          class="mobile-toggle" 
          @click="mobileMenuOpen = !mobileMenuOpen"
          aria-label="Toggle Menu"
        >
          <svg v-if="!mobileMenuOpen" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
            <line x1="4" y1="7" x2="20" y2="7"/>
            <line x1="4" y1="12" x2="20" y2="12"/>
            <line x1="4" y1="17" x2="20" y2="17"/>
          </svg>
          <svg v-else width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
            <line x1="18" y1="6" x2="6" y2="18"/>
            <line x1="6" y1="6" x2="18" y2="18"/>
          </svg>
        </button>
      </div>
    </nav>

    <!-- Dropdown Menu Mobile (Liquid Glass Panel) -->
    <transition name="glass-drop">
      <div v-if="mobileMenuOpen" class="mobile-glass-menu">
        <router-link to="/" class="mobile-item" @click="mobileMenuOpen = false">Beranda</router-link>
        <router-link to="/explore" class="mobile-item" @click="mobileMenuOpen = false">Eksplorasi</router-link>
        <router-link to="/projects" class="mobile-item" @click="mobileMenuOpen = false">Proyek</router-link>
        <router-link to="/about" class="mobile-item" @click="mobileMenuOpen = false">Tentang</router-link>
        
        <div class="mobile-divider"></div>

        <template v-if="isLoggedIn">
          <button @click="handleLogout" class="mobile-logout-btn">
            Logout dari Akun
          </button>
        </template>
        <template v-else>
          <div class="mobile-auth-grid">
            <router-link to="/login" class="liquid-btn-outline" @click="mobileMenuOpen = false">Masuk</router-link>
            <router-link to="/register" class="liquid-btn-dark" @click="mobileMenuOpen = false">Daftar</router-link>
          </div>
        </template>
      </div>
    </transition>
  </header>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { ref, onMounted, watch } from 'vue'

const router = useRouter()

const handleLogout = () => {
  localStorage.removeItem('token')
  router.push('/login')
  const router = useRouter()
const route = useRoute()

const isLoggedIn = ref(false)
const mobileMenuOpen = ref(false)

// Cek apakah token ada di localStorage
const checkAuth = () => {
  isLoggedIn.value = !!localStorage.getItem('token')
}

onMounted(() => {
  checkAuth()
})

// Cek ulang setiap kali pindah halaman (route berubah)
watch(() => route.fullPath, () => {
  checkAuth()
  mobileMenuOpen.value = false
})

const handleLogout = () => {
  localStorage.removeItem('token')
  isLoggedIn.value = false
  router.push('/login')
}
}
</script>

<style scoped>
.navbar-wrapper {
  position: sticky;
  top: 16px;
  z-index: 100;
  width: 100%;
  max-width: 1080px;
  margin: 0 auto;
  padding: 0 16px;
  box-sizing: border-box;
  font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "Segoe UI", Roboto, sans-serif;
}

/* 2. KAPSUL LIQUID GLASS UTAMA */
.liquid-navbar {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 62px;
  padding: 0 14px 0 18px;
  border-radius: 999px; /* Bentuk kapsul penuh */
  overflow: hidden;

  /* Permukaan Kaca Cair */
  background: linear-gradient(
    145deg,
    rgba(255, 255, 255, 0.68) 0%,
    rgba(255, 255, 255, 0.32) 100%
  );
  backdrop-filter: blur(28px) saturate(190%);
  -webkit-backdrop-filter: blur(28px) saturate(190%);
  border: 1px solid rgba(255, 255, 255, 0.78);

  /* 3D Specular Rim (Efek ketebalan kaca cembung) */
  box-shadow:
    0 18px 38px -10px rgba(15, 23, 42, 0.08),
    0 4px 12px -2px rgba(15, 23, 42, 0.04),
    inset 0 2px 3px 0 rgba(255, 255, 255, 0.95),
    inset 0 -2px 5px 0 rgba(255, 255, 255, 0.5);
}

/* Kilauan Lensa Atas */
.nav-glare {
  position: absolute;
  top: -60%;
  left: 10%;
  width: 80%;
  height: 100%;
  background: radial-gradient(
    ellipse at center,
    rgba(255, 255, 255, 0.65) 0%,
    rgba(255, 255, 255, 0) 70%
  );
  pointer-events: none;
}

/* 3. BRAND / LOGO DROPLET */
.brand {
  position: relative;
  display: flex;
  align-items: center;
  gap: 10px;
  text-decoration: none;
  color: #0f172a;
}

.brand-droplet {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  border-radius: 12px;
  color: #0f172a;
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.4));
  border: 1px solid rgba(255, 255, 255, 0.9);
  box-shadow:
    0 4px 10px rgba(15, 23, 42, 0.06),
    inset 0 1.5px 2px rgba(255, 255, 255, 1);
  transition: transform 0.2s ease;
}

.brand:hover .brand-droplet {
  transform: scale(1.05);
}

.brand-name {
  font-size: 16px;
  font-weight: 700;
  letter-spacing: -0.02em;
  color: #0f172a;
}

/* 4. NAVIGASI TENGAH (DROPLET PILLS) */
.nav-links {
  position: relative;
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 4px;
  border-radius: 999px;
  /* Cekungan kaca tipis tempat menu berada */
  background: rgba(255, 255, 255, 0.35);
  border: 1px solid rgba(255, 255, 255, 0.6);
  box-shadow: inset 0 1px 3px rgba(15, 23, 42, 0.04);
}

.nav-item {
  padding: 7px 16px;
  border-radius: 999px;
  font-size: 13.5px;
  font-weight: 500;
  color: #475569;
  text-decoration: none;
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

.nav-item:hover {
  color: #0f172a;
}

/* State Aktif: Timbul seperti tetesan kaca putih */
.nav-item.active {
  color: #0f172a;
  font-weight: 600;
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.75) 100%);
  box-shadow:
    0 2px 8px rgba(15, 23, 42, 0.07),
    inset 0 1px 1px rgba(255, 255, 255, 1);
}

/* 5. AKSI KANAN & TOMBOL */
.nav-actions {
  position: relative;
  display: flex;
  align-items: center;
  gap: 10px;
}

.user-pill {
  display: flex;
  align-items: center;
  gap: 7px;
  padding: 6px 12px;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.45);
  border: 1px solid rgba(255, 255, 255, 0.75);
  font-size: 12.5px;
  font-weight: 600;
  color: #334155;
}

.status-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background-color: #10b981;
  box-shadow: 0 0 8px rgba(16, 185, 129, 0.6);
}

/* Tombol Kapsul Kaca Bening (Logout) */
.liquid-btn-outline {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  height: 38px;
  padding: 0 15px;
  border-radius: 999px;
  font-size: 13px;
  font-weight: 600;
  color: #1e293b;
  text-decoration: none;
  cursor: pointer;
  background: rgba(255, 255, 255, 0.55);
  border: 1px solid rgba(255, 255, 255, 0.85);
  box-shadow:
    0 2px 6px rgba(15, 23, 42, 0.04),
    inset 0 1px 1px rgba(255, 255, 255, 0.9);
  transition: all 0.2s ease;
}

.liquid-btn-outline:hover {
  background: rgba(254, 226, 226, 0.7);
  border-color: rgba(252, 165, 165, 0.9);
  color: #b91c1c;
  transform: translateY(-1px);
}

/* Tombol Kapsul Obsidian Gelap (Daftar) */
.liquid-btn-dark {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  height: 38px;
  padding: 0 18px;
  border-radius: 999px;
  font-size: 13px;
  font-weight: 600;
  color: #ffffff;
  text-decoration: none;
  background: linear-gradient(180deg, rgba(30, 41, 59, 0.9) 0%, rgba(15, 23, 42, 0.96) 100%);
  border: 1px solid rgba(255, 255, 255, 0.2);
  box-shadow:
    0 6px 14px -3px rgba(15, 23, 42, 0.3),
    inset 0 1px 1px rgba(255, 255, 255, 0.35);
  transition: all 0.2s ease;
}

.liquid-btn-dark:hover {
  transform: translateY(-1px);
  box-shadow:
    0 10px 18px -3px rgba(15, 23, 42, 0.35),
    inset 0 1px 1.5px rgba(255, 255, 255, 0.45);
}

/* 6. MOBILE MENU & RESPONSIVE */
.mobile-toggle {
  display: none;
  align-items: center;
  justify-content: center;
  width: 38px;
  height: 38px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.5);
  border: 1px solid rgba(255, 255, 255, 0.8);
  color: #0f172a;
  cursor: pointer;
}

.mobile-glass-menu {
  margin-top: 10px;
  padding: 16px;
  border-radius: 24px;
  background: linear-gradient(145deg, rgba(255, 255, 255, 0.78), rgba(255, 255, 255, 0.45));
  backdrop-filter: blur(28px) saturate(190%);
  -webkit-backdrop-filter: blur(28px) saturate(190%);
  border: 1px solid rgba(255, 255, 255, 0.85);
  box-shadow:
    0 20px 40px -12px rgba(15, 23, 42, 0.12),
    inset 0 2px 3px rgba(255, 255, 255, 0.95);
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.mobile-item {
  padding: 11px 14px;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 600;
  color: #1e293b;
  text-decoration: none;
}

.mobile-item.router-link-exact-active {
  background: rgba(255, 255, 255, 0.75);
  box-shadow: inset 0 1px 1px #fff;
}

.mobile-divider {
  height: 1px;
  margin: 6px 0;
  background: rgba(148, 163, 184, 0.25);
}

.mobile-auth-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
}

.mobile-logout-btn {
  width: 100%;
  padding: 11px;
  border-radius: 12px;
  border: 1px solid rgba(252, 165, 165, 0.8);
  background: rgba(254, 226, 226, 0.65);
  color: #b91c1c;
  font-size: 13.5px;
  font-weight: 600;
  cursor: pointer;
}

/* Animasi Dropdown Mobile */
.glass-drop-enter-active,
.glass-drop-leave-active {
  transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
}
.glass-drop-enter-from,
.glass-drop-leave-to {
  opacity: 0;
  transform: translateY(-8px) scale(0.98);
}

@media (max-width: 768px) {
  .nav-links,
  .user-pill,
  .login-link,
  .nav-actions .liquid-btn-dark,
  .nav-actions .liquid-btn-outline {
    display: none;
  }
  .mobile-toggle {
    display: flex;
  }
}
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

/* Palet warna pastel organik agar pembiasan kaca terlihat jernih (tidak norak) */
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

button {
  padding: 8px 16px;
  background-color: #dc3545;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
</style>