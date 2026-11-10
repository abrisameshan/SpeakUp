<template>
  <div class="explore-page">
    <div class="explore-container">
      
      <!-- Header Eksplorasi -->
      <header class="explore-header">
        <div class="badge-pill">
          <span class="pulse-indicator"></span>
          <span>Suara Komunitas Terbuka</span>
        </div>
        <h1 class="page-title">Eksplorasi Aspirasi</h1>
        <p class="page-subtitle">
          Temukan, cari, dan berikan dukungan pada isu-isu prioritas di lingkungan sekolah kita.
        </p>

        <!-- Search Bar Liquid Glass -->
        <div class="search-box">
          <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
          </svg>
          <input 
            type="text" 
            v-model="searchQuery" 
            placeholder="Cari aspirasi berdasarkan kata kunci, fasilitas, atau topik..." 
          />
          <button v-if="searchQuery" @click="searchQuery = ''" class="clear-btn">✕</button>
        </div>

        <!-- Filter Controls -->
        <div class="controls-wrapper">
          <!-- Kategori Pills -->
          <div class="category-pills">
            <button 
              v-for="cat in categories" 
              :key="cat.value"
              :class="['pill-btn', { active: selectedCategory === cat.value }]"
              @click="selectedCategory = cat.value"
            >
              {{ cat.label }}
            </button>
          </div>

          <!-- Filter Status & Urutan -->
          <div class="filter-dropdowns">
            <select v-model="selectedStatus" class="glass-select">
              <option value="Semua">Semua Status</option>
              <option value="pending">Menunggu</option>
              <option value="diproses">Diproses</option>
              <option value="selesai">Selesai</option>
            </select>

            <select v-model="sortBy" class="glass-select">
              <option value="popular">Terpopuler</option>
              <option value="newest">Terbaru</option>
            </select>
          </div>
        </div>
      </header>

      <!-- Spotlight Aspirasi Teratas (Trending) -->
      <section v-if="topTrending && !searchQuery && selectedCategory === 'Semua'" class="spotlight-section">
        <div class="spotlight-card">
          <div class="spotlight-badge">
            <span>Aspirasi Terhangat Minggu Ini</span>
          </div>
          <div class="spotlight-body">
            <div class="spotlight-content">
              <span class="spotlight-category">{{ topTrending.category }}</span>
              <h2 class="spotlight-title">{{ topTrending.title }}</h2>
              <p class="spotlight-desc">{{ topTrending.description }}</p>
            </div>
            <div class="spotlight-action">
              <button class="upvote-btn-spotlight" :disabled="topTrending.hasUpvoted" @click="handleUpvote(topTrending)">
                ▲ {{ topTrending.upvotes_count || 0 }} {{ topTrending.hasUpvoted ? 'Telah Didukung' : 'Dukung Isu Ini' }}
              </button>
            </div>
          </div>
        </div>
      </section>

      <!-- Loading State -->
      <div v-if="loading" class="state-box">
        <div class="spinner"></div>
        <p>Memuat daftar aspirasi...</p>
      </div>

      <!-- Feed Aspirasi Grid (2 Kolom) -->
      <div v-else-if="filteredAspirations.length > 0" class="cards-grid">
        <div 
          v-for="item in filteredAspirations" 
          :key="item.id" 
          class="liquid-explore-card"
        >
          <div class="card-top">
            <span class="category-tag">{{ formatCategory(item.category) }}</span>
            <div :class="['status-chip', item.status]">
              <span class="dot"></span>
              <span>{{ formatStatus(item.status) }}</span>
            </div>
          </div>

          <h3 class="card-title">{{ item.title }}</h3>
          <p class="card-desc">{{ item.description }}</p>

          <!-- Kotak Respon Resmi Sekolah -->
          <div v-if="item.feedback" class="feedback-box">
            <div class="feedback-badge">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                <path d="m9 12 2 2 4-4"/>
              </svg>
              <span>Respon Resmi Sekolah</span>
            </div>
            <p class="feedback-text">"{{ item.feedback }}"</p>
          </div>

          <!-- Card Footer -->
          <div class="card-footer">
            <span class="time-meta">{{ formatDate(item.created_at) }}</span>

            <div class="action-group">
              <!-- Tombol Salin Tautan -->
              <button class="icon-btn" @click="copyShareLink(item.id)" title="Salin Tautan">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                  <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
                </svg>
              </button>

              <!-- Tombol Upvote -->
              <button 
                :class="['upvote-btn', { upvoted: item.hasUpvoted }]" 
                :disabled="item.hasUpvoted"
                @click="handleUpvote(item)"
              >
                ▲ {{ item.upvotes_count || 0 }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- State Kosong -->
      <div v-else class="state-box">
        <div class="empty-icon">🔍</div>
        <h3>Aspirasi Tidak Ditemukan</h3>
        <p>Tidak ada laporan atau saran yang sesuai dengan filter atau kata kunci pencarian Anda.</p>
        <button class="btn-reset" @click="resetFilters">Reset Pencarian</button>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { API_BASE_URL } from '../config'

const aspirations = ref([])
const loading = ref(true)
const searchQuery = ref('')
const selectedCategory = ref('Semua')
const selectedStatus = ref('Semua')
const sortBy = ref('popular')

const categories = [
  { label: 'Semua', value: 'Semua' },
  { label: 'Sarana & Prasarana', value: 'Sarana' },
  { label: 'Akademik', value: 'Akademik' },
  { label: 'Kebersihan', value: 'Kebersihan' },
  { label: 'Layanan & Fasilitas', value: 'Layanan' },
  { label: 'Umum', value: 'Umum' }
]

const fetchAspirations = async () => {
  try {
    const token = localStorage.getItem('token')
    const headers = token ? { 'Authorization': `Bearer ${token}` } : {}
    const res = await fetch(`${API_BASE_URL}/api/aspirations?category=Semua`, { headers })
    const json = await res.json()
    if (json.status === 'success') {
      aspirations.value = json.data.data.map(item => ({
        ...item,
        hasUpvoted: !!item.has_upvoted
      }))
    }
  } catch (err) {
    console.error('Gagal mengambil data aspirasi:', err)
  } finally {
    loading.value = false
  }
}

// Komputasi Aspirasi Paling Banyak Didukung untuk Banner Spotlight
const topTrending = computed(() => {
  if (aspirations.value.length === 0) return null
  const sorted = [...aspirations.value].sort((a, b) => (b.upvotes_count || 0) - (a.upvotes_count || 0))
  return sorted[0]?.upvotes_count > 0 ? sorted[0] : null
})

// Komputasi Filter & Sort
const filteredAspirations = computed(() => {
  let list = aspirations.value

  if (selectedCategory.value !== 'Semua') {
    list = list.filter(a => a.category === selectedCategory.value)
  }

  if (selectedStatus.value !== 'Semua') {
    list = list.filter(a => a.status === selectedStatus.value)
  }

  if (searchQuery.value.trim() !== '') {
    const q = searchQuery.value.toLowerCase()
    list = list.filter(a => 
      a.title.toLowerCase().includes(q) || 
      a.description.toLowerCase().includes(q)
    )
  }

  if (sortBy.value === 'popular') {
    return [...list].sort((a, b) => (b.upvotes_count || 0) - (a.upvotes_count || 0))
  } else {
    return [...list].sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
  }
})

const handleUpvote = async (item) => {
  if (item.hasUpvoted) {
    alert('Anda sudah memberikan dukungan pada aspirasi ini.')
    return
  }
  const token = localStorage.getItem('token')
  if (!token) {
    alert('Silakan login terlebih dahulu untuk memberikan dukungan!')
    return
  }

  try {
    const res = await fetch(`${API_BASE_URL}/api/aspirations/${item.id}/upvote`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })
    const data = await res.json()
    if (res.ok) {
      item.upvotes_count = data.upvotes_count
      item.hasUpvoted = true
    } else {
      if (data.already_upvoted || res.status === 422) {
        item.hasUpvoted = true
      }
      alert(data.message || 'Gagal memberikan dukungan.')
    }
  } catch (err) {
    console.error('Gagal upvote:', err)
  }
}

