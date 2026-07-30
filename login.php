<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

require_once 'config/database.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = "Permintaan tidak valid. Silakan coba lagi.";
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $error = "Harap isi username dan password";
        } elseif (strlen($username) > 50 || strlen($password) > 255) {
            $error = "Input terlalu panjang";
        } elseif (!preg_match('/^[a-zA-Z0-9_\.\-@]+$/', $username)) {
            $error = "Username hanya boleh berisi huruf, angka, dan karakter khusus (_ . - @)";
        } else {
            if (!isset($_SESSION['login_attempts'])) {
                $_SESSION['login_attempts'] = 0;
                $_SESSION['last_attempt'] = time();
                $_SESSION['blocked_until'] = 0;
            }

            if (isset($_SESSION['blocked_until']) && time() < $_SESSION['blocked_until']) {
                $remaining = $_SESSION['blocked_until'] - time();
                $error = "Terlalu banyak percobaan. Tunggu " . ceil($remaining / 60) . " menit lagi.";
            } else {
                if (time() - $_SESSION['last_attempt'] > 900) {
                    $_SESSION['login_attempts'] = 0;
                    $_SESSION['blocked_until'] = 0;
                }

                if ($_SESSION['login_attempts'] >= 5) {
                    $_SESSION['blocked_until'] = time() + 900;
                    $error = "Terlalu banyak percobaan. Coba lagi dalam 15 menit.";
                } else {
                    $stmt = $conn->prepare("SELECT id, username, password, nama_lengkap, desa, kecamatan, role, status, last_login, login_attempts, last_attempt, must_change_password FROM users WHERE username = ?");
                    $stmt->bind_param("s", $username);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows == 1) {
                        $user = $result->fetch_assoc();

                        if ($user['status'] !== 'active') {
                            $_SESSION['login_attempts']++;
                            $_SESSION['last_attempt'] = time();
                            $error = "Akun tidak aktif. Hubungi administrator.";
                        } elseif (!password_verify($password, $user['password'])) {
                            $_SESSION['login_attempts']++;
                            $_SESSION['last_attempt'] = time();
                            $error = "Username atau password salah";
                        } else {
                            session_regenerate_id(true);
                            $_SESSION = [];

                            $_SESSION['user_id'] = $user['id'];
                            $_SESSION['username'] = $user['username'];
                            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                            $_SESSION['desa'] = $user['desa'];
                            $_SESSION['kecamatan'] = $user['kecamatan'];
                            $_SESSION['role'] = $user['role'];
                            $_SESSION['login_time'] = time();
                            $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
                            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];

                            unset($_SESSION['login_attempts']);
                            unset($_SESSION['last_attempt']);
                            unset($_SESSION['blocked_until']);

                            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

                            $update_stmt = $conn->prepare("UPDATE users SET last_login = NOW(), login_attempts = 0, last_attempt = NULL WHERE id = ?");
                            $update_stmt->bind_param("i", $user['id']);
                            $update_stmt->execute();
                            $update_stmt->close();

                            if ($conn && isset($user['id'])) {
                                $log_stmt = $conn->prepare("INSERT INTO logs (user_id, action, table_name, record_id, ip_address, user_agent) VALUES (?, 'login', 'users', ?, ?, ?)");
                                $log_stmt->bind_param("iiss", $user['id'], $user['id'], $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
                                $log_stmt->execute();
                                $log_stmt->close();
                            }

                            header("Location: dashboard.php");
                            exit();
                        }
                    } else {
                        $_SESSION['login_attempts']++;
                        $_SESSION['last_attempt'] = time();
                        $error = "Username atau password salah";
                    }
                    $stmt->close();
                }
            }
        }
    }
}

