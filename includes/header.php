<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>AIoT Sok!Anak - Posyandu Digital</title>
    <link rel="icon" type="image/x-icon" href="assets/img/logo.png">

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        :root {
            --primary-red: #e2001a;
            --primary-red-dark: #b30015;
            --primary-red-light: #fef2f2;
            --primary-blue: #0066cc;
            --primary-blue-dark: #0052a3;
            --primary-blue-light: #eff6ff;
            --primary-green: #059669;
            --primary-purple: #7c3aed;
            --neutral-50: #f8fafc;
            --neutral-100: #f1f5f9;
            --neutral-200: #e2e8f0;
            --neutral-300: #cbd5e1;
            --neutral-400: #94a3b8;
            --neutral-500: #64748b;
            --neutral-600: #475569;
            --neutral-700: #334155;
            --neutral-800: #1e293b;
            --neutral-900: #0f172a;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            --shadow-2xl: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
            --radius-sm: 0.5rem;
            --radius-md: 0.75rem;
            --radius-lg: 1rem;
            --radius-xl: 1.25rem;
            --radius-2xl: 1.5rem;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            padding-top: 72px;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--neutral-50);
            color: var(--neutral-900);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--primary-red-dark) 100%);
            color: white;
            padding: 0.625rem 1.5rem;
            border-radius: var(--radius-md);
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(226, 0, 26, 0.2);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
            cursor: pointer;
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
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

        .btn-primary:hover::before {
            opacity: 1;
            transform: scale(1);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(226, 0, 26, 0.3);
            color: white;
            text-decoration: none;
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-secondary {
            background: var(--neutral-100);
            color: var(--neutral-800);
            padding: 0.625rem 1.5rem;
            border-radius: var(--radius-md);
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            border: 2px solid var(--neutral-200);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-secondary:hover {
            background: white;
            border-color: var(--primary-red);
            transform: translateY(-2px);
            color: var(--neutral-900);
            text-decoration: none;
        }

        .btn-outline-light {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 1.5rem;
            background: transparent;
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: var(--radius-md);
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-outline-light:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }

        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 50;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            transition: all 0.3s ease;
            width: 100%;
        }

        .navbar-scrolled {
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
            background: rgba(255, 255, 255, 0.98);
        }

        .navbar-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 72px;
        }

        .logo-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            transition: transform 0.3s ease;
            flex-shrink: 0;
        }

        .logo-link:hover {
            transform: scale(1.02);
        }

        .logo-img {
            height: 40px;
            width: auto;
            transition: all 0.3s ease;
        }

        .logo-text {
            display: none;
        }

        .logo-text .logo-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--neutral-900);
            transition: color 0.3s ease;
            line-height: 1.2;
        }

        .logo-text .logo-title:hover {
            color: var(--primary-red);
        }

        .logo-text .logo-title .red {
            color: var(--primary-red);
        }

        .logo-text .logo-title .blue {
            color: var(--primary-blue);
        }

        .logo-text .logo-sub {
            font-size: 0.6875rem;
            color: var(--neutral-400);
            margin-top: -0.125rem;
            letter-spacing: 0.025em;
        }

        .logo-divider {
            display: none;
            width: 1px;
            height: 32px;
            background: var(--neutral-200);
        }

        @media (min-width: 1024px) {
            .logo-text {
                display: block;
            }

            .logo-divider {
                display: block;
            }
        }

        .desktop-nav {
            display: none;
            align-items: center;
            gap: 1.5rem;
        }

        @media (min-width: 768px) {
            .desktop-nav {
                display: flex;
            }
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .nav-dropdown-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--neutral-500);
            background: transparent;
            border: none;
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: all 0.2s ease;
            font-family: inherit;
        }

        .nav-dropdown-btn:hover {
            color: var(--primary-red);
            background: rgba(226, 0, 26, 0.06);
        }

        .nav-dropdown-btn i.fa-chevron-down {
            font-size: 0.625rem;
            transition: transform 0.3s ease;
        }

        .nav-dropdown-btn.active i.fa-chevron-down {
            transform: rotate(180deg);
        }

        .dropdown-menu {
            position: absolute;
            top: calc(100% + 0.5rem);
            left: 0;
            min-width: 220px;
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-2xl);
            border: 1px solid var(--neutral-100);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-8px) scale(0.98);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 0.5rem;
            z-index: 100;
            transform-origin: top left;
        }

        .dropdown-menu.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0) scale(1);
        }

        .dropdown-menu a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.625rem 0.875rem;
            border-radius: var(--radius-sm);
            font-size: 0.875rem;
            color: var(--neutral-800);
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .dropdown-menu a:hover {
            background: var(--neutral-50);
            color: var(--primary-red);
        }

        .dropdown-menu a.active {
            background: rgba(226, 0, 26, 0.06);
            color: var(--primary-red);
        }

        .dropdown-menu a i {
            width: 20px;
            text-align: center;
            color: var(--neutral-400);
            font-size: 0.875rem;
            flex-shrink: 0;
        }

        .dropdown-menu a:hover i {
            color: var(--primary-red);
        }

        .dropdown-menu .dropdown-label {
            font-size: 0.6875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--neutral-400);
            padding: 0.375rem 0.875rem;
        }

        .dropdown-divider {
            height: 1px;
            background: var(--neutral-100);
            margin: 0.25rem 0;
        }

        .dropdown-menu .dropdown-sub {
            padding-left: 2rem;
            font-size: 0.8125rem;
        }

        .user-section {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding-left: 1.5rem;
            border-left: 1px solid var(--neutral-200);
        }

        .user-info {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            line-height: 1.3;
        }

        .user-info .name {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--neutral-900);
        }

        .user-info .location {
            font-size: 0.75rem;
            color: var(--neutral-400);
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--primary-blue) 100%);
            flex-shrink: 0;
            border: 2px solid transparent;
            font-size: 1rem;
        }

        .user-avatar:hover {
            transform: scale(1.05);
            border-color: rgba(226, 0, 26, 0.2);
            box-shadow: 0 4px 12px rgba(226, 0, 26, 0.2);
        }

        .mobile-controls {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        @media (min-width: 768px) {
            .mobile-controls {
                display: none;
            }
        }

        .mobile-menu-btn {
            background: none;
            border: none;
            color: var(--neutral-500);
            font-size: 1.25rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: var(--radius-sm);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .mobile-menu-btn:hover {
            color: var(--primary-red);
            background: rgba(226, 0, 26, 0.06);
        }

        .mobile-menu {
            display: none;
            background: white;
            border-top: 1px solid var(--neutral-100);
            padding: 1rem 1.5rem 1.5rem;
            max-height: calc(100vh - 72px);
            overflow-y: auto;
        }

        .mobile-menu.open {
            display: block;
        }

        .mobile-menu .mobile-nav-item {
            display: block;
            padding: 0.75rem 1rem;
            border-radius: var(--radius-sm);
            color: var(--neutral-800);
            text-decoration: none;
            transition: all 0.2s ease;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .mobile-menu .mobile-nav-item:hover {
            background: var(--neutral-50);
            color: var(--primary-red);
        }

        .mobile-menu .mobile-nav-item.active {
            background: rgba(226, 0, 26, 0.06);
            color: var(--primary-red);
        }

        .mobile-menu .mobile-nav-item i {
            width: 24px;
            text-align: center;
            color: var(--neutral-400);
            margin-right: 0.75rem;
        }

        .mobile-menu .mobile-nav-item:hover i {
            color: var(--primary-red);
        }

        .mobile-menu .mobile-divider {
            height: 1px;
            background: var(--neutral-100);
            margin: 0.5rem 0;
        }

        .mobile-menu .mobile-label {
            font-size: 0.6875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--neutral-400);
            padding: 0.5rem 1rem 0.25rem;
        }

        .mobile-menu .mobile-auth {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            padding-top: 0.75rem;
            border-top: 1px solid var(--neutral-100);
            margin-top: 0.5rem;
        }

        .mobile-menu .mobile-auth .btn-primary,
        .mobile-menu .mobile-auth .btn-secondary {
            justify-content: center;
            width: 100%;
        }

        .mobile-menu .mobile-user {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 1rem;
            border-radius: var(--radius-sm);
            background: var(--neutral-50);
            margin-bottom: 0.5rem;
        }

        .mobile-menu .mobile-user .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-red), var(--primary-blue));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .mobile-menu .mobile-user .info .name {
            font-weight: 600;
            color: var(--neutral-900);
            font-size: 0.875rem;
        }

        .mobile-menu .mobile-user .info .role {
            font-size: 0.75rem;
            color: var(--neutral-400);
        }

        @media (max-width: 768px) {
            body {
                padding-top: 64px;
            }

            .user-info {
                display: none;
            }

            .navbar-container {
                height: 64px;
                padding: 0 1rem;
            }

            .logo-img {
                height: 32px;
            }

            .user-avatar {
                width: 36px;
                height: 36px;
                font-size: 0.875rem;
            }

            .mobile-menu {
                max-height: calc(100vh - 64px);
            }
        }

        @media (max-width: 480px) {
            body {
                padding-top: 56px;
            }

            .navbar-container {
                height: 56px;
                padding: 0 0.75rem;
            }

            .logo-img {
                height: 28px;
            }

            .user-avatar {
                width: 32px;
                height: 32px;
                font-size: 0.75rem;
            }

            .mobile-menu {
                padding: 0.75rem 1rem 1rem;
                max-height: calc(100vh - 56px);
            }

            .btn-primary,
            .btn-secondary {
                padding: 0.5rem 1rem;
                font-size: 0.8125rem;
            }
        }

        .focus-ring:focus-visible {
            outline: 2px solid var(--primary-red);
            outline-offset: 2px;
        }

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--neutral-100);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--neutral-300);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--neutral-400);
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
    </style>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
