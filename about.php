<?php
session_start();
$page_title = "Tentang Kami - Sok!Anak";
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

    .hero-about {
        position: relative;
        min-height: 420px;
        display: flex;
        align-items: center;
        overflow: hidden;
        background: linear-gradient(135deg, #fef2f2 0%, #eff6ff 100%);
    }

    .hero-about::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url('assets/img/hero/image-3.jpg') center/cover no-repeat;
        opacity: 0.1;
        z-index: 0;
        pointer-events: none;
    }

    .hero-about .container {
        position: relative;
        z-index: 1;
        width: 100%;
    }

    .mission-card {
        background: white;
        border-radius: 1.25rem;
        padding: 2rem 1.75rem;
        border: 1px solid #f1f5f9;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    .mission-card::before {
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

    .mission-card:hover::before {
        opacity: 1;
    }

    .mission-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.06);
        border-color: #e2e8f0;
    }

    .mission-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }

    .mission-card:hover .mission-icon {
        transform: scale(1.05) rotate(-4deg);
    }

    .mission-card h3 {
        font-size: 1.125rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.5rem;
    }

    .mission-card p {
        font-size: 0.875rem;
        color: #64748b;
        line-height: 1.7;
    }

    .team-card {
        background: white;
        border-radius: 1rem;
        padding: 1.75rem 1.5rem;
        text-align: center;
        border: 1px solid #f1f5f9;
        transition: all 0.4s ease;
        height: 100%;
    }

    .team-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.06);
        border-color: rgba(226, 0, 26, 0.15);
    }

    .team-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        margin: 0 auto 1rem;
        object-fit: cover;
        border: 4px solid white;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }

    .team-card:hover .team-avatar {
        transform: scale(1.04);
        box-shadow: 0 12px 32px rgba(226, 0, 26, 0.15);
    }

    .team-card h4 {
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
    }

    .team-card .team-role {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        background: rgba(226, 0, 26, 0.08);
        color: #e2001a;
        border-radius: 9999px;
        font-size: 0.6875rem;
        font-weight: 600;
        margin-top: 0.25rem;
    }

    .team-card .team-bio {
        font-size: 0.8125rem;
        color: #64748b;
        margin-top: 0.5rem;
        line-height: 1.6;
    }

    .team-social {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 0.75rem;
    }

    .team-social a {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
        color: #64748b;
        transition: all 0.3s ease;
        text-decoration: none;
        font-size: 0.875rem;
    }

    .team-social a:hover {
        background: #e2001a;
        color: white;
        transform: translateY(-2px);
        text-decoration: none;
    }

    .tech-card {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        border: 1px solid #f1f5f9;
        transition: all 0.3s ease;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
    }

    .tech-card:hover {
        transform: translateX(4px);
        border-color: rgba(226, 0, 26, 0.15);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
    }

    .tech-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .tech-card h4 {
        font-weight: 700;
        color: #0f172a;
        font-size: 0.9375rem;
        margin-bottom: 0.125rem;
    }

    .tech-card p {
        font-size: 0.8125rem;
        color: #64748b;
        line-height: 1.6;
    }

    .value-item {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1rem 1.25rem;
        background: #f8fafc;
        border-radius: 0.75rem;
        border-left: 4px solid #e2001a;
        transition: all 0.3s ease;
    }

    .value-item:hover {
        background: #f1f5f9;
        transform: translateX(4px);
    }

    .value-item i {
        font-size: 1.125rem;
        margin-top: 0.125rem;
        flex-shrink: 0;
    }

    .value-item h4 {
        font-weight: 700;
        color: #0f172a;
        font-size: 0.9375rem;
    }

    .value-item p {
        font-size: 0.8125rem;
        color: #64748b;
        line-height: 1.6;
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

    .cta-gradient {
        background: linear-gradient(135deg, #e2001a 0%, #0066cc 100%);
    }

    @media (max-width: 1024px) {
        .section-title {
            font-size: 1.875rem;
        }
    }

    @media (max-width: 768px) {
        .hero-about {
            min-height: 340px;
        }

        .section-title {
            font-size: 1.625rem;
        }

        .section-subtitle {
            font-size: 1rem;
        }

        .mission-card {
            padding: 1.5rem;
        }

        .team-avatar {
            width: 80px;
            height: 80px;
        }

        .team-card {
            padding: 1.25rem;
        }

        section {
            padding: 3rem 0 !important;
        }

        .tech-card {
            padding: 1rem;
            flex-direction: column;
        }
    }

    @media (max-width: 640px) {
        .hero-about {
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

        .hero-about .flex-wrap {
            flex-direction: column;
            width: 100%;
        }

        .hero-about .flex-wrap a {
            width: 100%;
            justify-content: center;
        }

        section {
            padding: 2rem 0 !important;
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

<section class="hero-about" data-aos="fade-up">
    <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-10 items-center">
            <div>
                <span class="section-badge mb-4" data-aos="fade-up" data-aos-delay="100">
                    <i class="fas fa-flag mr-1"></i> Tentang Kami
                </span>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 leading-tight mb-4"
                    data-aos="fade-up" data-aos-delay="200">
                    Membangun Masa Depan Cerah
                    <br><span class="text-red-600">Anak Indonesia</span>
                </h1>
                <p class="text-base sm:text-lg text-gray-600 leading-relaxed mb-6" data-aos="fade-up"
                    data-aos-delay="300">
                    AIoT Sok!Anak adalah platform digital yang memadukan kecerdasan buatan dan Internet of Things
                    untuk memantau tumbuh kembang anak di posyandu secara akurat dan terintegrasi.
                </p>
                <div class="flex flex-wrap gap-3" data-aos="fade-up" data-aos-delay="400">
                    <a href="#mission" class="btn-primary-custom btn-glow">
                        <i class="fas fa-rocket"></i> Misi Kami
                    </a>
                    <a href="#team" class="btn-secondary-custom">
                        <i class="fas fa-users"></i> Tim Kami
                    </a>
                </div>
            </div>

            <div class="flex justify-center lg:justify-end" data-aos="fade-left" data-aos-duration="1000"
                data-aos-delay="300">
                <div class="relative floating">
                    <div class="w-64 h-64 sm:w-72 sm:h-72 lg:w-80 lg:h-80 rounded-full bg-gradient-to-br from-red-100 to-blue-100 
                                flex items-center justify-center">
                        <div class="text-center">
                            <i class="fas fa-child text-5xl sm:text-6xl lg:text-7xl text-red-400"></i>
                            <p class="text-sm text-gray-600 mt-2 font-semibold">#AnakSehatIndonesia</p>
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

<section id="mission" class="py-16 sm:py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12" data-aos="fade-up">
            <span class="section-badge">
                <i class="fas fa-bullseye mr-1"></i> Misi Kami
            </span>
            <h2 class="section-title mt-3" data-aos="fade-up" data-aos-delay="100">
                Mengapa <span>Sok!Anak</span> Hadir?
            </h2>
            <p class="section-subtitle mt-3" data-aos="fade-up" data-aos-delay="200">
                Kami percaya setiap anak berhak mendapatkan pemantauan tumbuh kembang yang optimal
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            <div class="mission-card" data-aos="fade-up" data-aos-delay="100">
                <div class="mission-icon" style="background: rgba(226, 0, 26, 0.1); color: #e2001a;">
                    <i class="fas fa-heartbeat"></i>
                </div>
                <h3>Deteksi Dini Stunting</h3>
                <p>Mengidentifikasi risiko stunting dan malnutrisi sejak dini melalui pengukuran akurat berbasis standar
                    WHO.</p>
            </div>
            <div class="mission-card" data-aos="fade-up" data-aos-delay="200">
                <div class="mission-icon" style="background: rgba(0, 102, 204, 0.1); color: #0066cc;">
                    <i class="fas fa-microchip"></i>
                </div>
                <h3>Teknologi Tepat Guna</h3>
                <p>Menghadirkan solusi IoT yang mudah digunakan oleh kader posyandu dengan antarmuka yang intuitif dan
                    sederhana.</p>
            </div>
            <div class="mission-card" data-aos="fade-up" data-aos-delay="300">
                <div class="mission-icon" style="background: rgba(124, 58, 237, 0.1); color: #7c3aed;">
                    <i class="fas fa-brain"></i>
                </div>
                <h3>Rekomendasi Cerdas</h3>
                <p>Memberikan saran berbasis AI untuk intervensi gizi yang tepat, didukung oleh data penelitian terkini.
                </p>
            </div>
        </div>
    </div>
</section>

<section class="py-16 sm:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-10 items-center">
            <div data-aos="fade-right">
                <span class="section-badge mb-4"
                    style="background: rgba(226, 0, 26, 0.1); color: #e2001a; border-color: rgba(0, 102, 204, 0.12);">
                    <i class="fas fa-image mr-1"></i> Posyandu Digital
                </span>
                <h2 class="section-title mb-4">
                    Mengubah Tradisional Menjadi <span style="color: #e2001a;">Digital</span>
                </h2>
                <p class="text-gray-600 leading-relaxed mb-6">
                    Sok!Anak membawa transformasi digital ke posyandu dengan menghadirkan sistem
                    pengukuran otomatis, pencatatan data real-time, dan analisis berbasis AI.
                    Kini kader posyandu dapat memantau pertumbuhan anak dengan lebih akurat,
                    cepat, dan menyeluruh.
                </p>
                <ul class="space-y-3 mb-8">
                    <li class="flex items-start gap-3">
                        <i class="fas fa-check-circle text-red-600 mt-0.5 flex-shrink-0"></i>
                        <span class="text-gray-700">Pengukuran berat & tinggi otomatis dengan sensor presisi</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fas fa-check-circle text-red-600 mt-0.5 flex-shrink-0"></i>
                        <span class="text-gray-700">Data tersimpan aman di server lokal tanpa cloud pihak ketiga</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fas fa-check-circle text-red-600 mt-0.5 flex-shrink-0"></i>
                        <span class="text-gray-700">Akses real-time melalui desktop maupun perangkat mobile</span>
                    </li>
                </ul>
                <a href="bantuan.php" class="btn-primary-custom btn-glow">
                    <i class="fas fa-book-open"></i> Pelajari Selengkapnya
                </a>
            </div>

            <div class="relative" data-aos="fade-left" data-aos-duration="1000">
                <div class="rounded-2xl overflow-hidden border border-gray-200">
                    <img src="assets/img/posyandu-digital.jpg" alt="Posyandu Digital - Transformasi Digital Posyandu"
                        class="w-full h-64 sm:h-72 md:h-80 object-cover"
                        onerror="this.onerror=null; this.src='assets/img/hero/image-3.jpg';">
                </div>
                <div class="absolute -bottom-4 -right-4 bg-white rounded-xl shadow-lg px-5 py-3 border border-gray-200">
                    <div class="flex items-center gap-3">
                        <span class="w-3 h-3 rounded-full bg-green-500 animate-pulse"></span>
                        <span class="text-sm font-medium text-gray-700">Sistem Aktif</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="team" class="py-16 sm:py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12" data-aos="fade-up">
            <span class="section-badge">
                <i class="fas fa-users mr-1"></i> Tim Kami
            </span>
            <h2 class="section-title mt-3" data-aos="fade-up" data-aos-delay="100">
                Para Ahli di Balik <span>Sok!Anak</span>
            </h2>
            <p class="section-subtitle mt-3" data-aos="fade-up" data-aos-delay="200">
                Kombinasi keahlian di bidang teknologi dan pemberdayaan masyarakat
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="team-card" data-aos="flip-up" data-aos-delay="100">
                <img src="assets/img/team/rical.png" alt="Risnanda Pascal" class="team-avatar"
                    onerror="this.onerror=null; this.src='assets/img/team/placeholder.jpg';">
                <h4>Risnanda Pascal</h4>
                <span class="team-role">Project Leader</span>
                <p class="team-bio">Memimpin pengembangan dan arsitektur sistem Sok!Anak.</p>
                <div class="team-social">
                    <a href="https://me.ricalnet.my.id" target="_blank" aria-label="Website">
                        <i class="fas fa-link"></i>
                    </a>
                    <a href="https://www.linkedin.com/in/risnandacandraabdurrozaq/" target="_blank"
                        aria-label="LinkedIn">
                        <i class="fab fa-linkedin"></i>
                    </a>
                </div>
            </div>

            <div class="team-card" data-aos="flip-up" data-aos-delay="200">
                <img src="assets/img/team/dyd.png" alt="Denny Darlis" class="team-avatar"
                    onerror="this.onerror=null; this.src='assets/img/team/placeholder.jpg';">
                <h4>Denny Darlis</h4>
                <span class="team-role">Advisor</span>
                <p class="team-bio">Memberikan arahan strategis di bidang IoT dan elektronika.</p>
                <div class="team-social">
                    <a href="https://scholar.google.com/citations?user=vh5vR6EAAAAJ&hl=en" target="_blank"
                        aria-label="Google Scholar">
                        <i class="fas fa-graduation-cap"></i>
                    </a>
                    <a href="https://www.linkedin.com/in/denny-darlis-422634108/" target="_blank" aria-label="LinkedIn">
                        <i class="fab fa-linkedin"></i>
                    </a>
                </div>
            </div>

            <div class="team-card" data-aos="flip-up" data-aos-delay="300">
                <img src="assets/img/team/yona.png" alt="Yona Putri Azzahra" class="team-avatar"
                    onerror="this.onerror=null; this.src='assets/img/team/placeholder.jpg';">
                <h4>Yona Putri Azzahra</h4>
                <span class="team-role">Business Engineer</span>
                <p class="team-bio">Mengembangkan strategi bisnis dan memperluas dampak produk.</p>
                <div class="team-social">
                    <a href="#" aria-label="Portfolio"><i class="fas fa-briefcase"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>

            <div class="team-card" data-aos="flip-up" data-aos-delay="400">
                <img src="assets/img/team/divia.png" alt="Divia Nuralika Namira" class="team-avatar"
                    onerror="this.onerror=null; this.src='assets/img/team/placeholder.jpg';">
                <h4>Divia Nuralika Namira</h4>
                <span class="team-role">Community Development</span>
                <p class="team-bio">Membangun komunitas dan mendorong adopsi teknologi di posyandu.</p>
                <div class="team-social">
                    <a href="#" aria-label="Website"><i class="fas fa-book"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-16 sm:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-10">
            <div data-aos="fade-right">
                <span class="section-badge mb-4"
                    style="background: rgba(226, 0, 26, 0.08); color: #e2001a; border-color: rgba(226, 0, 26, 0.12);">
                    <i class="fas fa-microchip mr-1"></i> Teknologi
                </span>
                <h2 class="section-title mb-4">
                    Inovasi Teknologi untuk <span>Kesehatan Anak</span>
                </h2>
                <p class="text-gray-600 leading-relaxed mb-6">
                    Sok!Anak menggabungkan tiga pilar teknologi utama untuk memberikan solusi
                    yang komprehensif bagi posyandu.
                </p>
                <div class="space-y-4">
                    <div class="tech-card">
                        <div class="tech-icon-box" style="background: rgba(226, 0, 26, 0.1); color: #e2001a;">
                            <i class="fas fa-satellite-dish"></i>
                        </div>
                        <div>
                            <h4>IoT Sensor</h4>
                            <p>Sensor presisi untuk pengukuran berat dan tinggi badan secara otomatis</p>
                        </div>
                    </div>
                    <div class="tech-card">
                        <div class="tech-icon-box" style="background: rgba(0, 102, 204, 0.1); color: #0066cc;">
                            <i class="fas fa-brain"></i>
                        </div>
                        <div>
                            <h4>AI & Machine Learning</h4>
                            <p>Analisis data untuk rekomendasi intervensi gizi yang tepat sasaran</p>
                        </div>
                    </div>
                    <div class="tech-card">
                        <div class="tech-icon-box" style="background: rgba(5, 150, 105, 0.1); color: #059669;">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div>
                            <h4>Keamanan Data</h4>
                            <p>Server on-premise dengan enkripsi AES-256 untuk privasi maksimal</p>
                        </div>
                    </div>
                </div>
            </div>

            <div data-aos="fade-left" data-aos-duration="1000">
                <span class="section-badge mb-4"
                    style="background: rgba(0, 102, 204, 0.08); color: #0066cc; border-color: rgba(0, 102, 204, 0.12);">
                    <i class="fas fa-star mr-1"></i> Nilai Kami
                </span>
                <h2 class="section-title mb-4">
                    Prinsip yang <span style="color: #0066cc;">Kami Pegang</span>
                </h2>
                <p class="text-gray-600 leading-relaxed mb-6">
                    Setiap keputusan dan inovasi yang kami hadirkan selalu berlandaskan pada nilai-nilai berikut:
                </p>
                <div class="space-y-3">
                    <div class="value-item">
                        <i class="fas fa-hand-holding-heart text-red-600"></i>
                        <div>
                            <h4>Berpihak pada Anak</h4>
                            <p>Setiap fitur dirancang untuk memberikan manfaat maksimal bagi tumbuh kembang anak</p>
                        </div>
                    </div>
                    <div class="value-item">
                        <i class="fas fa-lightbulb text-blue-600"></i>
                        <div>
                            <h4>Inovasi Berkelanjutan</h4>
                            <p>Terus mengembangkan solusi terbaik dengan teknologi terkini</p>
                        </div>
                    </div>
                    <div class="value-item">
                        <i class="fas fa-users text-purple-600"></i>
                        <div>
                            <h4>Kolaborasi & Pemberdayaan</h4>
                            <p>Bersama kader posyandu, ahli gizi, dan masyarakat untuk hasil terbaik</p>
                        </div>
                    </div>
                    <div class="value-item">
                        <i class="fas fa-lock text-green-600"></i>
                        <div>
                            <h4>Keamanan & Privasi</h4>
                            <p>Menjaga kerahasiaan data anak dan posyandu dengan standar keamanan tertinggi</p>
                        </div>
                    </div>
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
            Bergabunglah dalam Gerakan <br class="hidden sm:block"><span
                style="color: #fca5a5;">#AnakSehatIndonesia</span>
        </h2>
        <p class="text-base sm:text-lg text-white/90 max-w-2xl mx-auto mb-8" data-aos="fade-up" data-aos-delay="200">
            Mari bersama wujudkan generasi emas Indonesia bebas stunting dengan teknologi tepat guna di setiap posyandu.
        </p>
        <div class="flex flex-wrap justify-center gap-3 sm:gap-4" data-aos="fade-up" data-aos-delay="300">
            <a href="signup.php" class="inline-flex items-center gap-2 px-8 py-3.5 bg-white text-red-600 rounded-xl font-semibold 
                      hover:bg-red-50 transition-all duration-300 transform hover:-translate-y-1 
                      shadow-lg hover:shadow-2xl text-sm sm:text-base btn-glow">
                <i class="fas fa-user-plus"></i>
                Daftar Sekarang
            </a>
            <a href="bantuan.php" class="inline-flex items-center gap-2 px-8 py-3.5 bg-white/10 backdrop-blur-sm text-white 
                      rounded-xl font-semibold border border-white/30 hover:bg-white/20 
                      transition-all duration-300 text-sm sm:text-base">
                <i class="fas fa-question-circle"></i>
                Hubungi Kami
            </a>
        </div>
        <p class="mt-6 text-sm text-white/70" data-aos="fade-up" data-aos-delay="400">
            <i class="fas fa-clock mr-1"></i> Tim support siap membantu 07:00 - 17:00 WIB
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