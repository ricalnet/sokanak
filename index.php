<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$page_title = "Sok!Anak - Sistem Observasi Kesehatan Anak";
include 'includes/header.php';
?>

<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    html,
    body {
        overflow-x: hidden;
        max-width: 100vw;
    }

    .hero-gradient {
        background: linear-gradient(135deg, rgba(226, 0, 26, 0.88) 0%, rgba(0, 68, 136, 0.82) 100%);
    }

    .floating {
        animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
        0% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-20px);
        }

        100% {
            transform: translateY(0px);
        }
    }

    .section-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.375rem 1rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        background: rgba(226, 0, 26, 0.08);
        color: #e2001a;
        border: 1px solid rgba(226, 0, 26, 0.12);
    }

    .section-badge i {
        font-size: 0.625rem;
    }

    .section-title {
        font-size: 2.25rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.2;
    }

    .section-title span {
        color: #e2001a;
    }

    .section-subtitle {
        font-size: 1.125rem;
        color: #64748b;
        max-width: 600px;
        margin: 0 auto;
        line-height: 1.7;
    }

    .service-card {
        background: white;
        border-radius: 1.25rem;
        padding: 2rem 1.75rem;
        border: 1px solid #f1f5f9;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    .service-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #e2001a, #0066cc);
        opacity: 0;
        transition: opacity 0.4s ease;
    }

    .service-card:hover::before {
        opacity: 1;
    }

    .service-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.06);
        border-color: #e2e8f0;
    }

    .service-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1.25rem;
        flex-shrink: 0;
        transition: all 0.3s ease;
    }

    .service-card:hover .service-icon {
        transform: scale(1.05);
    }

    .service-icon-red {
        background: rgba(226, 0, 26, 0.1);
        color: #e2001a;
    }

    .service-icon-blue {
        background: rgba(0, 102, 204, 0.1);
        color: #0066cc;
    }

    .service-icon-green {
        background: rgba(5, 150, 105, 0.1);
        color: #059669;
    }

    .service-icon-purple {
        background: rgba(124, 58, 237, 0.1);
        color: #7c3aed;
    }

    .service-card h3 {
        font-size: 1.125rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.5rem;
    }

    .service-card p {
        font-size: 0.875rem;
        color: #64748b;
        line-height: 1.7;
        margin-bottom: 1rem;
    }

    .service-tag {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        font-size: 0.75rem;
        font-weight: 600;
        color: #e2001a;
        background: rgba(226, 0, 26, 0.06);
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
    }

    .validator-card {
        background: white;
        border-radius: 1rem;
        padding: 1.75rem 1.5rem;
        text-align: center;
        border: 1px solid #f1f5f9;
        transition: all 0.4s ease;
        height: 100%;
    }

    .validator-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.05);
        border-color: rgba(226, 0, 26, 0.12);
    }

    .validator-avatar {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        margin: 0 auto 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        transition: all 0.3s ease;
    }

    .validator-card:hover .validator-avatar {
        transform: scale(1.05);
    }

    .validator-card h4 {
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
    }

    .validator-card .validator-role {
        font-size: 0.8125rem;
        color: #64748b;
        margin-top: 0.125rem;
    }

    .validator-badge {
        display: inline-block;
        margin-top: 0.75rem;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.6875rem;
        font-weight: 600;
        background: rgba(5, 150, 105, 0.08);
        color: #059669;
    }

    .press-card {
        background: white;
        border-radius: 1rem;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid #f1f5f9;
        height: 100%;
    }

    .press-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
        border-color: rgba(226, 0, 26, 0.15);
    }

    .press-card .press-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        background: #f1f5f9;
    }

    .press-card .press-body {
        padding: 1.5rem;
    }

    .press-card .press-tag {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.6875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.75rem;
    }

    .press-tag-press {
        background: rgba(226, 0, 26, 0.1);
        color: #e2001a;
    }

    .press-tag-journal {
        background: rgba(0, 102, 204, 0.1);
        color: #0066cc;
    }

    .press-tag-haki {
        background: rgba(5, 150, 105, 0.1);
        color: #059669;
    }

    .press-tag-award {
        background: rgba(245, 158, 11, 0.1);
        color: #d97706;
    }

    .press-card .press-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.4;
        margin-bottom: 0.5rem;
    }

    .press-card .press-title a {
        color: #1e293b;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .press-card .press-title a:hover {
        color: #e2001a;
    }

    .press-card .press-excerpt {
        font-size: 0.875rem;
        color: #64748b;
        line-height: 1.7;
        margin-bottom: 0.75rem;
    }

    .press-card .press-meta {
        display: flex;
        align-items: center;
        gap: 1rem;
        font-size: 0.75rem;
        color: #94a3b8;
        flex-wrap: wrap;
    }

    .press-card .press-meta i {
        margin-right: 0.25rem;
    }

    .hero-container {
        position: relative;
        width: 100%;
        max-width: 100vw;
        padding-top: 56.25%;
        overflow: hidden;
        min-height: 100vh;
    }

    .hero-image {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
    }

    .hero-content {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
    }

    .btn-glow {
        position: relative;
        overflow: hidden;
    }

    .btn-glow::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.2) 0%, transparent 70%);
        opacity: 0;
        transition: opacity 0.5s ease;
        transform: scale(0.5);
        pointer-events: none;
    }

    .btn-glow:hover::before {
        opacity: 1;
        transform: scale(1);
    }

    .btn-primary-hero {
        display: inline-flex;
        align-items: center;
        gap: 0.625rem;
        padding: 0.875rem 2rem;
        background: white;
        color: #e2001a;
        border-radius: 0.75rem;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        text-decoration: none;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    }

    .btn-primary-hero:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 28px rgba(0, 0, 0, 0.2);
        color: #e2001a;
        text-decoration: none;
    }

    .btn-outline-hero {
        display: inline-flex;
        align-items: center;
        gap: 0.625rem;
        padding: 0.875rem 2rem;
        background: rgba(255, 255, 255, 0.12);
        color: white;
        border-radius: 0.75rem;
        font-weight: 600;
        font-size: 1rem;
        border: 1px solid rgba(255, 255, 255, 0.25);
        transition: all 0.3s ease;
        text-decoration: none;
        backdrop-filter: blur(4px);
    }

    .btn-outline-hero:hover {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        transform: translateY(-2px);
        text-decoration: none;
    }

    .partners-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 2rem;
    }

    @media (min-width: 640px) {
        .partners-grid {
            grid-template-columns: repeat(4, 1fr);
            gap: 2.5rem;
        }
    }

    .partner-logo {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .partner-logo img {
        height: 56px;
        width: auto;
        max-width: 100%;
        object-fit: contain;
        transition: all 0.3s ease;
        filter: grayscale(1) brightness(0.95);
        opacity: 0.6;
    }

    .partner-logo:hover img {
        filter: grayscale(0) brightness(1);
        opacity: 1;
    }

    @media (min-width: 640px) {
        .partner-logo img {
            height: 64px;
        }
    }

    .partner-placeholder {
        height: 56px;
        padding: 0 1.5rem;
        background: #f8fafc;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        font-weight: 500;
        font-size: 0.875rem;
        border: 1px dashed #e2e8f0;
    }

    @media (min-width: 640px) {
        .partner-placeholder {
            height: 64px;
        }
    }

    @media (max-width: 1024px) {
        .hero-container {
            padding-top: 75%;
            min-height: 80vh;
        }

        .section-title {
            font-size: 1.875rem;
        }
    }

    @media (max-width: 768px) {
        .hero-container {
            padding-top: 100%;
            min-height: 100vh;
        }

        .hero-title {
            font-size: 2rem !important;
        }

        .section-title {
            font-size: 1.625rem;
        }

        .section-subtitle {
            font-size: 1rem;
        }

        .service-card {
            padding: 1.5rem;
        }

        .press-card .press-image {
            height: 160px;
        }

        section {
            padding: 3rem 0 !important;
        }

        .validator-card {
            padding: 1.25rem;
        }

        .validator-avatar {
            width: 60px;
            height: 60px;
            font-size: 1.5rem;
        }
    }

    @media (max-width: 640px) {
        .hero-container {
            padding-top: 120%;
            min-height: 100vh;
        }

        .hero-title {
            font-size: 1.75rem !important;
        }

        .section-title {
            font-size: 1.5rem;
        }

        .btn-primary-hero,
        .btn-outline-hero {
            padding: 0.75rem 1.5rem;
            font-size: 0.875rem;
            width: 100%;
            justify-content: center;
        }

        .hero-content .flex-wrap {
            flex-direction: column;
            width: 100%;
        }

        .hero-content .flex-wrap a {
            width: 100%;
            justify-content: center;
        }

        .press-card .press-image {
            height: 140px;
        }

        section {
            padding: 2rem 0 !important;
        }

        .service-icon {
            width: 48px;
            height: 48px;
            font-size: 1.25rem;
        }

        .press-card .press-body {
            padding: 1rem;
        }

        .press-card .press-title {
            font-size: 1rem;
        }
    }

    [data-aos] {
        pointer-events: auto !important;
    }

    [data-aos].aos-animate {
        pointer-events: auto !important;
    }

    .gradient-border,
    .expert-card,
    .feature-icon,
    a,
    button,
    [role="button"],
    input,
    select,
    textarea {
        pointer-events: auto !important;
    }

    p,
    h1,
    h2,
    h3,
    h4,
    h5,
    h6,
    span,
    div,
    li,
    .text-content {
        user-select: text;
        -webkit-user-select: text;
        -moz-user-select: text;
        -ms-user-select: text;
    }

    .aos-animate {
        pointer-events: auto !important;
    }

    * {
        -webkit-tap-highlight-color: transparent;
    }

    a:not([data-aos]) {
        pointer-events: auto !important;
    }

    a[data-aos] {
        pointer-events: auto !important;
    }

    .max-w-7xl {
        max-width: 100% !important;
    }

    img,
    video,
    iframe {
        max-width: 100%;
        height: auto;
    }

    .container,
    .max-w-7xl,
    .max-w-3xl,
    .max-w-2xl,
    .max-w-md {
        max-width: 100% !important;
    }

    .grid {
        overflow: hidden;
    }
</style>

<section class="hero-container">
    <img src="assets/img/hero/image-1.jpg" alt="Sok!Anak - Sistem Observasi Kesehatan Anak" class="hero-image">
    <div class="absolute inset-0 hero-gradient"></div>

    <div class="absolute inset-0 opacity-10">
        <div
            class="absolute top-0 right-0 w-64 h-64 sm:w-96 sm:h-96 bg-white rounded-full -translate-y-1/2 translate-x-1/2">
        </div>
        <div
            class="absolute bottom-0 left-0 w-48 h-48 sm:w-64 sm:h-64 bg-white rounded-full translate-y-1/2 -translate-x-1/2">
        </div>
    </div>

    <div class="hero-content">
        <div class="relative z-10 w-full">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-8 md:gap-12 items-center">
                    <div class="text-white space-y-6">
                        <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold leading-tight hero-title"
                            data-aos="fade-up" data-aos-delay="100">
                            Sistem Observasi Kesehatan Anak
                            <span class="block text-red-200">untuk Generasi Sehat</span>
                        </h1>

                        <p class="text-base sm:text-lg lg:text-xl text-white/90 max-w-2xl leading-relaxed"
                            data-aos="fade-up" data-aos-delay="200">
                            Platform digital terintegrasi untuk pemantauan tumbuh kembang anak
                            di Posyandu dengan teknologi AIoT yang akurat dan andal.
                        </p>

                        <div class="flex flex-wrap gap-3 sm:gap-4" data-aos="fade-up" data-aos-delay="300">
                            <a href="signup.php" class="btn-primary-hero">
                                <i class="fas fa-user-plus"></i> Daftar Sekarang
                            </a>
                            <a href="#services" class="btn-outline-hero">
                                <i class="fas fa-arrow-right"></i> Pelajari
                            </a>
                        </div>
                    </div>

                    <div class="hidden lg:flex items-center justify-center" data-aos="fade-left"
                        data-aos-duration="1000" data-aos-delay="300">
                        <div class="relative floating">
                            <div class="w-64 h-64 md:w-72 md:h-72 lg:w-80 lg:h-80 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 
                                        flex items-center justify-center p-6 md:p-8">
                                <div class="text-center text-white">
                                    <i class="fas fa-child text-5xl md:text-6xl lg:text-7xl mb-3 text-red-200"></i>
                                    <h3 class="text-xl md:text-2xl font-bold">AIoT Sok!Anak</h3>
                                    <p class="text-sm md:text-base text-white/70">Sistem Observasi Kesehatan Anak</p>
                                    <div class="mt-3 flex justify-center space-x-3">
                                        <span class="inline-block px-3 py-1 bg-white/10 rounded-full text-xs">
                                            <i class="fas fa-weight-scale mr-1"></i> Akurat
                                        </span>
                                        <span class="inline-block px-3 py-1 bg-white/10 rounded-full text-xs">
                                            <i class="fas fa-robot mr-1"></i> AI-Powered
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-12 sm:py-16 md:py-20 bg-gray-50" id="services">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10 md:mb-14">
            <span class="section-badge" data-aos="fade-up">
                <i class="fas fa-star"></i> Layanan Unggulan
            </span>
            <h2 class="section-title mt-3" data-aos="fade-up" data-aos-delay="100">
                Solusi Lengkap <span>Monitoring Gizi</span>
            </h2>
            <p class="section-subtitle mt-3" data-aos="fade-up" data-aos-delay="200">
                Teknologi terintegrasi untuk mendukung program kesehatan anak di Posyandu
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="service-card" data-aos="fade-up" data-aos-delay="100">
                <div class="service-icon service-icon-red">
                    <i class="fas fa-weight-scale"></i>
                </div>
                <h3>Pengukuran Otomatis</h3>
                <p>Sistem pengukuran berat dan tinggi badan dengan akurasi tinggi terintegrasi IoT devices.</p>
                <span class="service-tag">
                    <i class="fas fa-circle text-[8px]"></i> Akurasi 99%
                </span>
            </div>

            <div class="service-card" data-aos="fade-up" data-aos-delay="200">
                <div class="service-icon service-icon-blue">
                    <i class="fas fa-robot"></i>
                </div>
                <h3>Analisis AI Cerdas</h3>
                <p>Rekomendasi dan analisis berbasis AI untuk deteksi dini masalah pertumbuhan anak.</p>
                <span class="service-tag">
                    <i class="fas fa-circle text-[8px]"></i> Deteksi Dini
                </span>
            </div>

            <div class="service-card" data-aos="fade-up" data-aos-delay="300">
                <div class="service-icon service-icon-green">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3>Monitoring Berkala</h3>
                <p>Pantau perkembangan anak dengan grafik pertumbuhan dan laporan bulanan informatif.</p>
                <span class="service-tag">
                    <i class="fas fa-circle text-[8px]"></i> Visual Data
                </span>
            </div>

            <div class="service-card" data-aos="fade-up" data-aos-delay="400">
                <div class="service-icon service-icon-purple">
                    <i class="fas fa-file-pdf"></i>
                </div>
                <h3>Laporan Digital</h3>
                <p>Generate laporan status gizi anak dalam format PDF yang dapat diunduh dan dibagikan.</p>
                <span class="service-tag">
                    <i class="fas fa-circle text-[8px]"></i> Exportable
                </span>
            </div>

            <div class="service-card" data-aos="fade-up" data-aos-delay="500">
                <div class="service-icon service-icon-blue">
                    <i class="fas fa-cloud-upload-alt"></i>
                </div>
                <h3>Sinkronisasi Data</h3>
                <p>Data terintegrasi antar posyandu untuk pemantauan wilayah yang lebih baik.</p>
                <span class="service-tag">
                    <i class="fas fa-circle text-[8px]"></i> Terintegrasi
                </span>
            </div>

            <div class="service-card" data-aos="fade-up" data-aos-delay="600">
                <div class="service-icon service-icon-green">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Keamanan Data</h3>
                <p>Perlindungan data anak dengan sistem keamanan berstandar dan backup terenkripsi.</p>
                <span class="service-tag">
                    <i class="fas fa-circle text-[8px]"></i> Terenkripsi
                </span>
            </div>
        </div>
    </div>
</section>

<section class="py-12 sm:py-16 md:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10 md:mb-14">
            <span class="section-badge" data-aos="fade-up">
                <i class="fas fa-check-circle"></i> Divalidasi
            </span>
            <h2 class="section-title mt-3" data-aos="fade-up" data-aos-delay="100">
                Divalidasi oleh <span>Para Ahli</span>
            </h2>
            <p class="section-subtitle mt-3" data-aos="fade-up" data-aos-delay="200">
                Dikembangkan dan divalidasi oleh tenaga profesional di bidang kesehatan dan teknologi
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <div class="validator-card" data-aos="flip-up" data-aos-delay="100">
                <div class="validator-avatar"
                    style="background: linear-gradient(135deg, #fef2f2, #fee2e2); color: #dc2626;">
                    <i class="fas fa-user-md"></i>
                </div>
                <h4>Ahli Gizi</h4>
                <p class="validator-role">Praktisi gizi berpengalaman</p>
                <span class="validator-badge"><i class="fas fa-certificate mr-1"></i> Bersertifikat</span>
            </div>

            <div class="validator-card" data-aos="flip-up" data-aos-delay="200">
                <div class="validator-avatar"
                    style="background: linear-gradient(135deg, #eff6ff, #dbeafe); color: #2563eb;">
                    <i class="fas fa-hospital-user"></i>
                </div>
                <h4>Bidan Desa</h4>
                <p class="validator-role">Tenaga kesehatan posyandu</p>
                <span class="validator-badge"><i class="fas fa-award mr-1"></i> Berpengalaman</span>
            </div>

            <div class="validator-card" data-aos="flip-up" data-aos-delay="300">
                <div class="validator-avatar"
                    style="background: linear-gradient(135deg, #faf5ff, #f3e8ff); color: #9333ea;">
                    <i class="fas fa-flask"></i>
                </div>
                <h4>Peneliti Kesehatan</h4>
                <p class="validator-role">Peneliti kesehatan masyarakat</p>
                <span class="validator-badge"><i class="fas fa-graduation-cap mr-1"></i> Akademisi</span>
            </div>

            <div class="validator-card" data-aos="flip-up" data-aos-delay="400">
                <div class="validator-avatar"
                    style="background: linear-gradient(135deg, #ecfdf5, #d1fae5); color: #059669;">
                    <i class="fas fa-laptop-code"></i>
                </div>
                <h4>Dosen Teknologi</h4>
                <p class="validator-role">Akademisi teknologi informasi</p>
                <span class="validator-badge"><i class="fas fa-lightbulb mr-1"></i> Inovator</span>
            </div>
        </div>
    </div>
</section>

<!-- <section class="py-12 sm:py-16 md:py-20 bg-gray-50" id="publications">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10 md:mb-14">
            <span class="section-badge" data-aos="fade-up">
                <i class="fas fa-newspaper"></i> Publikasi
            </span>
            <h2 class="section-title mt-3" data-aos="fade-up" data-aos-delay="100">
                Press Release & <span>Publikasi</span>
            </h2>
            <p class="section-subtitle mt-3" data-aos="fade-up" data-aos-delay="200">
                Berita dan publikasi terkait pengembangan AIoT Sok!Anak
            </p>
        </div>

        <div class="mb-8" data-aos="fade-up" data-aos-delay="300">
            <div class="press-card">
                <img src="https://assets.kompasiana.com/items/album/2026/02/18/screenshot-from-2026-02-18-18-32-14-6995bcd5ed64151c285165d4.png?t=o&v=770"
                    alt="Press Release AIoT Sok!Anak" class="press-image"
                    onerror="this.onerror=null; this.src='assets/img/hero/image-1.jpg';">
                <div class="press-body">
                    <span class="press-tag press-tag-press">
                        <i class="fas fa-bullhorn mr-1"></i> Press Release
                    </span>
                    <h3 class="press-title">
                        <a href="https://www.kompasiana.com/ceisya57737/699350d4ed64153b9508b723/sok-anak-ketika-posyandu-di-pelosok-bandung-berubah-jadi-klinik-digital-tanpa-internet?page=2&page_images=4"
                            target="_blank">
                            Sok!Anak: Ketika Posyandu di Pelosok Bandung Berubah Jadi Klinik Digital Tanpa Internet
                        </a>
                    </h3>
                    <p class="press-excerpt">
                        Sok!Anak adalah sistem observasi kesehatan anak berbasis IoT dan AI yang dioperasikan secara
                        lokal.
                        Dikembangkan oleh mahasiswa D3 Teknologi Telekomunikasi Telkom University, solusi ini bertujuan
                        untuk meningkatkan akurasi data dan efektivitas intervensi gizi di Posyandu.
                    </p>
                    <div class="press-meta">
                        <span><i class="fas fa-calendar-alt"></i> 17 Februari 2026</span>
                        <span><i class="fas fa-user"></i> Tim Innovillage</span>
                        <span><i class="fas fa-tag"></i> sustainability</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <div class="press-card" data-aos="fade-up" data-aos-delay="400">
                <img src="https://siaran-berita.com/wp-content/uploads/2026/02/Screenshot-4-750x536.jpg"
                    alt="Siaran Berita" class="press-image" onerror="this.onerror=null; this.src='assets/img/hero/image-1.jpg';">
                <div class="press-body">
                    <span class="press-tag press-tag-journal">
                        <i class="fas fa-book mr-1"></i> Siaran Berita
                    </span>
                    <h3 class="press-title">
                        <a href="https://siaran-berita.com/sokanak-posyandu-jadi-klinik-digital-tanpa-internet-di-kaki-pegunungan-bandung/"
                            target="_blank">
                            Sok!Anak: Posyandu Jadi Klinik Digital Tanpa Internet di Kaki Pegunungan Bandung
                        </a>
                    </h3>
                    <p class="press-excerpt">
                        Di kaki Gunung Bandung, Sok!Anak menjadi contoh inovasi layanan kesehatan masyarakat yang
                        cerdas, akurat, dan berbasis komunitas, meskipun tanpa akses internet.
                    </p>
                    <div class="press-meta">
                        <span><i class="fas fa-calendar-alt"></i> 17 Februari 2026</span>
                        <span><i class="fas fa-user"></i> Tim Innovillage</span>
                        <span><i class="fas fa-tag"></i> Pendidikan</span>
                    </div>
                </div>
            </div>

            <div class="press-card" data-aos="fade-up" data-aos-delay="500">
                <img src="assets/img/haki-certificate.jpg" alt="Sertifikat HAKI" class="press-image"
                    onerror="this.onerror=null; this.src='assets/img/hero/image-1.jpg';">
                <div class="press-body">
                    <span class="press-tag press-tag-haki">
                        <i class="fas fa-certificate mr-1"></i> HAKI
                    </span>
                    <h3 class="press-title">
                        <a href="#">Hak Kekayaan Intelektual Sistem AIoT Sok!Anak</a>
                    </h3>
                    <p class="press-excerpt">
                        Sistem AIoT Sok!Anak telah terdaftar dan mendapatkan sertifikat Hak Cipta dari
                        Direktorat Jenderal Kekayaan Intelektual (DJKI) Kemenkumham RI sebagai perlindungan
                        hukum atas inovasi teknologi yang dikembangkan.
                    </p>
                    <div class="press-meta">
                        <span><i class="fas fa-calendar-alt"></i> sedang berlangsung</span>
                        <span><i class="fas fa-file-alt"></i> Pendaftaran</span>
                        <span><i class="fas fa-check-circle"></i> Tersertifikasi</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section> -->

<section class="py-12 sm:py-16 md:py-20 relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-r from-red-600 to-blue-700"></div>
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 left-0 w-64 h-64 bg-white rounded-full -translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-white rounded-full translate-x-1/3 translate-y-1/3"></div>
    </div>

    <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
        <span
            class="inline-flex items-center gap-2 px-4 py-1.5 bg-white/10 backdrop-blur-sm rounded-full text-sm font-medium border border-white/10 mb-4"
            data-aos="fade-up">
            <i class="fas fa-rocket text-red-200"></i>
            Mulai Perjalanan Digital
        </span>

        <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold mb-4" data-aos="fade-up" data-aos-delay="100">
            Siap Mengelola Data Gizi <br class="hidden sm:block">dengan Lebih Baik?
        </h2>

        <p class="text-base sm:text-lg text-white/90 max-w-2xl mx-auto mb-8" data-aos="fade-up" data-aos-delay="200">
            Bergabunglah dengan petugas posyandu yang telah menggunakan Sok!Anak
            untuk meningkatkan kualitas layanan kesehatan anak.
        </p>

        <div class="flex flex-wrap justify-center gap-3 sm:gap-4" data-aos="fade-up" data-aos-delay="300">
            <a href="signup.php" class="inline-flex items-center gap-2 px-8 py-3.5 bg-white text-red-600 rounded-xl font-semibold 
                      hover:bg-red-50 transition-all duration-300 transform hover:-translate-y-1 
                      shadow-lg hover:shadow-2xl text-sm sm:text-base">
                <i class="fas fa-user-plus"></i>
                Daftar Sekarang
            </a>
            <a href="login.php" class="inline-flex items-center gap-2 px-8 py-3.5 bg-white/10 backdrop-blur-sm text-white 
                      rounded-xl font-semibold border border-white/30 hover:bg-white/20 
                      transition-all duration-300 text-sm sm:text-base">
                <i class="fas fa-sign-in-alt"></i>
                Masuk
            </a>
        </div>

        <p class="text-sm text-white/60 mt-6" data-aos="fade-up" data-aos-delay="400">
            <i class="fas fa-lock mr-1"></i> Data Anda aman dan terenkripsi
        </p>
    </div>
</section>

<section class="py-12 sm:py-16 bg-white border-t border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
            <span class="section-badge" data-aos="fade-up">
                <i class="fas fa-handshake"></i> Mitra Kami
            </span>
            <p class="text-gray-500 mt-2 text-sm" data-aos="fade-up" data-aos-delay="100">
                Telah dipercaya oleh institusi terkemuka
            </p>
        </div>

        <div class="partners-grid">
            <div class="partner-logo" data-aos="zoom-in" data-aos-delay="100">
                <img src="assets/img/innovillage.png" alt="Innovillage">
            </div>
            <div class="partner-logo" data-aos="zoom-in" data-aos-delay="200">
                <img src="assets/img/telyu.png" alt="Telkom University">
            </div>
            <div class="partner-logo" data-aos="zoom-in" data-aos-delay="300">
                <img src="assets/img/desa-sukarame.png" alt="Desa Sukarame">
            </div>
            <div class="partner-logo" data-aos="zoom-in" data-aos-delay="400">
                <div class="partner-placeholder">+3 Institusi Lainnya</div>
            </div>
        </div>
    </div>
</section>

<section class="py-12 sm:py-16 md:py-20 bg-gradient-to-br from-red-50 via-white to-blue-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-8 md:gap-12 items-center">
            <div>
                <span class="section-badge" data-aos="fade-up">
                    <i class="fas fa-calculator"></i> Fitur Publik
                </span>
                <h2 class="section-title mt-3" data-aos="fade-up" data-aos-delay="100">
                    Kalkulator <span>Gizi Anak</span>
                </h2>
                <p class="text-base sm:text-lg text-gray-600 mb-6 leading-relaxed" data-aos="fade-up"
                    data-aos-delay="200">
                    Akses publik untuk menghitung dan mengevaluasi status gizi anak berdasarkan standar WHO.
                    Dapat digunakan oleh orang tua dan masyarakat umum.
                </p>

                <div class="space-y-3" data-aos="fade-up" data-aos-delay="300">
                    <div class="flex items-center gap-3 bg-white/80 p-3 rounded-xl shadow-sm border border-gray-100">
                        <i class="fas fa-check-circle text-red-600 text-base flex-shrink-0"></i>
                        <span class="text-sm text-gray-700">Perhitungan berdasarkan standar WHO</span>
                    </div>
                    <div class="flex items-center gap-3 bg-white/80 p-3 rounded-xl shadow-sm border border-gray-100">
                        <i class="fas fa-check-circle text-red-600 text-base flex-shrink-0"></i>
                        <span class="text-sm text-gray-700">Evaluasi status gizi (stunting, wasting, underweight)</span>
                    </div>
                    <div class="flex items-center gap-3 bg-white/80 p-3 rounded-xl shadow-sm border border-gray-100">
                        <i class="fas fa-check-circle text-red-600 text-base flex-shrink-0"></i>
                        <span class="text-sm text-gray-700">Gratis dan mudah digunakan</span>
                    </div>
                </div>

                <div class="mt-6" data-aos="fade-up" data-aos-delay="400">
                    <button class="inline-flex items-center gap-2 px-6 py-3 bg-gray-300 text-gray-500 rounded-xl font-semibold 
                                   cursor-not-allowed text-sm">
                        <i class="fas fa-clock"></i>
                        Segera Hadir
                    </button>
                    <p class="text-xs text-gray-500 mt-2">
                        <i class="fas fa-info-circle mr-1"></i> Fitur ini sedang dalam pengujian internal
                    </p>
                </div>
            </div>

            <div class="flex justify-center mt-8 lg:mt-0" data-aos="fade-left" data-aos-duration="1000">
                <div class="relative w-full max-w-md">
                    <div class="bg-white rounded-2xl shadow-xl p-6 sm:p-8 border border-gray-100">
                        <div class="flex items-center justify-between mb-4 sm:mb-6">
                            <h3 class="text-base sm:text-lg font-bold text-gray-900">Preview Kalkulator</h3>
                            <span class="text-xs bg-red-100 text-red-600 px-3 py-1 rounded-full">Coming Soon</span>
                        </div>
                        <div class="space-y-3 sm:space-y-4 opacity-50">
                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Usia
                                    (bulan)</label>
                                <input type="text"
                                    class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-sm"
                                    placeholder="24" disabled>
                            </div>
                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Jenis
                                    Kelamin</label>
                                <div class="flex gap-3">
                                    <button class="flex-1 px-3 py-2 border rounded-lg bg-gray-50 text-sm"
                                        disabled>Laki-laki</button>
                                    <button class="flex-1 px-3 py-2 border rounded-lg bg-gray-50 text-sm"
                                        disabled>Perempuan</button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Berat Badan
                                    (kg)</label>
                                <input type="text"
                                    class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-sm"
                                    placeholder="12.5" disabled>
                            </div>
                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Tinggi Badan
                                    (cm)</label>
                                <input type="text"
                                    class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-sm"
                                    placeholder="85" disabled>
                            </div>
                            <button class="w-full py-2 sm:py-3 bg-gray-200 text-gray-400 rounded-lg font-medium text-sm"
                                disabled>
                                <i class="fas fa-calculator mr-2"></i> Hitung Status Gizi
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    AOS.init({
        duration: 700,
        once: true,
        offset: 40,
        easing: 'ease-out-cubic',
        disable: false
    });

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    function setHeroHeight() {
        const hero = document.querySelector('.hero-container');
        if (window.innerWidth <= 768) {
            const vh = window.innerHeight;
            hero.style.minHeight = `${vh}px`;
        }
    }

    setHeroHeight();
    window.addEventListener('resize', setHeroHeight);
</script>

<?php include 'includes/footer.php'; ?>