<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';
check_login();

$page_title = "Dashboard";
include 'includes/header.php';

$user_id = $_SESSION['user_id'];
$user_desa = $_SESSION['desa'];
$user_kecamatan = $_SESSION['kecamatan'];
$user_role = $_SESSION['role'];

$query_total = "SELECT COUNT(*) as total FROM anak WHERE desa = '$user_desa'";
if (is_admin()) {
    $query_total = "SELECT COUNT(*) as total FROM anak";
}
$result_total = mysqli_query($conn, $query_total);
$total_anak = mysqli_fetch_assoc($result_total)['total'];

$query_gender = "SELECT 
    COUNT(CASE WHEN jenis_kelamin = 'L' THEN 1 END) as laki_laki,
    COUNT(CASE WHEN jenis_kelamin = 'P' THEN 1 END) as perempuan
    FROM anak WHERE desa = '$user_desa'";
if (is_admin()) {
    $query_gender = "SELECT 
        COUNT(CASE WHEN jenis_kelamin = 'L' THEN 1 END) as laki_laki,
        COUNT(CASE WHEN jenis_kelamin = 'P' THEN 1 END) as perempuan
        FROM anak";
}
$result_gender = mysqli_query($conn, $query_gender);
$gender_stats = mysqli_fetch_assoc($result_gender);

$query_recent_measurements = "SELECT COUNT(*) as total FROM pengukuran 
                              WHERE DATE(tanggal_pengukuran) = CURDATE()";
$result_recent = mysqli_query($conn, $query_recent_measurements);
$recent_measurements = mysqli_fetch_assoc($result_recent)['total'];

$query_bulan = "SELECT 
    MONTHNAME(created_at) as bulan,
    COUNT(*) as jumlah 
    FROM anak 
    WHERE desa = '$user_desa' 
    AND YEAR(created_at) = YEAR(CURDATE())
    GROUP BY MONTH(created_at)
    ORDER BY MONTH(created_at)";
if (is_admin()) {
    $query_bulan = "SELECT 
        MONTHNAME(created_at) as bulan,
        COUNT(*) as jumlah 
        FROM anak 
        WHERE YEAR(created_at) = YEAR(CURDATE())
        GROUP BY MONTH(created_at)
        ORDER BY MONTH(created_at)";
}
$result_bulan = mysqli_query($conn, $query_bulan);
$data_chart = [];
$chart_labels = [];
$chart_data = [];
while ($row = mysqli_fetch_assoc($result_bulan)) {
    $data_chart[] = $row;
    $chart_labels[] = $row['bulan'];
    $chart_data[] = $row['jumlah'];
}

$desa_data = [];
$desa_labels = [];
$desa_values = [];
if (is_admin()) {
    $query_desa = "SELECT desa, COUNT(*) as jumlah FROM anak 
                   GROUP BY desa ORDER BY jumlah DESC LIMIT 5";
    $result_desa = mysqli_query($conn, $query_desa);
    while ($row = mysqli_fetch_assoc($result_desa)) {
        $desa_labels[] = $row['desa'];
        $desa_values[] = $row['jumlah'];
    }
}

$query_activity = "SELECT 
    a.nama_anak, 
    p.tanggal_pengukuran, 
    p.berat_badan,
    u.nama_lengkap as petugas
    FROM pengukuran p
    JOIN anak a ON p.anak_id = a.id
    LEFT JOIN users u ON p.created_by = u.id
    WHERE a.desa = '$user_desa'
    ORDER BY p.created_at DESC
    LIMIT 5";
