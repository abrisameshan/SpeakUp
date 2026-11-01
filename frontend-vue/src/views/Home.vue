<template>
  <div class="liquid-home">
    <!-- Ambient Background (Fluid Blobs) -->
    <div class="ambient-bg">
      <div class="blob blob-1"></div>
      <div class="blob blob-2"></div>
      <div class="blob blob-3"></div>
    </div>

    <div class="content-wrapper">
      <!-- 1. Hero Section -->
      <section class="hero-section">
        <div class="badge-capsule">
          <span class="pulse-dot"></span>
          <span>Platform Suara Mahasiswa & Siswa</span>
        </div>
        <h1 class="hero-title">Suarakan Aspirasi,<br><span class="highlight-text">Wujudkan Perubahan.</span></h1>
        <p class="hero-desc">
          Sampaikan kritik, saran, dan ide untuk fasilitas dan lingkungan yang lebih baik secara terbuka dan transparan.
        </p>
      </section>

      <!-- 2. Grid Statistik (Liquid Glass Cards) -->
      <section class="stats-grid">
        <div class="liquid-stat-card">
          <span class="stat-num">{{ stats.total }}</span>
          <span class="stat-label">Total Aspirasi</span>
        </div>
        <div class="liquid-stat-card">
          <span class="stat-num text-amber">{{ stats.diproses }}</span>
          <span class="stat-label">Sedang Diproses</span>
        </div>
        <div class="liquid-stat-card">
          <span class="stat-num text-emerald">{{ stats.selesai }}</span>
          <span class="stat-label">Selesai Ditindaklanjuti</span>
        </div>
      </section>

      <!-- 3. Form Kirim Aspirasi (Liquid Glass Box) -->
      <section class="create-section">
        <div class="liquid-glass-box">
          <div class="box-header">
            <h3>Tulis Aspirasi Baru</h3>
            <p>Aspirasi kamu akan dilihat dan didukung oleh komunitas.</p>
          </div>

          <form @submit.prevent="submitAspiration" class="aspiration-form">
            <div class="form-row">
              <div class="input-col">
                <label>Judul Aspirasi</label>
                <input 
                  type="text" 
                  v-model="form.title" 
                  placeholder="Contoh: AC Ruang Lab Komputer 2 Tidak Dingin" 
                  required 
                />
              </div>

              <div class="input-col category-col">
                <label>Kategori</label>
                <select v-model="form.category" required>
                  <option value="Sarana">Sarana & Prasarana</option>
                  <option value="Akademik">Akademik</option>
                  <option value="Kebersihan">Kebersihan</option>
                  <option value="Layanan">Layanan Mahasiswa</option>
                  <option value="Umum">Umum</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label>Detail Aspirasi</label>
              <textarea 
                v-model="form.description" 
                rows="3" 
                placeholder="Ceritakan kendala atau usulan perbaikan secara rinci..." 
                required
              ></textarea>
            </div>

            <div class="form-action">
              <span v-if="postMessage" class="feedback-msg" :class="{ error: isPostError }">
                {{ postMessage }}
              </span>
              <button type="submit" class="liquid-submit-btn" :disabled="submitting">
                <span>{{ submitting ? 'Mengirim...' : 'Kirim Aspirasi' }}</span>
              </button>
            </div>
          </form>
        </div>
      </section>

      <!-- 4. Daftar Aspirasi & Filter -->
      <section class="feed-section">
        <div class="feed-header">
          <h2>Aspirasi Terkini</h2>
          
          <!-- Category Filter Pills -->
          <div class="category-filters">
            <button 
              v-for="cat in categories" 
              :key="cat"
              class="filter-pill"
              :class="{ active: selectedCategory === cat }"
              @click="setCategory(cat)"
            >
              {{ cat }}
            </button>
          </div>
        </div>

        <!-- State Loading -->
        <div v-if="loadingFeed" class="feed-state-box">
          <div class="spinner"></div>
          <p>Memuat aspirasi...</p>
        </div>

        <!-- State Kosong -->
        <div v-else-if="aspirations.length === 0" class="feed-state-box">
          <p>Belum ada aspirasi untuk kategori ini.</p>
        </div>

        <!-- Feed Grid -->
        <div v-else class="feed-grid">
          <article 
            v-for="item in aspirations" 
            :key="item.id" 
            class="liquid-aspiration-card"
          >
            <div class="card-top">
              <div class="user-meta">
                <div class="avatar-droplet">
                  {{ item.user ? item.user.name.charAt(0).toUpperCase() : 'U' }}
                </div>
                <div>
                  <h4 class="user-name">{{ item.user ? item.user.name : 'Anonim' }}</h4>
                  <span class="post-time">{{ formatDate(item.created_at) }}</span>
                </div>
              </div>

              <span class="status-badge" :class="item.status">
                {{ item.status }}
              </span>
            </div>

            <div class="card-content">
              <span class="cat-tag">{{ item.category }}</span>
              <h3 class="aspiration-title">{{ item.title }}</h3>
              <p class="aspiration-desc">{{ item.description }}</p>
            </div>

            <div class="card-footer">
              <button @click="upvote(item)" class="upvote-btn" :disabled="item.hasUpvoted">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M12 19V5M5 12l7-7 7 7"/>
                </svg>
                <span>{{ item.upvotes_count }} Dukungan</span>
              </button>
            </div>
          </article>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'

