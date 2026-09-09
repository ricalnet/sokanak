<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';
check_login();

$page_title = "Data Anak";
include 'includes/header.php';

$user_id = $_SESSION['user_id'];
$user_desa = $_SESSION['desa'];
$user_kecamatan = $_SESSION['kecamatan'];
$user_role = $_SESSION['role'];

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($action == 'delete' && $id > 0) {
    $query_check = "SELECT * FROM anak WHERE id = $id";
    if (is_kader()) {
        $query_check .= " AND desa = '$user_desa'";
    }
    $result_check = mysqli_query($conn, $query_check);

    if (mysqli_num_rows($result_check) > 0) {
        $data = mysqli_fetch_assoc($result_check);

        if (!empty($data['foto_pengukuran']) && file_exists($data['foto_pengukuran'])) {
            unlink($data['foto_pengukuran']);
        }

        $query = "DELETE FROM anak WHERE id = $id";
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Data berhasil dihapus!'); window.location='anak.php';</script>";
        } else {
            echo "<script>alert('Error: " . mysqli_error($conn) . "'); window.location='anak.php';</script>";
        }
    } else {
        echo "<script>alert('Data tidak ditemukan atau tidak memiliki akses!'); window.location='anak.php';</script>";
    }
    exit();
}

$search_nik = isset($_GET['search_nik']) ? clean_input($_GET['search_nik'], $conn) : '';
$search_nama = isset($_GET['search_nama']) ? clean_input($_GET['search_nama'], $conn) : '';
$search_ortu = isset($_GET['search_ortu']) ? clean_input($_GET['search_ortu'], $conn) : '';
$filter_kecamatan = isset($_GET['filter_kecamatan']) ? clean_input($_GET['filter_kecamatan'], $conn) : '';
$filter_desa = isset($_GET['filter_desa']) ? clean_input($_GET['filter_desa'], $conn) : '';
$filter_rw = isset($_GET['filter_rw']) ? clean_input($_GET['filter_rw'], $conn) : '';
$filter_jenis_kelamin = isset($_GET['filter_jenis_kelamin']) ? clean_input($_GET['filter_jenis_kelamin'], $conn) : '';

$limit = 10;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1)
    $page = 1;
$offset = ($page - 1) * $limit;

$query_count = "SELECT COUNT(*) as total FROM anak";
$where_clauses = [];

if (is_kader()) {
    $where_clauses[] = "desa = '$user_desa'";
}

if (is_admin()) {
    if (!empty($filter_kecamatan)) {
        $where_clauses[] = "kecamatan = '$filter_kecamatan'";
    }
    if (!empty($filter_desa)) {
        $where_clauses[] = "desa = '$filter_desa'";
    }
}

if (!empty($filter_rw)) {
    $where_clauses[] = "rw = '$filter_rw'";
}

if (!empty($filter_jenis_kelamin)) {
    $where_clauses[] = "jenis_kelamin = '$filter_jenis_kelamin'";
}

if (!empty($search_nik)) {
    $where_clauses[] = "NIK LIKE '%$search_nik%'";
}

if (!empty($search_nama)) {
    $where_clauses[] = "nama_anak LIKE '%$search_nama%'";
}

if (!empty($search_ortu)) {
    $where_clauses[] = "nama_ortu LIKE '%$search_ortu%'";
}

if (!empty($where_clauses)) {
    $query_count .= " WHERE " . implode(" AND ", $where_clauses);
}

$result_count = mysqli_query($conn, $query_count);
$row_count = mysqli_fetch_assoc($result_count);
$total_data = $row_count['total'];
$total_pages = ceil($total_data / $limit);

$query = "SELECT * FROM anak";
if (!empty($where_clauses)) {
    $query .= " WHERE " . implode(" AND ", $where_clauses);
}
$query .= " ORDER BY created_at DESC LIMIT $limit OFFSET $offset";

