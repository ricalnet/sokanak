<?php
require_once 'config/database.php';

$error = '';
$success = '';

// ===================== FITUR DISABLE SIGNUP =====================
$signup_disabled = true; // Set ke true untuk menonaktifkan pendaftaran
// ===================== END FITUR DISABLE SIGNUP =================

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($signup_disabled) {
        $error = "Pendaftaran ditutup sementara. Silakan hubungi administrator untuk informasi lebih lanjut.";
    } else {
        $username = clean_input($_POST['username'], $conn);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $nama_lengkap = clean_input($_POST['nama_lengkap'], $conn);
        $desa = clean_input($_POST['desa'], $conn);
        $kecamatan = clean_input($_POST['kecamatan'], $conn);

        if ($password !== $confirm_password) {
            $error = "Password tidak cocok!";
        } else {
            $check_query = "SELECT id FROM users WHERE username = '$username'";
            $check_result = mysqli_query($conn, $check_query);

            if (mysqli_num_rows($check_result) > 0) {
                $error = "Username sudah digunakan!";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $query = "INSERT INTO users (username, password, nama_lengkap, desa, kecamatan, role) 
                          VALUES ('$username', '$hashed_password', '$nama_lengkap', '$desa', '$kecamatan', 'kader')";

                if (mysqli_query($conn, $query)) {
                    $success = "Pendaftaran berhasil! Silakan login.";
                } else {
                    $error = "Terjadi kesalahan: " . mysqli_error($conn);
                }
            }
        }
    }
}