const categories = ['Semua', 'Sarana', 'Akademik', 'Kebersihan', 'Layanan', 'Umum']
const selectedCategory = ref('Semua')

const aspirations = ref([])
const stats = reactive({ total: 0, diproses: 0, selesai: 0 })
const loadingFeed = ref(true)
const submitting = ref(false)
const postMessage = ref('')
const isPostError = ref(false)

const form = reactive({
  title: '',
  category: 'Sarana',
  description: ''
})

const fetchAspirations = async () => {
  loadingFeed.value = true
  try {
    const url = `http://127.0.0.1:8000/api/aspirations?category=${selectedCategory.value}`
    const res = await fetch(url)
    const json = await res.json()

    if (json.status === 'success') {
      aspirations.value = json.data.data
      stats.total = json.stats.total
      stats.diproses = json.stats.diproses
      stats.selesai = json.stats.selesai
    }
  } catch (err) {
    console.error('Gagal memuat feed:', err)
  } finally {
    loadingFeed.value = false
  }
}

const setCategory = (cat) => {
  selectedCategory.value = cat
  fetchAspirations()
}

const submitAspiration = async () => {
  const token = localStorage.getItem('token')
  if (!token) {
    isPostError.value = true
    postMessage.value = 'Silakan login terlebih dahulu untuk menulis aspirasi.'
    return
  }

  submitting.value = true
  postMessage.value = ''
  isPostError.value = false

  try {
    const res = await fetch('http://127.0.0.1:8000/api/aspirations', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Bearer ${token}`
      },
      body: JSON.stringify(form)
    })

    const data = await res.json()

    if (!res.ok) {
      throw new Error(data.message || 'Gagal mengirim aspirasi')
    }

    form.title = ''
    form.description = ''
    postMessage.value = 'Aspirasi berhasil dikirim!'
    fetchAspirations()
  } catch (err) {
    isPostError.value = true
    postMessage.value = err.message
  } finally {
    submitting.value = false
  }
}

const upvote = async (item) => {
  const token = localStorage.getItem('token')
  if (!token) {
    alert('Silakan login terlebih dahulu untuk memberikan dukungan!')
    return
  }

  try {
    const res = await fetch(`http://127.0.0.1:8000/api/aspirations/${item.id}/upvote`, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Authorization': `Bearer ${token}`
      }
    })
    const data = await res.json()
    if (res.ok) {
      item.upvotes_count = data.upvotes_count
      item.hasUpvoted = true
    }
  } catch (err) {
    console.error('Gagal upvote:', err)
  }
}