</head>

<body>
    <nav class="navbar" id="navbar" role="navigation" aria-label="Navigasi Utama">
        <div class="navbar-container">
            <a href="<?php echo isset($_SESSION['user_id']) ? 'dashboard.php' : 'index.php'; ?>" class="logo-link"
                aria-label="Beranda Sok!Anak">
                <img src="assets/img/logo.png" alt="AIoT Sok!Anak Logo" class="logo-img">
                <div class="logo-divider"></div>
                <div class="logo-text">
                    <div class="logo-title">
                        <span>Sok!Anak</span>
                    </div>
                    <div class="logo-sub">Sistem Observasi Kesehatan Anak</div>
                </div>
            </a>

            <div class="desktop-nav">
                <div class="nav-links">
                    <div class="relative" id="menuDropdown">
                        <button type="button" class="nav-dropdown-btn" id="menuDropdownBtn" aria-haspopup="true"
                            aria-expanded="false">
                            <i class="fas fa-list"></i>
                            <span>Menu</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="dropdown-menu" id="menuDropdownMenu" role="menu">
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <a href="dashboard.php" role="menuitem"
                                    class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                                    <i class="fas fa-chart-line"></i> Dashboard
                                </a>
                                <a href="anak.php" role="menuitem"
                                    class="<?php echo basename($_SERVER['PHP_SELF']) == 'anak.php' ? 'active' : ''; ?>">
                                    <i class="fas fa-child"></i> Data Anak
                                </a>
                                <a href="input-pengukuran.php" role="menuitem"
                                    class="<?php echo basename($_SERVER['PHP_SELF']) == 'input-pengukuran.php' ? 'active' : ''; ?>">
                                    <i class="fas fa-weight-scale"></i> Pengukuran
                                </a>
                                <a href="laporan-bulanan.php" role="menuitem"
                                    class="<?php echo basename($_SERVER['PHP_SELF']) == 'laporan-bulanan.php' ? 'active' : ''; ?>">
                                    <i class="fas fa-clipboard-list"></i> Laporan Bulanan
                                </a>
                                <div class="dropdown-divider"></div>
                            <?php endif; ?>
                            <a href="index.php" role="menuitem"
                                class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                                <i class="fas fa-house-chimney"></i> Beranda
                            </a>
                            <a href="bantuan.php" role="menuitem"
                                class="<?php echo basename($_SERVER['PHP_SELF']) == 'bantuan.php' ? 'active' : ''; ?>">
                                <i class="fas fa-question-circle"></i> Pusat Bantuan
                            </a>
                            <a href="about.php" role="menuitem"
                                class="<?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>">
                                <i class="fas fa-info-circle"></i> Tentang
                            </a>
                        </div>
                    </div>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="relative" id="layananDropdown">
                            <button type="button" class="nav-dropdown-btn" id="layananDropdownBtn" aria-haspopup="true"
                                aria-expanded="false">
                                <i class="fas fa-bullseye"></i>
                                <span>Layanan</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="dropdown-menu" id="layananDropdownMenu" role="menu">
                                <a href="https://ai.ricalnet.my.id" role="menuitem" target="_blank">
                                    <i class="fas fa-brain"></i>
                                    <div>
                                        <div class="font-medium">#TanyaGizi with AI</div>
                                        <div class="text-xs text-gray-500 font-normal">Analisis lanjutan perkembangan gizi
                                        </div>
                                    </div>
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="https://sokanak.id" role="menuitem" target="_blank">
                                    <i class="fas fa-heartbeat"></i>
                                    <div>
                                        <div class="font-medium">Layanan Sok!Anak</div>
                                        <div class="text-xs text-gray-500 font-normal">Dukung pertumbuhan gizi</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="user-section">
                        <div class="user-info">
                            <span
                                class="name"><?php echo htmlspecialchars($_SESSION['nama_lengkap'] ?? 'User', ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="location">
                                <i class="fas fa-map-marker-alt"></i>
                                <?php echo htmlspecialchars(($_SESSION['desa'] ?? '') . ', ' . ($_SESSION['kecamatan'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                        </div>
                        <div class="relative" id="userDropdown">
                            <button type="button" class="user-avatar" id="userDropdownBtn" aria-haspopup="true"
                                aria-expanded="false" aria-label="Menu pengguna">
                                <?php
                                $initial = isset($_SESSION['nama_lengkap']) ? strtoupper(substr($_SESSION['nama_lengkap'], 0, 1)) : 'U';
                                echo htmlspecialchars($initial, ENT_QUOTES, 'UTF-8');
                                ?>
                            </button>
                            <div class="dropdown-menu" id="userDropdownMenu" role="menu"
                                style="right:0; left:auto; min-width:200px;">
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($_SESSION['nama_lengkap'] ?? 'User', ENT_QUOTES, 'UTF-8'); ?>
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        <span
                                            class="capitalize"><?php echo htmlspecialchars($_SESSION['role'] ?? 'kader', ENT_QUOTES, 'UTF-8'); ?></span>
                                    </p>
                                </div>
                                <a href="settings.php" role="menuitem">
                                    <i class="fas fa-cog"></i> Pengaturan
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="logout.php" role="menuitem" style="color:#dc2626;">
                                    <i class="fas fa-sign-out-alt"></i> Keluar
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="flex items-center gap-3">
                        <a href="login.php" class="btn-secondary">
                            <i class="fas fa-sign-in-alt"></i> Masuk
                        </a>
                        <a href="signup.php" class="btn-primary">
                            <i class="fas fa-user-plus"></i> Daftar
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="mobile-controls">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="relative" id="mobileUserDropdown">
                        <button type="button" class="user-avatar" id="mobileUserDropdownBtn"
                            style="width:36px;height:36px;font-size:0.875rem;" aria-haspopup="true" aria-expanded="false"
                            aria-label="Menu pengguna">
                            <?php
                            $initial = isset($_SESSION['nama_lengkap']) ? strtoupper(substr($_SESSION['nama_lengkap'], 0, 1)) : 'U';
                            echo htmlspecialchars($initial, ENT_QUOTES, 'UTF-8');
                            ?>
                        </button>
                        <div class="dropdown-menu" id="mobileUserDropdownMenu" role="menu"
                            style="right:0; left:auto; min-width:180px;">
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="text-sm font-medium text-gray-900">
                                    <?php echo htmlspecialchars($_SESSION['nama_lengkap'] ?? 'User', ENT_QUOTES, 'UTF-8'); ?>
                                </p>
                                <p class="text-xs text-gray-500">
                                    <span
                                        class="capitalize"><?php echo htmlspecialchars($_SESSION['role'] ?? 'kader', ENT_QUOTES, 'UTF-8'); ?></span>
                                </p>
                            </div>
                            <a href="settings.php" role="menuitem">
                                <i class="fas fa-cog"></i> Pengaturan
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="logout.php" role="menuitem" style="color:#dc2626;">
                                <i class="fas fa-sign-out-alt"></i> Keluar
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
                <button type="button" class="mobile-menu-btn" id="mobileMenuBtn" aria-expanded="false"
                    aria-label="Buka menu navigasi">
                    <i class="fas fa-bars" id="mobileMenuIcon"></i>
                </button>
            </div>
        </div>

        <div class="mobile-menu" id="mobileMenu" role="navigation" aria-label="Menu Mobile">
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="mobile-user">
                    <div class="avatar">
                        <?php
                        $initial = isset($_SESSION['nama_lengkap']) ? strtoupper(substr($_SESSION['nama_lengkap'], 0, 1)) : 'U';
                        echo htmlspecialchars($initial, ENT_QUOTES, 'UTF-8');
                        ?>
                    </div>
                    <div class="info">
                        <div class="name">
                            <?php echo htmlspecialchars($_SESSION['nama_lengkap'] ?? 'User', ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                        <div class="role">
                            <span
                                class="capitalize"><?php echo htmlspecialchars($_SESSION['role'] ?? 'kader', ENT_QUOTES, 'UTF-8'); ?></span>
                            • <?php echo htmlspecialchars($_SESSION['desa'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="dashboard.php"
                    class="mobile-nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                    <i class="fas fa-chart-line"></i> Dashboard
                </a>
                <a href="anak.php"
                    class="mobile-nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'anak.php' ? 'active' : ''; ?>">
                    <i class="fas fa-child"></i> Data Anak
                </a>
                <a href="input-pengukuran.php"
                    class="mobile-nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'input-pengukuran.php' ? 'active' : ''; ?>">
                    <i class="fas fa-weight-scale"></i> Pengukuran
                </a>
                <a href="laporan-bulanan.php"
                    class="mobile-nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'laporan-bulanan.php' ? 'active' : ''; ?>">
                    <i class="fas fa-clipboard-list"></i> Laporan Bulanan
                </a>
                <div class="mobile-divider"></div>
                <div class="mobile-label">Layanan</div>
                <a href="https://ai.ricalnet.my.id" class="mobile-nav-item" target="_blank">
                    <i class="fas fa-brain"></i> #TanyaGizi with AI
                </a>
                <a href="https://sokanak.id" class="mobile-nav-item" target="_blank">
                    <i class="fas fa-heartbeat"></i> Layanan Sok!Anak
                </a>
                <div class="mobile-divider"></div>
            <?php endif; ?>

            <a href="index.php"
                class="mobile-nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                <i class="fas fa-house-chimney"></i> Beranda
            </a>
            <a href="bantuan.php"
                class="mobile-nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'bantuan.php' ? 'active' : ''; ?>">
                <i class="fas fa-question-circle"></i> Pusat Bantuan
            </a>
            <a href="about.php"
                class="mobile-nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>">
                <i class="fas fa-info-circle"></i> Tentang
            </a>

            <?php if (!isset($_SESSION['user_id'])): ?>
                <div class="mobile-auth">
                    <a href="login.php" class="btn-secondary">
                        <i class="fas fa-sign-in-alt"></i> Masuk
                    </a>
                    <a href="signup.php" class="btn-primary">
                        <i class="fas fa-user-plus"></i> Daftar
                    </a>
                </div>
            <?php else: ?>
                <div class="mobile-auth" style="border-top: none; padding-top: 0.5rem; margin-top: 0;">
                    <a href="settings.php" class="mobile-nav-item">
                        <i class="fas fa-cog"></i> Pengaturan
                    </a>
                    <a href="logout.php" class="mobile-nav-item" style="color:#dc2626;">
                        <i class="fas fa-sign-out-alt"></i> Keluar
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 fade-in">

        <script>
            function toggleDropdown(btnId, menuId) {
                const btn = document.getElementById(btnId);
                const menu = document.getElementById(menuId);
                if (!btn || !menu) return;

                btn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const isOpen = menu.classList.contains('show');
                    document.querySelectorAll('.dropdown-menu').forEach(m => m.classList.remove('show'));
                    document.querySelectorAll('.nav-dropdown-btn').forEach(b => b.classList.remove('active'));
                    if (!isOpen) {
                        menu.classList.add('show');
                        btn.classList.add('active');
                        btn.setAttribute('aria-expanded', 'true');
                    } else {
                        btn.setAttribute('aria-expanded', 'false');
                    }
                });
            }

            toggleDropdown('menuDropdownBtn', 'menuDropdownMenu');
            toggleDropdown('layananDropdownBtn', 'layananDropdownMenu');
            toggleDropdown('userDropdownBtn', 'userDropdownMenu');

            (function () {
                const btn = document.getElementById('mobileUserDropdownBtn');
                const menu = document.getElementById('mobileUserDropdownMenu');
                if (btn && menu) {
                    btn.addEventListener('click', function (e) {
                        e.stopPropagation();
                        const isOpen = menu.classList.contains('show');
                        document.querySelectorAll('.dropdown-menu').forEach(m => m.classList.remove('show'));
                        if (!isOpen) {
                            menu.classList.add('show');
                            btn.setAttribute('aria-expanded', 'true');
                        } else {
                            btn.setAttribute('aria-expanded', 'false');
                        }
                    });
                }
            })();

            document.addEventListener('click', function (e) {
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    if (!menu.contains(e.target) && !menu.previousElementSibling?.contains(e.target)) {
                        menu.classList.remove('show');
                        const btn = menu.id.replace('Menu', 'Btn');
                        const btnEl = document.getElementById(btn);
                        if (btnEl) {
                            btnEl.classList.remove('active');
                            btnEl.setAttribute('aria-expanded', 'false');
                        }
                    }
                });
            });

            (function () {
                const btn = document.getElementById('mobileMenuBtn');
                const menu = document.getElementById('mobileMenu');
                const icon = document.getElementById('mobileMenuIcon');

                if (btn && menu) {
                    btn.addEventListener('click', function () {
                        const isOpen = menu.classList.contains('open');
                        menu.classList.toggle('open');
                        btn.setAttribute('aria-expanded', !isOpen);
                        icon.className = isOpen ? 'fas fa-bars' : 'fas fa-times';
                    });

                    menu.querySelectorAll('a').forEach(link => {
                        link.addEventListener('click', function () {
                            menu.classList.remove('open');
                            btn.setAttribute('aria-expanded', 'false');
                            icon.className = 'fas fa-bars';
                        });
                    });
                }
            })();

            (function () {
                const navbar = document.getElementById('navbar');
                if (!navbar) return;

                function updateNavbar() {
                    if (window.scrollY > 30) {
                        navbar.classList.add('navbar-scrolled');
                    } else {
                        navbar.classList.remove('navbar-scrolled');
                    }
                }

                let ticking = false;

                function onScroll() {
                    if (!ticking) {
                        window.requestAnimationFrame(function () {
                            updateNavbar();
                            ticking = false;
                        });
                        ticking = true;
                    }
                }

                window.addEventListener('scroll', onScroll, { passive: true });
                updateNavbar();
            })();
        </script>