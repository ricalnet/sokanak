</main>

<footer class="footer" role="contentinfo">
    <div class="footer-container">
        <div class="footer-main">
            <div class="footer-brand">
                <div class="footer-logo">
                    <img src="assets/img/logo.png" alt="AIoT Sok!Anak Logo"
                        onerror="this.onerror=null; this.src='assets/img/logo.png';">
                    <span>AIoT Sok!Anak</span>
                </div>
                <p class="footer-description">
                    Sistem Observasi Kesehatan Anak berbasis digital untuk membantu petugas posyandu
                    dalam memantau perkembangan gizi anak secara akurat dan terintegrasi.
                </p>
                <div class="footer-social">
                    <a href="https://sokanak.id" target="_blank" aria-label="Website Sok!Anak">
                        <i class="fas fa-globe"></i>
                    </a>
                    <a href="https://www.instagram.com/aiot_sokanak" target="_blank" aria-label="Instagram Sok!Anak">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://youtube.com/@aiotsokanak" target="_blank" aria-label="YouTube Sok!Anak">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a href="bantuan.php" aria-label="Pusat Bantuan">
                        <i class="fas fa-question-circle"></i>
                    </a>
                </div>
            </div>

            <div class="footer-links">
                <h4 class="footer-heading">Navigasi</h4>
                <ul>
                    <li><a href="dashboard.php"><i class="fas fa-chevron-right"></i> Dashboard</a></li>
                    <li><a href="anak.php"><i class="fas fa-chevron-right"></i> Data Anak</a></li>
                    <li><a href="input-pengukuran.php"><i class="fas fa-chevron-right"></i> Pengukuran</a></li>
                    <li><a href="laporan-bulanan.php"><i class="fas fa-chevron-right"></i> Laporan Bulanan</a></li>
                </ul>
            </div>

            <div class="footer-links">
                <h4 class="footer-heading">Bantuan</h4>
                <ul>
                    <li><a href="bantuan.php"><i class="fas fa-chevron-right"></i> Pusat Bantuan</a></li>
                    <li><a href="about.php"><i class="fas fa-chevron-right"></i> Tentang Kami</a></li>
                    <li><a href="privacy.php"><i class="fas fa-chevron-right"></i> Kebijakan Privasi</a></li>
                    <li><a href="terms.php"><i class="fas fa-chevron-right"></i> Syarat &amp; Ketentuan</a></li>
                </ul>
            </div>

            <div class="footer-contact">
                <h4 class="footer-heading">Kontak</h4>
                <ul>
                    <li>
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Desa Sukarame 40385, Bandung, Indonesia</span>
                    </li>
                    <li>
                        <i class="fas fa-envelope"></i>
                        <a href="mailto:sokanak@duck.com">sokanak@duck.com</a>
                    </li>
                    <li>
                        <i class="fas fa-clock"></i>
                        <span>Senin - Minggu, 07:00 - 17:00 WIB</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="footer-powered">
            <p class="powered-label">Didukung oleh</p>
            <div class="powered-logos">
                <img src="assets/img/innovillage.png" alt="Innovillage"
                    onerror="this.onerror=null; this.src='assets/img/innovillage.png';">
                <img src="assets/img/telkom-indonesia.png" alt="Telkom Indonesia"
                    onerror="this.onerror=null; this.src='assets/img/telkom-indonesia.png';">
                <img src="assets/img/telyu.png" alt="Telkom University"
                    onerror="this.onerror=null; this.src='assets/img/telyu.png';">
            </div>
        </div>

        <div class="footer-bottom">
            <div class="footer-bottom-left">
                <p>&copy; <?php echo date('Y'); ?> AIoT Sok!Anak. Hak Cipta Dilindungi.</p>
                <p class="footer-tagline">Sistem untuk mendukung program kesehatan anak Indonesia</p>
            </div>
            <div class="footer-bottom-right">
                <span class="footer-badge">
                    <i class="fas fa-heart"></i> Proyek Sosial
                </span>
                <span class="footer-version">Versi 0.9</span>
            </div>
        </div>
    </div>
</footer>

