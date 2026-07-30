<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';
check_login();

$page_title = "Input Pengukuran Anak";
include 'includes/header.php';

$user_id = $_SESSION['user_id'];
$user_desa = $_SESSION['desa'];
$user_kecamatan = $_SESSION['kecamatan'];
$user_role = $_SESSION['role'];

$selected_anak_id = isset($_GET['anak_id']) ? intval($_GET['anak_id']) : 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $anak_id = clean_input($_POST['anak_id'], $conn);
    $tanggal_pengukuran = clean_input($_POST['tanggal_pengukuran'], $conn);
    $berat_badan = !empty($_POST['berat_badan']) ? clean_input($_POST['berat_badan'], $conn) : NULL;
    $panjang_badan = !empty($_POST['panjang_badan']) ? clean_input($_POST['panjang_badan'], $conn) : NULL;
    $lingkar_kepala = !empty($_POST['lingkar_kepala']) ? clean_input($_POST['lingkar_kepala'], $conn) : NULL;
    $lingkar_lengan = !empty($_POST['lingkar_lengan']) ? clean_input($_POST['lingkar_lengan'], $conn) : NULL;
    $catatan = clean_input($_POST['catatan'], $conn);

    $foto_pengukuran = '';
    $upload_error = '';

    if (isset($_POST['webcam_photo']) && !empty($_POST['webcam_photo'])) {
        $webcam_data = $_POST['webcam_photo'];

        if (strpos($webcam_data, 'data:image') === 0) {
            $target_dir = "uploads/pengukuran/";

            if (!is_dir($target_dir)) {
                if (!mkdir($target_dir, 0777, true)) {
                    $upload_error = "Gagal membuat folder uploads. Periksa permission folder.";
                }
            }

            if (is_dir($target_dir) && is_writable($target_dir)) {
                $file_name = time() . '_webcam_' . $anak_id . '.jpg';
                $target_file = $target_dir . $file_name;

                list($type, $webcam_data) = explode(';', $webcam_data);
                list(, $webcam_data) = explode(',', $webcam_data);
                $webcam_data = base64_decode($webcam_data);

                if (file_put_contents($target_file, $webcam_data)) {
                    $foto_pengukuran = $target_file;
                    echo "<script>console.log('Webcam photo saved to: $target_file');</script>";
                } else {
                    $upload_error = "Gagal menyimpan foto webcam. Periksa permission folder uploads.";
                    echo "<script>console.error('Failed to save webcam photo. Target: $target_file');</script>";
                }
            } else {
                $upload_error = "Folder uploads tidak dapat ditulisi. Periksa permission folder.";
                echo "<script>console.error('Upload directory not writable: $target_dir');</script>";
            }
        } else {
            $upload_error = "Format data webcam tidak valid.";
        }
    } elseif (isset($_FILES['foto_pengukuran']) && $_FILES['foto_pengukuran']['error'] == 0) {
        $target_dir = "uploads/pengukuran/";

        if (!is_dir($target_dir)) {
            if (!mkdir($target_dir, 0777, true)) {
                $upload_error = "Gagal membuat folder uploads. Periksa permission folder.";
            }
        }

        if (is_dir($target_dir) && is_writable($target_dir)) {
            $file_name = time() . '_' . basename($_FILES["foto_pengukuran"]["name"]);
            $target_file = $target_dir . $file_name;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            $check = getimagesize($_FILES["foto_pengukuran"]["tmp_name"]);
            if ($check !== false) {
                if ($_FILES["foto_pengukuran"]["size"] <= 2097152) {
                    if (in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
                        if (move_uploaded_file($_FILES["foto_pengukuran"]["tmp_name"], $target_file)) {
                            $foto_pengukuran = $target_file;
                            echo "<script>console.log('File uploaded to: $target_file');</script>";
                        } else {
                            $upload_error = "Gagal mengupload file. Error: " . $_FILES["foto_pengukuran"]["error"];
                        }
                    } else {
                        $upload_error = "Format file tidak didukung. Hanya JPG, JPEG, PNG, GIF yang diperbolehkan.";
                    }
                } else {
                    $upload_error = "Ukuran file terlalu besar. Maksimal 2MB.";
                }
            } else {
                $upload_error = "File yang diupload bukan gambar.";
            }
        } else {
            $upload_error = "Folder uploads tidak dapat ditulisi. Periksa permission folder.";
        }
    }

    if (!empty($upload_error)) {
        echo "<script>alert('$upload_error');</script>";
    } else {
        $check_query = "SELECT id FROM pengukuran WHERE anak_id = $anak_id AND tanggal_pengukuran = '$tanggal_pengukuran'";
        $check_result = mysqli_query($conn, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            echo "<script>alert('Data pengukuran untuk tanggal ini sudah ada!');</script>";
        } else {
            $query = "INSERT INTO pengukuran (
                anak_id, tanggal_pengukuran, berat_badan, panjang_badan, 
                lingkar_kepala, lingkar_lengan, foto_pengukuran, catatan, created_by
            ) VALUES (
                $anak_id, '$tanggal_pengukuran', 
                " . ($berat_badan ? "'$berat_badan'" : "NULL") . ",
                " . ($panjang_badan ? "'$panjang_badan'" : "NULL") . ",
                " . ($lingkar_kepala ? "'$lingkar_kepala'" : "NULL") . ",
                " . ($lingkar_lengan ? "'$lingkar_lengan'" : "NULL") . ",
                '$foto_pengukuran', '$catatan', $user_id
            )";

            if (mysqli_query($conn, $query)) {
                $update_anak_query = "UPDATE anak SET 
                    berat_badan = " . ($berat_badan ? "'$berat_badan'" : "berat_badan") . ",
                    panjang_badan = " . ($panjang_badan ? "'$panjang_badan'" : "panjang_badan") . ",
                    lingkar_kepala = " . ($lingkar_kepala ? "'$lingkar_kepala'" : "lingkar_kepala") . ",
                    lingkar_lengan = " . ($lingkar_lengan ? "'$lingkar_lengan'" : "lingkar_lengan") . ",
                    foto_pengukuran = " . ($foto_pengukuran ? "'$foto_pengukuran'" : "foto_pengukuran") . "
                    WHERE id = $anak_id";

                mysqli_query($conn, $update_anak_query);

                echo "<script>alert('Data pengukuran berhasil disimpan!'); window.location='riwayat-pengukuran.php?anak_id=$anak_id';</script>";
            } else {
                echo "<script>alert('Error database: " . mysqli_error($conn) . "');</script>";
            }
        }
    }
}

