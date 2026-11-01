<template>
  <div class="admin-scene">
    <!-- Ambient Fluid Blobs -->
    <div class="ambient-bg">
      <div class="blob blob-1"></div>
      <div class="blob blob-2"></div>
      <div class="blob blob-3"></div>
    </div>

    <div class="dashboard-container">
      <!-- Header Dashboard -->
      <header class="dash-header">
        <div>
          <div class="role-badge">
            <span class="role-dot"></span>
            <span>Panel Pengelola Sekolah</span>
          </div>
          <h1 class="dash-title">Kelola Aspirasi</h1>
          <p class="dash-subtitle">Tinjau, proses, dan berikan tanggapan resmi atas aspirasi yang masuk.</p>
        </div>

        <!-- Filter Cepat -->
        <div class="status-tabs">
          <button 
            v-for="st in statusOptions" 
            :key="st.value"
            class="tab-btn"
            :class="{ active: currentTab === st.value }"
            @click="currentTab = st.value"
          >
            {{ st.label }}
          </button>
        </div>
      </header>

      <!-- Loading State -->
      <div v-if="loading" class="state-card">
        <div class="spinner"></div>
        <p>Memuat data aspirasi...</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="filteredAspirations.length === 0" class="state-card">
        <p>Tidak ada aspirasi pada status ini.</p>
      </div>

      <!-- Daftar Aspirasi (Tabel / Card Liquid Glass) -->
      <div v-else class="aspiration-list">
        <div 
          v-for="item in filteredAspirations" 
          :key="item.id" 
          class="liquid-admin-card"
        >
          <div class="card-head">
            <div class="user-info">
              <div class="avatar-capsule">
                {{ item.user ? item.user.name.charAt(0).toUpperCase() : 'U' }}
              </div>
              <div>
                <h4 class="name">{{ item.user ? item.user.name : 'Anonim' }}</h4>
                <span class="meta-date">{{ item.category }} • {{ formatDate(item.created_at) }}</span>
              </div>
            </div>

            <!-- Kontrol Status -->
            <div class="status-control">
              <select 
                v-model="item.status" 
                :class="['status-select', item.status]"
                @change="quickUpdateStatus(item)"
              >
                <option value="pending">Pending</option>
                <option value="diproses">Sedang Diproses</option>
                <option value="selesai">Selesai</option>
              </select>

              <button @click="deleteAspiration(item.id)" class="delete-btn" title="Hapus Aspirasi">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="3 6 5 6 21 6"></polyline>
                  <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                </svg>
              </button>
            </div>
          </div>

          <div class="card-body">
            <h3 class="title">{{ item.title }}</h3>
            <p class="desc">{{ item.description }}</p>
          </div>

          <!-- Bagian Tanggapan Guru / Pihak Sekolah -->
          <div class="feedback-area">
            <label class="feedback-label">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
              </svg>
              Tanggapan Resmi Pengelola
            </label>
            <div class="feedback-input-row">
              <input 
                type="text" 
                v-model="item.feedback" 
                placeholder="Tuliskan respon resmi (misal: 'Sudah diperbaiki teknisi')..." 
              />
              <button @click="saveFeedback(item)" class="save-feedback-btn">
                Simpan
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const aspirations = ref([])
const loading = ref(true)
const currentTab = ref('all')

const statusOptions = [
  { label: 'Semua', value: 'all' },
  { label: 'Pending', value: 'pending' },
  { label: 'Diproses', value: 'diproses' },
  { label: 'Selesai', value: 'selesai' }
]

const fetchAspirations = async () => {
  loading.value = true
  try {
    const res = await fetch('http://127.0.0.1:8000/api/aspirations?category=Semua')
    const json = await res.json()
    if (json.status === 'success') {
      aspirations.value = json.data.data
    }
  } catch (err) {
    console.error('Gagal mengambil data:', err)
  } finally {
    loading.value = false
  }
}

const filteredAspirations = computed(() => {
  if (currentTab.value === 'all') return aspirations.value
  return aspirations.value.filter(item => item.status === currentTab.value)
})

