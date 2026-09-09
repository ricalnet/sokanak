<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';
check_login();

$page_title = "Laporan Bulanan";
include 'includes/header.php';

$user_id = $_SESSION['user_id'];
$user_desa = $_SESSION['desa'];
$user_kecamatan = $_SESSION['kecamatan'];
$user_role = $_SESSION['role'];

$current_month = date('m');
$current_year = date('Y');

$search_nik = isset($_GET['search_nik']) ? clean_input($_GET['search_nik'], $conn) : '';
$search_nama = isset($_GET['search_nama']) ? clean_input($_GET['search_nama'], $conn) : '';
$search_ortu = isset($_GET['search_ortu']) ? clean_input($_GET['search_ortu'], $conn) : '';
$filter_kecamatan = isset($_GET['filter_kecamatan']) ? clean_input($_GET['filter_kecamatan'], $conn) : '';
$filter_desa = isset($_GET['filter_desa']) ? clean_input($_GET['filter_desa'], $conn) : '';
$filter_rw = isset($_GET['filter_rw']) ? clean_input($_GET['filter_rw'], $conn) : '';
$filter_jenis_kelamin = isset($_GET['filter_jenis_kelamin']) ? clean_input($_GET['filter_jenis_kelamin'], $conn) : '';
$filter_bulan = isset($_GET['filter_bulan']) ? clean_input($_GET['filter_bulan'], $conn) : $current_month;
$filter_tahun = isset($_GET['filter_tahun']) ? clean_input($_GET['filter_tahun'], $conn) : $current_year;

$limit = 20;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1)
    $page = 1;
$offset = ($page - 1) * $limit;

$query_count = "SELECT COUNT(DISTINCT p.id) as total 
                FROM pengukuran p 
                JOIN anak a ON p.anak_id = a.id 
                WHERE MONTH(p.tanggal_pengukuran) = '$filter_bulan' 
                AND YEAR(p.tanggal_pengukuran) = '$filter_tahun'";
$where_clauses = [];

if (is_kader()) {
    $where_clauses[] = "a.desa = '$user_desa'";
}

if (is_admin()) {
    if (!empty($filter_kecamatan)) {
        $where_clauses[] = "a.kecamatan = '$filter_kecamatan'";
    }
    if (!empty($filter_desa)) {
        $where_clauses[] = "a.desa = '$filter_desa'";
    }
}

if (!empty($filter_rw)) {
    $where_clauses[] = "a.rw = '$filter_rw'";
}

if (!empty($filter_jenis_kelamin)) {
    $where_clauses[] = "a.jenis_kelamin = '$filter_jenis_kelamin'";
}

if (!empty($search_nik)) {
    $where_clauses[] = "a.NIK LIKE '%$search_nik%'";
}

if (!empty($search_nama)) {
    $where_clauses[] = "a.nama_anak LIKE '%$search_nama%'";
}

if (!empty($search_ortu)) {
    $where_clauses[] = "a.nama_ortu LIKE '%$search_ortu%'";
}

if (!empty($where_clauses)) {
    $query_count .= " AND " . implode(" AND ", $where_clauses);
}

$result_count = mysqli_query($conn, $query_count);
$row_count = mysqli_fetch_assoc($result_count);
$total_data = $row_count['total'];
$total_pages = ceil($total_data / $limit);

$query = "SELECT p.*, a.*, 
          DATE_FORMAT(p.tanggal_pengukuran, '%d %M %Y') as tanggal_format,
          u.nama_lengkap as petugas
          FROM pengukuran p 
          JOIN anak a ON p.anak_id = a.id 
          LEFT JOIN users u ON p.created_by = u.id 
          WHERE MONTH(p.tanggal_pengukuran) = '$filter_bulan' 
          AND YEAR(p.tanggal_pengukuran) = '$filter_tahun'";

if (!empty($where_clauses)) {
    $query .= " AND " . implode(" AND ", $where_clauses);
}
$query .= " ORDER BY p.tanggal_pengukuran DESC, p.created_at DESC LIMIT $limit OFFSET $offset";

$result = mysqli_query($conn, $query);
$num_rows = ($result) ? mysqli_num_rows($result) : 0;

$months = [
    '01' => 'Januari',
    '02' => 'Februari',
    '03' => 'Maret',
    '04' => 'April',
    '05' => 'Mei',
    '06' => 'Juni',
    '07' => 'Juli',
    '08' => 'Agustus',
    '09' => 'September',
    '10' => 'Oktober',
    '11' => 'November',
    '12' => 'Desember'
];

