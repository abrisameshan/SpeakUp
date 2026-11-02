<template>
  <div class="about-page">
    <div class="about-container">
      <!-- Hero Section -->
      <header class="about-hero">
        <div class="badge-pill">
          <span class="pulse-indicator"></span>
          <span>Transparansi & Demokrasi Digital</span>
        </div>
        <h1 class="hero-title">Tentang <span class="highlight-text">SpeakUp</span></h1>
        <p class="hero-subtitle">
          Wadah aspirasi digital yang menghubungkan siswa, guru, dan pengelola sekolah untuk mewujudkan lingkungan pendidikan yang lebih responsif, transparan, dan inklusif.
        </p>
      </header>

      <!-- Grid Fitur / Nilai Utama -->
      <section class="values-grid">
        <div class="glass-card value-item">
          <div class="card-icon">🎯</div>
          <h3>Tujuan Kami</h3>
          <p>
            Memberikan ruang aman bagi seluruh civitas sekolah untuk menyuarakan saran, keluhan sarana, maupun ide perbaikan sistem akademik tanpa hambatan birokrasi.
          </p>
        </div>

        <div class="glass-card value-item">
          <div class="card-icon">🔍</div>
          <h3>Prinsip Transparansi</h3>
          <p>
            Setiap aspirasi yang diajukan dapat dipantau statusnya secara terbuka mulai dari verifikasi pengelola, tahap tindak lanjut, hingga selesai ditangani.
          </p>
        </div>

        <div class="glass-card value-item">
          <div class="card-icon">🤝</div>
          <h3>Dukungan Komunitas</h3>
          <p>
            Fitur voting memungkinkan aspirasi penting yang berdampak pada kepentingan bersama mendapatkan atensi dan prioritas penanganan lebih cepat dari pihak sekolah.
          </p>
        </div>
      </section>

      <!-- Alur Penanganan Aspirasi -->
      <section class="glass-card workflow-section">
        <h2 class="section-title">Alur Penanganan Aspirasi</h2>
        <p class="section-subtitle">Bagaimana suara dan laporan Anda diproses hingga selesai:</p>

        <div class="steps-container">
          <div class="step-card">
            <div class="step-number">01</div>
            <h4>Pengajuan</h4>
            <p>Siswa mengirimkan aspirasi lengkap dengan kategori fasilitas, akademik, atau layanan.</p>
          </div>

          <div class="step-arrow">→</div>

          <div class="step-card">
            <div class="step-number">02</div>
            <h4>Verifikasi</h4>
            <p>Admin atau guru meninjau kevalidan data dan mengubah status menjadi <strong>Diproses</strong>.</p>
          </div>

          <div class="step-arrow">→</div>

          <div class="step-card">
            <div class="step-number">03</div>
            <h4>Tindak Lanjut</h4>
            <p>Unit terkait melakukan perbaikan di lapangan dan mengunggah tanggapan resmi.</p>
          </div>

          <div class="step-arrow">→</div>

          <div class="step-card">
            <div class="step-number">04</div>
            <h4>Selesai</h4>
            <p>Laporan ditandai <strong>Selesai</strong> dan hasil tindak lanjut dapat dilihat oleh pengirim.</p>
          </div>
        </div>
      </section>

      <!-- Panduan Etika Bersuara -->
      <section class="glass-card ethics-section">
        <h2 class="section-title">Pedoman Etika Pelaporan</h2>
        <ul class="ethics-list">
          <li>
            <strong>Gunakan Bahasa yang Santun:</strong> Sampaikan kritik secara konstruktif dan hindari penggunaan kata kasar atau provokatif.
          </li>
          <li>
            <strong>Sertakan Fakta dan Lokasi Jelas:</strong> Cantumkan detail yang spesifik (misal: ruang lab komputer, nomor koridor, atau waktu kejadian) agar mempermudah teknisi/guru melakukan pengecekan.
          </li>
          <li>
            <strong>Bebas SARA & Ujaran Kebencian:</strong> Laporan yang mengandung fitnah, kebencian antarsuku, agama, atau serangan personal tidak akan diproses dan akan dihapus oleh admin.
          </li>
        </ul>
      </section>

      <section class="glass-card contact-section">
        <div class="contact-header">
          <div class="contact-icon-badge">💬</div>
          <div>
            <h2 class="section-title">Pusat Bantuan & Kontak Admin</h2>
            <p class="section-subtitle">Punya pertanyaan seputar akun atau kendala teknis mendesak? Hubungi kami langsung.</p>
          </div>
        </div>

        <div class="contact-grid">
          <!-- WhatsApp / Chat Cepat -->
          <a href="https://wa.me/6281234567890" target="_blank" rel="noopener noreferrer" class="contact-item">
            <div class="item-icon-box wa">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
              </svg>
            </div>
            <div class="item-details">
              <span class="label">WhatsApp Support</span>
              <span class="value">+62 812-3456-7890</span>
            </div>
            <span class="item-arrow">↗</span>
          </a>

          <!-- Email Resmi Sekolah -->
          <a href="mailto:admin.speakup@sekolah.sch.id" class="contact-item">
            <div class="item-icon-box mail">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
              </svg>
            </div>
            <div class="item-details">
              <span class="label">Email Resmi</span>
              <span class="value">admin.speakup@sekolah.sch.id</span>
            </div>
            <span class="item-arrow">↗</span>
          </a>

          <!-- Ruang Tata Usaha / Offline -->
          <div class="contact-item static">
            <div class="item-icon-box room">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
              </svg>
            </div>
            <div class="item-details">
              <span class="label">Ruang Pengelola</span>
              <span class="value">Gedung Utama Lt. 2 (Ruang IT)</span>
            </div>
          </div>
        </div>
      </section>

    </div>
  </div>
