<template>
  <div class="admin-scene">
    <div class="dashboard-container">
      <!-- Header Dashboard -->
      <header class="dash-header">
        <div>
          <div class="role-badge">
            <span class="role-dot"></span>
            <span>Panel Moderasi & Pengelola Sekolah</span>
          </div>
          <h1 class="dash-title">Kelola & Verifikasi Aspirasi</h1>
          <p class="dash-subtitle">
            Tinjau aspirasi masuk, pilih yang layak dipublikasikan ke publik, dan berikan tindak lanjut resmi.
          </p>
        </div>
      </header>

      <!-- Grid Statistik Moderasi -->
      <section class="stats-overview">
        <div class="stat-pill-card">
          <div class="stat-meta">
            <span class="stat-num">{{ stats.total }}</span>
            <span class="stat-title">Total Masuk</span>
          </div>
          <div class="stat-icon-wrap">📋</div>
        </div>

        <div class="stat-pill-card highlight-amber" @click="publishFilter = 'unpublished'">
          <div class="stat-meta">
            <span class="stat-num text-amber">{{ stats.unverified }}</span>
            <span class="stat-title">Perlu Verifikasi</span>
          </div>
          <div class="stat-icon-wrap amber-bg">⏳</div>
        </div>

        <div class="stat-pill-card highlight-emerald" @click="publishFilter = 'published'">
          <div class="stat-meta">
            <span class="stat-num text-emerald">{{ stats.published }}</span>
            <span class="stat-title">Tampil di Publik</span>
          </div>
          <div class="stat-icon-wrap emerald-bg">🌐</div>
        </div>

        <div class="stat-pill-card">
          <div class="stat-meta">
            <span class="stat-num text-blue">{{ stats.selesai }}</span>
            <span class="stat-title">Tuntas Selesai</span>
          </div>
          <div class="stat-icon-wrap blue-bg">✅</div>
        </div>
      </section>

      <!-- Filter Bar Liquid Glass -->
      <section class="filter-glass-bar">
        <!-- Moderation Filter Tabs -->
        <div class="moderation-tabs">
          <button 
            class="tab-btn" 
            :class="{ active: publishFilter === 'all' }"
            @click="publishFilter = 'all'"
          >
            Semua ({{ stats.total }})
          </button>
          <button 
            class="tab-btn amber-tab" 
            :class="{ active: publishFilter === 'unpublished' }"
            @click="publishFilter = 'unpublished'"
          >
            Perlu Verifikasi ({{ stats.unverified }})
          </button>
          <button 
            class="tab-btn emerald-tab" 
            :class="{ active: publishFilter === 'published' }"
            @click="publishFilter = 'published'"
          >
            Sudah Publik ({{ stats.published }})
          </button>
        </div>

        <!-- Filter Dropdown Tambahan -->
        <div class="filter-extras">
          <select v-model="selectedCategory" class="admin-select">
            <option value="Semua">Semua Kategori</option>
            <option value="Sarana">Sarana & Prasarana</option>
            <option value="Akademik">Akademik</option>
            <option value="Kebersihan">Kebersihan</option>
            <option value="Layanan">Layanan Mahasiswa</option>
            <option value="Umum">Umum</option>
          </select>

          <select v-model="selectedStatus" class="admin-select">
            <option value="all">Semua Status Proses</option>
            <option value="pending">Pending</option>
            <option value="diproses">Sedang Diproses</option>
            <option value="selesai">Selesai</option>
          </select>
        </div>
      </section>

      <!-- Banner Feedback Singkat -->
      <transition name="fade">
        <div v-if="actionNotice" class="action-toast">
          {{ actionNotice }}
        </div>
      </transition>

      <!-- Loading State -->
      <div v-if="loading" class="state-card">
        <div class="spinner"></div>
        <p>Memuat data aspirasi...</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="filteredAspirations.length === 0" class="state-card">
        <p class="empty-text">Tidak ada aspirasi pada filter yang dipilih.</p>
        <button v-if="publishFilter !== 'all' || selectedCategory !== 'Semua' || selectedStatus !== 'all'" class="btn-reset" @click="resetFilters">
          Reset Filter
        </button>
      </div>

      <!-- Daftar Aspirasi Moderasi -->
      <div v-else class="aspiration-list">
        <div 
          v-for="item in filteredAspirations" 
          :key="item.id" 
          class="liquid-admin-card"
          :class="{ 'card-unverified': !item.is_published }"
        >
          <!-- Baris Kepala Kartu -->
          <div class="card-head">
            <div class="user-info">
              <div class="avatar-capsule">
                {{ item.user ? item.user.name.charAt(0).toUpperCase() : 'U' }}
              </div>
              <div>
                <div class="user-name-row">
                  <h4 class="name">{{ item.user ? item.user.name : 'Anonim' }}</h4>
                  <!-- Badge Status Publikasi -->
                  <span 
                    class="pub-badge" 
                    :class="item.is_published ? 'pub-online' : 'pub-offline'"
                  >
                    <span class="pub-dot"></span>
                    <span>{{ item.is_published ? 'Publik (Bisa Dilihat Semua)' : 'Tertutup (Khusus Guru & Admin)' }}</span>
                  </span>
                </div>
                <span class="meta-date">{{ item.category }} • {{ formatDate(item.created_at) }}</span>
              </div>
            </div>

            <!-- Kontrol Aksi Cepat: Moderasi Publikasi & Status -->
            <div class="card-actions-row">
              <!-- Tombol Verifikasi & Publikasi -->
              <button 
                class="btn-publish-toggle" 
                :class="item.is_published ? 'btn-unpublish' : 'btn-publish'"
                @click="togglePublish(item)"
                :title="item.is_published ? 'Tarik dari publik (Hanya guru & admin yang bisa lihat)' : 'Verifikasi dan publikasikan agar bisa dilihat publik'"
              >
                <svg v-if="item.is_published" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                  <line x1="1" y1="1" x2="23" y2="23"></line>
                </svg>
                <svg v-else width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                  <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span>{{ item.is_published ? 'Tarik Publikasi' : 'Publikasikan' }}</span>
              </button>

              <!-- Dropdown Status Tindak Lanjut -->
              <select 
                v-model="item.status" 
                :class="['status-select', item.status]"
                @change="quickUpdateStatus(item)"
                title="Status pengerjaan aspirasi"
              >
                <option value="pending">Pending</option>
                <option value="diproses">Sedang Diproses</option>
                <option value="selesai">Selesai</option>
              </select>

              <!-- Tombol Hapus (Jika melanggar aturan) -->
              <button @click="deleteAspiration(item.id)" class="delete-btn" title="Hapus Aspirasi (Spam / Pelanggaran)">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="3 6 5 6 21 6"></polyline>
                  <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                </svg>
              </button>
            </div>
          </div>

          <!-- Isi Konten Aspirasi -->
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
              Tanggapan Resmi Guru / Pihak Sekolah
            </label>
            <div class="feedback-input-row">
              <input 
                type="text" 
                v-model="item.feedback" 
                placeholder="Tuliskan respon resmi (misal: 'Sudah diperbaiki teknisi lab')..." 
              />
              <button @click="saveFeedback(item)" class="save-feedback-btn">
                Simpan Tanggapan
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
import { API_BASE_URL } from '../config'

