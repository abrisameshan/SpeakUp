<template>
  <header class="navbar-wrapper">
    <!-- Backdrop transparan untuk menutup menu saat klik di luar (khusus Mobile) -->
    <div 
      v-if="mobileMenuOpen" 
      class="mobile-backdrop" 
      @click="mobileMenuOpen = false"
    ></div>

    <nav class="liquid-navbar">
      <!-- Kilauan Cahaya Atas (Specular Glare) -->
      <div class="nav-glare"></div>

      <!-- 1. Brand / Logo Kiri -->
      <router-link to="/" class="brand" @click="mobileMenuOpen = false">
        <div class="brand-droplet">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/>
          </svg>
        </div>
        <span class="brand-name">SpeakUp</span>
      </router-link>

      <!-- 2. Menu Navigasi Tengah (Desktop & Tablet) -->
      <div class="nav-links">
        <router-link to="/" class="nav-item" exact-active-class="active">
          Beranda
        </router-link>
        <router-link to="/explore" class="nav-item" active-class="active">
          Eksplorasi
        </router-link>
        <router-link to="/riwayat" class="nav-item" active-class="active">
          MyAspirasi
        </router-link>
        <router-link to="/about" class="nav-item" active-class="active">
          Tentang
        </router-link>
      </div>

      <!-- 3. Aksi Kanan (Desktop Auth & Mobile Toggle) -->
      <div class="nav-actions">
        <!-- Tampilan Desktop saat Login -->
        <template v-if="isLoggedIn">
          <div class="user-pill" title="Status Akun Aktif">
            <span class="status-dot"></span>
            <span class="user-text">Aktif</span>
          </div>

          <button @click="handleLogout" class="liquid-btn-outline desktop-only" title="Keluar">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
              <polyline points="16 17 21 12 16 7"/>
              <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
            <span class="btn-label">Logout</span>
          </button>
        </template>

        <!-- Tampilan Desktop saat Belum Login -->
        <template v-else>
          <router-link to="/login" class="nav-item login-link desktop-only">
            Masuk
          </router-link>
          <router-link to="/register" class="liquid-btn-dark desktop-only">
            Daftar
          </router-link>
        </template>

        <!-- Tombol Hamburger Beranimasi (Hanya Muncul di Mobile) -->
        <button 
          class="mobile-toggle" 
          :class="{ 'is-active': mobileMenuOpen }"
          @click="mobileMenuOpen = !mobileMenuOpen"
          :aria-expanded="mobileMenuOpen"
          aria-label="Menu Navigasi"
        >
          <span class="bar bar-1"></span>
          <span class="bar bar-2"></span>
          <span class="bar bar-3"></span>
        </button>
      </div>
    </nav>

    <!-- 4. Dropdown Menu Mobile (Liquid Glass Panel) -->
    <transition name="glass-drop">
      <div v-if="mobileMenuOpen" class="mobile-glass-menu">
        <div class="nav-glare"></div>

        <!-- Status bar di dalam menu HP jika sudah login -->
        <div v-if="isLoggedIn" class="mobile-status-header">
          <div class="status-indicator">
            <span class="status-dot"></span>
            <span>Sesi Anda sedang aktif</span>
          </div>
        </div>

        <!-- Daftar Link Navigasi Mobile -->
        <div class="mobile-nav-list">
          <router-link to="/" class="mobile-item" exact-active-class="active" @click="mobileMenuOpen = false">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
              <polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
            <span>Beranda</span>
          </router-link>

          <router-link to="/explore" class="mobile-item" active-class="active" @click="mobileMenuOpen = false">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"/>
              <polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/>
            </svg>
            <span>Eksplorasi</span>
          </router-link>

          <router-link to="/riwayat" class="mobile-item" active-class="active" @click="mobileMenuOpen = false">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
            <span>MyAspirasi</span>
          </router-link>

          <router-link to="/about" class="mobile-item" active-class="active" @click="mobileMenuOpen = false">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"/>
              <line x1="12" y1="16" x2="12" y2="12"/>
              <line x1="12" y1="8" x2="12.01" y2="8"/>
            </svg>
            <span>Tentang</span>
          </router-link>
        </div>

        <div class="mobile-divider"></div>

        <!-- Tombol Auth di Mobile -->
        <template v-if="isLoggedIn">
          <button @click="handleLogout" class="mobile-logout-btn">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
              <polyline points="16 17 21 12 16 7"/>
              <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
            <span>Keluar dari Akun</span>
          </button>
        </template>

        <template v-else>
          <div class="mobile-auth-grid">
            <router-link to="/login" class="liquid-btn-outline mobile-full" @click="mobileMenuOpen = false">
              Masuk
            </router-link>
            <router-link to="/register" class="liquid-btn-dark mobile-full" @click="mobileMenuOpen = false">
              Daftar Akun
            </router-link>
          </div>
        </template>
      </div>
    </transition>
  </header>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'