const copyShareLink = (id) => {
  navigator.clipboard.writeText(`${window.location.origin}/#aspiration-${id}`)
  alert('Tautan aspirasi berhasil disalin ke clipboard!')
}

const resetFilters = () => {
  searchQuery.value = ''
  selectedCategory.value = 'Semua'
  selectedStatus.value = 'Semua'
}

const formatStatus = (st) => {
  if (st === 'pending') return 'Menunggu'
  if (st === 'diproses') return 'Diproses'
  if (st === 'selesai') return 'Selesai'
  return st
}

const formatCategory = (cat) => {
  const found = categories.find(c => c.value === cat)
  return found ? found.label : cat
}

const formatDate = (dateStr) => {
  if (!dateStr) return ''
  return new Date(dateStr).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric'
  })
}

onMounted(() => {
  fetchAspirations()
})
</script>

<style scoped>
.explore-page {
  width: 100%;
  padding-bottom: 80px;
  font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "Segoe UI", Roboto, sans-serif;
}

.explore-container {
  max-width: 1020px;
  margin: 0 auto;
  padding: 20px 16px 0;
}

/* Header */
.explore-header {
  text-align: center;
  margin-bottom: 32px;
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
}

.pulse-indicator {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: #3b82f6;
}

.page-title {
  font-size: 34px;
  font-weight: 800;
  color: #0f172a;
  letter-spacing: -0.02em;
  margin: 0 0 8px 0;
}

