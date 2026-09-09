<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';
check_login();

$page_title = "Form Data Anak";
include 'includes/header.php';

$user_id = $_SESSION['user_id'];
$user_desa = $_SESSION['desa'];
$user_kecamatan = $_SESSION['kecamatan'];
$user_role = $_SESSION['role'];

$action = isset($_GET['action']) ? $_GET['action'] : 'add';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$data = [];
$error_message = '';
$success_message = '';

if ($action == 'edit' && $id > 0) {
    $query = "SELECT * FROM anak WHERE id = $id";
    if (is_kader()) {
        $query .= " AND desa = '$user_desa'";
    }
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
    } else {
        echo "<script>alert('Data tidak ditemukan atau tidak memiliki akses!'); window.location='anak.php';</script>";
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tgl_lahir = clean_input($_POST['tgl_lahir'], $conn);
    $jenis_kelamin = clean_input($_POST['jenis_kelamin'], $conn);
    $nomor_KK = clean_input($_POST['nomor_KK'], $conn);
    $NIK = clean_input($_POST['NIK'], $conn);
    $nama_anak = clean_input($_POST['nama_anak'], $conn);
    $berat_badan = !empty($_POST['berat_badan']) ? clean_input($_POST['berat_badan'], $conn) : NULL;
    $panjang_badan = !empty($_POST['panjang_badan']) ? clean_input($_POST['panjang_badan'], $conn) : NULL;
    $lingkar_kepala = !empty($_POST['lingkar_kepala']) ? clean_input($_POST['lingkar_kepala'], $conn) : NULL;
    $lingkar_lengan = !empty($_POST['lingkar_lengan']) ? clean_input($_POST['lingkar_lengan'], $conn) : NULL;
    $nama_ortu = clean_input($_POST['nama_ortu'], $conn);
    $nik_ortu = clean_input($_POST['nik_ortu'], $conn);
    $hp_ortu = clean_input($_POST['hp_ortu'], $conn);
    $nama_wali = clean_input($_POST['nama_wali'], $conn);
    $hp_wali = clean_input($_POST['hp_wali'], $conn);
    $rw = clean_input($_POST['rw'], $conn);

    if (is_kader()) {
        $desa = $user_desa;
        $kecamatan = $user_kecamatan;
    } else {
        $desa = clean_input($_POST['desa'], $conn);
        $kecamatan = clean_input($_POST['kecamatan'], $conn);
    }

    $foto_pengukuran = isset($data['foto_pengukuran']) ? $data['foto_pengukuran'] : '';
    if (isset($_FILES['foto_pengukuran']) && $_FILES['foto_pengukuran']['error'] == 0) {
        $target_dir = "uploads/anak/";
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
                    } else {
                        $error_message = "Gagal mengupload file.";
                    }
                } else {
                    $error_message = "Format file tidak didukung. Hanya JPG, JPEG, PNG, GIF yang diperbolehkan.";
                }
            } else {
                $error_message = "Ukuran file terlalu besar. Maksimal 2MB.";
            }
        } else {
            $error_message = "File yang diupload bukan gambar.";
        }
    }

    if (empty($error_message)) {
        if ($action == 'add') {
            $check_nik = "SELECT id FROM anak WHERE NIK = '$NIK'";
            $result_nik = mysqli_query($conn, $check_nik);
            if (mysqli_num_rows($result_nik) > 0) {
                $error_message = 'NIK sudah terdaftar!';
            }
        } else {
            $check_nik = "SELECT id FROM anak WHERE NIK = '$NIK' AND id != $id";
            $result_nik = mysqli_query($conn, $check_nik);
            if (mysqli_num_rows($result_nik) > 0) {
                $error_message = 'NIK sudah terdaftar oleh anak lain!';
            }
        }
    }

    if (empty($error_message)) {
        if ($action == 'add') {
            $query = "INSERT INTO anak (
                tgl_lahir, jenis_kelamin, nomor_KK, NIK, nama_anak, 
                berat_badan, panjang_badan, lingkar_kepala, lingkar_lengan,
                nama_ortu, nik_ortu, hp_ortu, nama_wali, hp_wali,
                rw, desa, kecamatan, foto_pengukuran, created_by
            ) VALUES (
                '$tgl_lahir', '$jenis_kelamin', '$nomor_KK', '$NIK', '$nama_anak',
                " . ($berat_badan ? "'$berat_badan'" : "NULL") . ",
                " . ($panjang_badan ? "'$panjang_badan'" : "NULL") . ",
                " . ($lingkar_kepala ? "'$lingkar_kepala'" : "NULL") . ",
                " . ($lingkar_lengan ? "'$lingkar_lengan'" : "NULL") . ",
                '$nama_ortu', '$nik_ortu', '$hp_ortu', '$nama_wali', '$hp_wali',
                '$rw', '$desa', '$kecamatan', '$foto_pengukuran', $user_id
            )";

            if (mysqli_query($conn, $query)) {
                $success_message = 'Data berhasil ditambahkan!';
                echo "<script>
                    setTimeout(function() {
                        window.location.href = 'anak.php';
                    }, 1500);
                </script>";
            } else {
                $error_message = 'Error: ' . mysqli_error($conn);
            }
        } else {
            $query = "UPDATE anak SET 
                tgl_lahir = '$tgl_lahir',
                jenis_kelamin = '$jenis_kelamin',
                nomor_KK = '$nomor_KK',
                NIK = '$NIK',
                nama_anak = '$nama_anak',
                berat_badan = " . ($berat_badan ? "'$berat_badan'" : "NULL") . ",
                panjang_badan = " . ($panjang_badan ? "'$panjang_badan'" : "NULL") . ",
                lingkar_kepala = " . ($lingkar_kepala ? "'$lingkar_kepala'" : "NULL") . ",
                lingkar_lengan = " . ($lingkar_lengan ? "'$lingkar_lengan'" : "NULL") . ",
                nama_ortu = '$nama_ortu',
                nik_ortu = '$nik_ortu',
                hp_ortu = '$hp_ortu',
                nama_wali = '$nama_wali',
                hp_wali = '$hp_wali',
                rw = '$rw',
                desa = '$desa',
                kecamatan = '$kecamatan',
                foto_pengukuran = '$foto_pengukuran'
                WHERE id = $id";

            if (mysqli_query($conn, $query)) {
                $success_message = 'Data berhasil diperbarui!';
                echo "<script>
                    setTimeout(function() {
                        window.location.href = 'anak.php';
                    }, 1500);
                </script>";
            } else {
                $error_message = 'Error: ' . mysqli_error($conn);
            }
        }
    }
}
?>