const router = useRouter()
const route = useRoute()

const isLoggedIn = ref(false)
const mobileMenuOpen = ref(false)

const checkAuth = () => {
  isLoggedIn.value = !!localStorage.getItem('token')
}

// Tutup menu mobile otomatis jika layar ditarik melebar ke ukuran desktop
const handleResize = () => {
  if (window.innerWidth > 680 && mobileMenuOpen.value) {
    mobileMenuOpen.value = false
  }
}

// Tutup menu dengan tombol ESC
const handleKeydown = (e) => {
  if (e.key === 'Escape' && mobileMenuOpen.value) {
    mobileMenuOpen.value = false
  }
}

onMounted(() => {
  checkAuth()
  window.addEventListener('resize', handleResize)
  window.addEventListener('keydown', handleKeydown)
})

onUnmounted(() => {
  window.removeEventListener('resize', handleResize)
  window.removeEventListener('keydown', handleKeydown)
})

watch(() => route.fullPath, () => {
  checkAuth()
  mobileMenuOpen.value = false
})

const handleLogout = () => {
  localStorage.removeItem('token')
  isLoggedIn.value = false
  mobileMenuOpen.value = false
  router.push('/login')
}
</script>

<style scoped>
/* 1. WRAPPER MELAYANG */
.navbar-wrapper {
  position: relative;
  margin: 16px auto 0;
  z-index: 1000;
  width: 100%;
  max-width: 1080px;
  padding: 0 16px;
  box-sizing: border-box;
  font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "Segoe UI", Roboto, sans-serif;
}

/* Backdrop transparan saat menu HP terbuka */
.mobile-backdrop {
  position: fixed;
  inset: 0;
  z-index: -1;
  background: rgba(15, 23, 42, 0.08);
  backdrop-filter: blur(2px);
  -webkit-backdrop-filter: blur(2px);
}

/* 2. KAPSUL LIQUID GLASS UTAMA */
.liquid-navbar {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 62px;
  padding: 0 12px 0 16px;
  border-radius: 999px;
  overflow: hidden;

  background: linear-gradient(
    145deg,
    rgba(255, 255, 255, 0.72) 0%,
    rgba(255, 255, 255, 0.36) 100%
  );
  backdrop-filter: blur(28px) saturate(190%);
  -webkit-backdrop-filter: blur(28px) saturate(190%);
  border: 1px solid rgba(255, 255, 255, 0.8);

  box-shadow:
    0 18px 38px -10px rgba(15, 23, 42, 0.08),
    0 4px 12px -2px rgba(15, 23, 42, 0.04),
    inset 0 2px 3px 0 rgba(255, 255, 255, 0.95),
    inset 0 -2px 5px 0 rgba(255, 255, 255, 0.5);
  transition: height 0.2s ease, padding 0.2s ease;
}

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

/* 3. BRAND / LOGO */
.brand {
  position: relative;
  display: flex;
  align-items: center;
  gap: 10px;
  text-decoration: none;
  color: #0f172a;
  flex-shrink: 0;
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

/* 4. NAVIGASI TENGAH (DESKTOP/TABLET) */
.nav-links {
  position: relative;
  display: flex;
  align-items: center;
  gap: 4px;
  padding: 4px;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.35);
  border: 1px solid rgba(255, 255, 255, 0.6);
  box-shadow: inset 0 1px 3px rgba(15, 23, 42, 0.04);
}

.nav-item {
  padding: 7px 15px;
  border-radius: 999px;
  font-size: 13.5px;
  font-weight: 500;
  color: #475569;
  text-decoration: none;
  white-space: nowrap;
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

.nav-item:hover {
  color: #0f172a;
}

.nav-item.active {
  color: #0f172a;
  font-weight: 600;
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.75) 100%);
  box-shadow:
    0 2px 8px rgba(15, 23, 42, 0.07),
    inset 0 1px 1px rgba(255, 255, 255, 1);
}

/* 5. AKSI KANAN */
.nav-actions {
  position: relative;
  display: flex;
  align-items: center;
  gap: 8px;
  flex-shrink: 0;
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
  box-shadow: 0 0 8px rgba(16, 185, 129, 0.65);
  flex-shrink: 0;
}

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
  background: rgba(254, 226, 226, 0.75);
  border-color: rgba(252, 165, 165, 0.9);
  color: #b91c1c;
}

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