const router = useRouter()
const aspirations = ref([])
const loading = ref(true)
const actionNotice = ref('')

const publishFilter = ref('all') // 'all', 'unpublished', 'published'
const selectedCategory = ref('Semua')
const selectedStatus = ref('all')

const stats = ref({
  total: 0,
  unverified: 0,
  published: 0,
  diproses: 0,
  selesai: 0
})

const showNotice = (msg) => {
  actionNotice.value = msg
  setTimeout(() => {
    if (actionNotice.value === msg) {
      actionNotice.value = ''
    }
  }, 3500)
}

const fetchAdminAspirations = async () => {
  loading.value = true
  const token = localStorage.getItem('token')

  try {
    const res = await fetch(`${API_BASE_URL}/api/admin/aspirations`, {
      headers: {
        'Accept': 'application/json',
        'Authorization': `Bearer ${token}`
      }
    })

    if (res.status === 401 || res.status === 403) {
      alert('Sesi habis atau Anda tidak memiliki akses admin/guru.')
      router.replace('/')
      return
    }

    const json = await res.json()
    if (json.status === 'success') {
      aspirations.value = json.data.data
      stats.value = json.stats
    }
  } catch (err) {
    console.error('Gagal mengambil data:', err)
  } finally {
    loading.value = false
  }
}

const filteredAspirations = computed(() => {
  return aspirations.value.filter(item => {
    // Filter status publikasi
    if (publishFilter.value === 'unpublished' && item.is_published) return false
    if (publishFilter.value === 'published' && !item.is_published) return false

    // Filter kategori
    if (selectedCategory.value !== 'Semua' && item.category !== selectedCategory.value) return false

    // Filter status proses
    if (selectedStatus.value !== 'all' && item.status !== selectedStatus.value) return false

    return true
  })
})

const resetFilters = () => {
  publishFilter.value = 'all'
  selectedCategory.value = 'Semua'
  selectedStatus.value = 'all'
}

