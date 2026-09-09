<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';
check_login();

$page_title = "Edit Pengukuran";
include 'includes/header.php';

$user_id = $_SESSION['user_id'];
$user_desa = $_SESSION['desa'];
$user_kecamatan = $_SESSION['kecamatan'];
$user_role = $_SESSION['role'];

$measurement_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($measurement_id <= 0) {
    echo "<script>alert('ID pengukuran tidak valid!'); window.location='anak.php';</script>";
    exit();
}

$query = "SELECT p.*, a.* FROM pengukuran p 
          JOIN anak a ON p.anak_id = a.id 
          WHERE p.id = $measurement_id";

if (is_kader()) {
    $query .= " AND a.desa = '$user_desa'";
}

$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "<script>alert('Data pengukuran tidak ditemukan atau tidak memiliki akses!'); window.location='anak.php';</script>";
    exit();
}

$measurement = mysqli_fetch_assoc($result);
$anak_id = $measurement['anak_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal_pengukuran = clean_input($_POST['tanggal_pengukuran'], $conn);
    $berat_badan = !empty($_POST['berat_badan']) ? clean_input($_POST['berat_badan'], $conn) : NULL;
    $panjang_badan = !empty($_POST['panjang_badan']) ? clean_input($_POST['panjang_badan'], $conn) : NULL;
    $lingkar_kepala = !empty($_POST['lingkar_kepala']) ? clean_input($_POST['lingkar_kepala'], $conn) : NULL;
    $lingkar_lengan = !empty($_POST['lingkar_lengan']) ? clean_input($_POST['lingkar_lengan'], $conn) : NULL;
    $catatan = clean_input($_POST['catatan'], $conn);

    $foto_pengukuran = $measurement['foto_pengukuran'];
    if (isset($_FILES['foto_pengukuran']) && $_FILES['foto_pengukuran']['error'] == 0) {
        $target_dir = "uploads/pengukuran/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_name = time() . '_' . basename($_FILES["foto_pengukuran"]["name"]);
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["foto_pengukuran"]["tmp_name"]);
        if ($check !== false) {
            if ($_FILES["foto_pengukuran"]["size"] <= 2097152) {
                if (in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
                    if (move_uploaded_file($_FILES["foto_pengukuran"]["tmp_name"], $target_file)) {
                        if (!empty($foto_pengukuran) && file_exists($foto_pengukuran)) {
                            unlink($foto_pengukuran);
                        }
                        $foto_pengukuran = $target_file;
                    }
                }
            }
        }
    }

    $update_query = "UPDATE pengukuran SET 
        tanggal_pengukuran = '$tanggal_pengukuran',
        berat_badan = " . ($berat_badan ? "'$berat_badan'" : "NULL") . ",
        panjang_badan = " . ($panjang_badan ? "'$panjang_badan'" : "NULL") . ",
        lingkar_kepala = " . ($lingkar_kepala ? "'$lingkar_kepala'" : "NULL") . ",
        lingkar_lengan = " . ($lingkar_lengan ? "'$lingkar_lengan'" : "NULL") . ",
        foto_pengukuran = '$foto_pengukuran',
        catatan = '$catatan'
        WHERE id = $measurement_id";

    if (mysqli_query($conn, $update_query)) {
        $latest_query = "SELECT * FROM pengukuran 
                        WHERE anak_id = $anak_id 
                        ORDER BY tanggal_pengukuran DESC, created_at DESC 
                        LIMIT 1";
        $latest_result = mysqli_query($conn, $latest_query);
        if ($latest_result && $latest = mysqli_fetch_assoc($latest_result)) {
            $update_anak_query = "UPDATE anak SET 
                berat_badan = " . ($latest['berat_badan'] ? "'{$latest['berat_badan']}'" : "NULL") . ",
                panjang_badan = " . ($latest['panjang_badan'] ? "'{$latest['panjang_badan']}'" : "NULL") . ",
                lingkar_kepala = " . ($latest['lingkar_kepala'] ? "'{$latest['lingkar_kepala']}'" : "NULL") . ",
                lingkar_lengan = " . ($latest['lingkar_lengan'] ? "'{$latest['lingkar_lengan']}'" : "NULL") . ",
                foto_pengukuran = '{$latest['foto_pengukuran']}'
                WHERE id = $anak_id";
            mysqli_query($conn, $update_anak_query);
        }

        echo "<script>alert('Data pengukuran berhasil diperbarui!'); window.location='riwayat-pengukuran.php?anak_id=$anak_id';</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Edit Pengukuran</h1>
    <p class="text-gray-600 mt-2">Edit data pengukuran untuk <?php echo htmlspecialchars($measurement['nama_anak']); ?>
    </p>
</div>

<form method="POST" action="" enctype="multipart/form-data" class="bg-white shadow-md rounded-lg p-6 max-w-2xl mx-auto">
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pengukuran *</label>
        <input type="date" name="tanggal_pengukuran" required value="<?php echo $measurement['tanggal_pengukuran']; ?>"
            max="<?php echo date('Y-m-d'); ?>"
            class="block w-full border border-gray-300 rounded-md shadow-sm py-3 px-4 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Berat Badan (kg)</label>
            <div class="relative">
                <input type="number" step="0.01" min="0" max="100" name="berat_badan"
                    value="<?php echo $measurement['berat_badan']; ?>"
                    class="block w-full border border-gray-300 rounded-md shadow-sm py-3 px-4 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <span class="text-gray-500 sm:text-sm">kg</span>
                </div>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Panjang Badan (cm)</label>
            <div class="relative">
                <input type="number" step="0.01" min="0" max="200" name="panjang_badan"
                    value="<?php echo $measurement['panjang_badan']; ?>"
                    class="block w-full border border-gray-300 rounded-md shadow-sm py-3 px-4 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <span class="text-gray-500 sm:text-sm">cm</span>
                </div>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Lingkar Kepala (cm)</label>
            <div class="relative">
                <input type="number" step="0.01" min="0" max="100" name="lingkar_kepala"
                    value="<?php echo $measurement['lingkar_kepala']; ?>"
                    class="block w-full border border-gray-300 rounded-md shadow-sm py-3 px-4 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <span class="text-gray-500 sm:text-sm">cm</span>
                </div>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Lingkar Lengan (cm)</label>
            <div class="relative">
                <input type="number" step="0.01" min="0" max="100" name="lingkar_lengan"
                    value="<?php echo $measurement['lingkar_lengan']; ?>"
                    class="block w-full border border-gray-300 rounded-md shadow-sm py-3 px-4 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <span class="text-gray-500 sm:text-sm">cm</span>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
        <textarea name="catatan" rows="3"
            class="block w-full border border-gray-300 rounded-md shadow-sm py-3 px-4 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"><?php echo $measurement['catatan']; ?></textarea>
    </div>

    <div class="flex justify-end space-x-3">
        <a href="riwayat-pengukuran.php?anak_id=<?php echo $anak_id; ?>"
            class="px-6 py-3 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
            Batal
        </a>
        <button type="submit"
            class="px-6 py-3 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
            <i class="fas fa-save mr-2"></i>Update Pengukuran
        </button>
    </div>
</form>

<script>
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        const file = input.files[0];

        if (file) {
            const reader = new FileReader();

            reader.onload = function (e) {
                preview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
            }

            reader.readAsDataURL(file);
        }
    }
</script>

<?php include 'includes/footer.php'; ?>