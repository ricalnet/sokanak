<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';
check_login();

$page_title = "Riwayat Pengukuran Anak";
include 'includes/header.php';

$user_id = $_SESSION['user_id'];
$user_desa = $_SESSION['desa'];
$user_kecamatan = $_SESSION['kecamatan'];
$user_role = $_SESSION['role'];

$anak_id = isset($_GET['anak_id']) ? intval($_GET['anak_id']) : 0;

if ($anak_id <= 0) {
    echo "<script>alert('ID anak tidak valid!'); window.location='anak.php';</script>";
    exit();
}

$query_anak = "SELECT a.* FROM anak a WHERE a.id = $anak_id";
if (is_kader()) {
    $query_anak .= " AND a.desa = '$user_desa'";
}
$result_anak = mysqli_query($conn, $query_anak);

if (!$result_anak || mysqli_num_rows($result_anak) == 0) {
    echo "<script>alert('Data anak tidak ditemukan atau tidak memiliki akses!'); window.location='anak.php';</script>";
    exit();
}

$anak = mysqli_fetch_assoc($result_anak);

$usia = '';
if (!empty($anak['tgl_lahir'])) {
    $birthDate = new DateTime($anak['tgl_lahir']);
    $today = new DateTime();
    $age = $today->diff($birthDate);
    if ($age->y > 0) {
        $usia = $age->y . ' tahun';
        if ($age->m > 0) {
            $usia .= ' ' . $age->m . ' bulan';
        }
    } elseif ($age->m > 0) {
        $usia = $age->m . ' bulan';
        if ($age->d > 0) {
            $usia .= ' ' . $age->d . ' hari';
        }
    } else {
        $usia = $age->d . ' hari';
    }
}

$query_riwayat = "SELECT p.*, u.nama_lengkap as nama_petugas 
                  FROM pengukuran p 
                  LEFT JOIN users u ON p.created_by = u.id 
                  WHERE p.anak_id = $anak_id 
                  ORDER BY p.tanggal_pengukuran DESC, p.created_at DESC";
$result_riwayat = mysqli_query($conn, $query_riwayat);
$num_riwayat = ($result_riwayat) ? mysqli_num_rows($result_riwayat) : 0;

$query_chart_data = "SELECT 
    DATE_FORMAT(tanggal_pengukuran, '%Y-%m-%d') as tanggal,
    berat_badan,
    panjang_badan,
    lingkar_kepala,
    lingkar_lengan
    FROM pengukuran 
    WHERE anak_id = $anak_id 
    AND (berat_badan IS NOT NULL OR panjang_badan IS NOT NULL)
    ORDER BY tanggal_pengukuran ASC";
$result_chart_data = mysqli_query($conn, $query_chart_data);
$chart_data = [];
$dates = [];
$berat_data = [];
$panjang_data = [];
$lingkar_kepala_data = [];
$lingkar_lengan_data = [];

while ($row = mysqli_fetch_assoc($result_chart_data)) {
    $chart_data[] = $row;
    $dates[] = date('d M', strtotime($row['tanggal']));
    $berat_data[] = $row['berat_badan'];
    $panjang_data[] = $row['panjang_badan'];
    $lingkar_kepala_data[] = $row['lingkar_kepala'];
    $lingkar_lengan_data[] = $row['lingkar_lengan'];
}

$query_stats = "SELECT 
    COUNT(*) as total,
    MIN(berat_badan) as min_berat,
    MAX(berat_badan) as max_berat,
    AVG(berat_badan) as avg_berat,
    MIN(panjang_badan) as min_panjang,
    MAX(panjang_badan) as max_panjang,
    AVG(panjang_badan) as avg_panjang,
    MIN(lingkar_kepala) as min_lingkar_kepala,
    MAX(lingkar_kepala) as max_lingkar_kepala,
    AVG(lingkar_kepala) as avg_lingkar_kepala,
    MIN(lingkar_lengan) as min_lingkar_lengan,
    MAX(lingkar_lengan) as max_lingkar_lengan,
    AVG(lingkar_lengan) as avg_lingkar_lengan
    FROM pengukuran 
    WHERE anak_id = $anak_id";
