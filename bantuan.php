<?php
$page_title = "Pusat Bantuan - Sok!Anak";
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

    .btn-primary-custom {
        display: inline-flex;
        align-items: center;
        gap: 0.625rem;
        padding: 0.75rem 1.75rem;
        background: linear-gradient(135deg, #e2001a, #b30015);
        color: white;
        border-radius: 0.75rem;
        font-weight: 600;
        font-size: 0.9375rem;
        transition: all 0.3s ease;
        text-decoration: none;
        box-shadow: 0 4px 16px rgba(226, 0, 26, 0.2);
        border: none;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .btn-primary-custom::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, transparent 70%);
        opacity: 0;
        transition: opacity 0.5s ease;
        transform: scale(0.5);
        pointer-events: none;
    }

    .btn-primary-custom:hover::before {
        opacity: 1;
        transform: scale(1);
    }

    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(226, 0, 26, 0.3);
        color: white;
        text-decoration: none;
    }

    .btn-secondary-custom {
        display: inline-flex;
        align-items: center;
        gap: 0.625rem;
        padding: 0.75rem 1.75rem;
        background: #f8fafc;
        color: #1e293b;
        border-radius: 0.75rem;
        font-weight: 600;
        font-size: 0.9375rem;
        transition: all 0.3s ease;
        border: 2px solid #e2e8f0;
        text-decoration: none;
        cursor: pointer;
    }

    .btn-secondary-custom:hover {
        background: white;
        border-color: #e2001a;
        transform: translateY(-2px);
        color: #1e293b;
        text-decoration: none;
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

    .hero-help {
        position: relative;
        min-height: 420px;
        display: flex;
        align-items: center;
        overflow: hidden;
        background: linear-gradient(135deg, #fef2f2 0%, #eff6ff 100%);
    }

    .hero-help::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url('assets/img/hero/image-2.png') center/cover no-repeat;
        opacity: 0.1;
        z-index: 0;
        pointer-events: none;
    }

    .hero-help .container {
        position: relative;
        z-index: 1;
        width: 100%;
    }

    .hero-help .deco-circle {
        position: absolute;
        border-radius: 50%;
        background: rgba(226, 0, 26, 0.04);
        pointer-events: none;
    }

    .category-card {
        background: white;
        border-radius: 1.25rem;
        padding: 2rem 1.75rem;
        border: 1px solid #f1f5f9;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        text-align: center;
        cursor: pointer;
        height: 100%;
        display: block;
        text-decoration: none;
        color: inherit;
        position: relative;
        overflow: hidden;
    }

    .category-card::before {
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

    .category-card:hover::before {
        opacity: 1;
    }

    .category-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.06);
        border-color: #e2e8f0;
        text-decoration: none;
        color: inherit;
    }

    .category-icon {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        margin: 0 auto 1rem;
        transition: all 0.3s ease;
    }

    .category-card:hover .category-icon {
        transform: scale(1.08) rotate(-4deg);
    }

    .category-card h3 {
        font-size: 1.125rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.25rem;
    }

    .category-card p {
        font-size: 0.875rem;
        color: #64748b;
        line-height: 1.6;
    }

    .faq-item {
        border: 1px solid #f1f5f9;
        border-radius: 0.75rem;
        overflow: hidden;
        transition: all 0.3s ease;
        background: white;
    }

    .faq-item:hover {
        border-color: rgba(226, 0, 26, 0.15);
    }

    .faq-question {
        padding: 1rem 1.25rem;
        background: #fafbfc;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.3s ease;
        font-weight: 600;
        color: #1e293b;
        user-select: none;
        width: 100%;
        border: none;
        text-align: left;
        font-size: 1rem;
        font-family: inherit;
    }

    .faq-question:hover {
        background: #f1f5f9;
    }

    .faq-question.active {
        background: rgba(226, 0, 26, 0.05);
        color: #e2001a;
    }

    .faq-question i {
        transition: transform 0.3s ease;
        flex-shrink: 0;
        margin-left: 0.5rem;
    }

    .faq-answer {
        padding: 0 1.25rem;
        max-height: 0;
        overflow: hidden;
        transition: all 0.4s ease;
        color: #64748b;
        line-height: 1.7;
        background: white;
    }

    .faq-answer.open {
        padding: 1rem 1.25rem 1.25rem;
        max-height: 300px;
    }

    .support-card {
        background: white;
        border-radius: 1.25rem;
        padding: 2rem 1.75rem;
        border: 1px solid #f1f5f9;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        text-align: center;
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    .support-card::before {
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

    .support-card:hover::before {
        opacity: 1;
    }

    .support-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.06);
        border-color: #e2e8f0;
    }

    .support-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin: 0 auto 1rem;
        transition: all 0.3s ease;
    }

    .support-card:hover .support-icon {
        transform: scale(1.05);
    }

    .support-card h3 {
        font-size: 1.125rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.25rem;
    }

    .support-card .support-desc {
        font-size: 0.875rem;
        color: #64748b;
        margin-bottom: 0.75rem;
    }

    .support-card .support-value {
        font-weight: 700;
        color: #0f172a;
    }

    .support-card .support-value a {
        color: #e2001a;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .support-card .support-value a:hover {
        color: #b30015;
    }

    .problem-card {
        background: white;
        border-radius: 1.25rem;
        padding: 2rem 1.75rem;
        border: 1px solid #f1f5f9;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    .problem-card::before {
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

    .problem-card:hover::before {
        opacity: 1;
    }

    .problem-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.06);
        border-color: #e2e8f0;
    }

    .problem-card .problem-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
        transition: all 0.3s ease;
    }

    .problem-card:hover .problem-icon {
        transform: scale(1.05) rotate(-4deg);
    }

    .problem-card h3 {
        font-size: 1.125rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.5rem;
    }

    .problem-card p {
        font-size: 0.875rem;
        color: #64748b;
        line-height: 1.7;
        margin-bottom: 1rem;
    }

    .problem-card .solution-box {
        background: #f8fafc;
        border-radius: 0.75rem;
        padding: 0.75rem 1rem;
        border: 1px solid #f1f5f9;
        transition: all 0.3s ease;
    }

    .problem-card .solution-box:hover {
        border-color: rgba(226, 0, 26, 0.2);
        background: #fef2f2;
    }

    .cta-gradient {
        background: linear-gradient(135deg, #e2001a 0%, #0066cc 100%);
    }

    .stat-mini {
        text-align: center;
        padding: 1rem;
        border-radius: 0.75rem;
        transition: all 0.3s ease;
    }

    .stat-mini:hover {
        background: rgba(226, 0, 26, 0.04);
    }

    .stat-mini .stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: #e2001a;
        line-height: 1.2;
    }

    .stat-mini .stat-label {
        font-size: 0.875rem;
        color: #64748b;
        margin-top: 0.125rem;
    }

    .feature-mini {
        background: #f8fafc;
        border-radius: 0.75rem;
        padding: 1rem;
        text-align: center;
        transition: all 0.3s ease;
        border: 1px solid transparent;
    }

    .feature-mini:hover {
        background: white;
        border-color: #f1f5f9;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    }

    .feature-mini i {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
        display: block;
    }

    .feature-mini p {
        font-size: 0.8125rem;
        font-weight: 500;
        color: #1e293b;
        margin-bottom: 0.125rem;
    }

    .feature-mini span {
        font-size: 0.6875rem;
        color: #94a3b8;
    }

    @media (max-width: 1024px) {
        .section-title {
            font-size: 1.875rem;
        }
    }

    @media (max-width: 768px) {
        .hero-help {
            min-height: 340px;
        }

        .section-title {
            font-size: 1.625rem;
        }

        .section-subtitle {
            font-size: 1rem;
        }

        .category-card {
            padding: 1.5rem;
        }

        .category-icon {
            width: 52px;
            height: 52px;
            font-size: 1.5rem;
        }

        .faq-question {
            font-size: 0.95rem;
            padding: 0.875rem 1rem;
        }

        .faq-answer {
            font-size: 0.9rem;
        }

        section {
            padding: 3rem 0 !important;
        }

        .stat-mini .stat-number {
            font-size: 1.5rem;
        }

        .problem-card {
            padding: 1.5rem;
        }

        .support-card {
            padding: 1.5rem;
        }
    }

    @media (max-width: 640px) {
        .hero-help {
            min-height: 300px;
        }

        .section-title {
            font-size: 1.5rem;
        }

        .btn-primary-custom,
        .btn-secondary-custom {
            padding: 0.625rem 1.25rem;
            font-size: 0.875rem;
            width: 100%;
            justify-content: center;
        }

        .hero-help .flex-wrap {
            flex-direction: column;
            width: 100%;
        }

        .hero-help .flex-wrap a {
            width: 100%;
            justify-content: center;
        }

        section {
            padding: 2rem 0 !important;
        }

        .stat-mini .stat-number {
            font-size: 1.25rem;
        }
    }

    [data-aos] {
        pointer-events: auto !important;
    }

    [data-aos].aos-animate {
        pointer-events: auto !important;
    }

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
    .max-w-4xl,
    .max-w-3xl,
    .max-w-2xl,
    .max-w-md {
        max-width: 100% !important;
    }

    .grid {
        overflow: hidden;
    }

    .relative img {
        max-width: 100%;
    }
</style>

<section class="hero-help" data-aos="fade-up">
    <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-10 items-center">
            <div>
                <span class="section-badge mb-4" data-aos="fade-up" data-aos-delay="100">
                    <i class="fas fa-life-ring mr-1"></i> Pusat Bantuan
                </span>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 leading-tight mb-4"
                    data-aos="fade-up" data-aos-delay="200">
                    Ada yang Bisa <br><span class="text-red-600">Kami Bantu?</span>
                </h1>
                <p class="text-base sm:text-lg text-gray-600 leading-relaxed mb-6" data-aos="fade-up"
                    data-aos-delay="300">
                    Temukan panduan lengkap, solusi kendala, dan cara menggunakan Sok!Anak
                    dengan mudah. Tim support kami siap membantu Anda 7 hari seminggu.
                </p>
                <div class="flex flex-wrap gap-3" data-aos="fade-up" data-aos-delay="400">
                    <a href="#panduan" class="btn-primary-custom btn-glow">
                        <i class="fas fa-book-open"></i> Panduan Penggunaan
                    </a>
                    <a href="#faq" class="btn-secondary-custom">
                        <i class="fas fa-question-circle"></i> FAQ
                    </a>
                </div>
            </div>

            <div class="flex justify-center lg:justify-end" data-aos="fade-left" data-aos-duration="1000"
                data-aos-delay="300">
                <div class="relative">
                    <div class="w-56 h-56 sm:w-64 sm:h-64 lg:w-72 lg:h-72 rounded-full bg-gradient-to-br from-red-100 to-blue-100 
                                flex items-center justify-center floating">
                        <div class="text-center">
                            <i class="fas fa-headset text-4xl sm:text-5xl lg:text-6xl text-red-400"></i>
                            <p class="text-sm text-gray-600 mt-2 font-semibold">Support 24/7</p>
                        </div>
                    </div>
                    <div class="absolute -top-3 -right-3 w-16 h-16 rounded-full bg-red-200/30 animate-pulse"
                        style="pointer-events: none;"></div>
                    <div class="absolute -bottom-3 -left-3 w-12 h-12 rounded-full bg-blue-200/30 animate-pulse"
                        style="animation-delay: 1s; pointer-events: none;"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-8 bg-white border-b border-gray-100" data-aos="fade-up">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="stat-mini" data-aos="zoom-in" data-aos-delay="100">
                <div class="stat-number">24/7</div>
                <div class="stat-label">Support Tersedia</div>
            </div>
            <div class="stat-mini" data-aos="zoom-in" data-aos-delay="200">
                <div class="stat-number">&lt; 5</div>
                <div class="stat-label">Menit Respon</div>
            </div>
            <div class="stat-mini" data-aos="zoom-in" data-aos-delay="300">
                <div class="stat-number">100+</div>
                <div class="stat-label">Pertanyaan Terjawab</div>
            </div>
            <div class="stat-mini" data-aos="zoom-in" data-aos-delay="400">
                <div class="stat-number">98%</div>
                <div class="stat-label">Kepuasan Pengguna</div>
            </div>
        </div>
    </div>
</section>

<section class="py-16 sm:py-20 bg-gray-50" data-aos="fade-up">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="section-badge" data-aos="fade-up">
                <i class="fas fa-folder-open mr-1"></i> Kategori Bantuan
            </span>
            <h2 class="section-title mt-3" data-aos="fade-up" data-aos-delay="100">
                Pilih Topik yang <span>Anda Butuhkan</span>
            </h2>
            <p class="section-subtitle mt-3" data-aos="fade-up" data-aos-delay="200">
                Kami telah mengelompokkan panduan dan solusi berdasarkan topik untuk memudahkan Anda
            </p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <a href="#panduan" class="category-card" data-aos="flip-up" data-aos-delay="100">
                <div class="category-icon" style="background: rgba(226, 0, 26, 0.1); color: #e2001a;">
                    <i class="fas fa-book"></i>
                </div>
                <h3>Panduan Penggunaan</h3>
                <p>Langkah-langkah menggunakan Sok!Anak</p>
            </a>
            <a href="#kendala" class="category-card" data-aos="flip-up" data-aos-delay="200">
                <div class="category-icon" style="background: rgba(0, 102, 204, 0.1); color: #0066cc;">
                    <i class="fas fa-tools"></i>
                </div>
                <h3>Kendala & Solusi</h3>
                <p>Atasi masalah umum aplikasi</p>
            </a>
            <a href="#faq" class="category-card" data-aos="flip-up" data-aos-delay="300">
                <div class="category-icon" style="background: rgba(124, 58, 237, 0.1); color: #7c3aed;">
                    <i class="fas fa-question-circle"></i>
                </div>
                <h3>Tanya Jawab</h3>
                <p>Pertanyaan yang sering diajukan</p>
            </a>
            <a href="#kontak" class="category-card" data-aos="flip-up" data-aos-delay="400">
                <div class="category-icon" style="background: rgba(5, 150, 105, 0.1); color: #059669;">
                    <i class="fas fa-headset"></i>
                </div>
                <h3>Hubungi Kami</h3>
                <p>Tim support siap membantu</p>
            </a>
        </div>
    </div>
</section>

<section id="panduan" class="py-16 sm:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-10 items-center">
            <div data-aos="fade-right">
                <span class="section-badge mb-4">
                    <i class="fas fa-book-open mr-1"></i> Panduan
                </span>
                <h2 class="section-title mb-4">
                    Panduan Penggunaan <span>Sok!Anak</span>
                </h2>
                <p class="text-gray-600 leading-relaxed mb-6">
                    Mulai menggunakan Sok!Anak dengan mudah. Unduh panduan lengkap atau tonton
                    video tutorial yang telah kami siapkan untuk membantu Anda.
                </p>
                <div class="space-y-4">
                    <div
                        class="flex items-center p-4 bg-gray-50 rounded-xl border border-gray-100 hover:border-red-200 transition-all">
                        <div
                            class="w-12 h-12 rounded-lg bg-red-100 flex items-center justify-center mr-4 flex-shrink-0">
                            <i class="fas fa-file-pdf text-red-600 text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900">Panduan Lengkap PDF</h4>
                            <p class="text-sm text-gray-600">Dokumen panduan penggunaan lengkap</p>
                        </div>
                        <a href="https://cloud.ricalnet.my.id/s/RtnTkgRkZX74eFK"
                            class="btn-primary-custom text-sm py-2 px-4" target="_blank">
                            <i class="fas fa-download mr-1"></i> Unduh
                        </a>
                    </div>
                    <div
                        class="flex items-center p-4 bg-gray-50 rounded-xl border border-gray-100 hover:border-blue-200 transition-all">
                        <div
                            class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center mr-4 flex-shrink-0">
                            <i class="fab fa-youtube text-blue-600 text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900">Video Tutorial</h4>
                            <p class="text-sm text-gray-600">Tonton tutorial di YouTube</p>
                        </div>
                        <a href="https://youtube.com/@aiotsokanak" class="btn-secondary-custom text-sm py-2 px-4"
                            target="_blank">
                            <i class="fas fa-play mr-1"></i> Tonton
                        </a>
                    </div>
                </div>
            </div>

            <div data-aos="fade-left" data-aos-duration="1000">
                <span class="section-badge mb-4"
                    style="background: rgba(0, 102, 204, 0.08); color: #0066cc; border-color: rgba(0, 102, 204, 0.12);">
                    <i class="fas fa-star mr-1"></i> Fitur Utama
                </span>
                <h2 class="section-title mb-4">
                    Fitur yang <span style="color: #0066cc;">Sering Digunakan</span>
                </h2>
                <p class="text-gray-600 leading-relaxed mb-6">
                    Berikut adalah fitur-fitur utama Sok!Anak yang paling sering digunakan oleh
                    petugas posyandu dan kader kesehatan.
                </p>
                <div class="grid grid-cols-2 gap-4">
                    <div class="feature-mini" style="border-color: rgba(226, 0, 26, 0.15);">
                        <i class="fas fa-child text-red-600"></i>
                        <p>Data Anak</p>
                        <span>Pendaftaran & kelola</span>
                    </div>
                    <div class="feature-mini" style="border-color: rgba(226, 0, 26, 0.15);">
                        <i class="fas fa-weight-scale text-red-600"></i>
                        <p>Pengukuran</p>
                        <span>Otomatis & akurat</span>
                    </div>
                    <div class="feature-mini" style="border-color: rgba(0, 102, 204, 0.15);">
                        <i class="fas fa-chart-line text-blue-600"></i>
                        <p>Monitoring</p>
                        <span>Grafik pertumbuhan</span>
                    </div>
                    <div class="feature-mini" style="border-color: rgba(124, 58, 237, 0.15);">
                        <i class="fas fa-brain text-purple-600"></i>
                        <p>AI Assistant</p>
                        <span>Rekomendasi cerdas</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="kendala" class="py-16 sm:py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12" data-aos="fade-up">
            <span class="section-badge">
                <i class="fas fa-tools mr-1"></i> Solusi
            </span>
            <h2 class="section-title mt-3" data-aos="fade-up" data-aos-delay="100">
                Kendala & <span>Solusi</span>
            </h2>
            <p class="section-subtitle mt-3" data-aos="fade-up" data-aos-delay="200">
                Temukan solusi untuk masalah umum yang mungkin Anda hadapi saat menggunakan Sok!Anak
            </p>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <div class="problem-card" data-aos="fade-up" data-aos-delay="100">
                <div class="flex items-start gap-4">
                    <div class="problem-icon" style="background: rgba(226, 0, 26, 0.1); color: #e2001a;">
                        <i class="fas fa-key"></i>
                    </div>
                    <div class="flex-1">
                        <h3>Lupa Password</h3>
                        <p>Jika Anda lupa password akun Sok!Anak, tim support kami siap membantu reset password Anda.
                        </p>
                        <div class="solution-box">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center">
                                    <i class="fas fa-envelope text-red-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 text-sm">Email Support</p>
                                    <p class="text-sm text-gray-700">sokanak@duck.com</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="problem-card" data-aos="fade-up" data-aos-delay="200">
                <div class="flex items-start gap-4">
                    <div class="problem-icon" style="background: rgba(0, 102, 204, 0.1); color: #0066cc;">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="flex-1">
                        <h3>Masalah Pendaftaran Akun</h3>
                        <p>Mengalami kendala saat mendaftar atau verifikasi akun? Hubungi koordinator posyandu atau tim
                            IT support.</p>
                        <div class="grid grid-cols-2 gap-3">
                            <div
                                class="bg-gray-50 rounded-xl p-3 text-center hover:bg-blue-50 transition-all border border-gray-100">
                                <i class="fas fa-user-tie text-gray-600 mb-1 text-lg"></i>
                                <p class="text-xs font-medium text-gray-900">Koordinator Wilayah</p>
                            </div>
                            <div
                                class="bg-gray-50 rounded-xl p-3 text-center hover:bg-blue-50 transition-all border border-gray-100">
                                <i class="fas fa-headset text-gray-600 mb-1 text-lg"></i>
                                <p class="text-xs font-medium text-gray-900">IT Support</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="problem-card" data-aos="fade-up" data-aos-delay="300">
                <div class="flex items-start gap-4">
                    <div class="problem-icon" style="background: rgba(5, 150, 105, 0.1); color: #059669;">
                        <i class="fas fa-sync-alt"></i>
                    </div>
                    <div class="flex-1">
                        <h3>Sinkronisasi Data Gagal</h3>
                        <p>Pastikan koneksi internet stabil dan coba refresh halaman. Jika masih gagal, hubungi tim
                            support.</p>
                        <div class="solution-box">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                                    <i class="fas fa-wifi text-green-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 text-sm">Cek Koneksi</p>
                                    <p class="text-sm text-gray-700">Pastikan jaringan stabil</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="problem-card" data-aos="fade-up" data-aos-delay="400">
                <div class="flex items-start gap-4">
                    <div class="problem-icon" style="background: rgba(124, 58, 237, 0.1); color: #7c3aed;">
                        <i class="fas fa-print"></i>
                    </div>
                    <div class="flex-1">
                        <h3>Cetak Laporan Error</h3>
                        <p>Pastikan printer terhubung dengan benar. Gunakan fitur export PDF sebagai alternatif.</p>
                        <div class="solution-box">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                                    <i class="fas fa-file-pdf text-purple-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 text-sm">Export PDF</p>
                                    <p class="text-sm text-gray-700">Alternatif cetak laporan</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="faq" class="py-16 sm:py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12" data-aos="fade-up">
            <span class="section-badge">
                <i class="fas fa-question-circle mr-1"></i> FAQ
            </span>
            <h2 class="section-title mt-3" data-aos="fade-up" data-aos-delay="100">
                Pertanyaan yang <span>Sering Diajukan</span>
            </h2>
            <p class="section-subtitle mt-3" data-aos="fade-up" data-aos-delay="200">
                Temukan jawaban cepat untuk pertanyaan-pertanyaan umum tentang Sok!Anak
            </p>
        </div>

        <div class="space-y-4" data-aos="fade-up">
            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    <span>Apa itu Sok!Anak?</span>
                    <i class="fas fa-chevron-down text-gray-400"></i>
                </button>
                <div class="faq-answer">
                    Sok!Anak adalah sistem observasi kesehatan dan gizi anak berbasis digital yang
                    menggabungkan teknologi Internet of Things (IoT) dan Kecerdasan Buatan (AI)
                    untuk memantau tumbuh kembang anak di posyandu secara akurat dan terintegrasi.
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    <span>Siapa yang bisa menggunakan Sok!Anak?</span>
                    <i class="fas fa-chevron-down text-gray-400"></i>
                </button>
                <div class="faq-answer">
                    Sok!Anak dapat digunakan oleh petugas posyandu, ahli gizi, kader kesehatan,
                    bidan desa, dan orang tua yang terdaftar. Sistem ini dirancang dengan antarmuka
                    yang intuitif sehingga mudah digunakan oleh semua kalangan.
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    <span>Apakah data anak aman disimpan?</span>
                    <i class="fas fa-chevron-down text-gray-400"></i>
                </button>
                <div class="faq-answer">
                    Ya, keamanan data adalah prioritas utama kami. Semua data disimpan di server
                    on-premise lokal dengan enkripsi AES-256. Tidak ada data yang dikirim ke cloud
                    pihak ketiga, sehingga privasi dan keamanan data anak dan posyandu terjamin.
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    <span>Bagaimana cara melakukan pengukuran dengan Sok!Anak?</span>
                    <i class="fas fa-chevron-down text-gray-400"></i>
                </button>
                <div class="faq-answer">
                    Gampang! Letakkan anak di alat ukur, klik tombol Sinkron. Sensor IoT kami akan otomatis mencatat
                    berat dan tinggi badan secara akurat. Cepat, praktis, dan anti-salah catat!
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    <span>Bagaimana cara mendapatkan bantuan jika mengalami kendala?</span>
                    <i class="fas fa-chevron-down text-gray-400"></i>
                </button>
                <div class="faq-answer">
                    Anda dapat menghubungi tim support kami melalui email di sokanak@duck.com
                    atau melalui koordinator posyandu wilayah Anda. Tim support kami siap membantu
                    Anda 7 hari seminggu dengan waktu respon kurang dari 5 menit.
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    <span>Apakah Sok!Anak bisa digunakan tanpa internet?</span>
                    <i class="fas fa-chevron-down text-gray-400"></i>
                </button>
                <div class="faq-answer">
                    Ya, Sok!Anak dirancang dengan arsitektur on-premise yang memungkinkan operasi
                    tanpa koneksi internet. Semua data disimpan secara lokal di server posyandu,
                    sehingga tetap dapat digunakan di daerah dengan akses internet terbatas.
                </div>
            </div>
        </div>
    </div>
</section>

<section id="kontak" class="py-16 sm:py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12" data-aos="fade-up">
            <span class="section-badge">
                <i class="fas fa-headset mr-1"></i> Hubungi Kami
            </span>
            <h2 class="section-title mt-3" data-aos="fade-up" data-aos-delay="100">
                Kami Siap <span>Membantu Anda</span>
            </h2>
            <p class="section-subtitle mt-3" data-aos="fade-up" data-aos-delay="200">
                Tim support kami tersedia 7 hari seminggu untuk menjawab pertanyaan dan membantu
                menyelesaikan kendala Anda
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            <div class="support-card" data-aos="fade-up" data-aos-delay="100">
                <div class="support-icon" style="background: rgba(226, 0, 26, 0.1); color: #e2001a;">
                    <i class="fas fa-envelope"></i>
                </div>
                <h3>Email Support</h3>
                <p class="support-desc">Kirim pertanyaan via email</p>
                <div class="support-value">
                    <a href="mailto:sokanak@duck.com">sokanak@duck.com</a>
                </div>
            </div>

            <div class="support-card" data-aos="fade-up" data-aos-delay="200">
                <div class="support-icon" style="background: rgba(0, 102, 204, 0.1); color: #0066cc;">
                    <i class="fas fa-clock"></i>
                </div>
                <h3>Jam Operasional</h3>
                <p class="support-desc">Kapan kami tersedia</p>
                <div class="support-value">
                    <p>Senin - Minggu</p>
                    <p style="font-weight: 400; color: #64748b; font-size: 0.875rem;">07:00 - 17:00 WIB</p>
                </div>
            </div>

            <div class="support-card" data-aos="fade-up" data-aos-delay="300">
                <div class="support-icon" style="background: rgba(5, 150, 105, 0.1); color: #059669;">
                    <i class="fas fa-life-ring"></i>
                </div>
                <h3>Waktu Respon</h3>
                <p class="support-desc">Cepat & tanggap</p>
                <div class="support-value">
                    <p style="font-size: 2rem; color: #e2001a;">&lt; 5</p>
                    <p style="font-weight: 400; color: #64748b; font-size: 0.875rem;">Menit rata-rata</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-16 sm:py-20 relative overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-gradient-to-r from-red-600 to-blue-600"></div>
        <div class="absolute inset-0 opacity-10">
            <div
                class="absolute top-0 left-0 w-48 h-48 sm:w-64 sm:h-64 bg-white rounded-full -translate-x-1/2 -translate-y-1/2">
            </div>
            <div
                class="absolute bottom-0 right-0 w-64 h-64 sm:w-96 sm:h-96 bg-white rounded-full translate-x-1/3 translate-y-1/3">
            </div>
        </div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
        <span
            class="inline-flex items-center gap-2 px-4 py-1.5 bg-white/10 backdrop-blur-sm rounded-full text-sm font-medium border border-white/10 mb-4"
            data-aos="fade-up">
            <i class="fas fa-hands-helping text-red-200"></i>
            Siap Membantu
        </span>
        <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold mb-4" data-aos="fade-up" data-aos-delay="100">
            Masih Membutuhkan <br class="hidden sm:block"><span style="color: #fca5a5;">Bantuan?</span>
        </h2>
        <p class="text-base sm:text-lg text-white/90 max-w-2xl mx-auto mb-8" data-aos="fade-up" data-aos-delay="200">
            Jangan ragu untuk menghubungi kami. Tim support Sok!Anak siap membantu Anda dengan senang hati.
        </p>
        <div class="flex flex-wrap justify-center gap-3 sm:gap-4" data-aos="fade-up" data-aos-delay="300">
            <a href="mailto:sokanak@duck.com" class="inline-flex items-center gap-2 px-8 py-3.5 bg-white text-red-600 rounded-xl font-semibold 
                      hover:bg-red-50 transition-all duration-300 transform hover:-translate-y-1 
                      shadow-lg hover:shadow-2xl text-sm sm:text-base btn-glow">
                <i class="fas fa-envelope"></i>
                Kirim Email
            </a>
            <a href="#" class="inline-flex items-center gap-2 px-8 py-3.5 bg-white/10 backdrop-blur-sm text-white 
                      rounded-xl font-semibold border border-white/30 hover:bg-white/20 
                      transition-all duration-300 text-sm sm:text-base">
                <i class="fas fa-headset"></i>
                Live Chat
            </a>
        </div>
        <p class="mt-6 text-sm text-white/70" data-aos="fade-up" data-aos-delay="400">
            <i class="fas fa-clock mr-1"></i> Kami akan merespon dalam waktu kurang dari 5 menit
        </p>
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

    function toggleFaq(element) {
        const answer = element.nextElementSibling;
        const icon = element.querySelector('i.fa-chevron-down');
        const isOpen = answer.classList.contains('open');

        document.querySelectorAll('.faq-answer').forEach(el => {
            if (el !== answer) {
                el.classList.remove('open');
                el.previousElementSibling.classList.remove('active');
                const otherIcon = el.previousElementSibling.querySelector('i.fa-chevron-down');
                if (otherIcon) otherIcon.style.transform = 'rotate(0deg)';
            }
        });

        if (isOpen) {
            answer.classList.remove('open');
            element.classList.remove('active');
            icon.style.transform = 'rotate(0deg)';
        } else {
            answer.classList.add('open');
            element.classList.add('active');
            icon.style.transform = 'rotate(180deg)';
        }
    }

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

    document.querySelector('.floating')?.classList.add('floating');
</script>

<?php include 'includes/footer.php'; ?>