$years = [];
$start_year = 2020;
$current_year = date('Y');
for ($year = $current_year; $year >= $start_year; $year--) {
    $years[] = $year;
}
?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Laporan Bulanan</h1>
        <p class="text-gray-600 mt-2">
            Daftar pengukuran anak di bulan
            <span class="font-semibold text-red-600">
                <?php echo $months[$filter_bulan] . ' ' . $filter_tahun; ?>
            </span>
            <span class="ml-2 text-sm bg-gray-100 px-2 py-1 rounded">
                Total: <?php echo $total_data; ?> pengukuran
            </span>
        </p>
    </div>
    <div>
        <a href="anak.php"
            class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
            <i class="fas fa-list mr-2"></i>Data Anak
        </a>
        <a href="input-pengukuran.php"
            class="inline-flex items-center px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors ml-2">
            <i class="fas fa-plus mr-2"></i>Pengukuran Baru
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">
        <i class="fas fa-filter mr-2"></i>Filter & Pencarian Pengukuran
    </h3>

    <form method="GET" action="laporan-bulanan.php" class="space-y-6">
        <input type="hidden" name="page" value="1">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                <select name="filter_bulan"
                    class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    <?php foreach ($months as $key => $month): ?>
                        <option value="<?php echo $key; ?>" <?php echo $filter_bulan == $key ? 'selected' : ''; ?>>
                            <?php echo $month; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                <select name="filter_tahun"
                    class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    <?php foreach ($years as $year): ?>
                        <option value="<?php echo $year; ?>" <?php echo $filter_tahun == $year ? 'selected' : ''; ?>>
                            <?php echo $year; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                <select name="filter_jenis_kelamin"
                    class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    <option value="">Semua Jenis Kelamin</option>
                    <option value="L" <?php echo $filter_jenis_kelamin == 'L' ? 'selected' : ''; ?>>Laki-laki</option>
                    <option value="P" <?php echo $filter_jenis_kelamin == 'P' ? 'selected' : ''; ?>>Perempuan</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <!-- <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari NIK Anak</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-id-card text-gray-400"></i>
                    </div>
                    <input type="text" name="search_nik" 
                           value="<?php echo htmlspecialchars($search_nik); ?>"
                           placeholder="Masukkan NIK..."
                           class="pl-10 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                </div>
            </div> -->

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari Nama Anak</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-child text-gray-400"></i>
                    </div>
                    <input type="text" name="search_nama" value="<?php echo htmlspecialchars($search_nama); ?>"
                        placeholder="Masukkan nama anak..."
                        class="pl-10 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari Nama Orang Tua</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-user text-gray-400"></i>
                    </div>
                    <input type="text" name="search_ortu" value="<?php echo htmlspecialchars($search_ortu); ?>"
                        placeholder="Masukkan nama orang tua..."
                        class="pl-10 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <?php if (is_admin()): ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kecamatan</label>
                    <select name="filter_kecamatan"
                        class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                        <option value="">Semua Kecamatan</option>
                        <?php foreach ($kecamatan_list as $kec): ?>
                            <option value="<?php echo $kec; ?>" <?php echo $filter_kecamatan == $kec ? 'selected' : ''; ?>>
                                <?php echo $kec; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Desa</label>
                    <select name="filter_desa"
                        class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                        <option value="">Semua Desa</option>
                        <?php
                        $all_desa = [];
                        foreach ($desa_list as $kec_desa) {
                            foreach ($kec_desa as $desa) {
                                if (!in_array($desa, $all_desa)) {
                                    $all_desa[] = $desa;
                                }
                            }
                        }
                        sort($all_desa);
                        foreach ($all_desa as $desa): ?>
                            <option value="<?php echo $desa; ?>" <?php echo $filter_desa == $desa ? 'selected' : ''; ?>>
                                <?php echo $desa; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">RW</label>
                <select name="filter_rw"
                    class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    <option value="">Semua RW</option>
                    <?php foreach ($rw_list as $rw): ?>
                        <option value="<?php echo $rw; ?>" <?php echo $filter_rw == $rw ? 'selected' : ''; ?>>
                            RW <?php echo $rw; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="flex justify-between items-center pt-4 border-t border-gray-200">
            <div class="text-sm text-gray-500">
                <?php if ($search_nik || $search_nama || $search_ortu || $filter_kecamatan || $filter_desa || $filter_rw || $filter_jenis_kelamin || $filter_bulan != $current_month || $filter_tahun != $current_year): ?>
                    <span class="text-red-600">
                        <i class="fas fa-filter mr-1"></i>Filter aktif
                    </span>
                <?php endif; ?>
            </div>

            <div class="flex space-x-3">
                <?php if ($search_nik || $search_nama || $search_ortu || $filter_kecamatan || $filter_desa || $filter_rw || $filter_jenis_kelamin || $filter_bulan != $current_month || $filter_tahun != $current_year): ?>
                    <a href="laporan-bulanan.php"
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 inline-flex items-center">
                        <i class="fas fa-times mr-2"></i>Reset Filter
                    </a>
                <?php endif; ?>

                <button type="submit"
                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 inline-flex items-center">
                    <i class="fas fa-search mr-2"></i>Cari Data
                </button>
            </div>
        </div>
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="p-2 rounded-full bg-red-100 text-red-600 mr-3">
                <i class="fas fa-weight-scale text-lg"></i>
            </div>
            <div>
                <p class="text-sm text-gray-600">Total Pengukuran</p>
                <p class="text-xl font-bold text-gray-900"><?php echo $total_data; ?></p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="p-2 rounded-full bg-blue-100 text-blue-600 mr-3">
                <i class="fas fa-child text-lg"></i>
            </div>
            <div>
                <p class="text-sm text-gray-600">Anak Terukur</p>
                <?php
                $query_anak_unik = "SELECT COUNT(DISTINCT a.id) as total 
                                   FROM pengukuran p 
                                   JOIN anak a ON p.anak_id = a.id 
                                   WHERE MONTH(p.tanggal_pengukuran) = '$filter_bulan' 
                                   AND YEAR(p.tanggal_pengukuran) = '$filter_tahun'";
                if (is_kader()) {
                    $query_anak_unik .= " AND a.desa = '$user_desa'";
                }
                $result_anak_unik = mysqli_query($conn, $query_anak_unik);
                $total_anak_unik = mysqli_fetch_assoc($result_anak_unik)['total'];
                ?>
                <p class="text-xl font-bold text-gray-900"><?php echo $total_anak_unik; ?></p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="p-2 rounded-full bg-green-100 text-green-600 mr-3">
                <i class="fas fa-chart-line text-lg"></i>
            </div>
            <div>
                <p class="text-sm text-gray-600">Rata-rata Berat</p>
                <?php
                $query_avg_bb = "SELECT AVG(p.berat_badan) as avg_bb 
                                FROM pengukuran p 
                                JOIN anak a ON p.anak_id = a.id 
                                WHERE MONTH(p.tanggal_pengukuran) = '$filter_bulan' 
                                AND YEAR(p.tanggal_pengukuran) = '$filter_tahun'";
                if (is_kader()) {
                    $query_avg_bb .= " AND a.desa = '$user_desa'";
                }
                $result_avg_bb = mysqli_query($conn, $query_avg_bb);
                $avg_bb = mysqli_fetch_assoc($result_avg_bb)['avg_bb'];
                ?>
                <p class="text-xl font-bold text-gray-900">
                    <?php echo $avg_bb ? number_format($avg_bb, 1) . ' kg' : '-'; ?>
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="p-2 rounded-full bg-purple-100 text-purple-600 mr-3">
                <i class="fas fa-ruler-vertical text-lg"></i>
            </div>
            <div>
                <p class="text-sm text-gray-600">Rata-rata Tinggi</p>
                <?php
                $query_avg_tb = "SELECT AVG(p.panjang_badan) as avg_tb 
                                FROM pengukuran p 
                                JOIN anak a ON p.anak_id = a.id 
                                WHERE MONTH(p.tanggal_pengukuran) = '$filter_bulan' 
                                AND YEAR(p.tanggal_pengukuran) = '$filter_tahun'";
                if (is_kader()) {
                    $query_avg_tb .= " AND a.desa = '$user_desa'";
                }
                $result_avg_tb = mysqli_query($conn, $query_avg_tb);
                $avg_tb = mysqli_fetch_assoc($result_avg_tb)['avg_tb'];
                ?>
                <p class="text-xl font-bold text-gray-900">
                    <?php echo $avg_tb ? number_format($avg_tb, 1) . ' cm' : '-'; ?>
                </p>
            </div>
        </div>
    </div>
