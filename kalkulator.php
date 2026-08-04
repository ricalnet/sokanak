<?php
/**
 * Halaman Kalkulator Status Gizi Anak - Sok!Anak
 * Menggunakan standar WHO Growth (LMS) untuk usia 0–60 bulan
 */

$page_title = 'Kalkulator Status Gizi Anak';

include 'includes/header.php';
?>

<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
<link rel="stylesheet" href="assets/kalkulator/main.css">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<!-- ===== HERO SECTION ===== -->
<div class="relative overflow-hidden bg-gradient-to-br from-red-50 via-white to-red-50 rounded-2xl shadow-sm mb-10"
    data-aos="fade-up" data-aos-duration="600">
    <div class="container mx-auto px-6 py-8 md:py-12">
        <div class="flex flex-col md:flex-row items-center gap-8">
            <!-- Kiri: Teks -->
            <div class="flex-1 text-center md:text-left">
                <div
                    class="inline-flex items-center gap-2 bg-red-100 text-red-700 px-4 py-1.5 rounded-full text-sm font-semibold mb-4">
                    <i class="fas fa-heartbeat"></i>
                    <span>Layanan Publik</span>
                </div>
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-gray-900 leading-tight">
                    Kalkulator Status Gizi
                </h1>
                <p class="text-gray-600 text-base sm:text-lg mt-3 max-w-2xl mx-auto md:mx-0">
                    Pantau pertumbuhan anak Anda dengan standar <strong class="text-red-600">WHO Growth</strong> 0–60
                    bulan.
                    Metode LMS terpercaya untuk evaluasi gizi secara akurat.
                </p>
                <div class="flex flex-wrap items-center gap-4 mt-6 justify-center md:justify-start">
                    <a href="#formCard"
                        class="bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-xl shadow-lg hover:shadow-red-200 transition-all flex items-center gap-2">
                        <i class="fas fa-calculator"></i> Mulai Hitung
                    </a>
                    <span class="text-sm text-gray-400 flex items-center gap-1">
                        <i class="fas fa-check-circle text-green-500"></i> Gratis
                    </span>
                </div>
                <div
                    class="flex flex-wrap items-center gap-4 mt-5 text-sm text-gray-500 justify-center md:justify-start">
                    <span
                        class="inline-flex items-center gap-1.5 bg-white/80 px-3 py-1.5 rounded-full border border-gray-200 shadow-sm">
                        <i class="fas fa-lock text-green-600"></i> Data tidak disimpan &amp; aman
                    </span>
                    <a href="https://github.com/ricalnet/sokanak" target="_blank"
                        class="inline-flex items-center gap-2 bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg transition shadow-md hover:shadow-lg">
                        <i class="fab fa-github"></i> <span>Audit Kode</span>
                    </a>
                </div>
            </div>

            <!-- Kanan: Gambar -->
            <div class="flex-1 max-w-sm md:max-w-md lg:max-w-lg">
                <img src="assets/img/hero/image-4.jpg" alt="Anak sehat tersenyum"
                    class="w-full h-auto rounded-2xl shadow-2xl object-cover border-4 border-white" loading="lazy">
            </div>
        </div>
    </div>
</div>

<div class="glass-card mb-10" id="formCard" data-aos="fade-up" data-aos-delay="100" data-aos-duration="600">
    <div class="px-6 py-5 border-b border-gray-100/80">
        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <i class="fas fa-edit text-red-600"></i>
            Masukkan Data Anak
        </h2>
    </div>
    <div class="p-6 sm:p-8">
        <form id="calcForm" novalidate>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <label class="form-label-enhanced" for="tglLahir">
                        <i class="fas fa-calendar-alt"></i> Tanggal Lahir <span class="text-red-600 font-bold">*</span>
                    </label>
                    <input type="date" id="tglLahir" class="form-input-enhanced" required />
                    <p class="text-xs text-gray-400 mt-1.5 flex items-center gap-1">
                        <i class="fas fa-info-circle text-gray-300"></i> Usia dihitung otomatis (WHO)
                    </p>
                </div>
                <div>
                    <label class="form-label-enhanced" for="jenisKelamin">
                        <i class="fas fa-venus-mars"></i> Jenis Kelamin <span class="text-red-600 font-bold">*</span>
                    </label>
                    <select id="jenisKelamin" class="form-input-enhanced" required>
                        <option value="">-- Pilih --</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="form-label-enhanced" for="beratBadan">
                        <i class="fas fa-weight"></i> Berat Badan (kg) <span class="text-red-600 font-bold">*</span>
                    </label>
                    <input type="number" id="beratBadan" step="0.01" min="0.5" max="35" placeholder="misal 12.5"
                        class="form-input-enhanced" required />
                    <p class="text-xs text-gray-400 mt-1.5 flex items-center gap-1">
                        <i class="fas fa-info-circle text-gray-300"></i> 0.5 – 35 kg
                    </p>
                </div>
                <div>
                    <label class="form-label-enhanced" for="panjangBadan">
                        <i class="fas fa-ruler-vertical"></i> Panjang / Tinggi (cm) <span
                            class="text-red-600 font-bold">*</span>
                    </label>
                    <input type="number" id="panjangBadan" step="0.1" min="30" max="120" placeholder="misal 85.0"
                        class="form-input-enhanced" required />
                    <p class="text-xs text-gray-400 mt-1.5 flex items-center gap-1">
                        <i class="fas fa-info-circle text-gray-300"></i> 30 – 120 cm
                    </p>
                </div>
            </div>
            <div class="flex flex-wrap gap-3 mt-8 justify-end">
                <button type="reset" class="btn-secondary-enhanced">
                    <i class="fas fa-undo"></i> Reset
                </button>
                <button type="submit" class="btn-primary-enhanced" id="submitBtn">
                    <i class="fas fa-calculator"></i> Analisis Status Gizi
                </button>
            </div>
        </form>
    </div>