const quickUpdateStatus = async (item) => {
  const token = localStorage.getItem('token')
  try {
    await fetch(`http://127.0.0.1:8000/api/aspirations/${item.id}/status`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Bearer ${token}`
      },
      body: JSON.stringify({
        status: item.status,
        feedback: item.feedback
      })
    })
  } catch (err) {
    alert('Gagal memperbarui status: ' + err.message)
  }
}

const saveFeedback = async (item) => {
  await quickUpdateStatus(item)
  alert('Tanggapan berhasil disimpan!')
}

const deleteAspiration = async (id) => {
  if (!confirm('Yakin ingin menghapus aspirasi ini?')) return

  const token = localStorage.getItem('token')
  try {
    const res = await fetch(`http://127.0.0.1:8000/api/aspirations/${id}`, {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })
    if (res.ok) {
      aspirations.value = aspirations.value.filter(a => a.id !== id)
    }
  } catch (err) {
    alert('Gagal menghapus aspirasi.')
  }
}

const formatDate = (dateStr) => {
  if (!dateStr) return ''
  return new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

const router = useRouter()

onMounted(() => {
  const role = localStorage.getItem('role')
  if (role !== 'admin' && role !== 'guru') {
    router.replace('/')
  } else {
    fetchAspirations()
  }
})

onMounted(() => {
  fetchAspirations()
})
</script>

<style scoped>
.admin-scene {
  position: relative;
  min-height: 100vh;
  width: 100%;
  padding-bottom: 80px;
  background-color: #eef2f9;
  font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "Segoe UI", Roboto, sans-serif;
  box-sizing: border-box;
}

.ambient-bg {
  position: absolute;
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
}
.blob-1 { width: 450px; height: 450px; background: #93c5fd; top: 0%; left: 15%; }
.blob-2 { width: 400px; height: 400px; background: #c4b5fd; top: 35%; right: 15%; }
.blob-3 { width: 350px; height: 350px; background: #a5f3fc; bottom: 10%; left: 35%; }

.dashboard-container {
  position: relative;
  z-index: 1;
  max-width: 980px;
  margin: 0 auto;
  padding: 40px 16px 0;
}

/* Header */
.dash-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  flex-wrap: wrap;
  gap: 20px;
  margin-bottom: 32px;
}

.role-badge {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  padding: 5px 12px;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.65);
  border: 1px solid rgba(255, 255, 255, 0.85);
  font-size: 12px;
  font-weight: 600;
  color: #334155;
  margin-bottom: 10px;
}

.role-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background-color: #6366f1;
}

.dash-title {
  font-size: 30px;
  font-weight: 800;
  letter-spacing: -0.02em;
  color: #0f172a;
  margin: 0 0 6px 0;
}

.dash-subtitle {
  font-size: 14px;
  color: #64748b;
  margin: 0;
}

/* Tabs */
.status-tabs {
  display: flex;
  gap: 6px;
  padding: 4px;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.45);
  border: 1px solid rgba(255, 255, 255, 0.75);
}

.tab-btn {
  padding: 8px 16px;
  border-radius: 999px;
  font-size: 13px;
  font-weight: 600;
  border: none;
  background: transparent;
  color: #64748b;
  cursor: pointer;
  transition: all 0.2s ease;
}

.tab-btn.active {
  background: #0f172a;
  color: #ffffff;
  box-shadow: 0 4px 10px rgba(15, 23, 42, 0.15);
}

/* List & Liquid Card */
.aspiration-list {
  display: flex;
  flex-direction: column;
  gap: 18px;
}

.liquid-admin-card {
  padding: 24px;
  border-radius: 24px;
  background: linear-gradient(145deg, rgba(255, 255, 255, 0.72) 0%, rgba(255, 255, 255, 0.38) 100%);
  backdrop-filter: blur(28px) saturate(190%);
  -webkit-backdrop-filter: blur(28px) saturate(190%);
  border: 1px solid rgba(255, 255, 255, 0.85);
  box-shadow: 
    0 16px 32px -8px rgba(15, 23, 42, 0.05),
    inset 0 2px 2px rgba(255, 255, 255, 0.95);
}

.card-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 14px;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 12px;
}

.avatar-capsule {
  width: 38px;
  height: 38px;
  border-radius: 12px;
  background: #0f172a;
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 14px;
}

.name {
  margin: 0;
  font-size: 14.5px;
  font-weight: 700;
  color: #0f172a;
}

.meta-date {
  font-size: 12px;
  color: #64748b;
}

.status-control {
  display: flex;
  align-items: center;
  gap: 8px;
}

/* Status Dropdown Pill */
.status-select {
  padding: 6px 12px;
  border-radius: 999px;
  font-size: 12.5px;
  font-weight: 600;
  border: 1px solid rgba(255, 255, 255, 0.8);
  outline: none;
  cursor: pointer;
}

.status-select.pending {
  background: rgba(245, 158, 11, 0.15);
  color: #b45309;
}
.status-select.diproses {
  background: rgba(59, 130, 246, 0.15);
  color: #1d4ed8;
}
.status-select.selesai {
  background: rgba(16, 185, 129, 0.15);
  color: #047857;
}

.delete-btn {
  background: rgba(254, 226, 226, 0.65);
  border: 1px solid rgba(252, 165, 165, 0.8);
  color: #b91c1c;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
}

.delete-btn:hover {
  background: #b91c1c;
  color: #ffffff;
}

.card-body .title {
  margin: 0 0 6px 0;
  font-size: 17px;
  font-weight: 700;
  color: #0f172a;
}

.card-body .desc {
  margin: 0 0 16px 0;
  font-size: 14px;
  color: #475569;
  line-height: 1.5;
}

/* Feedback Box */
.feedback-area {
  padding: 12px 14px;
  border-radius: 14px;
  background: rgba(255, 255, 255, 0.45);
  border: 1px solid rgba(255, 255, 255, 0.7);
}

.feedback-label {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  font-weight: 600;
  color: #334155;
  margin-bottom: 8px;
}

.feedback-input-row {
  display: flex;
  gap: 8px;
}

.feedback-input-row input {
  flex: 1;
  height: 38px;
  padding: 0 12px;
  border-radius: 10px;
  border: 1px solid rgba(255, 255, 255, 0.9);
  background: rgba(255, 255, 255, 0.8);
  font-size: 13px;
  outline: none;
}

.save-feedback-btn {
  height: 38px;
  padding: 0 16px;
  border-radius: 10px;
  background: #0f172a;
  color: #ffffff;
  border: none;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.15s ease;
}

.save-feedback-btn:hover {
  background: #1e293b;
}

/* State Card */
.state-card {
  padding: 50px;
  text-align: center;
  color: #64748b;
  border-radius: 20px;
  background: rgba(255, 255, 255, 0.45);
  border: 1px solid rgba(255, 255, 255, 0.7);
}

.spinner {
  width: 22px;
  height: 22px;
  border: 2px solid rgba(15, 23, 42, 0.2);
  border-top-color: #0f172a;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
  margin: 0 auto 10px;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

@media (max-width: 640px) {
  .dash-header { flex-direction: column; align-items: flex-start; }
  .card-head { flex-direction: column; align-items: flex-start; gap: 10px; }
  .status-control { width: 100%; justify-content: space-between; }
  .feedback-input-row { flex-direction: column; }
}
</style>