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

    .btn-kalkulator {
        display: inline-flex;
        align-items: center;
        gap: 0.625rem;
        padding: 0.875rem 2.25rem;
        background: linear-gradient(135deg, #e2001a 0%, #b30015 100%);
        color: white;
        border-radius: 1rem;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 16px rgba(226, 0, 26, 0.25);
        text-decoration: none;
        border: none;
        cursor: pointer;
    }

    .btn-kalkulator:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 28px rgba(226, 0, 26, 0.35);
        color: white;
        text-decoration: none;
    }

    .btn-kalkulator-outline {
        display: inline-flex;
        align-items: center;
        gap: 0.625rem;
        padding: 0.875rem 2.25rem;
        background: transparent;
        color: #e2001a;
        border-radius: 1rem;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        border: 2px solid #e2001a;
        text-decoration: none;
        cursor: pointer;
    }

    .btn-kalkulator-outline:hover {
        background: #e2001a;
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 4px 16px rgba(226, 0, 26, 0.15);
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

    /* Kalkulator section image placeholder styling */
    .kalkulator-image-wrapper {
        position: relative;
        border-radius: 1.5rem;
        overflow: hidden;
        box-shadow: 0 20px 48px rgba(0, 0, 0, 0.08);
    }

    .kalkulator-image-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .kalkulator-image-wrapper:hover img {
        transform: scale(1.02);
    }

    .kalkulator-image-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 2rem 1.5rem 1.5rem;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.6), transparent);
        pointer-events: none;
    }

    .kalkulator-image-overlay span {
        color: white;
        font-size: 0.8rem;
        font-weight: 500;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(4px);
        padding: 0.25rem 1rem;
        border-radius: 9999px;
        display: inline-block;
    }

    .feature-list-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .feature-list-item:last-child {
        border-bottom: none;
    }

    .feature-list-item .icon {
        flex-shrink: 0;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(226, 0, 26, 0.08);
        color: #e2001a;
        font-size: 0.75rem;
        margin-top: 0.125rem;
    }

    .feature-list-item .text {
        font-size: 0.9rem;
        color: #334155;
        line-height: 1.6;
    }

    .feature-list-item .text strong {
        color: #0f172a;
        font-weight: 600;
    }

    @media (max-width: 640px) {
        .kalkulator-image-wrapper {
            border-radius: 1rem;
            max-height: 280px;
        }

        .feature-list-item .text {
            font-size: 0.85rem;
        }
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

<!-- ===== SECTION KALKULATOR GIZI ===== -->
<section class="py-12 sm:py-16 md:py-20 bg-gradient-to-br from-red-50 via-white to-blue-50" id="kalkulator">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-10 md:gap-14 items-center">
            <!-- KOLOM KIRI: Gambar -->
            <div data-aos="fade-right" data-aos-duration="800" data-aos-delay="100">
                <div class="kalkulator-image-wrapper">
                    <img src="assets/img/hero/image-2.jpg" alt="Kalkulator Status Gizi Anak - Sok!Anak"
                        class="w-full h-72 sm:h-80 md:h-96 object-cover"
                        onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22800%22 height=%22600%22%3E%3Crect fill=%22%23f1f5f9%22 width=%22800%22 height=%22600%22/%3E%3Ctext x=%22400%22 y=%22300%22 font-family=%22Inter%22 font-size=%2236%22 fill=%22%2394a3b8%22 text-anchor=%22middle%22 dominant-baseline=%22central%22%3E🧒 Kalkulator Gizi%3C/text%3E%3C/svg%3E';">
                    <div class="kalkulator-image-overlay">
                        <span><i class="fas fa-calculator mr-2"></i> Kalkulator Status Gizi</span>
                    </div>
                </div>

                <!-- Statistik kecil -->
                <div class="grid grid-cols-3 gap-3 mt-4">
                    <div class="bg-white rounded-xl p-3 text-center shadow-sm border border-gray-100" data-aos="fade-up"
                        data-aos-delay="200">
                        <div class="text-2xl font-bold text-red-600">WHO</div>
                        <div class="text-xs text-gray-500">Standar</div>
                    </div>
                    <div class="bg-white rounded-xl p-3 text-center shadow-sm border border-gray-100" data-aos="fade-up"
                        data-aos-delay="250">
                        <div class="text-2xl font-bold text-blue-600">0-60</div>
                        <div class="text-xs text-gray-500">Bulan</div>
                    </div>
                    <div class="bg-white rounded-xl p-3 text-center shadow-sm border border-gray-100" data-aos="fade-up"
                        data-aos-delay="300">
                        <div class="text-2xl font-bold text-green-600">4</div>
                        <div class="text-xs text-gray-500">Indikator</div>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN: Konten -->
            <div data-aos="fade-left" data-aos-duration="800" data-aos-delay="200">
                <span class="section-badge">
                    <i class="fas fa-calculator"></i> Layanan Publik
                </span>

                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 mt-3 leading-tight">
                    Kalkulator <span class="text-red-600">Status Gizi</span> Anak
                </h2>

                <p class="text-base sm:text-lg text-gray-600 mt-3 leading-relaxed">
                    Evaluasi status gizi anak Anda secara cepat dan akurat menggunakan standar WHO.
                    Cukup masukkan data usia, jenis kelamin, berat badan, dan tinggi badan.
                </p>

                <div class="mt-5 space-y-1">
                    <div class="feature-list-item">
                        <div class="icon"><i class="fas fa-check"></i></div>
                        <div class="text"><strong>BB/U</strong> – Berat Badan berdasarkan Usia</div>
                    </div>
                    <div class="feature-list-item">
                        <div class="icon"><i class="fas fa-check"></i></div>
                        <div class="text"><strong>TB/U</strong> – Tinggi Badan berdasarkan Usia</div>
                    </div>
                    <div class="feature-list-item">
                        <div class="icon"><i class="fas fa-check"></i></div>
                        <div class="text"><strong>BB/TB</strong> – Berat Badan berdasarkan Tinggi Badan</div>
                    </div>
                    <div class="feature-list-item">
                        <div class="icon"><i class="fas fa-check"></i></div>
                        <div class="text"><strong>IMT/U</strong> – Indeks Massa Tubuh berdasarkan Usia</div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-4 mt-7">
                    <a href="kalkulator.php" class="btn-kalkulator">
                        <i class="fas fa-arrow-right"></i> Buka Kalkulator
                    </a>
                    <a href="#services" class="btn-kalkulator-outline">
                        <i class="fas fa-info-circle"></i> Pelajari Lainnya
                    </a>
                </div>

                <p class="text-xs text-gray-400 mt-4 flex items-center gap-2">
                    <i class="fas fa-lock text-gray-300"></i>
                    Gratis untuk publik • Data tidak disimpan • Berbasis WHO LMS
                </p>
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