if (is_admin()) {
    $query_activity = "SELECT 
        a.nama_anak, 
        p.tanggal_pengukuran, 
        p.berat_badan,
        u.nama_lengkap as petugas,
        a.desa
        FROM pengukuran p
        JOIN anak a ON p.anak_id = a.id
        LEFT JOIN users u ON p.created_by = u.id
        ORDER BY p.created_at DESC
        LIMIT 5";
}
$result_activity = mysqli_query($conn, $query_activity);
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    :root {
        --primary-red: #e2001a;
        --primary-red-dark: #b30015;
        --primary-red-light: #ff4d4d;
        --neutral-dark: #2d3748;
        --neutral-medium: #4a5568;
        --neutral-light: #e2e8f0;
        --neutral-bg: #f7fafc;
        --accent-blue: #3182ce;
        --accent-green: #38a169;
        --accent-purple: #805ad5;
        --accent-orange: #ed8936;
    }

    .stat-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 16px;
        border: 1px solid rgba(226, 232, 240, 0.8);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .stat-card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.07), 0 4px 6px -2px rgba(0, 0, 0, 0.04);
        transform: translateY(-2px);
    }

    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    .gradient-bg {
        background: linear-gradient(135deg, var(--primary-red) 0%, var(--primary-red-dark) 100%);
    }

    .gradient-text {
        background: linear-gradient(135deg, var(--primary-red) 0%, var(--primary-red-dark) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .chart-container {
        background: white;
        border-radius: 16px;
        border: 1px solid rgba(226, 232, 240, 0.8);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        padding: 24px;
    }

    .timeline-item {
        position: relative;
        padding-left: 28px;
        padding-bottom: 20px;
    }

    .timeline-item:before {
        content: '';
        position: absolute;
        left: 0;
        top: 4px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: var(--primary-red);
        border: 3px solid white;
        box-shadow: 0 0 0 3px var(--primary-red);
    }

    .timeline-item:after {
        content: '';
        position: absolute;
        left: 5px;
        top: 16px;
        width: 2px;
        height: calc(100% - 4px);
        background: linear-gradient(to bottom, var(--neutral-light) 0%, transparent 100%);
    }

    .timeline-item:last-child {
        padding-bottom: 0;
    }

    .timeline-item:last-child:after {
        display: none;
    }

    .progress-ring {
        transform: rotate(-90deg);
    }

    .progress-ring-circle {
        transition: stroke-dashoffset 0.35s;
        stroke-linecap: round;
    }
</style>

<div class="mb-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Dashboard Posyandu</h1>
            <p class="text-gray-600 mt-2">
                Selamat datang, <span
                    class="font-semibold gradient-text"><?php echo $_SESSION['nama_lengkap']; ?></span>
                <span
                    class="ml-3 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gradient-to-r from-red-50 to-red-100 text-red-800 border border-red-200">
                    <i class="fas fa-map-marker-alt mr-1.5"></i>
                    <?php echo $_SESSION['desa'] . ', ' . $_SESSION['kecamatan']; ?>
                </span>
                <?php if (is_admin()): ?>
                    <span
                        class="ml-2 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800 border border-purple-200">
                        <i class="fas fa-shield-alt mr-1.5"></i>
                        Administrator
                    </span>
                <?php endif; ?>
            </p>
        </div>
        <div class="mt-4 md:mt-0">
            <div class="flex items-center space-x-3">
                <div class="text-right">
                    <div class="text-sm text-gray-500">Hari ini</div>
                    <div class="text-lg font-bold text-gray-900"><?php echo date('d F Y'); ?></div>
                </div>
                <div
                    class="w-12 h-12 rounded-xl bg-gradient-to-br from-red-600 to-red-700 flex items-center justify-center shadow-sm">
                    <i class="fas fa-heartbeat text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="stat-card p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Anak</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2"><?php echo $total_anak; ?></p>
                    <div class="flex items-center mt-3">
                        <span class="text-sm text-green-600 font-medium">
                            <i class="fas fa-chart-line mr-1"></i>
                            <?php echo count($data_chart); ?> bulan aktif
                        </span>
                    </div>
                </div>
                <div class="stat-icon bg-gradient-to-br from-red-50 to-red-100 text-red-600">
                    <i class="fas fa-child"></i>
                </div>
            </div>
            <div class="mt-6 pt-6 border-t border-gray-100">
                <div class="text-xs text-gray-500">Wilayah: <?php echo is_kader() ? $user_desa : 'Semua Desa'; ?></div>
            </div>
        </div>

        <div class="stat-card p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Jenis Kelamin</p>
                    <div class="flex items-center space-x-4 mt-2">
                        <div>
                            <p class="text-2xl font-bold text-blue-600"><?php echo $gender_stats['laki_laki']; ?></p>
                            <p class="text-xs text-gray-500">Laki-laki</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-pink-600"><?php echo $gender_stats['perempuan']; ?></p>
                            <p class="text-xs text-gray-500">Perempuan</p>
                        </div>
                    </div>
                </div>
                <div class="stat-icon bg-gradient-to-br from-blue-50 to-blue-100 text-blue-600">
                    <i class="fas fa-venus-mars"></i>
                </div>
            </div>
            <div class="mt-6 pt-6 border-t border-gray-100">
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <?php
                    $total_gender = $gender_stats['laki_laki'] + $gender_stats['perempuan'];
                    $male_percentage = $total_gender > 0 ? ($gender_stats['laki_laki'] / $total_gender) * 100 : 0;
                    $female_percentage = $total_gender > 0 ? ($gender_stats['perempuan'] / $total_gender) * 100 : 0;
                    ?>
                    <div class="flex h-2 rounded-full overflow-hidden">
                        <div class="bg-blue-500" style="width: <?php echo $male_percentage; ?>%"></div>
                        <div class="bg-pink-500" style="width: <?php echo $female_percentage; ?>%"></div>
                    </div>
                </div>
                <div class="flex justify-between text-xs text-gray-500 mt-2">
                    <span>Laki-laki <?php echo round($male_percentage, 1); ?>%</span>
                    <span>Perempuan <?php echo round($female_percentage, 1); ?>%</span>
                </div>
            </div>
        </div>

        <div class="stat-card p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Pengukuran Hari Ini</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2"><?php echo $recent_measurements; ?></p>
                    <div class="flex items-center mt-3">
                        <span
                            class="text-sm <?php echo $recent_measurements > 0 ? 'text-green-600' : 'text-gray-500'; ?> font-medium">
                            <i
                                class="fas fa-<?php echo $recent_measurements > 0 ? 'check-circle' : 'clock'; ?> mr-1"></i>
                            <?php echo $recent_measurements > 0 ? 'Aktif hari ini' : 'Belum ada data'; ?>
                        </span>
                    </div>
                </div>
                <div class="stat-icon bg-gradient-to-br from-green-50 to-green-100 text-green-600">
                    <i class="fas fa-weight-scale"></i>
                </div>
            </div>
            <div class="mt-6 pt-6 border-t border-gray-100">
                <a href="input-pengukuran.php"
                    class="text-sm font-medium text-red-600 hover:text-red-700 inline-flex items-center">
                    <i class="fas fa-plus mr-1.5"></i>
                    Tambah Pengukuran
                </a>
            </div>
        </div>

        <div class="stat-card p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Data Bulan Ini</p>
                    <?php
                    $current_month = date('F');
                    $current_month_data = 0;
                    foreach ($data_chart as $data) {
                        if ($data['bulan'] == $current_month) {
                            $current_month_data = $data['jumlah'];
                            break;
                        }
                    }
                    ?>
                    <p class="text-3xl font-bold text-gray-900 mt-2"><?php echo $current_month_data; ?></p>
                    <div class="flex items-center mt-3">
                        <span
                            class="text-sm <?php echo $current_month_data > 0 ? 'text-green-600' : 'text-yellow-600'; ?> font-medium">
                            <i class="fas fa-<?php echo $current_month_data > 0 ? 'arrow-up' : 'minus'; ?> mr-1"></i>
                            <?php echo date('F Y'); ?>
                        </span>
                    </div>
                </div>
                <div class="stat-icon bg-gradient-to-br from-purple-50 to-purple-100 text-purple-600">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
            <div class="mt-6 pt-6 border-t border-gray-100">
                <div class="text-xs text-gray-500">Bulan: <?php echo $current_month; ?></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <div class="chart-container">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Pertumbuhan Data Anak</h3>
                    <p class="text-gray-600 text-sm mt-1">Tahun <?php echo date('Y'); ?></p>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-xs px-2 py-1 rounded-full bg-red-100 text-red-700">
                        <?php echo is_kader() ? $user_desa : 'Semua Desa'; ?>
                    </span>
                </div>
            </div>
            <div class="h-72">
                <canvas id="monthlyChart"></canvas>
            </div>
            <div class="mt-4 text-center text-sm text-gray-500">
                Data registrasi anak per bulan
            </div>
        </div>

        <?php if (is_admin() && !empty($desa_labels)): ?>
            <div class="chart-container">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Distribusi per Desa</h3>
                        <p class="text-gray-600 text-sm mt-1">Top 5 desa dengan data terbanyak</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-xs px-2 py-1 rounded-full bg-purple-100 text-purple-700">
                            <i class="fas fa-chart-pie mr-1"></i>
                            Distribusi
                        </span>
                    </div>
                </div>
                <div class="h-72">
                    <canvas id="desaChart"></canvas>
                </div>
                <div class="mt-4 text-center text-sm text-gray-500">
                    Total <?php echo array_sum($desa_values); ?> anak di 5 desa teratas
                </div>
            </div>
        <?php else: ?>
            <div class="chart-container">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Aktivitas Terkini</h3>
                        <p class="text-gray-600 text-sm mt-1">Pengukuran terbaru di <?php echo $user_desa; ?></p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700">
                            <i class="fas fa-history mr-1"></i>
                            Timeline
                        </span>
                    </div>
                </div>
                <div class="space-y-6">
                    <?php if (mysqli_num_rows($result_activity) > 0): ?>
                        <?php while ($activity = mysqli_fetch_assoc($result_activity)): ?>
                            <div class="timeline-item">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($activity['nama_anak']); ?>
                                        </div>
                                        <div class="text-sm text-gray-600 mt-1">
                                            Pengukuran:
                                            <?php echo $activity['berat_badan'] ? number_format($activity['berat_badan'], 1) . ' kg' : 'Baru'; ?>
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            <i class="fas fa-user mr-1"></i><?php echo $activity['petugas']; ?>
                                        </div>
                                    </div>
                                    <div class="text-xs text-gray-500 text-right">
                                        <?php echo date('d M', strtotime($activity['tanggal_pengukuran'])); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                                <i class="fas fa-clipboard-list text-gray-400 text-2xl"></i>
                            </div>
                            <p class="text-gray-500">Belum ada aktivitas pengukuran</p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="mt-6 text-center">
                    <a href="anak.php" class="text-sm font-medium text-red-600 hover:text-red-700 inline-flex items-center">
                        <i class="fas fa-list mr-1.5"></i>
                        Lihat Semua Data
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <div class="chart-container">
            <div class="mb-6">
                <h3 class="text-lg font-bold text-gray-900">Aksi Cepat</h3>
                <p class="text-gray-600 text-sm mt-1">Akses fitur utama dengan satu klik</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="anak-form.php?action=add"
                    class="group p-4 bg-gradient-to-br from-red-50 to-white border border-red-100 rounded-xl hover:border-red-200 transition-all duration-200 hover:shadow-md">
                    <div class="flex items-center">
                        <div
                            class="w-12 h-12 rounded-lg bg-red-100 group-hover:bg-red-200 transition-colors flex items-center justify-center mr-4">
                            <i class="fas fa-plus text-red-600 text-lg"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Tambah Data Anak</p>
                            <p class="text-sm text-gray-500 mt-1">Registrasi anak baru</p>
                        </div>
                    </div>
                </a>

                <a href="anak.php"
                    class="group p-4 bg-gradient-to-br from-blue-50 to-white border border-blue-100 rounded-xl hover:border-blue-200 transition-all duration-200 hover:shadow-md">
                    <div class="flex items-center">
                        <div
                            class="w-12 h-12 rounded-lg bg-blue-100 group-hover:bg-blue-200 transition-colors flex items-center justify-center mr-4">
                            <i class="fas fa-list text-blue-600 text-lg"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Lihat Data Anak</p>
                            <p class="text-sm text-gray-500 mt-1">Kelola semua data anak</p>
                        </div>
                    </div>
                </a>

                <a href="input-pengukuran.php"
                    class="group p-4 bg-gradient-to-br from-green-50 to-white border border-green-100 rounded-xl hover:border-green-200 transition-all duration-200 hover:shadow-md">
                    <div class="flex items-center">
                        <div
                            class="w-12 h-12 rounded-lg bg-green-100 group-hover:bg-green-200 transition-colors flex items-center justify-center mr-4">
                            <i class="fas fa-weight-scale text-green-600 text-lg"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Input Pengukuran</p>
                            <p class="text-sm text-gray-500 mt-1">Catat perkembangan anak</p>
                        </div>
                    </div>
                </a>

                <?php if (is_admin()): ?>
                    <a href="#"
                        class="group p-4 bg-gradient-to-br from-purple-50 to-white border border-purple-100 rounded-xl hover:border-purple-200 transition-all duration-200 hover:shadow-md">
                        <div class="flex items-center">
                            <div
                                class="w-12 h-12 rounded-lg bg-purple-100 group-hover:bg-purple-200 transition-colors flex items-center justify-center mr-4">
                                <i class="fas fa-users text-purple-600 text-lg"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Kelola Kader</p>
                                <p class="text-sm text-gray-500 mt-1">Manajemen user sistem</p>
                            </div>
                        </div>
                    </a>
                <?php else: ?>
                    <a href="riwayat-pengukuran.php?anak_id=<?php echo $anak_id ?? ''; ?>"
                        class="group p-4 bg-gradient-to-br from-purple-50 to-white border border-purple-100 rounded-xl hover:border-purple-200 transition-all duration-200 hover:shadow-md">
                        <div class="flex items-center">
                            <div
                                class="w-12 h-12 rounded-lg bg-purple-100 group-hover:bg-purple-200 transition-colors flex items-center justify-center mr-4">
                                <i class="fas fa-chart-line text-purple-600 text-lg"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Riwayat Pengukuran</p>
                                <p class="text-sm text-gray-500 mt-1">Monitor perkembangan</p>
                            </div>
                        </div>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="chart-container">
            <div class="mb-6">
                <h3 class="text-lg font-bold text-gray-900">Informasi Sistem</h3>
                <p class="text-gray-600 text-sm mt-1">Status dan panduan penggunaan</p>
            </div>

            <div class="space-y-6">
                <div class="bg-gradient-to-br from-blue-50 to-white border border-blue-100 rounded-xl p-5">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center mr-3">
                            <i class="fas fa-user text-blue-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900">Informasi Akun</p>
                            <p class="text-sm text-gray-500">Hak akses dan wilayah kerja</p>
                        </div>
                        <span
                            class="px-3 py-1 text-xs font-medium rounded-full 
                            <?php echo is_admin() ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800'; ?>">
                            <?php echo is_admin() ? 'Administrator' : 'Kader Posyandu'; ?>
                        </span>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center text-sm">
                            <i class="fas fa-map-marker-alt text-gray-400 mr-3 w-5"></i>
                            <span class="text-gray-600">Wilayah:</span>
                            <span
                                class="ml-2 font-medium text-gray-900"><?php echo $user_desa . ', ' . $user_kecamatan; ?></span>
                        </div>
                        <?php if (is_kader()): ?>
                            <div class="flex items-center text-sm">
                                <i class="fas fa-eye text-gray-400 mr-3 w-5"></i>
                                <span class="text-gray-600">Akses Data:</span>
                                <span class="ml-2 font-medium text-gray-900">Desa <?php echo $user_desa; ?> saja</span>
                            </div>
                        <?php endif; ?>
                        <div class="flex items-center text-sm">
                            <i class="fas fa-calendar-alt text-gray-400 mr-3 w-5"></i>
                            <span class="text-gray-600">Bergabung:</span>
                            <span class="ml-2 font-medium text-gray-900">
                                <?php
                                echo date('d M Y');
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-6 border-t border-gray-100">
                <p class="text-sm font-medium text-gray-900 mb-3">
                    <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                    Tips Cepat
                </p>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mt-0.5 mr-2"></i>
                        <span>Gunakan filter untuk mencari data anak dengan cepat</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mt-0.5 mr-2"></i>
                        <span>Upload foto pengukuran untuk dokumentasi yang lebih baik</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mt-0.5 mr-2"></i>
                        <span>Catat pengukuran rutin setiap bulan untuk memantau perkembangan</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <?php if (is_admin()): ?>
        <div class="chart-container mb-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Ringkasan Aktivitas</h3>
                    <p class="text-gray-600 text-sm mt-1">Aktivitas terkini di semua wilayah</p>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-700">
                        <i class="fas fa-globe mr-1"></i>
                        Semua Desa
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php
                $query_summary = "SELECT 
                (SELECT COUNT(*) FROM pengukuran WHERE DATE(tanggal_pengukuran) = CURDATE()) as today_measurements,
                (SELECT COUNT(*) FROM anak WHERE DATE(created_at) = CURDATE()) as today_registrations,
                (SELECT COUNT(DISTINCT desa) FROM anak) as active_desa";
                $result_summary = mysqli_query($conn, $query_summary);
                $summary = mysqli_fetch_assoc($result_summary);
                ?>

                <div class="bg-gradient-to-br from-red-50 to-white border border-red-100 rounded-xl p-5">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-lg bg-red-100 flex items-center justify-center mr-4">
                            <i class="fas fa-weight-scale text-red-600"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $summary['today_measurements']; ?></p>
                            <p class="text-sm text-gray-500">Pengukuran Hari Ini</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-blue-50 to-white border border-blue-100 rounded-xl p-5">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center mr-4">
                            <i class="fas fa-child text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $summary['today_registrations']; ?></p>
                            <p class="text-sm text-gray-500">Registrasi Baru</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-green-50 to-white border border-green-100 rounded-xl p-5">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-lg bg-green-100 flex items-center justify-center mr-4">
                            <i class="fas fa-map text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $summary['active_desa']; ?></p>
                            <p class="text-sm text-gray-500">Desa Aktif</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        const monthlyChart = new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($chart_labels); ?>,
                datasets: [{
                    label: 'Jumlah Anak',
                    data: <?php echo json_encode($chart_data); ?>,
                    borderColor: '#e2001a',
                    backgroundColor: 'rgba(226, 0, 26, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true,
                    pointBackgroundColor: '#e2001a',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            boxWidth: 12,
                            padding: 20,
                            usePointStyle: true,
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 12
                        },
                        bodyFont: {
                            size: 13
                        },
                        callbacks: {
                            label: function (context) {
                                return `Anak: ${context.parsed.y}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            },
                            callback: function (value) {
                                return value;
                            }
                        }
                    }
                }
            }
        });

        <?php if (is_admin() && !empty($desa_labels)): ?>
            const desaCtx = document.getElementById('desaChart').getContext('2d');
            const desaChart = new Chart(desaCtx, {
                type: 'doughnut',
                data: {
                    labels: <?php echo json_encode($desa_labels); ?>,
                    datasets: [{
                        data: <?php echo json_encode($desa_values); ?>,
                        backgroundColor: [
                            '#e2001a',
                            '#3182ce',
                            '#38a169',
                            '#805ad5',
                            '#ed8936'
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 12,
                                padding: 20,
                                usePointStyle: true,
                                font: {
                                    size: 11
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            bodyFont: {
                                size: 13
                            },
                            callbacks: {
                                label: function (context) {
                                    const total = <?php echo array_sum($desa_values); ?>;
                                    const percentage = ((context.parsed / total) * 100).toFixed(1);
                                    return `${context.label}: ${context.parsed} (${percentage}%)`;
                                }
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
        <?php endif; ?>

        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fadeInUp');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.stat-card').forEach(card => {
            observer.observe(card);
        });
    });

    const style = document.createElement('style');
    style.textContent = `
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fadeInUp {
    animation: fadeInUp 0.6s ease-out;
}

.stat-card, .chart-container {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.group:hover .group-hover\\:bg-red-200 {
    background-color: #fed7d7;
}
`;
    document.head.appendChild(style);
</script>

<?php include 'includes/footer.php'; ?>