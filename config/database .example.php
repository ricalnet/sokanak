<?php
session_start();

$host = 'localhost';
$username = 'admin';            # sesuaikan
$password = 'changeme';         # sesuaikan
$database = 'posyandu_db';      # sesuaikan

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

date_default_timezone_set('Asia/Jakarta');

$kecamatan_list = ['Pacet', 'Lainnya'];
$desa_list = [
    'Pacet' => ['Sukarame', 'Cikawao', 'Cikitu'],
    'Lainnya' => ['Lainnya']
    // 'Ciparay' => ['Mekarjaya', 'Mekarmulya', 'Mekarwangi']
];
$rw_list = ['001', '002', '003', '004', '005', '006', '007', '008', '009', '010', 'Lainnya'];

function clean_input($data, $conn)
{
    return mysqli_real_escape_string($conn, htmlspecialchars(strip_tags(trim($data))));
}

function check_login()
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

function is_kader()
{
    return $_SESSION['role'] === 'kader';
}

function is_admin()
{
    return $_SESSION['role'] === 'admin';
}
?>