/* 6. TOMBOL HAMBURGER BERANIMASI */
.mobile-toggle {
  display: none;
  position: relative;
  width: 38px;
  height: 38px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.6);
  border: 1px solid rgba(255, 255, 255, 0.9);
  box-shadow:
    0 2px 6px rgba(15, 23, 42, 0.05),
    inset 0 1px 1px rgba(255, 255, 255, 1);
  cursor: pointer;
  padding: 0;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  gap: 4px;
  transition: background-color 0.2s ease;
}

.mobile-toggle .bar {
  display: block;
  width: 16px;
  height: 1.8px;
  background-color: #0f172a;
  border-radius: 2px;
  transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.2s ease;
}

.mobile-toggle.is-active .bar-1 {
  transform: translateY(5.8px) rotate(45deg);
}
.mobile-toggle.is-active .bar-2 {
  opacity: 0;
}
.mobile-toggle.is-active .bar-3 {
  transform: translateY(-5.8px) rotate(-45deg);
}

/* 7. PANEL MENU MOBILE (LIQUID GLASS DROPDOWN) */
.mobile-glass-menu {
  position: relative;
  margin-top: 10px;
  padding: 16px;
  border-radius: 26px;
  overflow: hidden;

  background: linear-gradient(
    145deg,
    rgba(255, 255, 255, 0.84) 0%,
    rgba(255, 255, 255, 0.55) 100%
  );
  backdrop-filter: blur(30px) saturate(190%);
  -webkit-backdrop-filter: blur(30px) saturate(190%);
  border: 1px solid rgba(255, 255, 255, 0.88);
  box-shadow:
    0 24px 48px -12px rgba(15, 23, 42, 0.14),
    0 6px 16px -4px rgba(15, 23, 42, 0.06),
    inset 0 2px 3px rgba(255, 255, 255, 0.95);
}

.mobile-status-header {
  padding: 8px 12px;
  margin-bottom: 10px;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.45);
  border: 1px solid rgba(255, 255, 255, 0.7);
}

.status-indicator {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 12.5px;
  font-weight: 600;
  color: #334155;
}

.mobile-nav-list {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.mobile-item {
  display: flex;
  align-items: center;
  gap: 12px;
  min-height: 44px;
  padding: 0 14px;
  border-radius: 14px;
  font-size: 14.5px;
  font-weight: 500;
  color: #334155;
  text-decoration: none;
  transition: all 0.15s ease;
}

.mobile-item:hover {
  background: rgba(255, 255, 255, 0.45);
  color: #0f172a;
}

.mobile-item.active {
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.75));
  color: #0f172a;
  font-weight: 600;
  box-shadow:
    0 2px 8px rgba(15, 23, 42, 0.06),
    inset 0 1px 1px #ffffff;
}

.mobile-divider {
  height: 1px;
  margin: 12px 4px;
  background: linear-gradient(90deg, transparent, rgba(148, 163, 184, 0.35), transparent);
}

.mobile-auth-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
}

.mobile-full {
  width: 100%;
  height: 42px;
  box-sizing: border-box;
}

.mobile-logout-btn {
  width: 100%;
  height: 44px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  border-radius: 14px;
  border: 1px solid rgba(252, 165, 165, 0.85);
  background: rgba(254, 226, 226, 0.7);
  box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.8);
  color: #b91c1c;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
}

/* Transisi Halus Buka/Tutup Menu Mobile */
.glass-drop-enter-active,
.glass-drop-leave-active {
  transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
  transform-origin: top center;
}
.glass-drop-enter-from,
.glass-drop-leave-to {
  opacity: 0;
  transform: translateY(-10px) scale(0.97);
}

/* =========================================
   8. BREAKPOINTS RESPONSIF (TABLET & MOBILE)
   ========================================= */

/* Tablet (681px - 860px): Rapatkan spasi agar tetap muat 1 baris */
@media (max-width: 860px) {
  .nav-item {
    padding: 7px 11px;
    font-size: 13px;
  }
  .user-text {
    display: none; /* Sisakan titik hijau saja di tablet agar hemat ruang */
  }
  .user-pill {
    padding: 8px;
  }
}

/* Mobile (<= 680px): Beralih ke Hamburger Menu */
@media (max-width: 680px) {
  .navbar-wrapper {
    top: 10px;
    padding: 0 12px;
  }

  .liquid-navbar {
    height: 54px;
    padding: 0 10px 0 14px;
  }

  .brand-droplet {
    width: 32px;
    height: 32px;
    border-radius: 10px;
  }

  .brand-name {
    font-size: 15px;
  }

  .nav-links,
  .desktop-only,
  .user-pill {
    display: none;
  }

  .mobile-toggle {
    display: flex;
  }
}

/* HP Layar Sangat Kecil (<= 360px) */
@media (max-width: 360px) {
  .mobile-auth-grid {
    grid-template-columns: 1fr;
  }
}
</style>