<style>
    .footer {
        background: #0f172a;
        color: #e2e8f0;
        border-top: 1px solid rgba(255, 255, 255, 0.05);
        margin-top: 2rem;
        position: relative;
        overflow: hidden;
    }

    .footer::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(226, 0, 26, 0.04), transparent 70%);
        pointer-events: none;
        border-radius: 50%;
    }

    .footer-container {
        max-width: 1280px;
        margin: 0 auto;
        padding: 3rem 1.5rem 1.5rem;
        position: relative;
        z-index: 1;
    }

    .footer-main {
        display: grid;
        grid-template-columns: 1fr;
        gap: 2.5rem;
        padding-bottom: 2.5rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }

    @media (min-width: 640px) {
        .footer-main {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (min-width: 1024px) {
        .footer-main {
            grid-template-columns: 2fr 1fr 1fr 1.5fr;
            gap: 3rem;
        }
    }

    .footer-brand {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .footer-logo {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .footer-logo img {
        height: 40px;
        width: auto;
        transition: transform 0.3s ease;
    }

    .footer-logo img:hover {
        transform: scale(1.05);
    }

    .footer-logo span {
        font-size: 1.25rem;
        font-weight: 700;
        color: white;
        letter-spacing: -0.01em;
    }

    .footer-logo span .text-red {
        color: #e2001a;
    }

    .footer-logo span .text-blue {
        color: #0066cc;
    }

    .footer-description {
        font-size: 0.875rem;
        color: #94a3b8;
        line-height: 1.7;
        max-width: 400px;
    }

    .footer-social {
        display: flex;
        gap: 0.75rem;
        margin-top: 0.25rem;
    }

    .footer-social a {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.06);
        color: #94a3b8;
        transition: all 0.3s ease;
        text-decoration: none;
        font-size: 1rem;
    }

    .footer-social a:hover {
        background: #e2001a;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(226, 0, 26, 0.3);
        text-decoration: none;
    }

    .footer-links {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .footer-heading {
        font-size: 0.8125rem;
        font-weight: 600;
        color: white;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 0.25rem;
    }

    .footer-links ul {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .footer-links ul li a {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #94a3b8;
        font-size: 0.875rem;
        text-decoration: none;
        transition: all 0.2s ease;
        padding: 0.25rem 0;
    }

    .footer-links ul li a i {
        font-size: 0.5rem;
        color: #475569;
        transition: all 0.2s ease;
    }

    .footer-links ul li a:hover {
        color: white;
        transform: translateX(4px);
        text-decoration: none;
    }

    .footer-links ul li a:hover i {
        color: #e2001a;
    }

    .footer-contact ul {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .footer-contact ul li {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        font-size: 0.875rem;
        color: #94a3b8;
        line-height: 1.5;
    }

    .footer-contact ul li i {
        color: #64748b;
        font-size: 0.875rem;
        margin-top: 0.125rem;
        flex-shrink: 0;
        width: 18px;
        text-align: center;
        transition: color 0.2s ease;
    }

    .footer-contact ul li:hover i {
        color: #e2001a;
    }

    .footer-contact ul li a {
        color: #94a3b8;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .footer-contact ul li a:hover {
        color: white;
        text-decoration: none;
    }

    .footer-powered {
        padding: 2rem 0 1.5rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        text-align: center;
    }

    .powered-label {
        font-size: 0.6875rem;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        color: #64748b;
        margin-bottom: 1.25rem;
        font-weight: 500;
    }

    .powered-logos {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: center;
        gap: 2.5rem;
    }

    .powered-logos img {
        height: 36px;
        width: auto;
        filter: grayscale(1) brightness(0.7);
        opacity: 0.5;
        transition: all 0.4s ease;
        object-fit: contain;
    }

    .powered-logos img:hover {
        filter: grayscale(0) brightness(1);
        opacity: 1;
        transform: scale(1.05);
    }

    @media (min-width: 768px) {
        .powered-logos img {
            height: 44px;
        }
    }

    .footer-bottom {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        padding-top: 1.5rem;
        text-align: center;
    }

    @media (min-width: 768px) {
        .footer-bottom {
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            text-align: left;
        }
    }

    .footer-bottom-left {
        display: flex;
        flex-direction: column;
        gap: 0.125rem;
    }

    .footer-bottom-left p {
        font-size: 0.8125rem;
        color: #64748b;
        margin: 0;
    }

    .footer-tagline {
        font-size: 0.75rem !important;
        color: #475569 !important;
    }

    .footer-bottom-right {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
        justify-content: center;
    }

    .footer-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.6875rem;
        font-weight: 500;
        background: rgba(226, 0, 26, 0.15);
        color: #fca5a5;
        border: 1px solid rgba(226, 0, 26, 0.2);
    }

    .footer-badge i {
        font-size: 0.625rem;
        color: #f87171;
    }

    .footer-version {
        font-size: 0.6875rem;
        color: #475569;
        background: rgba(255, 255, 255, 0.04);
        padding: 0.25rem 0.625rem;
        border-radius: 9999px;
        border: 1px solid rgba(255, 255, 255, 0.06);
    }

    @media (max-width: 639px) {
        .footer-container {
            padding: 2rem 1rem 1rem;
        }

        .footer-main {
            gap: 2rem;
        }

        .footer-logo img {
            height: 32px;
        }

        .footer-logo span {
            font-size: 1rem;
        }

        .footer-social a {
            width: 36px;
            height: 36px;
            font-size: 0.875rem;
        }

        .powered-logos {
            gap: 1.5rem;
        }

        .powered-logos img {
            height: 28px;
        }

        .footer-bottom-right {
            flex-direction: column;
            gap: 0.5rem;
        }
    }

    @media (max-width: 380px) {
        .footer-main {
            grid-template-columns: 1fr;
            gap: 1.75rem;
        }

        .footer-links ul li a {
            font-size: 0.8125rem;
        }

        .footer-contact ul li {
            font-size: 0.8125rem;
        }

        .footer-description {
            font-size: 0.8125rem;
        }
    }

    .footer a:focus-visible {
        outline: 2px solid #e2001a;
        outline-offset: 2px;
        border-radius: 4px;
    }
</style>

<script>
    document.querySelectorAll('.footer a[href^="#"]').forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            var target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    document.querySelectorAll('.footer a').forEach(function (link) {
        link.addEventListener('click', function () {
        });
    });
</script>
</body>

</html>