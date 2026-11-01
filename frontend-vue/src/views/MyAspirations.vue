<template>
  <div class="history-page">
    <div class="history-container">
      <!-- Header -->
      <header class="page-header">
        <div class="badge-pill">
          <span class="pulse-indicator"></span>
          <span>Aktivitas Pengguna</span>
        </div>
        <h1 class="page-title">Riwayat Aspirasi Saya</h1>
        <p class="page-subtitle">Pantau status verifikasi dan tanggapan resmi dari pihak sekolah.</p>
      </header>

      <!-- Loading State -->
      <div v-if="loading" class="state-box">
        <div class="spinner"></div>
        <p>Memuat riwayat aspirasi Anda...</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="aspirations.length === 0" class="state-box">
        <div class="empty-icon">📭</div>
        <h3>Belum Ada Aspirasi</h3>
        <p>Anda belum pernah mengirimkan laporan atau aspirasi apa pun.</p>
        <router-link to="/" class="btn-create">Tulis Aspirasi Baru</router-link>
      </div>

      <!-- List Aspirasi Saya -->
      <div v-else class="aspiration-feed">
        <div 
          v-for="item in aspirations" 
          :key="item.id" 
          class="liquid-history-card"
        >
          <!-- Baris Atas Kartu -->
          <div class="card-top">
            <span class="category-tag">{{ item.category }}</span>
            <div :class="['status-chip', item.status]">
              <span class="dot"></span>
              <span>{{ formatStatus(item.status) }}</span>
            </div>
          </div>

          <!-- Konten Aspirasi -->
          <h2 class="item-title">{{ item.title }}</h2>
          <p class="item-desc">{{ item.description }}</p>

          <!-- Kotak Tanggapan Resmi dari Sekolah (Jika Sudah Dibalas) -->
          <div v-if="item.feedback" class="feedback-box">
            <div class="feedback-badge">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                <path d="m9 12 2 2 4-4"/>
              </svg>
              <span>Respon Resmi Pengelola</span>
            </div>
            <p class="feedback-text">"{{ item.feedback }}"</p>
          </div>

          <!-- Pending notice jika belum ada feedback -->
          <div v-else class="waiting-box">
            <span>⏳ Menunggu tindak lanjut dan respon dari pihak sekolah.</span>
          </div>

          <!-- Footer Kartu -->
          <div class="card-footer">
            <span class="time-meta">Dikirim pada {{ formatDate(item.created_at) }}</span>
            <div class="upvote-count">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path>
              </svg>
              <span>{{ item.upvotes_count || 0 }} Dukungan</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const aspirations = ref([])
const loading = ref(true)

const fetchMyAspirations = async () => {
  const token = localStorage.getItem('token')
  if (!token) {
    router.push('/login')
    return
  }

  try {
    const res = await fetch('http://127.0.0.1:8000/api/user/aspirations', {
      headers: {
        'Accept': 'application/json',
        'Authorization': `Bearer ${token}`
      }
    })

    if (res.status === 401) {
      localStorage.removeItem('token')
      router.push('/login')
      return
    }

    const json = await res.json()
    if (json.status === 'success') {
      aspirations.value = json.data
    }
  } catch (err) {
    console.error('Gagal mengambil riwayat:', err)
  } finally {
    loading.value = false
  }
}

const formatStatus = (st) => {
  if (st === 'pending') return 'Menunggu'
  if (st === 'diproses') return 'Sedang Diproses'
  if (st === 'selesai') return 'Selesai'
  return st
}

const formatDate = (dateStr) => {
  if (!dateStr) return ''
  return new Date(dateStr).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
}

onMounted(() => {
  fetchMyAspirations()
})
</script>

<style scoped>
.history-page {
  width: 100%;
  padding-bottom: 80px;
  font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "Segoe UI", Roboto, sans-serif;
}

.history-container {
  max-width: 860px;
  margin: 0 auto;
  padding: 20px 16px 0;
}

/* Header */
.page-header {
  text-align: center;
  margin-bottom: 36px;
}

.badge-pill {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 6px 14px;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.65);
  border: 1px solid rgba(255, 255, 255, 0.85);
  font-size: 12px;
  font-weight: 600;
  color: #3b82f6;
  margin-bottom: 12px;
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.08);
}