</div>

<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <?php if (!$result): ?>
        <div class="p-6 text-center text-red-600">
            <i class="fas fa-exclamation-triangle text-3xl mb-2"></i>
            <p>Error query database: <?php echo mysqli_error($conn); ?></p>
        </div>
    <?php elseif ($num_rows == 0): ?>
        <div class="p-8 text-center text-gray-500">
            <i class="fas fa-database text-4xl mb-3"></i>
            <p class="text-lg font-medium">Tidak ada data pengukuran di bulan
                <?php echo $months[$filter_bulan] . ' ' . $filter_tahun; ?>
            </p>
            <p class="text-sm text-gray-400 mt-1">
                <?php if ($search_nik || $search_nama || $search_ortu || $filter_kecamatan || $filter_desa || $filter_rw || $filter_jenis_kelamin): ?>
                    Coba ubah kriteria pencarian atau
                <?php endif; ?>
                <a href="input-pengukuran.php" class="text-red-600 hover:text-red-800">tambahkan pengukuran baru</a>
            </p>
            <?php if ($search_nik || $search_nama || $search_ortu || $filter_kecamatan || $filter_desa || $filter_rw || $filter_jenis_kelamin): ?>
                <a href="laporan-bulanan.php"
                    class="inline-block mt-4 px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-redo mr-2"></i>Tampilkan Semua Data
                </a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Anak
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Pengukuran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Petugas
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wilayah
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php
                    $no = ($page - 1) * $limit + 1;
                    while ($row = mysqli_fetch_assoc($result)):
                        $tgl_lahir = new DateTime($row['tgl_lahir']);
                        $tgl_ukur = new DateTime($row['tanggal_pengukuran']);
                        $usia = $tgl_ukur->diff($tgl_lahir);
                        $usia_text = '';

                        if ($usia->y > 0) {
                            $usia_text = $usia->y . ' tahun';
                            if ($usia->m > 0) {
                                $usia_text .= ' ' . $usia->m . ' bulan';
                            }
                        } elseif ($usia->m > 0) {
                            $usia_text = $usia->m . ' bulan';
                            if ($usia->d > 0) {
                                $usia_text .= ' ' . $usia->d . ' hari';
                            }
                        } else {
                            $usia_text = $usia->d . ' hari';
                        }
                        ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $no++; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?php echo $row['tanggal_format']; ?></div>
                                <div class="text-xs text-gray-500"><?php echo date('H:i', strtotime($row['created_at'])); ?>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <?php if (!empty($row['foto_pengukuran']) && file_exists($row['foto_pengukuran'])): ?>
                                        <div class="flex-shrink-0 h-10 w-10 mr-3">
                                            <img class="h-10 w-10 rounded-full object-cover"
                                                src="<?php echo $row['foto_pengukuran']; ?>"
                                                alt="<?php echo htmlspecialchars($row['nama_anak']); ?>">
                                        </div>
                                    <?php else: ?>
                                        <div
                                            class="flex-shrink-0 h-10 w-10 mr-3 rounded-full bg-gray-200 flex items-center justify-center">
                                            <i class="fas fa-child text-gray-400"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($row['nama_anak']); ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?php echo $row['jenis_kelamin'] == 'L' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800'; ?>">
                                                <?php echo $row['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?>
                                            </span>
                                            <span class="ml-2 text-xs">Usia: <?php echo $usia_text; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    <div class="flex items-center">
                                        <span class="text-xs text-gray-500 w-16">BB:</span>
                                        <span class="text-sm font-medium text-gray-900">
                                            <?php echo $row['berat_badan'] ? number_format($row['berat_badan'], 2) . ' kg' : '-'; ?>
                                        </span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="text-xs text-gray-500 w-16">TB:</span>
                                        <span class="text-sm font-medium text-gray-900">
                                            <?php echo $row['panjang_badan'] ? number_format($row['panjang_badan'], 2) . ' cm' : '-'; ?>
                                        </span>
                                    </div>
                                    <?php if ($row['lingkar_kepala']): ?>
                                        <div class="flex items-center">
                                            <span class="text-xs text-gray-500 w-16">LK:</span>
                                            <span class="text-sm font-medium text-gray-900">
                                                <?php echo number_format($row['lingkar_kepala'], 2) . ' cm'; ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($row['lingkar_lengan']): ?>
                                        <div class="flex items-center">
                                            <span class="text-xs text-gray-500 w-16">LL:</span>
                                            <span class="text-sm font-medium text-gray-900">
                                                <?php echo number_format($row['lingkar_lengan'], 2) . ' cm'; ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?php echo $row['petugas'] ?: 'System'; ?></div>
                                <div class="text-xs text-gray-500">Kader</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">RW <?php echo $row['rw']; ?></div>
                                <div class="text-sm text-gray-500"><?php echo $row['desa']; ?></div>
                                <div class="text-xs text-gray-400"><?php echo $row['kecamatan']; ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="riwayat-pengukuran.php?anak_id=<?php echo $row['anak_id']; ?>"
                                        class="text-blue-600 hover:text-blue-900 px-2 py-1 rounded hover:bg-blue-50"
                                        title="Riwayat">
                                        <i class="fas fa-history"></i>
                                    </a>
                                    <?php if ($row['catatan']): ?>
                                        <button onclick="showNote('<?php echo addslashes($row['catatan']); ?>')"
                                            class="text-purple-600 hover:text-purple-900 px-2 py-1 rounded hover:bg-purple-50"
                                            title="Catatan">
                                            <i class="fas fa-sticky-note"></i>
                                        </button>
                                    <?php endif; ?>
                                    <?php if (!empty($row['foto_pengukuran']) && file_exists($row['foto_pengukuran'])): ?>
                                        <button
                                            onclick="showPhoto('<?php echo $row['foto_pengukuran']; ?>', '<?php echo $row['tanggal_format']; ?>')"
                                            class="text-green-600 hover:text-green-900 px-2 py-1 rounded hover:bg-green-50"
                                            title="Foto">
                                            <i class="fas fa-image"></i>
                                        </button>
                                    <?php endif; ?>
                                    <a href="edit-pengukuran.php?id=<?php echo $row['id']; ?>"
                                        class="text-yellow-600 hover:text-yellow-900 px-2 py-1 rounded hover:bg-yellow-50"
                                        title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="text-sm text-gray-700 mb-2 sm:mb-0">
                    Menampilkan <span class="font-medium"><?php echo min(($page - 1) * $limit + 1, $total_data); ?></span>
                    sampai <span class="font-medium"><?php echo min($page * $limit, $total_data); ?></span>
                    dari <span class="font-medium"><?php echo $total_data; ?></span> data
                </div>

                <?php if ($total_pages > 1): ?>
                    <div class="text-sm text-gray-700">
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                            <?php
                            function buildPaginationUrl($pageNum)
                            {
                                $params = $_GET;
                                $params['page'] = $pageNum;
                                return 'laporan-bulanan.php?' . http_build_query($params);
                            }

                            if ($page > 1):
                                ?>
                                <a href="<?php echo buildPaginationUrl($page - 1); ?>"
                                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">Previous</span>
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            <?php else: ?>
                                <span
                                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400 cursor-not-allowed">
                                    <i class="fas fa-chevron-left"></i>
                                </span>
                            <?php endif; ?>

                            <?php
                            $start_page = max(1, $page - 2);
                            $end_page = min($total_pages, $page + 2);

                            if ($page <= 3) {
                                $end_page = min($total_pages, 5);
                            }

                            if ($page >= $total_pages - 2) {
                                $start_page = max(1, $total_pages - 4);
                            }

                            if ($start_page > 1): ?>
                                <a href="<?php echo buildPaginationUrl(1); ?>"
                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    1
                                </a>
                                <?php if ($start_page > 2): ?>
                                    <span
                                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                                        ...
                                    </span>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                                <a href="<?php echo buildPaginationUrl($i); ?>"
                                    class="<?php echo $i == $page ? 'z-10 bg-red-50 border-red-500 text-red-600' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50'; ?> relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>

                            <?php
                            if ($end_page < $total_pages): ?>
                                <?php if ($end_page < $total_pages - 1): ?>
                                    <span
                                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                                        ...
                                    </span>
                                <?php endif; ?>
                                <a href="<?php echo buildPaginationUrl($total_pages); ?>"
                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    <?php echo $total_pages; ?>
                                </a>
                            <?php endif; ?>

                            <?php if ($page < $total_pages): ?>
                                <a href="<?php echo buildPaginationUrl($page + 1); ?>"
                                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">Next</span>
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php else: ?>
                                <span
                                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400 cursor-not-allowed">
                                    <i class="fas fa-chevron-right"></i>
                                </span>
                            <?php endif; ?>
                        </nav>

                        <div class="mt-2 text-xs text-gray-500 text-center">
                            Halaman <?php echo $page; ?> dari <?php echo $total_pages; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
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
    document.addEventListener('DOMContentLoaded', function () {
        const searchInputs = document.querySelectorAll('input[type="text"]');
        searchInputs.forEach(input => {
            input.addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    document.querySelector('form').submit();
                }
            });
        });

        const searchForm = document.querySelector('form');
        const searchButton = searchForm.querySelector('button[type="submit"]');
        if (searchButton) {
            searchButton.addEventListener('click', function (e) {
                const pageInput = searchForm.querySelector('input[name="page"]');
                if (pageInput) {
                    pageInput.value = 1;
                }
            });
        }

        const filterSelects = searchForm.querySelectorAll('select');
        filterSelects.forEach(select => {
            select.addEventListener('change', function () {
                const pageInput = searchForm.querySelector('input[name="page"]');
                if (pageInput) {
                    pageInput.value = 1;
                }
            });
        });
    });

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

<?php include 'includes/footer.php'; ?>