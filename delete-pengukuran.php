<?php
require_once 'config/database.php';
check_login();

$measurement_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$anak_id = isset($_GET['anak_id']) ? intval($_GET['anak_id']) : 0;

if ($measurement_id <= 0 || $anak_id <= 0) {
    echo "<script>alert('Parameter tidak valid!'); window.location='anak.php';</script>";
    exit();
}

$user_desa = $_SESSION['desa'];

$query = "SELECT p.*, a.desa FROM pengukuran p 
          JOIN anak a ON p.anak_id = a.id 
          WHERE p.id = $measurement_id";

if (is_kader()) {
    $query .= " AND a.desa = '$user_desa'";
}

$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $measurement = mysqli_fetch_assoc($result);

    if (!empty($measurement['foto_pengukuran']) && file_exists($measurement['foto_pengukuran'])) {
        unlink($measurement['foto_pengukuran']);
    }

    $delete_query = "DELETE FROM pengukuran WHERE id = $measurement_id";
    if (mysqli_query($conn, $delete_query)) {
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
        } else {
            $update_anak_query = "UPDATE anak SET 
                berat_badan = NULL,
                panjang_badan = NULL,
                lingkar_kepala = NULL,
                lingkar_lengan = NULL,
                foto_pengukuran = NULL
                WHERE id = $anak_id";
            mysqli_query($conn, $update_anak_query);
        }

        echo "<script>alert('Data pengukuran berhasil dihapus!'); window.location='riwayat-pengukuran.php?anak_id=$anak_id';</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "'); window.location='riwayat-pengukuran.php?anak_id=$anak_id';</script>";
    }
} else {
    echo "<script>alert('Data tidak ditemukan atau tidak memiliki akses!'); window.location='anak.php';</script>";
}
?>