.pulse-indicator {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: #3b82f6;
}

.page-title {
  font-size: 32px;
  font-weight: 800;
  letter-spacing: -0.02em;
  color: #0f172a;
  margin: 0 0 8px 0;
}

.page-subtitle {
  font-size: 14.5px;
  color: #64748b;
  margin: 0;
}

/* Cards */
.aspiration-feed {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.liquid-history-card {
  padding: 24px 28px;
  border-radius: 24px;
  background: linear-gradient(145deg, rgba(255, 255, 255, 0.75) 0%, rgba(255, 255, 255, 0.42) 100%);
  backdrop-filter: blur(28px) saturate(190%);
  -webkit-backdrop-filter: blur(28px) saturate(190%);
  border: 1px solid rgba(255, 255, 255, 0.85);
  box-shadow: 
    0 16px 32px -8px rgba(15, 23, 42, 0.05),
    inset 0 2px 2px rgba(255, 255, 255, 0.95);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.liquid-history-card:hover {
  transform: translateY(-2px);
  box-shadow: 
    0 20px 40px -10px rgba(15, 23, 42, 0.08),
    inset 0 2px 2px rgba(255, 255, 255, 0.95);
}

.card-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
}

.category-tag {
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: #3b82f6;
}

/* Status Chips */
.status-chip {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 4px 12px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 700;
}

.status-chip .dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
}

.status-chip.pending {
  background: rgba(245, 158, 11, 0.15);
  color: #b45309;
}
.status-chip.pending .dot { background: #f59e0b; }

.status-chip.diproses {
  background: rgba(59, 130, 246, 0.15);
  color: #1d4ed8;
}
.status-chip.diproses .dot { background: #3b82f6; }

.status-chip.selesai {
  background: rgba(16, 185, 129, 0.15);
  color: #047857;
}
.status-chip.selesai .dot { background: #10b981; }

.item-title {
  font-size: 18px;
  font-weight: 700;
  color: #0f172a;
  margin: 0 0 8px 0;
}

.item-desc {
  font-size: 14.5px;
  color: #475569;
  line-height: 1.6;
  margin: 0 0 16px 0;
}

/* Feedback & Waiting Box */
.feedback-box {
  padding: 14px 18px;
  border-radius: 16px;
  background: linear-gradient(135deg, rgba(238, 242, 255, 0.85) 0%, rgba(224, 231, 255, 0.55) 100%);
  border: 1px solid rgba(199, 210, 254, 0.9);
  margin-bottom: 16px;
}

.feedback-badge {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 11.5px;
  font-weight: 700;
  text-transform: uppercase;
  color: #4338ca;
  margin-bottom: 6px;
}

.feedback-text {
  margin: 0;
  font-size: 13.5px;
  color: #1e1b4b;
  font-style: italic;
  line-height: 1.5;
}

.waiting-box {
  padding: 10px 14px;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.45);
  border: 1px dashed rgba(203, 213, 225, 0.9);
  font-size: 12.5px;
  color: #64748b;
  margin-bottom: 16px;
}

.card-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-top: 1px solid rgba(226, 232, 240, 0.6);
  padding-top: 14px;
  font-size: 12.5px;
  color: #64748b;
}

.upvote-count {
  display: flex;
  align-items: center;
  gap: 5px;
  font-weight: 600;
  color: #475569;
}

/* State Box */
.state-box {
  padding: 60px 20px;
  text-align: center;
  background: rgba(255, 255, 255, 0.5);
  border-radius: 24px;
  border: 1px solid rgba(255, 255, 255, 0.8);
  color: #64748b;
}

.empty-icon {
  font-size: 42px;
  margin-bottom: 10px;
}

.btn-create {
  display: inline-block;
  margin-top: 16px;
  padding: 10px 20px;
  border-radius: 999px;
  background: #0f172a;
  color: #ffffff;
  text-decoration: none;
  font-size: 13px;
  font-weight: 600;
}

.spinner {
  width: 26px;
  height: 26px;
  border: 2px solid rgba(15, 23, 42, 0.15);
  border-top-color: #0f172a;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
  margin: 0 auto 12px;
}

@keyframes spin { to { transform: rotate(360deg); } }
</style>