.page-subtitle {
  font-size: 14.5px;
  color: #64748b;
  margin: 0 0 24px 0;
}

/* Search Bar */
.search-box {
  max-width: 620px;
  margin: 0 auto 20px;
  position: relative;
  display: flex;
  align-items: center;
}

.search-icon {
  position: absolute;
  left: 18px;
  color: #94a3b8;
}

.search-box input {
  width: 100%;
  height: 50px;
  padding: 0 44px 0 48px;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.75);
  border: 1px solid rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  font-size: 14px;
  outline: none;
  box-shadow: 0 10px 25px rgba(15, 23, 42, 0.04);
  transition: all 0.2s ease;
}

.search-box input:focus {
  background: rgba(255, 255, 255, 0.95);
  border-color: #3b82f6;
  box-shadow: 0 12px 30px rgba(59, 130, 246, 0.12);
}

.clear-btn {
  position: absolute;
  right: 14px;
  background: none;
  border: none;
  font-size: 14px;
  color: #94a3b8;
  cursor: pointer;
}

/* Controls */
.controls-wrapper {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 12px;
}

.category-pills {
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
}

.pill-btn {
  padding: 6px 14px;
  border-radius: 999px;
  border: 1px solid rgba(255, 255, 255, 0.8);
  background: rgba(255, 255, 255, 0.45);
  font-size: 12.5px;
  font-weight: 600;
  color: #64748b;
  cursor: pointer;
  transition: all 0.2s ease;
}

.pill-btn:hover {
  background: rgba(255, 255, 255, 0.85);
  color: #0f172a;
}

.pill-btn.active {
  background: #0f172a;
  color: #ffffff;
  border-color: #0f172a;
}

.filter-dropdowns {
  display: flex;
  gap: 8px;
}

.glass-select {
  padding: 6px 14px;
  border-radius: 999px;
  border: 1px solid rgba(255, 255, 255, 0.85);
  background: rgba(255, 255, 255, 0.65);
  font-size: 12.5px;
  font-weight: 600;
  color: #334155;
  outline: none;
  cursor: pointer;
}

/* Spotlight Section */
.spotlight-section {
  margin-bottom: 24px;
}

.spotlight-card {
  padding: 24px 28px;
  border-radius: 24px;
  background: linear-gradient(135deg, rgba(255, 247, 237, 0.85) 0%, rgba(254, 243, 199, 0.45) 100%);
  border: 1px solid rgba(251, 191, 36, 0.5);
  backdrop-filter: blur(28px);
  -webkit-backdrop-filter: blur(28px);
  box-shadow: 0 16px 32px -8px rgba(245, 158, 11, 0.12);
}

.spotlight-badge {
  display: inline-block;
  font-size: 11.5px;
  font-weight: 700;
  color: #b45309;
  text-transform: uppercase;
  margin-bottom: 12px;
}

.spotlight-body {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 20px;
}

.spotlight-content {
  flex: 1;
}

.spotlight-category {
  font-size: 12px;
  font-weight: 700;
  color: #d97706;
}

.spotlight-title {
  font-size: 19px;
  font-weight: 800;
  color: #0f172a;
  margin: 4px 0 6px 0;
}

.spotlight-desc {
  font-size: 14px;
  color: #475569;
  line-height: 1.5;
  margin: 0;
}

.upvote-btn-spotlight {
  padding: 10px 18px;
  border-radius: 999px;
  border: none;
  background: #f59e0b;
  color: #fff;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  white-space: nowrap;
  box-shadow: 0 6px 16px rgba(245, 158, 11, 0.3);
  transition: transform 0.15s ease;
}

.upvote-btn-spotlight:hover {
  transform: translateY(-2px);
  background: #d97706;
}