</div>

<div id="resultSection" class="hidden">

    <div class="glass-card mb-8" data-aos="fade-up" data-aos-delay="150" data-aos-duration="600">
        <div class="px-6 py-5 border-b border-gray-100/80">
            <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-clipboard-list text-red-600"></i>
                Ringkasan Pasien
            </h2>
        </div>
        <div class="p-6 sm:p-8">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                <div class="stat-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="icon-circle"><i class="fas fa-clock"></i></div>
                    <div class="value" id="sUsia">—</div>
                    <div class="label">Usia</div>
                </div>
                <div class="stat-card" data-aos="fade-up" data-aos-delay="250">
                    <div class="icon-circle"><i class="fas fa-weight"></i></div>
                    <div class="value" id="sBB">—</div>
                    <div class="label">Berat Badan</div>
                </div>
                <div class="stat-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="icon-circle"><i class="fas fa-ruler-vertical"></i></div>
                    <div class="value" id="sTB">—</div>
                    <div class="label">Panjang Badan</div>
                </div>
                <div class="stat-card" data-aos="fade-up" data-aos-delay="350">
                    <div class="icon-circle"><i class="fas fa-circle"></i></div>
                    <div class="value" id="sIMT">—</div>
                    <div class="label">IMT (kg/m²)</div>
                </div>
                <div class="stat-card" data-aos="fade-up" data-aos-delay="400">
                    <div class="icon-circle"><i class="fas fa-venus-mars"></i></div>
                    <div class="value" id="sKelamin">—</div>
                    <div class="label">Jenis Kelamin</div>
                </div>
            </div>
        </div>
    </div>

    <div class="glass-card mb-8" data-aos="fade-up" data-aos-delay="200" data-aos-duration="600">
        <div class="px-6 py-5 border-b border-gray-100/80">
            <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-chart-pie text-red-600"></i>
                Hasil Analisis Z-Score
            </h2>
        </div>
        <div class="p-6 sm:p-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5" id="indicatorGrid">
                <!-- diisi JS -->
            </div>

            <div id="diagnosisContainer" class="mt-8"></div>

            <div class="mt-8">
                <div class="flex justify-between items-center text-sm text-gray-500 mb-2">
                    <span class="font-medium">Z-Score rata-rata</span>
                    <span class="text-xs bg-gray-100 px-3 py-1 rounded-full">-4 &nbsp;·&nbsp; -3 &nbsp;·&nbsp; -2
                        &nbsp;·&nbsp; 0 &nbsp;·&nbsp; +2 &nbsp;·&nbsp; +3 &nbsp;·&nbsp; +4</span>
                </div>
                <div class="zscore-track">
                    <div class="zscore-fill" id="zscoreBarFill" style="width:50%;"></div>
                </div>
                <div class="flex justify-between text-xs text-gray-400 mt-2">
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-500"></span>
                        Kurang</span>
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-green-500"></span>
                        Normal</span>
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-yellow-500"></span>
                        Lebih</span>
                </div>
            </div>
        </div>
    </div>

    <div class="glass-card" data-aos="fade-up" data-aos-delay="300" data-aos-duration="600">
        <div class="p-6 sm:p-8 flex flex-wrap gap-3 justify-end items-center">
            <button class="btn-secondary-enhanced" onclick="resetForm()">
                <i class="fas fa-arrow-left"></i> Kembali
            </button>
            <button class="btn-primary-enhanced" onclick="copyToClipboard()"
                style="background:linear-gradient(135deg,#0f172a,#1e293b);box-shadow:0 4px 16px rgba(15,23,42,0.25);">
                <i class="fas fa-copy"></i> Salin untuk AI
            </button>
            <button class="btn-primary-enhanced" onclick="window.print()"
                style="background:linear-gradient(135deg,#2563eb,#1d4ed8);box-shadow:0 4px 16px rgba(37,99,235,0.25);">
                <i class="fas fa-print"></i> Cetak Laporan
            </button>
        </div>
    </div>
</div>

<div id="toast" class="toast-enhanced">
    <i class="fas fa-check-circle"></i>
    <span id="toastMessage">Data berhasil disalin!</span>
</div>

<script src="assets/kalkulator/main.js"></script>

<?php
include 'includes/footer.php';
?>