$result_stats = mysqli_query($conn, $query_stats);
$stats = mysqli_fetch_assoc($result_stats);

define('INCLUDED', true);
include 'includes/status_gizi.php';
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

    .card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        border: 1px solid rgba(226, 232, 240, 0.8);
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.07), 0 4px 6px -2px rgba(0, 0, 0, 0.04);
    }

    .metric-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-left: 4px solid var(--primary-red);
    }

    .progress-ring {
        transform: rotate(-90deg);
    }

    .progress-ring-circle {
        transition: stroke-dashoffset 0.35s;
    }

    .stat-badge {
        background: linear-gradient(135deg, var(--primary-red) 0%, var(--primary-red-dark) 100%);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .gradient-text {
        background: linear-gradient(135deg, var(--primary-red) 0%, var(--primary-red-dark) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .table-row-hover:hover {
        background-color: rgba(226, 0, 26, 0.02);
        transform: translateY(-1px);
        transition: all 0.2s ease;
    }

    .chart-container {
        background: linear-gradient(180deg, #ffffff 0%, #fafbfc 100%);
        border-radius: 12px;
        padding: 20px;
    }

    .timeline-item {
        position: relative;
        padding-left: 30px;
        margin-bottom: 20px;
    }

    .timeline-item:before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: var(--primary-red);
    }

    .timeline-item:after {
        content: '';
        position: absolute;
        left: 5px;
        top: 12px;
        width: 2px;
        height: calc(100% + 20px);
        background: var(--neutral-light);
    }

    .timeline-item:last-child:after {
        display: none;
    }
</style>

<div class="mb-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
        <div>
            <div class="flex items-center space-x-3 mb-2">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Riwayat Pengukuran</h1>
                    <p class="text-gray-600 mt-1">Monitor perkembangan fisik anak secara visual dan terstruktur</p>
                </div>
            </div>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-3">
            <a href="input-pengukuran.php?anak_id=<?php echo $anak_id; ?>"
                class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-red-600 to-red-700 text-white font-medium rounded-lg hover:from-red-700 hover:to-red-800 transition-all duration-300 shadow-sm">
                <i class="fas fa-plus mr-2"></i>Pengukuran Baru
            </a>
            <button onclick="showStatusGizi()"
                class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-red-600 to-red-700 text-white font-medium rounded-lg hover:from-red-700 hover:to-red-800 transition-all duration-300 shadow-sm">
                <i class="fas fa-stethoscope mr-2"></i>Status Gizi
            </button>
            <a href="anak.php"
                class="inline-flex items-center px-4 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-list mr-2"></i>Data Anak
            </a>
        </div>
    </div>

    <div class="card p-6 mb-8">
        <div class="flex flex-col md:flex-row items-start md:items-center space-y-4 md:space-y-0">
            <div class="flex items-center space-x-4 flex-1">
                <div class="relative">
                    <?php if (!empty($anak['foto_pengukuran']) && file_exists($anak['foto_pengukuran'])): ?>
                        <img src="<?php echo $anak['foto_pengukuran']; ?>"
                            alt="<?php echo htmlspecialchars($anak['nama_anak']); ?>"
                            class="w-20 h-20 rounded-xl object-cover border-2 border-white shadow-sm">
                    <?php else: ?>
                        <div
                            class="w-20 h-20 rounded-xl bg-gradient-to-br from-red-100 to-red-50 border-2 border-white shadow-sm flex items-center justify-center">
                            <i class="fas fa-child text-3xl text-red-300"></i>
                        </div>
                    <?php endif; ?>
                    <div
                        class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full bg-red-600 border-2 border-white flex items-center justify-center">
                        <i class="fas fa-heartbeat text-xs text-white"></i>
                    </div>
                </div>

                <div class="flex-1">
                    <div class="flex flex-col md:flex-row md:items-center justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">
                                <?php echo htmlspecialchars($anak['nama_anak']); ?></h2>
                            <div class="flex flex-wrap items-center gap-2 mt-2">
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                                    <?php echo $anak['jenis_kelamin'] == 'L' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800'; ?>">
                                    <i
                                        class="fas fa-<?php echo $anak['jenis_kelamin'] == 'L' ? 'mars' : 'venus'; ?> mr-1.5"></i>
                                    <?php echo $anak['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?>
                                </span>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full bg-gray-100 text-gray-800 text-xs font-medium">
                                    <i class="fas fa-calendar-alt mr-1.5"></i>
                                    <?php echo date('d/m/Y', strtotime($anak['tgl_lahir'])); ?>
                                </span>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full bg-red-100 text-red-800 text-xs font-medium">
                                    <i class="fas fa-baby mr-1.5"></i>
                                    Usia: <?php echo $usia; ?>
                                </span>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full bg-green-100 text-green-800 text-xs font-medium">
                                    <i class="fas fa-chart-line mr-1.5"></i>
                                    <?php echo $num_riwayat; ?> Pengukuran
                                </span>
                            </div>
                        </div>

                        <div class="mt-4 md:mt-0">
                            <div class="text-right">
                                <div class="text-xs text-gray-500 mb-1">Data Terakhir</div>
                                <div class="text-lg font-bold text-red-600">
                                    <?php
                                    if (!empty($anak['updated_at'])) {
                                        echo date('d M Y', strtotime($anak['updated_at']));
                                    } else {
                                        echo date('d M Y', strtotime($anak['created_at']));
                                    }
                                    ?>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    NIK: <?php echo $anak['NIK']; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="metric-card p-4 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Berat Badan</div>
                        <div class="text-2xl font-bold text-gray-900 mt-1">
                            <?php echo $anak['berat_badan'] ? number_format($anak['berat_badan'], 1) : '0.0'; ?> kg
                        </div>
                    </div>
                    <div
                        class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center">
                        <i class="fas fa-weight text-blue-600 text-xl"></i>
                    </div>
                </div>
                <?php if ($stats['avg_berat']): ?>
                    <div class="mt-3 text-xs text-gray-600">
                        Rata-rata: <?php echo number_format($stats['avg_berat'], 1); ?> kg
                    </div>
                <?php endif; ?>
            </div>

            <div class="metric-card p-4 rounded-lg" style="border-left-color: var(--accent-green);">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Panjang Badan</div>
                        <div class="text-2xl font-bold text-gray-900 mt-1">
                            <?php echo $anak['panjang_badan'] ? number_format($anak['panjang_badan'], 1) : '0.0'; ?> cm
                        </div>
                    </div>
                    <div
                        class="w-12 h-12 rounded-full bg-gradient-to-br from-green-50 to-green-100 flex items-center justify-center">
                        <i class="fas fa-ruler-vertical text-green-600 text-xl"></i>
                    </div>
                </div>
                <?php if ($stats['avg_panjang']): ?>
                    <div class="mt-3 text-xs text-gray-600">
                        Rata-rata: <?php echo number_format($stats['avg_panjang'], 1); ?> cm
                    </div>
                <?php endif; ?>
            </div>

            <div class="metric-card p-4 rounded-lg" style="border-left-color: var(--accent-purple);">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Lingkar Kepala</div>
                        <div class="text-2xl font-bold text-gray-900 mt-1">
                            <?php echo $anak['lingkar_kepala'] ? number_format($anak['lingkar_kepala'], 1) : '0.0'; ?>
                            cm
                        </div>
                    </div>
                    <div
                        class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-50 to-purple-100 flex items-center justify-center">
                        <i class="fas fa-brain text-purple-600 text-xl"></i>
                    </div>
                </div>
                <?php if ($stats['avg_lingkar_kepala']): ?>
                    <div class="mt-3 text-xs text-gray-600">
                        Rata-rata: <?php echo number_format($stats['avg_lingkar_kepala'], 1); ?> cm
                    </div>
                <?php endif; ?>
            </div>

            <div class="metric-card p-4 rounded-lg" style="border-left-color: var(--accent-orange);">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Lingkar Lengan</div>
                        <div class="text-2xl font-bold text-gray-900 mt-1">
                            <?php echo $anak['lingkar_lengan'] ? number_format($anak['lingkar_lengan'], 1) : '0.0'; ?>
                            cm
                        </div>
                    </div>
                    <div
                        class="w-12 h-12 rounded-full bg-gradient-to-br from-orange-50 to-orange-100 flex items-center justify-center">
                        <i class="fas fa-hand-paper text-orange-600 text-xl"></i>
                    </div>
                </div>
                <?php if ($stats['avg_lingkar_lengan']): ?>
                    <div class="mt-3 text-xs text-gray-600">
                        Rata-rata: <?php echo number_format($stats['avg_lingkar_lengan'], 1); ?> cm
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if ($num_riwayat >= 2): ?>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <div class="card p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Perkembangan Berat & Panjang Badan</h3>
                    <p class="text-gray-600 text-sm mt-1">Trend pertumbuhan dari waktu ke waktu</p>
                </div>
                <div class="flex space-x-2">
                    <button onclick="toggleChart('weight')"
                        class="px-3 py-1.5 text-xs font-medium rounded-lg bg-red-100 text-red-700">
                        Berat
                    </button>
                    <button onclick="toggleChart('height')"
                        class="px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-100 text-gray-700">
                        Panjang
                    </button>
                </div>
            </div>
            <div class="h-80">
                <canvas id="growthChart"></canvas>
            </div>
            <div class="mt-4 text-xs text-gray-500 flex items-center">
                <i class="fas fa-info-circle mr-2"></i>
                Klik tombol di atas untuk mengubah tampilan grafik
            </div>
        </div>

        <div class="card p-6">
            <div class="mb-6">
                <h3 class="text-lg font-bold text-gray-900">Statistik Perkembangan</h3>
                <p class="text-gray-600 text-sm mt-1">Ringkasan data pengukuran</p>
            </div>

            <div class="space-y-6">
                <div>
                    <div class="flex justify-between text-sm mb-2">
                        <span class="font-medium text-gray-700">Pertumbuhan Berat Badan</span>
                        <span class="font-bold text-red-600">
                            <?php
                            if ($stats['max_berat'] && $stats['min_berat']) {
                                echo '+' . number_format($stats['max_berat'] - $stats['min_berat'], 1) . ' kg';
                            }
                            ?>
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-red-600 h-2.5 rounded-full"
                            style="width: <?php echo min(100, ($stats['avg_berat'] / max($stats['max_berat'], 1)) * 100); ?>%">
                        </div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 mt-1">
                        <span><?php echo $stats['min_berat'] ? number_format($stats['min_berat'], 1) . ' kg' : '-'; ?></span>
                        <span><?php echo $stats['max_berat'] ? number_format($stats['max_berat'], 1) . ' kg' : '-'; ?></span>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-sm mb-2">
                        <span class="font-medium text-gray-700">Pertumbuhan Panjang Badan</span>
                        <span class="font-bold text-green-600">
                            <?php
                            if ($stats['max_panjang'] && $stats['min_panjang']) {
                                echo '+' . number_format($stats['max_panjang'] - $stats['min_panjang'], 1) . ' cm';
                            }
                            ?>
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-green-600 h-2.5 rounded-full"
                            style="width: <?php echo min(100, ($stats['avg_panjang'] / max($stats['max_panjang'], 1)) * 100); ?>%">
                        </div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 mt-1">
                        <span><?php echo $stats['min_panjang'] ? number_format($stats['min_panjang'], 1) . ' cm' : '-'; ?></span>
                        <span><?php echo $stats['max_panjang'] ? number_format($stats['max_panjang'], 1) . ' cm' : '-'; ?></span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mt-8">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4">
                    <div class="text-xs font-medium text-blue-800 uppercase tracking-wider">Pengukuran Tertinggi</div>
                    <div class="text-xl font-bold text-blue-900 mt-1">
                        <?php echo $stats['max_berat'] ? number_format($stats['max_berat'], 1) . ' kg' : '-'; ?>
                    </div>
                    <div class="text-xs text-blue-700 mt-1">Berat Badan</div>
                </div>

                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4">
                    <div class="text-xs font-medium text-green-800 uppercase tracking-wider">Pengukuran Terpanjang</div>
                    <div class="text-xl font-bold text-green-900 mt-1">
                        <?php echo $stats['max_panjang'] ? number_format($stats['max_panjang'], 1) . ' cm' : '-'; ?>
                    </div>
                    <div class="text-xs text-green-700 mt-1">Panjang Badan</div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <div class="card p-6">
            <div class="mb-6">
                <h3 class="text-lg font-bold text-gray-900">Lingkar Kepala & Lengan</h3>
                <p class="text-gray-600 text-sm mt-1">Perbandingan pengukuran lingkar</p>
            </div>
            <div class="h-64">
                <canvas id="circumferenceChart"></canvas>
            </div>
        </div>

        <div class="card p-6">
            <div class="mb-6">
                <h3 class="text-lg font-bold text-gray-900">Timeline Pengukuran</h3>
                <p class="text-gray-600 text-sm mt-1">Riwayat pengukuran dalam timeline</p>
            </div>
            <div class="space-y-4 max-h-64 overflow-y-auto pr-2">
                <?php
                $timeline_query = "SELECT p.*, u.nama_lengkap 
                               FROM pengukuran p 
                               LEFT JOIN users u ON p.created_by = u.id 
                               WHERE p.anak_id = $anak_id 
                               ORDER BY p.tanggal_pengukuran DESC 
                               LIMIT 5";
                $timeline_result = mysqli_query($conn, $timeline_query);
                while ($timeline = mysqli_fetch_assoc($timeline_result)):
                    ?>
                    <div class="timeline-item">
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="text-sm font-medium text-gray-900">
                                    <?php echo date('d M Y', strtotime($timeline['tanggal_pengukuran'])); ?>
                                </div>
                                <div class="text-xs text-gray-600 mt-1">
                                    <?php
                                    $measurements = [];
                                    if ($timeline['berat_badan'])
                                        $measurements[] = 'BB: ' . $timeline['berat_badan'] . ' kg';
                                    if ($timeline['panjang_badan'])
                                        $measurements[] = 'PB: ' . $timeline['panjang_badan'] . ' cm';
                                    if ($timeline['lingkar_kepala'])
                                        $measurements[] = 'LK: ' . $timeline['lingkar_kepala'] . ' cm';
                                    if ($timeline['lingkar_lengan'])
                                        $measurements[] = 'LL: ' . $timeline['lingkar_lengan'] . ' cm';
                                    echo implode(' • ', $measurements);
                                    ?>
                                </div>
                                <?php if ($timeline['catatan']): ?>
                                    <div class="text-xs text-gray-500 mt-1 italic">
                                        "<?php echo mb_strimwidth($timeline['catatan'], 0, 60, '...'); ?>"
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="text-xs text-gray-500">
                                <?php echo $timeline['nama_lengkap'] ?: 'System'; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="card overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex flex-col md:flex-row md:items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Riwayat Pengukuran Detail</h3>
                <p class="text-gray-600 text-sm mt-1">Tabel lengkap data pengukuran</p>
            </div>
            <div class="mt-2 md:mt-0">
                <span class="text-sm text-gray-500">
                    Total: <span class="font-bold text-red-600"><?php echo $num_riwayat; ?></span> catatan
                </span>
            </div>
        </div>
    </div>

    <?php if ($num_riwayat == 0): ?>
        <div class="p-12 text-center">
            <div
                class="w-24 h-24 mx-auto mb-4 rounded-full bg-gradient-to-br from-red-50 to-red-100 flex items-center justify-center">
                <i class="fas fa-chart-line text-3xl text-red-300"></i>
            </div>
            <h4 class="text-xl font-medium text-gray-900 mb-2">Belum ada data pengukuran</h4>
            <p class="text-gray-600 mb-6">Mulai dengan menambahkan pengukuran pertama untuk anak ini</p>
            <a href="input-pengukuran.php?anak_id=<?php echo $anak_id; ?>"
                class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-red-600 to-red-700 text-white font-medium rounded-lg hover:from-red-700 hover:to-red-800 transition-all duration-300">
                <i class="fas fa-plus mr-2"></i>Tambah Pengukuran Pertama
            </a>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Berat
                            Badan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Panjang
                            Badan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lingkar
                            Kepala</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lingkar
                            Lengan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Petugas
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php
                    mysqli_data_seek($result_riwayat, 0);
                    $prev_berat = null;
                    $prev_panjang = null;
                    while ($row = mysqli_fetch_assoc($result_riwayat)):
                        ?>
                        <tr class="table-row-hover">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    <?php echo date('d M Y', strtotime($row['tanggal_pengukuran'])); ?>
                                </div>
                                <div class="text-xs text-gray-500">
                                    <?php echo date('H:i', strtotime($row['created_at'])); ?>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?php echo $row['berat_badan'] ? number_format($row['berat_badan'], 1) . ' kg' : '-'; ?>
                                    </div>
                                    <?php if ($row['berat_badan'] && $prev_berat !== null):
                                        $diff = $row['berat_badan'] - $prev_berat;
                                        if ($diff != 0):
                                            ?>
                                            <div class="ml-2 <?php echo $diff > 0 ? 'text-green-600' : 'text-red-600'; ?>">
                                                <i class="fas fa-arrow-<?php echo $diff > 0 ? 'up' : 'down'; ?> text-xs"></i>
                                                <span class="text-xs"><?php echo number_format(abs($diff), 1); ?></span>
                                            </div>
                                        <?php
                                        endif;
                                    endif;
                                    ?>
                                </div>
                                <?php $prev_berat = $row['berat_badan']; ?>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?php echo $row['panjang_badan'] ? number_format($row['panjang_badan'], 1) . ' cm' : '-'; ?>
                                    </div>
                                    <?php if ($row['panjang_badan'] && $prev_panjang !== null):
                                        $diff = $row['panjang_badan'] - $prev_panjang;
                                        if ($diff != 0):
                                            ?>
                                            <div class="ml-2 <?php echo $diff > 0 ? 'text-green-600' : 'text-red-600'; ?>">
                                                <i class="fas fa-arrow-<?php echo $diff > 0 ? 'up' : 'down'; ?> text-xs"></i>
                                                <span class="text-xs"><?php echo number_format(abs($diff), 1); ?></span>
                                            </div>
                                        <?php
                                        endif;
                                    endif;
                                    ?>
                                </div>
                                <?php $prev_panjang = $row['panjang_badan']; ?>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <?php echo $row['lingkar_kepala'] ? number_format($row['lingkar_kepala'], 1) . ' cm' : '-'; ?>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <?php echo $row['lingkar_lengan'] ? number_format($row['lingkar_lengan'], 1) . ' cm' : '-'; ?>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?php echo $row['nama_petugas'] ?: 'System'; ?></div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <?php if (!empty($row['foto_pengukuran']) && file_exists($row['foto_pengukuran'])): ?>
                                        <button
                                            onclick="showPhoto('<?php echo $row['foto_pengukuran']; ?>', '<?php echo date('d M Y', strtotime($row['tanggal_pengukuran'])); ?>')"
                                            class="text-blue-600 hover:text-blue-800 p-1.5 rounded hover:bg-blue-50 transition-colors"
                                            title="Lihat Foto">
                                            <i class="fas fa-image"></i>
                                        </button>
                                    <?php endif; ?>

                                    <?php if ($row['catatan']): ?>
                                        <button onclick="showNote('<?php echo addslashes($row['catatan']); ?>')"
                                            class="text-purple-600 hover:text-purple-800 p-1.5 rounded hover:bg-purple-50 transition-colors"
                                            title="Lihat Catatan">
                                            <i class="fas fa-sticky-note"></i>
                                        </button>
                                    <?php endif; ?>

                                    <a href="edit-pengukuran.php?id=<?php echo $row['id']; ?>"
                                        class="text-yellow-600 hover:text-yellow-800 p-1.5 rounded hover:bg-yellow-50 transition-colors"
                                        title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <button
                                        onclick="confirmDeleteMeasurement(<?php echo $row['id']; ?>, '<?php echo date('d M Y', strtotime($row['tanggal_pengukuran'])); ?>')"
                                        class="text-red-600 hover:text-red-800 p-1.5 rounded hover:bg-red-50 transition-colors"
                                        title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<div id="photoModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden transition-opacity duration-300">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full overflow-hidden">
            <div class="flex justify-between items-center p-6 border-b">
                <div>
                    <h3 class="text-xl font-bold text-gray-900" id="photoModalTitle"></h3>
                    <p class="text-gray-600 text-sm" id="photoModalDate"></p>
                </div>
                <button onclick="closePhoto()"
                    class="text-gray-400 hover:text-gray-600 p-2 rounded-full hover:bg-gray-100">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-4">
                <div class="relative">
                    <img id="photoView" src="" alt="" class="w-full h-auto rounded-lg shadow-sm">
                    <div id="photoLoading"
                        class="absolute inset-0 bg-gray-100 flex items-center justify-center rounded-lg">
                        <div class="text-center">
                            <i class="fas fa-spinner fa-spin text-3xl text-gray-400 mb-2"></i>
                            <p class="text-gray-500">Memuat gambar...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let growthChart, circumferenceChart;
    let currentChartType = 'weight';

    document.addEventListener('DOMContentLoaded', function () {
        <?php if ($num_riwayat >= 2): ?>
            initGrowthChart();
            initCircumferenceChart();
        <?php endif; ?>
    });

    function initGrowthChart() {
        const ctx = document.getElementById('growthChart').getContext('2d');

        growthChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($dates); ?>,
                datasets: [{
                    label: 'Berat Badan (kg)',
                    data: <?php echo json_encode($berat_data); ?>,
                    borderColor: '#e2001a',
                    backgroundColor: 'rgba(226, 0, 26, 0.1)',
                    tension: 0.3,
                    fill: true,
                    yAxisID: 'y'
                }, {
                    label: 'Panjang Badan (cm)',
                    data: <?php echo json_encode($panjang_data); ?>,
                    borderColor: '#38a169',
                    backgroundColor: 'rgba(56, 161, 105, 0.1)',
                    tension: 0.3,
                    fill: true,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            boxWidth: 12,
                            padding: 20,
                            usePointStyle: true,
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.7)',
                        padding: 12,
                        titleFont: {
                            size: 12
                        },
                        bodyFont: {
                            size: 13
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
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Berat Badan (kg)',
                            color: '#e2001a'
                        },
                        grid: {
                            drawBorder: false
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Panjang Badan (cm)',
                            color: '#38a169'
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                    }
                }
            }
        });
    }

    function initCircumferenceChart() {
        const ctx = document.getElementById('circumferenceChart').getContext('2d');

        circumferenceChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($dates); ?>,
                datasets: [{
                    label: 'Lingkar Kepala (cm)',
                    data: <?php echo json_encode($lingkar_kepala_data); ?>,
                    backgroundColor: 'rgba(128, 90, 213, 0.7)',
                    borderColor: 'rgba(128, 90, 213, 1)',
                    borderWidth: 1
                }, {
                    label: 'Lingkar Lengan (cm)',
                    data: <?php echo json_encode($lingkar_lengan_data); ?>,
                    backgroundColor: 'rgba(237, 137, 54, 0.7)',
                    borderColor: 'rgba(237, 137, 54, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            boxWidth: 12,
                            padding: 20,
                            usePointStyle: true,
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
                        title: {
                            display: true,
                            text: 'Lingkar (cm)'
                        }
                    }
                }
            }
        });
    }

    function toggleChart(type) {
        if (type === currentChartType) return;

        currentChartType = type;

        if (type === 'weight') {
            growthChart.data.datasets[0].hidden = false;
            growthChart.data.datasets[1].hidden = true;
            growthChart.options.scales.y.display = true;
            growthChart.options.scales.y1.display = false;
        } else {
            growthChart.data.datasets[0].hidden = true;
            growthChart.data.datasets[1].hidden = false;
            growthChart.options.scales.y.display = false;
            growthChart.options.scales.y1.display = true;
        }

        growthChart.update();

        document.querySelectorAll('[onclick^="toggleChart"]').forEach(btn => {
            if (btn.textContent.toLowerCase().includes(type)) {
                btn.className = 'px-3 py-1.5 text-xs font-medium rounded-lg bg-red-100 text-red-700';
            } else {
                btn.className = 'px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-100 text-gray-700';
            }
        });
    }

    function showPhoto(photoUrl, tanggal) {
        const modal = document.getElementById('photoModal');
        const photoView = document.getElementById('photoView');
        const loading = document.getElementById('photoLoading');

        document.getElementById('photoModalTitle').textContent = 'Foto Pengukuran';
        document.getElementById('photoModalDate').textContent = 'Tanggal: ' + tanggal;

        loading.classList.remove('hidden');
        photoView.classList.add('opacity-0');

        photoView.onload = function () {
            loading.classList.add('hidden');
            photoView.classList.remove('opacity-0');
        };

        photoView.src = photoUrl;
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closePhoto() {
        document.getElementById('photoModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function showNote(note) {
        const noteText = note.replace(/\\'/g, "'");
        Swal.fire({
            title: '<span class="text-lg font-bold text-gray-900">Catatan Pengukuran</span>',
            html: `<div class="text-left"><p class="text-gray-700 whitespace-pre-wrap p-4 bg-gray-50 rounded-lg">${noteText}</p></div>`,
            showCloseButton: true,
            showConfirmButton: false,
            width: '600px',
            padding: '1rem'
        });
    }

    function confirmDeleteMeasurement(id, tanggal) {
        Swal.fire({
            title: 'Konfirmasi Hapus',
            html: `<div class="text-left">
                <p class="text-gray-700">Apakah Anda yakin ingin menghapus data pengukuran tanggal <span class="font-bold">${tanggal}</span>?</p>
                <p class="text-sm text-gray-500 mt-2">Data yang sudah dihapus tidak dapat dikembalikan.</p>
               </div>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e2001a',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                confirmButton: 'bg-red-600 hover:bg-red-700',
                cancelButton: 'bg-gray-600 hover:bg-gray-700'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `delete-pengukuran.php?id=${id}&anak_id=<?php echo $anak_id; ?>`;
            }
        });
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closePhoto();
        }
    });

    document.getElementById('photoModal').addEventListener('click', function (e) {
        if (e.target === this) {
            closePhoto();
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    .swal2-popup {
        border-radius: 12px !important;
        font-family: 'Inter', sans-serif !important;
    }
</style>

<div id="aiModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden transition-opacity duration-300">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
            <div class="flex justify-between items-center p-6 border-b">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Analisis AI Status Gizi</h3>
                    <p class="text-gray-600 text-sm"><?php echo htmlspecialchars($anak['nama_anak']); ?></p>
                </div>
                <button onclick="closeAIModal()"
                    class="text-gray-400 hover:text-gray-600 p-2 rounded-full hover:bg-gray-100">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="aiModalContent" class="p-0">
            </div>
        </div>
    </div>
</div>

<script>
    function openAIAnalysis() {
        const modal = document.getElementById('aiModal');
        const content = document.getElementById('aiModalContent');

        content.innerHTML = `
        <div class="p-12 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-br from-red-50 to-red-100 flex items-center justify-center">
                <i class="fas fa-robot text-2xl text-red-300 animate-pulse"></i>
            </div>
            <h4 class="text-lg font-medium text-gray-900 mb-2">Menganalisis Data...</h4>
            <p class="text-gray-600">AI sedang menganalisis status gizi anak</p>
        </div>
    `;

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        fetch(`includes/ai.php?anak_id=<?php echo $anak_id; ?>`)
            .then(response => response.text())
            .then(data => {
                content.innerHTML = data;
            })
            .catch(error => {
                console.error('Error loading AI analysis:', error);
                content.innerHTML = `
                <div class="p-12 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-br from-red-50 to-red-100 flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-2xl text-red-300"></i>
                    </div>
                    <h4 class="text-lg font-medium text-gray-900 mb-2">Gagal memuat analisis</h4>
                    <p class="text-gray-600">Silahkan coba lagi nanti</p>
                </div>
            `;
            });
    }

    function closeAIModal() {
        document.getElementById('aiModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeAIModal();
        }
    });

    document.getElementById('aiModal').addEventListener('click', function (e) {
        if (e.target === this) {
            closeAIModal();
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        const aiButton = document.querySelector('a[href*="ai.php"]');
        if (aiButton) {
            aiButton.href = 'javascript:void(0)';
            aiButton.onclick = openAIAnalysis;
        }
    });
</script>
<?php include 'includes/footer.php'; ?>