</template>

<style scoped>
.about-page {
  width: 100%;
  padding-bottom: 80px;
  font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "Segoe UI", Roboto, sans-serif;
}

.about-container {
  max-width: 980px;
  margin: 0 auto;
  padding: 24px 16px 0;
}

/* Hero Section */
.about-hero {
  text-align: center;
  max-width: 720px;
  margin: 0 auto 44px;
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
  margin-bottom: 14px;
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.08);
}

.pulse-indicator {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: #3b82f6;
}

.hero-title {
  font-size: 38px;
  font-weight: 800;
  letter-spacing: -0.02em;
  color: #0f172a;
  margin: 0 0 12px 0;
}

.highlight-text {
  background: linear-gradient(135deg, #1e3a8a, #3b82f6);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
}

.hero-subtitle {
  font-size: 15.5px;
  color: #64748b;
  line-height: 1.6;
  margin: 0;
}

/* Glass Card Global Structure */
.glass-card {
  border-radius: 24px;
  background: linear-gradient(145deg, rgba(255, 255, 255, 0.75) 0%, rgba(255, 255, 255, 0.42) 100%);
  backdrop-filter: blur(28px) saturate(190%);
  -webkit-backdrop-filter: blur(28px) saturate(190%);
  border: 1px solid rgba(255, 255, 255, 0.85);
  box-shadow: 
    0 16px 32px -8px rgba(15, 23, 42, 0.05),
    inset 0 2px 2px rgba(255, 255, 255, 0.95);
}

/* Values Grid */
.values-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
  margin-bottom: 28px;
}

.value-item {
  padding: 28px 24px;
}

.card-icon {
  font-size: 28px;
  margin-bottom: 12px;
}

.value-item h3 {
  font-size: 17px;
  font-weight: 700;
  color: #0f172a;
  margin: 0 0 8px 0;
}

.value-item p {
  font-size: 13.5px;
  color: #64748b;
  line-height: 1.6;
  margin: 0;
}

/* Workflow Section */
.workflow-section {
  padding: 32px 30px;
  margin-bottom: 28px;
}

.section-title {
  font-size: 22px;
  font-weight: 800;
  color: #0f172a;
  margin: 0 0 6px 0;
}

.section-subtitle {
  font-size: 14px;
  color: #64748b;
  margin: 0 0 26px 0;
}

.steps-container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
}

.step-card {
  flex: 1;
  padding: 16px;
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.45);
  border: 1px solid rgba(255, 255, 255, 0.7);
}

.step-number {
  font-size: 12px;
  font-weight: 800;
  color: #3b82f6;
  margin-bottom: 6px;
}

.step-card h4 {
  font-size: 15px;
  font-weight: 700;
  color: #0f172a;
  margin: 0 0 6px 0;
}

.step-card p {
  font-size: 12.5px;
  color: #64748b;
  line-height: 1.5;
  margin: 0;
}

.step-arrow {
  font-size: 20px;
  color: #94a3b8;
  font-weight: bold;
}

/* Ethics Section */
.ethics-section {
  padding: 32px 30px;
}

.ethics-list {
  margin: 16px 0 0 0;
  padding-left: 20px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.ethics-list li {
  font-size: 14px;
  color: #475569;
  line-height: 1.6;
}

.ethics-list strong {
  color: #0f172a;
}

/* Responsiveness */
@media (max-width: 820px) {
  .values-grid {
    grid-template-columns: 1fr;
  }
  .steps-container {
    flex-direction: column;
    align-items: stretch;
  }
  .step-arrow {
    display: none;
  }
}

/* Contact Section */
.contact-section {
  padding: 32px 30px;
  margin-top: 28px;
}

.contact-header {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 24px;
}

.contact-icon-badge {
  font-size: 26px;
  width: 48px;
  height: 48px;
  border-radius: 14px;
  background: rgba(255, 255, 255, 0.6);
  border: 1px solid rgba(255, 255, 255, 0.9);
  display: flex;
  align-items: center;
  justify-content: center;
}

.contact-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
}

.contact-item {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 16px;
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.45);
  border: 1px solid rgba(255, 255, 255, 0.8);
  text-decoration: none;
  color: inherit;
  transition: all 0.2s ease;
  position: relative;
}

.contact-item:not(.static):hover {
  transform: translateY(-2px);
  background: rgba(255, 255, 255, 0.7);
  box-shadow: 0 8px 20px rgba(15, 23, 42, 0.05);
}

.item-icon-box {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.item-icon-box.wa {
  background: rgba(37, 211, 102, 0.15);
  color: #15803d;
}

.item-icon-box.mail {
  background: rgba(59, 130, 246, 0.15);
  color: #1d4ed8;
}

.item-icon-box.room {
  background: rgba(99, 102, 241, 0.15);
  color: #4338ca;
}

.item-details {
  display: flex;
  flex-direction: column;
  gap: 2px;
  overflow: hidden;
}

.item-details .label {
  font-size: 11.5px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  color: #64748b;
}

.item-details .value {
  font-size: 13px;
  font-weight: 600;
  color: #0f172a;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.item-arrow {
  margin-left: auto;
  font-size: 16px;
  color: #94a3b8;
  font-weight: 700;
}

/* Responsiveness */
@media (max-width: 820px) {
  .contact-grid {
    grid-template-columns: 1fr;
  }
}
</style>