$query_anak = "SELECT a.* FROM anak a";
if (is_kader()) {
    $query_anak .= " WHERE a.desa = '$user_desa'";
}
$query_anak .= " ORDER BY a.nama_anak ASC";
$result_anak = mysqli_query($conn, $query_anak);
?>

<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Input Pengukuran Anak</h1>
    <p class="text-gray-600 mt-2">Catat data pengukuran perkembangan anak</p>
</div>

<?php
$upload_dir = "uploads/pengukuran/";
if (!is_dir($upload_dir)) {
    echo '<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">Folder uploads belum dibuat</h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <p>Folder <code>uploads/pengukuran/</code> tidak ditemukan. Sistem akan mencoba membuat folder ini secara otomatis.</p>
                    <p class="mt-1">Jika pesan ini tetap muncul, buat folder manual:</p>
                    <pre class="mt-2 bg-yellow-100 p-2 rounded text-xs">mkdir -p uploads/pengukuran && chmod 777 uploads/pengukuran</pre>
                </div>
            </div>
        </div>
    </div>';
} elseif (!is_writable($upload_dir)) {
    echo '<div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">Permission folder tidak cukup</h3>
                <div class="mt-2 text-sm text-red-700">
                    <p>Folder <code>uploads/pengukuran/</code> tidak dapat ditulisi.</p>
                    <p class="mt-1">Perbaiki permission dengan perintah:</p>
                    <pre class="mt-2 bg-red-100 p-2 rounded text-xs">chmod 777 uploads/pengukuran</pre>
                    <p class="mt-2">Atau untuk Linux:</p>
                    <pre class="mt-2 bg-red-100 p-2 rounded text-xs">sudo chown -R www-data:www-data uploads/ && sudo chmod -R 755 uploads/</pre>
                </div>
            </div>
        </div>
    </div>';
} else {
    echo '<div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-green-800">Folder uploads siap digunakan</h3>
                <div class="mt-2 text-sm text-green-700">
                    <p>Folder <code>uploads/pengukuran/</code> dapat ditulisi.</p>
                </div>
            </div>
        </div>
    </div>';
}
?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2">
        <form method="POST" action="" enctype="multipart/form-data" class="bg-white shadow-md rounded-lg p-6">
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Anak *</label>
                <select name="anak_id" id="anakSelect" required
                    class="block w-full border border-gray-300 rounded-md shadow-sm py-3 px-4 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    <option value="">-- Pilih Anak --</option>
                    <?php
                    if ($result_anak && mysqli_num_rows($result_anak) > 0):
                        while ($row = mysqli_fetch_assoc($result_anak)):
                            $usia = '';
                            if (!empty($row['tgl_lahir'])) {
                                $birthDate = new DateTime($row['tgl_lahir']);
                                $today = new DateTime();
                                $age = $today->diff($birthDate);
                                if ($age->y > 0) {
                                    $usia = $age->y . ' tahun';
                                } elseif ($age->m > 0) {
                                    $usia = $age->m . ' bulan';
                                } else {
                                    $usia = $age->d . ' hari';
                                }
                            }
                            $selected = ($selected_anak_id == $row['id']) ? 'selected' : '';
                            ?>
                            <option value="<?php echo $row['id']; ?>" <?php echo $selected; ?>>
                                <?php echo htmlspecialchars($row['nama_anak']); ?>
                                (<?php echo $row['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?>,
                                Usia: <?php echo $usia; ?>)
                            </option>
                        <?php
                        endwhile;
                    else:
                        ?>
                        <option value="">Tidak ada data anak</option>
                    <?php endif; ?>
                </select>
                <?php if (!($result_anak && mysqli_num_rows($result_anak) > 0)): ?>
                    <p class="text-sm text-red-600 mt-2">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Tidak ada data anak. Silakan tambah data anak terlebih dahulu.
                    </p>
                <?php endif; ?>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pengukuran *</label>
                <input type="date" name="tanggal_pengukuran" required value="<?php echo date('Y-m-d'); ?>"
                    max="<?php echo date('Y-m-d'); ?>"
                    class="block w-full border border-gray-300 rounded-md shadow-sm py-3 px-4 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Berat Badan (kg)</label>
                    <div class="flex">
                        <div class="relative flex-grow">
                            <input type="number" step="0.01" min="0" max="100" name="berat_badan" id="berat_badan"
                                class="block w-full border border-gray-300 rounded-l-md shadow-sm py-3 px-4 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                                placeholder="Contoh: 5.20">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">kg</span>
                            </div>
                        </div>
                        <button type="button" id="syncWeight"
                            class="inline-flex items-center px-4 py-3 border border-transparent text-sm font-medium rounded-r-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-sync-alt mr-2"></i>Sinkron
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Tekan tombol sinkron untuk mengambil data berat dari
                        timbangan.</p>
                    <div id="syncWeightStatus" class="hidden mt-2 text-sm"></div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Panjang Badan (cm)</label>
                    <div class="flex">
                        <div class="relative flex-grow">
                            <input type="number" step="0.01" min="0" max="200" name="panjang_badan" id="panjang_badan"
                                class="block w-full border border-gray-300 rounded-l-md shadow-sm py-3 px-4 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                                placeholder="Contoh: 60.5">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">cm</span>
                            </div>
                        </div>
                        <button type="button" id="syncHeight"
                            class="inline-flex items-center px-4 py-3 border border-transparent text-sm font-medium rounded-r-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-sync-alt mr-2"></i>Sinkron
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Tekan tombol sinkron untuk mengambil data terbaru dari sensor.
                    </p>
                    <div id="syncStatus" class="hidden mt-2 text-sm"></div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lingkar Kepala (cm)</label>
                    <div class="relative">
                        <input type="number" step="0.01" min="0" max="100" name="lingkar_kepala"
                            class="block w-full border border-gray-300 rounded-md shadow-sm py-3 px-4 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                            placeholder="Contoh: 40.2">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">cm</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lingkar Lengan (cm)</label>
                    <div class="relative">
                        <input type="number" step="0.01" min="0" max="100" name="lingkar_lengan"
                            class="block w-full border border-gray-300 rounded-md shadow-sm py-3 px-4 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                            placeholder="Contoh: 12.5">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">cm</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-4">Foto Pengukuran</label>

                <?php include 'includes/webcam.php'; ?>

                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Foto yang Akan Disimpan:</h4>
                    <div id="finalPhotoPreview"
                        class="flex flex-col items-center justify-center p-4 border-2 border-dashed border-gray-300 rounded-lg">
                        <div id="finalPhotoPlaceholder" class="text-center">
                            <i class="fas fa-camera text-4xl text-gray-400 mb-2"></i>
                            <p class="text-sm text-gray-500">Belum ada foto yang dipilih</p>
                        </div>
                        <img id="finalPhotoImage" src="" alt="Foto Final" class="w-full max-w-xs rounded-lg hidden">
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                <textarea name="catatan" rows="3"
                    class="block w-full border border-gray-300 rounded-md shadow-sm py-3 px-4 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                    placeholder="Tambahkan catatan khusus jika diperlukan..."></textarea>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="anak.php"
                    class="px-6 py-3 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit"
                    class="px-6 py-3 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <i class="fas fa-save mr-2"></i>Simpan Pengukuran
                </button>
            </div>
        </form>
    </div>

    <div class="lg:col-span-1">
        <div id="anakInfo" class="bg-purple-50 border border-purple-200 rounded-lg p-6 hidden">
            <h3 class="text-lg font-semibold text-purple-800 mb-4">
                <i class="fas fa-child mr-2"></i>Info Anak Terpilih
            </h3>
            <div id="anakInfoContent" class="text-sm text-purple-700">
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('anakSelect').addEventListener('change', function () {
        const anakId = this.value;
        const anakInfoDiv = document.getElementById('anakInfo');
        const anakInfoContent = document.getElementById('anakInfoContent');

        if (anakId) {
            const selectedOption = this.options[this.selectedIndex];
            anakInfoContent.innerHTML = `
            <div class="space-y-2">
                <p><span class="font-medium">Nama:</span> ${selectedOption.text.split('(')[0].trim()}</p>
                <p><span class="font-medium">Dipilih:</span> ${selectedOption.text}</p>
                <p class="text-xs text-purple-600 mt-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    Pastikan data anak sudah benar sebelum menyimpan pengukuran.
                </p>
            </div>
        `;
            anakInfoDiv.classList.remove('hidden');
        } else {
            anakInfoDiv.classList.add('hidden');
        }
    });

    <?php if ($selected_anak_id > 0): ?>
        document.addEventListener('DOMContentLoaded', function () {
            const anakSelect = document.getElementById('anakSelect');
            if (anakSelect) {
                anakSelect.dispatchEvent(new Event('change'));
            }
        });
    <?php endif; ?>

    document.querySelector('form').addEventListener('submit', function (e) {
        const anakId = document.querySelector('select[name="anak_id"]').value;
        const tanggal = document.querySelector('input[name="tanggal_pengukuran"]').value;
        const fotoData = document.getElementById('webcamPhotoData').value;
        const fileInput = document.getElementById('fileUpload');

        if (!anakId) {
            e.preventDefault();
            alert('Silakan pilih anak terlebih dahulu.');
            return;
        }

        if (!tanggal) {
            e.preventDefault();
            alert('Silakan isi tanggal pengukuran.');
            return;
        }

        if (!fotoData && (!fileInput.files || fileInput.files.length === 0)) {
            if (!confirm('Anda belum menambahkan foto pengukuran. Lanjutkan tanpa foto?')) {
                e.preventDefault();
                return;
            }
        }
    });

    document.getElementById('syncHeight').addEventListener('click', function () {
        const button = this;
        const heightInput = document.getElementById('panjang_badan');
        const statusDiv = document.getElementById('syncStatus');

        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyinkronkan...';
        button.disabled = true;
        statusDiv.className = 'mt-2 text-sm text-blue-600';
        statusDiv.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Mengambil data dari sensor...';
        statusDiv.classList.remove('hidden');

        fetch('iot/get_latest_height.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    heightInput.value = data.height;

                    statusDiv.className = 'mt-2 text-sm text-green-600';
                    statusDiv.innerHTML = `<i class="fas fa-check-circle mr-1"></i>Berhasil! Tinggi badan: ${data.height} cm (${data.timestamp})`;

                    heightInput.classList.add('border-green-500', 'bg-green-50');
                    setTimeout(() => {
                        heightInput.classList.remove('border-green-500', 'bg-green-50');
                    }, 2000);

                    showNotification('Data tinggi badan berhasil disinkronisasi!', 'success');
                } else {
                    statusDiv.className = 'mt-2 text-sm text-red-600';
                    statusDiv.innerHTML = `<i class="fas fa-exclamation-circle mr-1"></i>${data.message}`;

                    showNotification('Gagal mengambil data dari sensor', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                statusDiv.className = 'mt-2 text-sm text-red-600';
                statusDiv.innerHTML = '<i class="fas fa-exclamation-circle mr-1"></i>Terjadi kesalahan saat menyinkronkan. Periksa koneksi server.';
                showNotification('Terjadi kesalahan pada sistem', 'error');
            })
            .finally(() => {
                button.innerHTML = originalText;
                button.disabled = false;
            });
    });

    function showNotification(message, type = 'info') {
        const oldNotification = document.querySelector('.mqtt-notification');
        if (oldNotification) {
            oldNotification.remove();
        }

        const colors = {
            'success': 'bg-green-50 border-green-200 text-green-800',
            'error': 'bg-red-50 border-red-200 text-red-800',
            'warning': 'bg-yellow-50 border-yellow-200 text-yellow-800',
            'info': 'bg-blue-50 border-blue-200 text-blue-800'
        };

        const icons = {
            'success': 'fa-check-circle',
            'error': 'fa-exclamation-circle',
            'warning': 'fa-exclamation-triangle',
            'info': 'fa-info-circle'
        };

        const notification = document.createElement('div');
        notification.className = `mqtt-notification fixed top-4 right-4 z-50 ${colors[type]} border rounded-lg shadow-lg p-4 max-w-sm transition-all duration-300 transform translate-x-0`;
        notification.innerHTML = `
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas ${icons[type]} ${type === 'success' ? 'text-green-400' : type === 'error' ? 'text-red-400' : type === 'warning' ? 'text-yellow-400' : 'text-blue-400'}"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium">${message}</p>
            </div>
            <div class="ml-auto pl-3">
                <button onclick="this.parentElement.parentElement.remove()" class="inline-flex text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    `;

        document.body.appendChild(notification);

        setTimeout(() => {
            if (notification.parentNode) {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.remove();
                    }
                }, 300);
            }
        }, 5000);
    }

    let autoSyncInterval = null;

    function startAutoSync(interval = 10000) {
        if (autoSyncInterval) clearInterval(autoSyncInterval);

        autoSyncInterval = setInterval(() => {
            const heightInput = document.getElementById('panjang_badan');
            if (!heightInput.value && document.activeElement !== heightInput) {
                console.log('Auto-sync triggered...');
                document.getElementById('syncHeight').click();
            }
        }, interval);
    }

    function stopAutoSync() {
        if (autoSyncInterval) {
            clearInterval(autoSyncInterval);
            autoSyncInterval = null;
        }
    }

    document.getElementById('syncWeight').addEventListener('click', function () {
        const button = this;
        const weightInput = document.getElementById('berat_badan');
        const statusDiv = document.getElementById('syncWeightStatus');

        const originalText = button.innerHTML;

        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyinkronkan...';
        button.disabled = true;

        statusDiv.className = 'mt-2 text-sm text-blue-600';
        statusDiv.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Mengambil data dari timbangan...';
        statusDiv.classList.remove('hidden');

        fetch('iot/get_latest_weight.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    weightInput.value = data.weight;

                    statusDiv.className = 'mt-2 text-sm text-green-600';
                    statusDiv.innerHTML =
                        `<i class="fas fa-check-circle mr-1"></i>Berhasil! Berat badan: ${data.weight} kg (${data.timestamp})`;

                    weightInput.classList.add('border-green-500', 'bg-green-50');
                    setTimeout(() => {
                        weightInput.classList.remove('border-green-500', 'bg-green-50');
                    }, 2000);

                    showNotification('Data berat badan berhasil disinkronisasi!', 'success');
                } else {
                    statusDiv.className = 'mt-2 text-sm text-red-600';
                    statusDiv.innerHTML =
                        `<i class="fas fa-exclamation-circle mr-1"></i>${data.message}`;

                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                statusDiv.className = 'mt-2 text-sm text-red-600';
                statusDiv.innerHTML =
                    '<i class="fas fa-exclamation-circle mr-1"></i>Terjadi kesalahan koneksi ke server';

                showNotification('Gagal menghubungi server', 'error');
            })
            .finally(() => {
                button.innerHTML = originalText;
                button.disabled = false;
            });
    });

    document.addEventListener('DOMContentLoaded', function () {
        console.log('Input pengukuran page loaded');
    });
</script>

<?php include 'includes/footer.php'; ?>