$result = mysqli_query($conn, $query);
$num_rows = ($result) ? mysqli_num_rows($result) : 0;
?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Data Anak Posyandu</h1>
        <p class="text-gray-600 mt-2">
            Kelola data anak di wilayah
            <span class="font-semibold text-red-600">
                <?php echo is_kader() ? $user_desa . ', ' . $user_kecamatan : 'Semua Wilayah'; ?>
            </span>
            <span class="ml-2 text-sm bg-gray-100 px-2 py-1 rounded">
                Total: <?php echo $total_data; ?> data
            </span>
        </p>
    </div>
</div>

<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">
        <i class="fas fa-search mr-2"></i>Filter & Pencarian Data Anak
    </h3>

    <form method="GET" action="anak.php" class="space-y-6">
        <input type="hidden" name="action" value="list">
        <input type="hidden" name="page" value="1">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
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

        <div class="flex justify-between items-center pt-4 border-t border-gray-200">
            <div class="text-sm text-gray-500">
                <?php if ($search_nik || $search_nama || $search_ortu || $filter_kecamatan || $filter_desa || $filter_rw || $filter_jenis_kelamin): ?>
                    <span class="text-red-600">
                        <i class="fas fa-filter mr-1"></i>Filter aktif
                    </span>
                <?php endif; ?>
            </div>

            <div class="flex space-x-3">
                <?php if ($search_nik || $search_nama || $search_ortu || $filter_kecamatan || $filter_desa || $filter_rw || $filter_jenis_kelamin): ?>
                    <a href="anak.php"
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 inline-flex items-center">
                        <i class="fas fa-times mr-2"></i>Reset Filter
                    </a>
                <?php endif; ?>

                <button type="submit"
                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 inline-flex items-center">
                    <i class="fas fa-search mr-2"></i>Cari Data
                </button>
                <button
                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 inline-flex items-center">
                    <a href="anak-form.php?action=add">
                        <i class="fas fa-plus mr-2"></i>Tambah Anak
                    </a>
                </button>
            </div>
        </div>
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="p-2 rounded-full bg-red-100 text-red-600 mr-3">
                <i class="fas fa-child text-lg"></i>
            </div>
            <div>
                <p class="text-sm text-gray-600">Total Anak</p>
                <p class="text-xl font-bold text-gray-900"><?php echo $total_data; ?></p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="p-2 rounded-full bg-blue-100 text-blue-600 mr-3">
                <i class="fas fa-male text-lg"></i>
            </div>
            <div>
                <p class="text-sm text-gray-600">Laki-laki</p>
                <?php
                $query_laki = "SELECT COUNT(*) as total FROM anak WHERE jenis_kelamin = 'L'";
                if (is_kader()) {
                    $query_laki .= " AND desa = '$user_desa'";
                }
                $result_laki = mysqli_query($conn, $query_laki);
                $total_laki = mysqli_fetch_assoc($result_laki)['total'];
                ?>
                <p class="text-xl font-bold text-gray-900"><?php echo $total_laki; ?></p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="p-2 rounded-full bg-pink-100 text-pink-600 mr-3">
                <i class="fas fa-female text-lg"></i>
            </div>
            <div>
                <p class="text-sm text-gray-600">Perempuan</p>
                <?php
                $query_perempuan = "SELECT COUNT(*) as total FROM anak WHERE jenis_kelamin = 'P'";
                if (is_kader()) {
                    $query_perempuan .= " AND desa = '$user_desa'";
                }
                $result_perempuan = mysqli_query($conn, $query_perempuan);
                $total_perempuan = mysqli_fetch_assoc($result_perempuan)['total'];
                ?>
                <p class="text-xl font-bold text-gray-900"><?php echo $total_perempuan; ?></p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="p-2 rounded-full bg-green-100 text-green-600 mr-3">
                <i class="fas fa-home text-lg"></i>
            </div>
            <div>
                <p class="text-sm text-gray-600">Desa <?php echo $user_desa; ?></p>
                <?php
                $query_desa = "SELECT COUNT(*) as total FROM anak WHERE desa = '$user_desa'";
                $result_desa = mysqli_query($conn, $query_desa);
                $total_desa = mysqli_fetch_assoc($result_desa)['total'];
                ?>
                <p class="text-xl font-bold text-gray-900"><?php echo $total_desa; ?></p>
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
            <p class="text-lg font-medium">Tidak ada data yang ditemukan</p>
            <p class="text-sm text-gray-400 mt-1">
                <?php if ($search_nik || $search_nama || $search_ortu || $filter_kecamatan || $filter_desa || $filter_rw || $filter_jenis_kelamin): ?>
                    Coba ubah kriteria pencarian atau
                <?php endif; ?>
                <a href="anak-form.php?action=add" class="text-red-600 hover:text-red-800">tambahkan data baru</a>
            </p>
            <?php if ($search_nik || $search_nama || $search_ortu || $filter_kecamatan || $filter_desa || $filter_rw || $filter_jenis_kelamin): ?>
                <a href="anak.php"
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
                        <!-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIK</th> -->
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Anak
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis
                            Kelamin</th>
                        <!-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TTL</th> -->
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orang Tua
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wilayah
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php
                    $no = ($page - 1) * $limit + 1;
                    while ($row = mysqli_fetch_assoc($result)):
                        $tgl_lahir = new DateTime($row['tgl_lahir']);
                        $today = new DateTime();
                        $usia = $today->diff($tgl_lahir);
                        $usia_text = '';

                        if ($usia->y > 0) {
                            $usia_text = $usia->y . ' tahun';
                        } elseif ($usia->m > 0) {
                            $usia_text = $usia->m . ' bulan';
                        } else {
                            $usia_text = $usia->d . ' hari';
                        }
                        ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $no++; ?></td>
                            <!-- <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900"><?php echo $row['NIK']; ?></td> -->
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
                                            BB:
                                            <?php echo $row['berat_badan'] ? number_format($row['berat_badan'], 2) . ' kg' : '-'; ?>
                                            |
                                            TB:
                                            <?php echo $row['panjang_badan'] ? number_format($row['panjang_badan'], 2) . ' cm' : '-'; ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="input-pengukuran.php?anak_id=<?php echo $row['id']; ?>"
                                        class="text-green-600 hover:text-green-900 px-2 py-1 rounded hover:bg-green-50"
                                        title="Input Pengukuran">
                                        <i class="fas fa-plus"></i>
                                    </a>
                                    <a href="riwayat-pengukuran.php?anak_id=<?php echo $row['id']; ?>"
                                        class="text-blue-600 hover:text-blue-900 px-2 py-1 rounded hover:bg-blue-50"
                                        title="Edit">
                                        <i class="fas fa-chart-line"></i>
                                    </a>
                                    <button onclick="showDetail(<?php echo htmlspecialchars(json_encode($row)); ?>)"
                                        class="text-purple-600 hover:text-purple-900 px-2 py-1 rounded hover:bg-purple-50"
                                        title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <a href="anak.php?action=delete&id=<?php echo $row['id']; ?>"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus data <?php echo addslashes($row['nama_anak']); ?>?')"
                                        class="text-red-600 hover:text-red-900 px-2 py-1 rounded hover:bg-red-50" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            <?php echo $row['jenis_kelamin'] == 'L' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800'; ?>">
                                    <?php echo $row['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?>
                                </span>
                                <div class="text-xs text-gray-500 mt-1">Usia: <?php echo $usia_text; ?></div>
                            </td>
                            <!-- <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?php echo date('d/m/Y', strtotime($row['tgl_lahir'])); ?>
                                        </td> -->
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">
                                    <?php echo htmlspecialchars($row['nama_ortu']); ?>
                                </div>
                                <div class="text-sm text-gray-500"><?php echo $row['hp_ortu']; ?></div>
                                <div class="text-xs text-gray-400">NIK: <?php echo $row['nik_ortu']; ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">RW <?php echo $row['rw']; ?></div>
                                <div class="text-sm text-gray-500"><?php echo $row['desa']; ?></div>
                                <div class="text-xs text-gray-400"><?php echo $row['kecamatan']; ?></div>
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
                                return 'anak.php?' . http_build_query($params);
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

                            <?php // Next button ?>
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

        const firstSearchInput = document.querySelector('input[name="search_nik"]');
        if (firstSearchInput) {
            firstSearchInput.focus();
        }

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
</script>

<?php include 'includes/modal_detail.php'; ?>

<?php include 'includes/footer.php'; ?>