$page_title = "Daftar - Sok!Anak";
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

    .signup-wrapper {
        min-height: calc(100vh - 72px);
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #fef2f2 0%, #eff6ff 100%);
        position: relative;
        overflow: hidden;
        padding: 2rem 1rem;
    }

    .signup-wrapper::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url('assets/img/bg.png') center/cover no-repeat;
        opacity: 0.05;
        z-index: 0;
        pointer-events: none;
    }

    .deco-ring {
        position: absolute;
        border-radius: 50%;
        pointer-events: none;
        opacity: 0.25;
    }

    .deco-ring-1 {
        top: -80px;
        right: -80px;
        width: 240px;
        height: 240px;
        background: radial-gradient(circle, rgba(226, 0, 26, 0.08), transparent 70%);
    }

    .deco-ring-2 {
        bottom: -60px;
        left: -60px;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(0, 102, 204, 0.06), transparent 70%);
    }

    .deco-ring-3 {
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(226, 0, 26, 0.03), transparent 70%);
    }

    .signup-card {
        position: relative;
        z-index: 1;
        width: 100%;
        max-width: 500px;
        background: white;
        border-radius: 1.5rem;
        padding: 2.5rem 2rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.12);
        border: 1px solid rgba(226, 0, 26, 0.06);
        transition: all 0.3s ease;
    }

    .signup-card:hover {
        box-shadow: 0 30px 60px -12px rgba(226, 0, 26, 0.10);
    }

    .signup-logo {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        margin-bottom: 0.5rem;
    }

    .signup-logo img {
        height: 40px;
        width: auto;
        transition: transform 0.3s ease;
    }

    .signup-logo img:hover {
        transform: scale(1.04);
    }

    .signup-logo span {
        font-size: 1.25rem;
        font-weight: 700;
        color: #0f172a;
    }

    .signup-logo span .red {
        color: #e2001a;
    }

    .signup-logo span .blue {
        color: #0066cc;
    }

    .signup-title {
        text-align: center;
        margin-bottom: 1.75rem;
    }

    .signup-title h2 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.25rem;
    }

    .signup-title p {
        font-size: 0.875rem;
        color: #94a3b8;
    }

    .alert {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.875rem 1rem;
        border-radius: 0.75rem;
        margin-bottom: 1.25rem;
        font-size: 0.875rem;
        border: 1px solid;
    }

    .alert i {
        font-size: 1rem;
        flex-shrink: 0;
        margin-top: 0.125rem;
    }

    .alert .alert-content {
        flex: 1;
    }

    .alert-error {
        background: #fef2f2;
        border-color: #fecaca;
        color: #dc2626;
    }

    .alert-success {
        background: #f0fdf4;
        border-color: #bbf7d0;
        color: #16a34a;
    }

    .alert-warning {
        background: #fffbeb;
        border-color: #fde68a;
        color: #d97706;
    }

    .alert .alert-content strong {
        display: block;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .alert .alert-content p {
        margin: 0;
        line-height: 1.6;
    }

    .form-group {
        margin-bottom: 1.125rem;
    }

    .form-group label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 0.375rem;
    }

    .form-group .input-wrapper {
        position: relative;
    }

    .form-group .input-wrapper .input-icon {
        position: absolute;
        left: 0.875rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 0.875rem;
        pointer-events: none;
        transition: color 0.3s ease;
        z-index: 1;
    }

    .form-group .input-wrapper input,
    .form-group .input-wrapper select {
        width: 100%;
        padding: 0.75rem 0.875rem 0.75rem 2.5rem;
        border: 2px solid #e2e8f0;
        border-radius: 0.75rem;
        font-size: 0.875rem;
        transition: all 0.3s ease;
        background: #f8fafc;
        color: #0f172a;
        outline: none;
        font-family: inherit;
        appearance: none;
    }

    .form-group .input-wrapper select {
        padding-right: 2.5rem;
        cursor: pointer;
    }

    .form-group .input-wrapper select:disabled {
        background: #f1f5f9;
        cursor: not-allowed;
        opacity: 0.7;
    }

    .form-group .input-wrapper input:focus,
    .form-group .input-wrapper select:focus {
        border-color: #e2001a;
        background: white;
        box-shadow: 0 0 0 4px rgba(226, 0, 26, 0.06);
    }

    .form-group .input-wrapper input:focus~.input-icon,
    .form-group .input-wrapper select:focus~.input-icon {
        color: #e2001a;
    }

    .form-group .input-wrapper input::placeholder {
        color: #94a3b8;
    }

    .form-group .input-wrapper .select-arrow {
        position: absolute;
        right: 0.875rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        pointer-events: none;
        font-size: 0.6875rem;
        z-index: 1;
    }

    .form-group .input-wrapper select:disabled~.select-arrow {
        opacity: 0.5;
    }

    .form-hint {
        display: block;
        margin-top: 0.25rem;
        font-size: 0.75rem;
        color: #94a3b8;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.875rem;
    }

    .btn-signup {
        width: 100%;
        padding: 0.75rem;
        background: linear-gradient(135deg, #e2001a, #b30015);
        color: white;
        font-weight: 600;
        font-size: 0.9375rem;
        border: none;
        border-radius: 0.75rem;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 16px rgba(226, 0, 26, 0.2);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.625rem;
        position: relative;
        overflow: hidden;
    }

    .btn-signup::before {
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

    .btn-signup:hover::before {
        opacity: 1;
        transform: scale(1);
    }

    .btn-signup:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(226, 0, 26, 0.3);
    }

    .btn-signup:active {
        transform: translateY(0);
    }

    .btn-signup:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none !important;
    }

    .signup-footer {
        text-align: center;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid #f1f5f9;
    }

    .signup-footer p {
        font-size: 0.875rem;
        color: #64748b;
    }

    .signup-footer a {
        color: #e2001a;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .signup-footer a:hover {
        color: #b30015;
        text-decoration: underline;
    }

    .security-badge {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 1.25rem;
        font-size: 0.75rem;
        color: #94a3b8;
    }

    .security-badge i {
        color: #22c55e;
    }

    .success-actions {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        margin-top: 1rem;
    }

    .btn-login-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.75rem;
        background: linear-gradient(135deg, #e2001a, #b30015);
        color: white;
        font-weight: 600;
        font-size: 0.9375rem;
        border: none;
        border-radius: 0.75rem;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        box-shadow: 0 4px 16px rgba(226, 0, 26, 0.2);
    }

    .btn-login-link:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(226, 0, 26, 0.3);
        color: white;
        text-decoration: none;
    }

    @media (max-width: 480px) {
        .signup-card {
            padding: 1.75rem 1.25rem;
            border-radius: 1.25rem;
        }

        .signup-title h2 {
            font-size: 1.25rem;
        }

        .signup-logo img {
            height: 32px;
        }

        .signup-logo span {
            font-size: 1rem;
        }

        .form-group .input-wrapper input,
        .form-group .input-wrapper select {
            padding: 0.625rem 0.75rem 0.625rem 2.25rem;
            font-size: 0.8125rem;
        }

        .form-row {
            grid-template-columns: 1fr;
            gap: 0;
        }

        .signup-wrapper {
            padding: 1rem 0.75rem;
            min-height: calc(100vh - 64px);
        }

        .deco-ring-1 {
            width: 160px;
            height: 160px;
            top: -60px;
            right: -60px;
        }

        .deco-ring-2 {
            width: 140px;
            height: 140px;
            bottom: -40px;
            left: -40px;
        }
    }

    @media (max-width: 380px) {
        .signup-card {
            padding: 1.25rem 0.875rem;
        }

        .signup-title h2 {
            font-size: 1.125rem;
        }

        .btn-signup {
            font-size: 0.875rem;
            padding: 0.625rem;
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
</style>

<div class="signup-wrapper">
    <div class="deco-ring deco-ring-1"></div>
    <div class="deco-ring deco-ring-2"></div>
    <div class="deco-ring deco-ring-3"></div>

    <div class="signup-card" data-aos="fade-up" data-aos-duration="800">
        <div class="signup-logo" data-aos="fade-up" data-aos-delay="100">
            <img src="assets/img/logo.png" alt="Sok!Anak Logo"
                onerror="this.onerror=null; this.src='assets/img/logo.png';">
            <span>AIoT <span class="red">Sok!Anak</span></span>
        </div>

        <div class="signup-title" data-aos="fade-up" data-aos-delay="150">
            <h2>Daftar Akun Kader</h2>
            <p>Untuk kader posyandu di wilayah masing-masing</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error" data-aos="fade-up" data-aos-delay="200">
                <i class="fas fa-exclamation-circle"></i>
                <div class="alert-content"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success" data-aos="fade-up" data-aos-delay="200">
                <i class="fas fa-check-circle"></i>
                <div class="alert-content">
                    <?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?>
                    <div class="success-actions">
                        <a href="login.php" class="btn-login-link">
                            <i class="fas fa-sign-in-alt"></i>
                            Login ke Akun Anda
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($signup_disabled && !$success): ?>
            <div class="alert alert-warning" data-aos="fade-up" data-aos-delay="200">
                <i class="fas fa-lock"></i>
                <div class="alert-content">
                    <strong>Pendaftaran Ditutup Sementara</strong>
                    <p>Untuk menjaga keamanan dan kontrol akses, pendaftaran akun kader baru ditutup sementara.</p>
                    <div class="mt-4 space-y-2">
                        <div class="flex items-start gap-2 text-sm">
                            <i class="fas fa-info-circle text-yellow-600 mt-0.5"></i>
                            <span>Jika Anda adalah kader posyandu yang berwenang, hubungi administrator sistem untuk
                                mendapatkan akses.</span>
                        </div>
                        <div class="flex items-start gap-2 text-sm">
                            <i class="fas fa-user-shield text-yellow-600 mt-0.5"></i>
                            <span>Fitur ini memastikan hanya kader terverifikasi yang dapat mengakses data kesehatan
                                anak.</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center" data-aos="fade-up" data-aos-delay="300">
                <a href="login.php" class="btn-signup">
                    <i class="fas fa-sign-in-alt"></i>
                    Login ke Akun Anda
                </a>

                <div class="signup-footer">
                    <p>
                        Sudah punya akun?
                        <a href="login.php">Login di sini</a>
                    </p>
                </div>
            </div>

        <?php else: ?>
            <?php if (!$success): ?>
                <form method="POST" action="" data-aos="fade-up" data-aos-delay="250">
                    <div class="form-group">
                        <label for="nama_lengkap">Nama Lengkap</label>
                        <div class="input-wrapper">
                            <input type="text" id="nama_lengkap" name="nama_lengkap" placeholder="Masukkan nama lengkap Anda"
                                value="<?php echo htmlspecialchars($_POST['nama_lengkap'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                required>
                            <i class="fas fa-user input-icon"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="username">Username</label>
                        <div class="input-wrapper">
                            <input type="text" id="username" name="username" placeholder="Masukkan username Anda"
                                value="<?php echo htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required
                                minlength="4">
                            <i class="fas fa-at input-icon"></i>
                        </div>
                        <span class="form-hint">Minimal 4 karakter, tanpa spasi</span>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">Password</label>
                            <div class="input-wrapper">
                                <input type="password" id="password" name="password" placeholder="Masukkan password" required
                                    minlength="6">
                                <i class="fas fa-lock input-icon"></i>
                            </div>
                            <span class="form-hint">Minimal 6 karakter</span>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Konfirmasi Password</label>
                            <div class="input-wrapper">
                                <input type="password" id="confirm_password" name="confirm_password"
                                    placeholder="Ulangi password" required>
                                <i class="fas fa-lock input-icon"></i>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="kecamatan">Kecamatan</label>
                            <div class="input-wrapper">
                                <select id="kecamatan" name="kecamatan" required>
                                    <option value="">Pilih Kecamatan</option>
                                    <option value="Pacet" <?php echo (isset($_POST['kecamatan']) && $_POST['kecamatan'] == 'Pacet') ? 'selected' : ''; ?>>Pacet</option>
                                    <option value="Lainnya" <?php echo (isset($_POST['kecamatan']) && $_POST['kecamatan'] == 'Lainnya') ? 'selected' : ''; ?>>Lainnya</option>
                                </select>
                                <i class="fas fa-map-marker-alt input-icon"></i>
                                <i class="fas fa-chevron-down select-arrow"></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="desa">Desa</label>
                            <div class="input-wrapper">
                                <select id="desa" name="desa" required>
                                    <option value="">Pilih Desa</option>
                                </select>
                                <i class="fas fa-home input-icon"></i>
                                <i class="fas fa-chevron-down select-arrow"></i>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-signup">
                        <i class="fas fa-user-plus"></i>
                        Daftar Akun Kader
                    </button>
                </form>

                <div class="signup-footer" data-aos="fade-up" data-aos-delay="300">
                    <p>
                        Sudah punya akun?
                        <a href="login.php">Login di sini</a>
                    </p>
                </div>

                <div class="security-badge" data-aos="fade-up" data-aos-delay="350">
                    <i class="fas fa-shield-alt"></i>
                    <span>Data terenkripsi • Hanya untuk kader terverifikasi</span>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<script>
    AOS.init({
        duration: 700,
        once: true,
        offset: 20,
        easing: 'ease-out-cubic',
        disable: false
    });

    <?php if (!$signup_disabled && !$success): ?>
        document.addEventListener('DOMContentLoaded', function () {
            const kecamatanSelect = document.getElementById('kecamatan');
            const desaSelect = document.getElementById('desa');

            const desaByKecamatan = {
                'Pacet': ['Sukarame', 'Cikawao', 'Cikitu', 'Cibodas', 'Mekarjaya'],
                'Lainnya': ['Desa Lainnya']
            };

            function updateDesa() {
                const selectedKecamatan = kecamatanSelect.value;
                desaSelect.innerHTML = '<option value="">Pilih Desa</option>';

                if (selectedKecamatan && desaByKecamatan[selectedKecamatan]) {
                    desaByKecamatan[selectedKecamatan].forEach(function (desa) {
                        const option = document.createElement('option');
                        option.value = desa;
                        option.textContent = desa;
                        <?php if (isset($_POST['desa'])): ?>
                            if (desa === '<?php echo htmlspecialchars($_POST['desa'], ENT_QUOTES); ?>') {
                                option.selected = true;
                            }
                        <?php endif; ?>
                        desaSelect.appendChild(option);
                    });
                    desaSelect.disabled = false;
                } else {
                    desaSelect.disabled = true;
                }
            }

            <?php if (isset($_POST['kecamatan']) && !empty($_POST['kecamatan'])): ?>
                setTimeout(updateDesa, 100);
            <?php endif; ?>

            kecamatanSelect.addEventListener('change', updateDesa);
        });
    <?php endif; ?>
</script>

<?php include 'includes/footer.php'; ?>