// Toggle status verifikasi & publikasi ke publik
const togglePublish = async (item) => {
  const token = localStorage.getItem('token')
  const newStatus = !item.is_published

  try {
    const res = await fetch(`${API_BASE_URL}/api/aspirations/${item.id}/publish`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Bearer ${token}`
      },
      body: JSON.stringify({
        is_published: newStatus
      })
    })

    const json = await res.json()
    if (res.ok && json.status === 'success') {
      item.is_published = newStatus
      // Update stats secara reaktif
      if (newStatus) {
        stats.value.published++
        stats.value.unverified = Math.max(0, stats.value.unverified - 1)
        showNotice('✅ Aspirasi berhasil diverifikasi dan dipublikasikan ke publik!')
      } else {
        stats.value.published = Math.max(0, stats.value.published - 1)
        stats.value.unverified++
        showNotice('🔒 Publikasi ditarik. Aspirasi kini hanya dapat dilihat oleh guru & admin.')
      }
    } else {
      throw new Error(json.message || 'Gagal mengubah status publikasi')
    }
  } catch (err) {
    alert('Terjadi kesalahan: ' + err.message)
  }
}

const quickUpdateStatus = async (item) => {
  const token = localStorage.getItem('token')
  try {
    const res = await fetch(`${API_BASE_URL}/api/aspirations/${item.id}/status`, {
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
    if (res.ok) {
      showNotice('Status proses aspirasi berhasil diperbarui.')
    }
  } catch (err) {
    alert('Gagal memperbarui status: ' + err.message)
  }
}

const saveFeedback = async (item) => {
  await quickUpdateStatus(item)
  showNotice('Tanggapan resmi pengelola berhasil disimpan.')
}

const deleteAspiration = async (id) => {
  if (!confirm('Apakah Anda yakin ingin menghapus aspirasi ini secara permanen?')) return

  const token = localStorage.getItem('token')
  try {
    const res = await fetch(`${API_BASE_URL}/api/aspirations/${id}`, {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })
    if (res.ok) {
      aspirations.value = aspirations.value.filter(a => a.id !== id)
      stats.value.total = Math.max(0, stats.value.total - 1)
      showNotice('Aspirasi berhasil dihapus.')
    }
  } catch (err) {
    alert('Gagal menghapus aspirasi.')
  }
}

const formatDate = (dateStr) => {
  if (!dateStr) return ''
  return new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

onMounted(() => {
  const role = localStorage.getItem('role')
  if (role !== 'admin' && role !== 'guru') {
    alert('Akses khusus guru atau admin sekolah.')
    router.replace('/')
  } else {
    fetchAdminAspirations()
  }
})
</script>

<style scoped>
.admin-scene {
  position: relative;
  min-height: 100vh;
  width: 100%;
  padding-bottom: 80px;
  font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "Segoe UI", Roboto, sans-serif;
  box-sizing: border-box;
}

.dashboard-container {
  position: relative;
  z-index: 1;
  max-width: 1000px;
  margin: 0 auto;
  padding: 40px 18px 0;
}

/* Header */
.dash-header {
  margin-bottom: 24px;
}

.role-badge {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  padding: 5px 12px;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.7);
  border: 1px solid rgba(255, 255, 255, 0.9);
  font-size: 12px;
  font-weight: 600;
  color: #334155;
  margin-bottom: 10px;
}

.role-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background-color: #059669;
}

.dash-title {
  font-size: 30px;
  font-weight: 800;
  letter-spacing: -0.02em;
  color: #0f172a;
  margin: 0 0 8px 0;
}

.dash-subtitle {
  font-size: 14.5px;
  color: #64748b;
  margin: 0;
  line-height: 1.5;
}

/* Overview Stats Grid */
.stats-overview {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 14px;
  margin-bottom: 24px;
}

.stat-pill-card {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 20px;
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.65);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.85);
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
  cursor: pointer;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stat-pill-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
}

.stat-meta {
  display: flex;
  flex-direction: column;
}

.stat-num {
  font-size: 26px;
  font-weight: 800;
  color: #0f172a;
  line-height: 1.1;
}

.stat-title {
  font-size: 12px;
  font-weight: 600;
  color: #64748b;
  margin-top: 4px;
}

.text-amber { color: #d97706; }
.text-emerald { color: #059669; }
.text-blue { color: #2563eb; }

.stat-icon-wrap {
  font-size: 20px;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 12px;
  background: rgba(241, 245, 249, 0.8);
}

.amber-bg { background: rgba(254, 243, 199, 0.8); }
.emerald-bg { background: rgba(209, 250, 229, 0.8); }
.blue-bg { background: rgba(224, 231, 255, 0.8); }

/* Filter Glass Bar */
.filter-glass-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 12px;
  padding: 12px 16px;
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.55);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.8);
  margin-bottom: 24px;
}

.moderation-tabs {
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
}

.tab-btn {
  padding: 7px 14px;
  border-radius: 999px;
  font-size: 12.5px;
  font-weight: 600;
  border: 1px solid transparent;
  background: transparent;
  color: #475569;
  cursor: pointer;
  transition: all 0.2s ease;
}

.tab-btn:hover {
  background: rgba(255, 255, 255, 0.7);
}

.tab-btn.active {
  background: #0f172a;
  color: #ffffff;
  box-shadow: 0 4px 10px rgba(15, 23, 42, 0.15);
}

.tab-btn.amber-tab.active {
  background: #b45309;
  color: #ffffff;
}

.tab-btn.emerald-tab.active {
  background: #047857;
  color: #ffffff;
}

.filter-extras {
  display: flex;
  gap: 8px;
}

.admin-select {
  padding: 6px 12px;
  border-radius: 10px;
  font-size: 12.5px;
  font-weight: 600;
  border: 1px solid rgba(203, 213, 225, 0.8);
  background: rgba(255, 255, 255, 0.85);
  color: #1e293b;
  outline: none;
}

/* Action Toast */
.action-toast {
  padding: 12px 18px;
  margin-bottom: 20px;
  border-radius: 14px;
  background: rgba(15, 23, 42, 0.9);
  color: #ffffff;
  font-size: 13.5px;
  font-weight: 500;
  display: flex;
  align-items: center;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
}

/* Liquid Admin Card */
.aspiration-list {
  display: flex;
  flex-direction: column;
  gap: 18px;
}

.liquid-admin-card {
  padding: 24px;
  border-radius: 24px;
  background: linear-gradient(145deg, rgba(255, 255, 255, 0.76) 0%, rgba(255, 255, 255, 0.44) 100%);
  backdrop-filter: blur(28px) saturate(190%);
  border: 1px solid rgba(255, 255, 255, 0.85);
  box-shadow: 0 16px 32px -8px rgba(15, 23, 42, 0.05);
  transition: border-color 0.2s ease;
}

.liquid-admin-card.card-unverified {
  border-left: 5px solid #f59e0b;
}

.card-head {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 14px;
  gap: 16px;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 12px;
}

.avatar-capsule {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  background: #0f172a;
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 15px;
}

.user-name-row {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}

.name {
  margin: 0;
  font-size: 15px;
  font-weight: 700;
  color: #0f172a;
}

.pub-badge {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  font-size: 11px;
  font-weight: 700;
  padding: 3px 9px;
  border-radius: 999px;
}

.pub-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
}

.pub-online {
  background: rgba(16, 185, 129, 0.15);
  color: #065f46;
  border: 1px solid rgba(16, 185, 129, 0.3);
}
.pub-online .pub-dot {
  background: #10b981;
}

.pub-offline {
  background: rgba(245, 158, 11, 0.15);
  color: #92400e;
  border: 1px solid rgba(245, 158, 11, 0.3);
}
.pub-offline .pub-dot {
  background: #f59e0b;
}

.meta-date {
  font-size: 12px;
  color: #64748b;
  display: block;
  margin-top: 2px;
}

.card-actions-row {
  display: flex;
  align-items: center;
  gap: 8px;
}

/* Tombol Publikasi */
.btn-publish-toggle {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 14px;
  border-radius: 999px;
  font-size: 12.5px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  border: none;
}

.btn-publish {
  background: #059669;
  color: #ffffff;
  box-shadow: 0 2px 8px rgba(5, 150, 105, 0.25);
}
.btn-publish:hover {
  background: #047857;
}

.btn-unpublish {
  background: rgba(241, 245, 249, 0.9);
  color: #64748b;
  border: 1px solid rgba(203, 213, 225, 0.8);
}
.btn-unpublish:hover {
  background: #fee2e2;
  color: #b91c1c;
  border-color: #fca5a5;
}

/* Status Dropdown */
.status-select {
  padding: 6px 12px;
  border-radius: 999px;
  font-size: 12px;
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
  line-height: 1.55;
}

/* Feedback Box */
.feedback-area {
  padding: 12px 14px;
  border-radius: 14px;
  background: rgba(255, 255, 255, 0.5);
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
  background: rgba(255, 255, 255, 0.85);
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
  white-space: nowrap;
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

.empty-text {
  font-size: 14px;
  margin-bottom: 12px;
}

.btn-reset {
  padding: 8px 16px;
  border-radius: 10px;
  background: #0f172a;
  color: #ffffff;
  font-size: 13px;
  border: none;
  cursor: pointer;
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

@media (max-width: 840px) {
  .stats-overview {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 640px) {
  .stats-overview {
    grid-template-columns: 1fr;
  }
  .card-head {
    flex-direction: column;
    align-items: flex-start;
  }
  .card-actions-row {
    width: 100%;
    flex-wrap: wrap;
  }
  .filter-glass-bar {
    flex-direction: column;
    align-items: stretch;
  }
  .filter-extras {
    flex-direction: column;
  }
  .feedback-input-row {
    flex-direction: column;
  }
}
</style>