const formatDate = (dateStr) => {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

onMounted(() => {
  fetchAspirations()
})
</script>

<style scoped>
/* Base Scene */
.liquid-home {
  position: relative;
  min-height: 100vh;
  width: 100%;
  padding-bottom: 80px;
  background-color: #eef2f9;
  font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "Segoe UI", Roboto, sans-serif;
  overflow-x: hidden;
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
.blob-1 { width: 500px; height: 500px; background: #93c5fd; top: 5%; left: 10%; }
.blob-2 { width: 450px; height: 450px; background: #c4b5fd; top: 40%; right: 10%; }
.blob-3 { width: 350px; height: 350px; background: #a5f3fc; bottom: 10%; left: 30%; }

.content-wrapper {
  position: relative;
  z-index: 1;
  max-width: 920px;
  margin: 0 auto;
  padding: 40px 16px 0;
}

/* Hero */
.hero-section {
  text-align: center;
  margin-bottom: 40px;
}
.badge-capsule {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 6px 14px;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.6);
  border: 1px solid rgba(255, 255, 255, 0.8);
  font-size: 12.5px;
  font-weight: 600;
  color: #334155;
  margin-bottom: 16px;
}
.pulse-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background-color: #3b82f6;
  box-shadow: 0 0 8px #3b82f6;
}
.hero-title {
  font-size: 38px;
  font-weight: 800;
  letter-spacing: -0.03em;
  color: #0f172a;
  line-height: 1.2;
  margin: 0 0 14px 0;
}
.highlight-text {
  background: linear-gradient(135deg, #1e3a8a, #3b82f6);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
}
.hero-desc {
  max-width: 540px;
  margin: 0 auto;
  font-size: 15px;
  color: #64748b;
  line-height: 1.6;
}

/* Stats */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
  margin-bottom: 36px;
}
.liquid-stat-card {
  padding: 20px;
  border-radius: 20px;
  background: linear-gradient(145deg, rgba(255, 255, 255, 0.65), rgba(255, 255, 255, 0.3));
  backdrop-filter: blur(24px) saturate(180%);
  -webkit-backdrop-filter: blur(24px) saturate(180%);
  border: 1px solid rgba(255, 255, 255, 0.75);
  box-shadow: 0 10px 24px -6px rgba(15, 23, 42, 0.05), inset 0 2px 2px rgba(255, 255, 255, 0.9);
  text-align: center;
}
.stat-num {
  display: block;
  font-size: 28px;
  font-weight: 800;
  color: #0f172a;
}
.text-amber { color: #d97706; }
.text-emerald { color: #059669; }
.stat-label {
  font-size: 13px;
  font-weight: 500;
  color: #64748b;
}

/* Form Liquid Glass Box */
.liquid-glass-box {
  padding: 30px;
  border-radius: 26px;
  background: linear-gradient(145deg, rgba(255, 255, 255, 0.7), rgba(255, 255, 255, 0.35));
  backdrop-filter: blur(28px) saturate(190%);
  -webkit-backdrop-filter: blur(28px) saturate(190%);
  border: 1px solid rgba(255, 255, 255, 0.8);
  box-shadow: 0 20px 40px -12px rgba(15, 23, 42, 0.08), inset 0 2px 3px rgba(255, 255, 255, 0.95);
  margin-bottom: 44px;
}
.box-header { margin-bottom: 20px; }
.box-header h3 { margin: 0 0 4px; font-size: 19px; font-weight: 700; color: #0f172a; }
.box-header p { margin: 0; font-size: 13.5px; color: #64748b; }

.form-row {
  display: flex;
  gap: 16px;
  margin-bottom: 14px;
}
.input-col { flex: 1; }
.category-col { max-width: 200px; }
.form-group { margin-bottom: 16px; }

label {
  display: block;
  font-size: 12.5px;
  font-weight: 600;
  color: #334155;
  margin-bottom: 6px;
}

input, select, textarea {
  width: 100%;
  padding: 10px 14px;
  font-size: 13.5px;
  color: #0f172a;
  box-sizing: border-box;
  border-radius: 12px;
  outline: none;
  background: rgba(255, 255, 255, 0.5);
  border: 1px solid rgba(255, 255, 255, 0.75);
  box-shadow: inset 0 2px 4px rgba(15, 23, 42, 0.03);
  font-family: inherit;
  transition: all 0.2s ease;
}
input:focus, select:focus, textarea:focus {
  background: rgba(255, 255, 255, 0.85);
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
}

.form-action {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.feedback-msg { font-size: 13px; color: #059669; }
.feedback-msg.error { color: #dc2626; }

.liquid-submit-btn {
  padding: 10px 24px;
  border-radius: 12px;
  background: linear-gradient(180deg, #1e293b, #0f172a);
  color: #fff;
  border: 1px solid rgba(255, 255, 255, 0.2);
  font-size: 13.5px;
  font-weight: 600;
  cursor: pointer;
  box-shadow: 0 6px 16px -4px rgba(15, 23, 42, 0.35);
  transition: transform 0.15s ease;
}
.liquid-submit-btn:hover { transform: translateY(-1px); }

/* Feed Section */
.feed-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 12px;
  margin-bottom: 20px;
}
.feed-header h2 { font-size: 20px; font-weight: 700; color: #0f172a; margin: 0; }
.category-filters { display: flex; gap: 6px; flex-wrap: wrap; }
.filter-pill {
  padding: 6px 14px;
  border-radius: 999px;
  border: 1px solid rgba(255, 255, 255, 0.8);
  background: rgba(255, 255, 255, 0.45);
  font-size: 12.5px;
  font-weight: 500;
  color: #475569;
  cursor: pointer;
}
.filter-pill.active {
  background: #0f172a;
  color: #fff;
  border-color: #0f172a;
}

/* Feed Grid & Card */
.feed-grid { display: flex; flex-direction: column; gap: 16px; }
.liquid-aspiration-card {
  padding: 22px 24px;
  border-radius: 20px;
  background: linear-gradient(145deg, rgba(255, 255, 255, 0.65), rgba(255, 255, 255, 0.3));
  backdrop-filter: blur(24px) saturate(180%);
  -webkit-backdrop-filter: blur(24px) saturate(180%);
  border: 1px solid rgba(255, 255, 255, 0.75);
  box-shadow: 0 10px 24px -6px rgba(15, 23, 42, 0.04), inset 0 1.5px 2px rgba(255, 255, 255, 0.9);
}
.card-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; }
.user-meta { display: flex; align-items: center; gap: 10px; }
.avatar-droplet {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  background: #0f172a;
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  font-weight: 700;
}
.user-name { margin: 0; font-size: 13.5px; font-weight: 600; color: #0f172a; }
.post-time { font-size: 11.5px; color: #64748b; }

.status-badge {
  padding: 4px 10px;
  border-radius: 999px;
  font-size: 11px;
  font-weight: 600;
  text-transform: capitalize;
}
.status-badge.pending { background: rgba(245, 158, 11, 0.15); color: #d97706; }
.status-badge.diproses { background: rgba(59, 130, 246, 0.15); color: #2563eb; }
.status-badge.selesai { background: rgba(16, 185, 129, 0.15); color: #059669; }

.cat-tag {
  display: inline-block;
  font-size: 11px;
  font-weight: 600;
  color: #3b82f6;
  text-transform: uppercase;
  margin-bottom: 6px;
}
.aspiration-title { margin: 0 0 6px; font-size: 16px; font-weight: 700; color: #0f172a; }
.aspiration-desc { margin: 0; font-size: 14px; color: #475569; line-height: 1.5; }

.card-footer { margin-top: 16px; }
.upvote-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 14px;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.6);
  border: 1px solid rgba(255, 255, 255, 0.85);
  font-size: 12.5px;
  font-weight: 600;
  color: #1e293b;
  cursor: pointer;
  transition: all 0.2s ease;
}
.upvote-btn:hover:not(:disabled) {
  background: #0f172a;
  color: #fff;
}
.upvote-btn:disabled {
  background: rgba(59, 130, 246, 0.15);
  color: #2563eb;
  border-color: rgba(59, 130, 246, 0.3);
  cursor: default;
}

.feed-state-box {
  padding: 40px;
  text-align: center;
  color: #64748b;
  font-size: 14px;
}
.spinner {
  width: 20px;
  height: 20px;
  border: 2px solid rgba(15, 23, 42, 0.2);
  border-top-color: #0f172a;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
  margin: 0 auto 10px;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* Responsif Mobile */
@media (max-width: 640px) {
  .hero-title { font-size: 28px; }
  .stats-grid { grid-template-columns: 1fr; }
  .form-row { flex-direction: column; }
  .category-col { max-width: 100%; }
}
</style>