$page_title = "Login - Sok!Anak";
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

    .login-wrapper {
        min-height: calc(100vh - 72px);
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #fef2f2 0%, #eff6ff 100%);
        position: relative;
        overflow: hidden;
        padding: 2rem 1rem;
    }

    .login-wrapper::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url('assets/img/hero/image-1.jpg') center/cover no-repeat;
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

    .login-card {
        position: relative;
        z-index: 1;
        width: 100%;
        max-width: 420px;
        background: white;
        border-radius: 1.5rem;
        padding: 2.5rem 2rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.12);
        border: 1px solid rgba(226, 0, 26, 0.06);
        transition: all 0.3s ease;
    }

    .login-card:hover {
        box-shadow: 0 30px 60px -12px rgba(226, 0, 26, 0.10);
    }

    .login-logo {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        margin-bottom: 0.5rem;
    }

    .login-logo img {
        height: 40px;
        width: auto;
        transition: transform 0.3s ease;
    }

    .login-logo img:hover {
        transform: scale(1.04);
    }

    .login-logo span {
        font-size: 1.25rem;
        font-weight: 700;
        color: #0f172a;
    }

    .login-logo span .red {
        color: #e2001a;
    }

    .login-logo span .blue {
        color: #0066cc;
    }

    .login-title {
        text-align: center;
        margin-bottom: 1.75rem;
    }

    .login-title h2 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.25rem;
    }

    .login-title p {
        font-size: 0.875rem;
        color: #94a3b8;
    }

    .error-alert {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        background: #fef2f2;
        border-radius: 0.75rem;
        border: 1px solid #fecaca;
        margin-bottom: 1.25rem;
        color: #dc2626;
        font-size: 0.875rem;
    }

    .error-alert i {
        font-size: 1rem;
        flex-shrink: 0;
    }

    .form-group {
        margin-bottom: 1.25rem;
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

    .form-group .input-wrapper input {
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
    }

    .form-group .input-wrapper input:focus {
        border-color: #e2001a;
        background: white;
        box-shadow: 0 0 0 4px rgba(226, 0, 26, 0.06);
    }

    .form-group .input-wrapper input:focus~.input-icon,
    .form-group .input-wrapper input:focus+.input-icon {
        color: #e2001a;
    }

    .form-group .input-wrapper input::placeholder {
        color: #94a3b8;
    }

    .form-group .input-wrapper input:disabled {
        background: #f1f5f9;
        cursor: not-allowed;
    }

    .btn-login {
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

    .btn-login::before {
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

    .btn-login:hover::before {
        opacity: 1;
        transform: scale(1);
    }

    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(226, 0, 26, 0.3);
    }

    .btn-login:active {
        transform: translateY(0);
    }

    .login-footer {
        text-align: center;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid #f1f5f9;
    }

    .login-footer p {
        font-size: 0.875rem;
        color: #64748b;
    }

    .login-footer a {
        color: #e2001a;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .login-footer a:hover {
        color: #b30015;
        text-decoration: underline;
    }

    .login-footer .help-link {
        display: inline-block;
        margin-top: 0.5rem;
        font-size: 0.8125rem;
        color: #94a3b8;
        font-weight: 400;
    }

    .login-footer .help-link:hover {
        color: #64748b;
        text-decoration: none;
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

    @media (max-width: 480px) {
        .login-card {
            padding: 1.75rem 1.25rem;
            border-radius: 1.25rem;
        }

        .login-title h2 {
            font-size: 1.25rem;
        }

        .login-logo img {
            height: 32px;
        }

        .login-logo span {
            font-size: 1rem;
        }

        .form-group .input-wrapper input {
            padding: 0.625rem 0.75rem 0.625rem 2.25rem;
            font-size: 0.8125rem;
        }

        .login-wrapper {
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
        .login-card {
            padding: 1.25rem 0.875rem;
        }

        .login-title h2 {
            font-size: 1.125rem;
        }

        .btn-login {
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

<div class="login-wrapper">
    <div class="deco-ring deco-ring-1"></div>
    <div class="deco-ring deco-ring-2"></div>
    <div class="deco-ring deco-ring-3"></div>

    <div class="login-card" data-aos="fade-up" data-aos-duration="800">
        <div class="login-logo" data-aos="fade-up" data-aos-delay="100">
            <img src="assets/img/logo.png" alt="Sok!Anak Logo"
                onerror="this.onerror=null; this.src='assets/img/logo.png';">
            <span>AIoT <span class="red">Sok!Anak</span></span>
        </div>

        <div class="login-title" data-aos="fade-up" data-aos-delay="150">
            <h2>Selamat Datang Kembali</h2>
            <p>Masukkan kredensial Anda untuk melanjutkan</p>
        </div>

        <?php if ($error): ?>
            <div class="error-alert" data-aos="fade-up" data-aos-delay="200">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="" data-aos="fade-up" data-aos-delay="250">
            <input type="hidden" name="csrf_token"
                value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

            <div class="form-group">
                <label for="username">Username</label>
                <div class="input-wrapper">
                    <input type="text" id="username" name="username" placeholder="Masukkan username Anda"
                        value="<?php echo htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required
                        autofocus>
                    <i class="fas fa-user input-icon"></i>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <input type="password" id="password" name="password" placeholder="Masukkan password Anda" required>
                    <i class="fas fa-lock input-icon"></i>
                </div>
            </div>

            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i>
                Masuk ke Sistem
            </button>
        </form>

        <div class="login-footer" data-aos="fade-up" data-aos-delay="300">
            <p>
                Belum memiliki akun?
                <a href="signup.php">Daftar Sekarang</a>
            </p>
            <a href="bantuan.php" class="help-link">
                <i class="fas fa-question-circle mr-1"></i> Lupa password? Hubungi support
            </a>
        </div>

        <div class="security-badge" data-aos="fade-up" data-aos-delay="350">
            <i class="fas fa-shield-alt"></i>
            <span>Koneksi aman terenkripsi</span>
            <span class="hidden sm:inline">•</span>
            <span class="hidden sm:inline">SSL Secure</span>
        </div>
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

    document.addEventListener('DOMContentLoaded', function () {
        const usernameField = document.getElementById('username');
        if (usernameField) {
            usernameField.focus();
        }
    });
</script>

<?php include 'includes/footer.php'; ?>