/* Cards Grid */
.cards-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 18px;
}

.liquid-explore-card {
  padding: 22px;
  border-radius: 22px;
  background: linear-gradient(145deg, rgba(255, 255, 255, 0.75) 0%, rgba(255, 255, 255, 0.42) 100%);
  backdrop-filter: blur(28px) saturate(190%);
  -webkit-backdrop-filter: blur(28px) saturate(190%);
  border: 1px solid rgba(255, 255, 255, 0.85);
  box-shadow: 0 16px 32px -8px rgba(15, 23, 42, 0.05);
  display: flex;
  flex-direction: column;
}

.card-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
}

.category-tag {
  font-size: 11.5px;
  font-weight: 700;
  color: #3b82f6;
  text-transform: uppercase;
}

/* Status Chips */
.status-chip {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 3px 10px;
  border-radius: 999px;
  font-size: 11.5px;
  font-weight: 700;
}
.status-chip .dot { width: 5px; height: 5px; border-radius: 50%; }
.status-chip.pending { background: rgba(245, 158, 11, 0.15); color: #b45309; }
.status-chip.pending .dot { background: #f59e0b; }
.status-chip.diproses { background: rgba(59, 130, 246, 0.15); color: #1d4ed8; }
.status-chip.diproses .dot { background: #3b82f6; }
.status-chip.selesai { background: rgba(16, 185, 129, 0.15); color: #047857; }
.status-chip.selesai .dot { background: #10b981; }

.card-title {
  font-size: 16.5px;
  font-weight: 700;
  color: #0f172a;
  margin: 0 0 6px 0;
}

.card-desc {
  font-size: 13.5px;
  color: #475569;
  line-height: 1.5;
  margin: 0 0 14px 0;
  flex: 1;
}

/* Feedback Box */
.feedback-box {
  padding: 10px 14px;
  border-radius: 12px;
  background: rgba(238, 242, 255, 0.75);
  border: 1px solid rgba(199, 210, 254, 0.85);
  margin-bottom: 14px;
}

.feedback-badge {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  color: #4338ca;
  margin-bottom: 4px;
}

.feedback-text {
  margin: 0;
  font-size: 12.5px;
  color: #1e1b4b;
  font-style: italic;
}

/* Card Footer */
.card-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-top: 1px solid rgba(226, 232, 240, 0.6);
  padding-top: 12px;
}

.time-meta {
  font-size: 12px;
  color: #94a3b8;
}

.action-group {
  display: flex;
  align-items: center;
  gap: 8px;
}

.icon-btn {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  border: 1px solid rgba(226, 232, 240, 0.8);
  background: rgba(255, 255, 255, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #64748b;
  cursor: pointer;
  transition: all 0.15s ease;
}

.icon-btn:hover {
  background: #ffffff;
  color: #0f172a;
}

.upvote-btn {
  padding: 6px 14px;
  border-radius: 999px;
  border: 1px solid rgba(203, 213, 225, 0.8);
  background: rgba(255, 255, 255, 0.8);
  font-size: 12px;
  font-weight: 700;
  color: #0f172a;
  cursor: pointer;
  transition: all 0.15s ease;
}

.upvote-btn:hover {
  background: #0f172a;
  color: #ffffff;
}

.upvote-btn.upvoted {
  background: #0f172a;
  color: #ffffff;
  border-color: #0f172a;
}

/* State Box */
.state-box {
  text-align: center;
  padding: 60px 20px;
  background: rgba(255, 255, 255, 0.45);
  border-radius: 24px;
  border: 1px solid rgba(255, 255, 255, 0.8);
  color: #64748b;
}

.empty-icon { font-size: 38px; margin-bottom: 8px; }

.btn-reset {
  margin-top: 14px;
  padding: 8px 18px;
  border-radius: 999px;
  background: #0f172a;
  color: #fff;
  border: none;
  font-size: 12.5px;
  font-weight: 600;
  cursor: pointer;
}

.spinner {
  width: 26px;
  height: 26px;
  border: 2px solid rgba(15, 23, 42, 0.15);
  border-top-color: #0f172a;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
  margin: 0 auto 10px;
}

@keyframes spin { to { transform: rotate(360deg); } }

@media (max-width: 800px) {
  .cards-grid { grid-template-columns: 1fr; }
  .controls-wrapper { flex-direction: column; align-items: stretch; }
  .spotlight-body { flex-direction: column; align-items: flex-start; }
}
</style>