<div class="container mx-auto px-4 py-8">
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="dashboard.php"
                    class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-red-600">
                    <i class="fas fa-home mr-2"></i>Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400"></i>
                    <a href="anak.php" class="ml-1 text-sm font-medium text-gray-700 hover:text-red-600 md:ml-2">Data
                        Anak</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400"></i>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">
                        <?php echo $action == 'add' ? 'Tambah Data' : 'Edit Data'; ?>
                    </span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">
            <?php echo $action == 'add' ? 'Tambah Data Anak' : 'Edit Data Anak'; ?>
        </h1>
        <p class="text-gray-600 mt-2">Isi formulir di bawah ini dengan data lengkap anak</p>
    </div>

    <?php if ($error_message): ?>
        <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700"><?php echo $error_message; ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($success_message): ?>
        <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700"><?php echo $success_message; ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data" class="bg-white shadow-md rounded-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal Lahir <span
                            class="text-red-500">*</span></label>
                    <input type="date" name="tgl_lahir" required
                        value="<?php echo isset($data['tgl_lahir']) ? $data['tgl_lahir'] : ''; ?>"
                        max="<?php echo date('Y-m-d'); ?>"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Jenis Kelamin <span
                            class="text-red-500">*</span></label>
                    <select name="jenis_kelamin" required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="L" <?php echo (isset($data['jenis_kelamin']) && $data['jenis_kelamin'] == 'L') ? 'selected' : ''; ?>>Laki-laki</option>
                        <option value="P" <?php echo (isset($data['jenis_kelamin']) && $data['jenis_kelamin'] == 'P') ? 'selected' : ''; ?>>Perempuan</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Nomor KK <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="nomor_KK" required
                        value="<?php echo isset($data['nomor_KK']) ? $data['nomor_KK'] : ''; ?>"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">NIK Anak <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="NIK" required
                        value="<?php echo isset($data['NIK']) ? $data['NIK'] : ''; ?>"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                        maxlength="16" pattern="[0-9]{16}" title="NIK harus 16 digit angka">
                    <p class="text-xs text-gray-500 mt-1">16 digit angka</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama Anak <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="nama_anak" required
                        value="<?php echo isset($data['nama_anak']) ? $data['nama_anak'] : ''; ?>"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama Orang Tua <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="nama_ortu" required
                        value="<?php echo isset($data['nama_ortu']) ? $data['nama_ortu'] : ''; ?>"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">NIK Orang Tua <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="nik_ortu" required
                        value="<?php echo isset($data['nik_ortu']) ? $data['nik_ortu'] : ''; ?>"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                        maxlength="16" pattern="[0-9]{16}" title="NIK harus 16 digit angka">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">HP Orang Tua <span
                            class="text-red-500">*</span></label>
                    <input type="tel" name="hp_ortu" required
                        value="<?php echo isset($data['hp_ortu']) ? $data['hp_ortu'] : ''; ?>"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                        pattern="[0-9]{10,13}" title="Nomor HP harus 10-13 digit angka">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama Wali</label>
                    <input type="text" name="nama_wali"
                        value="<?php echo isset($data['nama_wali']) ? $data['nama_wali'] : ''; ?>"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">HP Wali</label>
                    <input type="tel" name="hp_wali"
                        value="<?php echo isset($data['hp_wali']) ? $data['hp_wali'] : ''; ?>"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                        pattern="[0-9]{10,13}" title="Nomor HP harus 10-13 digit angka">
                </div>
            </div>
        </div>

        <div class="mt-6 pt-6 border-t border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Alamat</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <?php if (!is_kader()): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kecamatan <span
                                class="text-red-500">*</span></label>
                        <select name="kecamatan" id="kecamatan" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                            <option value="">Pilih Kecamatan</option>
                                <?php foreach ($kecamatan_list as $kec): ?>
                                <option value="<?php echo $kec; ?>" <?php echo (isset($data['kecamatan']) && $data['kecamatan'] == $kec) ? 'selected' : ''; ?>>
                                            <?php echo $kec; ?>
                                </option>
                                <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Desa <span
                                class="text-red-500">*</span></label>
                        <select name="desa" id="desa" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                            <option value="">Pilih Desa</option>
                                <?php
                                if (isset($data['kecamatan']) && !empty($data['kecamatan']) && isset($desa_list[$data['kecamatan']])) {
                                    foreach ($desa_list[$data['kecamatan']] as $desa) {
                                        $selected = (isset($data['desa']) && $data['desa'] == $desa) ? 'selected' : '';
                                        echo "<option value='$desa' $selected>$desa</option>";
                                    }
                                }
                                ?>
                        </select>
                    </div>
                <?php else: ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kecamatan</label>
                        <input type="text" value="<?php echo $user_kecamatan; ?>"
                            class="mt-1 block w-full border border-gray-300 bg-gray-50 rounded-md shadow-sm py-2 px-3 sm:text-sm"
                            readonly>
                        <input type="hidden" name="kecamatan" value="<?php echo $user_kecamatan; ?>">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Desa</label>
                        <input type="text" value="<?php echo $user_desa; ?>"
                            class="mt-1 block w-full border border-gray-300 bg-gray-50 rounded-md shadow-sm py-2 px-3 sm:text-sm"
                            readonly>
                        <input type="hidden" name="desa" value="<?php echo $user_desa; ?>">
                    </div>
                <?php endif; ?>

                <div>
                    <label class="block text-sm font-medium text-gray-700">RW <span
                            class="text-red-500">*</span></label>
                    <select name="rw" required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                        <option value="">Pilih RW</option>
                        <?php foreach ($rw_list as $rw): ?>
                            <option value="<?php echo $rw; ?>" <?php echo (isset($data['rw']) && $data['rw'] == $rw) ? 'selected' : ''; ?>>
                                RW <?php echo $rw; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="mt-8 flex justify-end space-x-3">
            <a href="anak.php"
                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                <i class="fas fa-times mr-2"></i>Batal
            </a>
            <button type="submit"
                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                <i class="fas fa-save mr-2"></i>
                <?php echo $action == 'add' ? 'Simpan Data' : 'Update Data'; ?>
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const kecamatanSelect = document.getElementById('kecamatan');
        const desaSelect = document.getElementById('desa');

        if (kecamatanSelect && desaSelect) {
            const desaData = <?php echo json_encode($desa_list); ?>;

            kecamatanSelect.addEventListener('change', function () {
                const selectedKecamatan = this.value;
                desaSelect.innerHTML = '<option value="">Pilih Desa</option>';

                if (selectedKecamatan && desaData[selectedKecamatan]) {
                    desaData[selectedKecamatan].forEach(function (desa) {
                        const option = document.createElement('option');
                        option.value = desa;
                        option.textContent = desa;
                        desaSelect.appendChild(option);
                    });
                }
            });
        }
    });

    document.querySelectorAll('input[name="NIK"], input[name="nik_ortu"]').forEach(input => {
        input.addEventListener('input', function (e) {
            this.value = this.value.replace(/\D/g, '').slice(0, 16);
        });
    });

    document.querySelectorAll('input[name="hp_ortu"], input[name="hp_wali"]').forEach(input => {
        input.addEventListener('input', function (e) {
            this.value = this.value.replace(/\D/g, '').slice(0, 13);
        });
    });

    document.querySelector('input[name="nomor_KK"]').addEventListener('input', function (e) {
        this.value = this.value.replace(/\D/g, '').slice(0, 16);
    });
</script>

<?php include 'includes/footer.php'; ?>