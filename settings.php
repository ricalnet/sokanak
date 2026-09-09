<?php
require_once 'config/database.php';
check_login();

if (!is_admin()) {
    header("Location: dashboard.php");
    exit();
}

$page_title = "Admin - Manajemen Pengguna";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = clean_input($_POST['action'], $conn);

    switch ($action) {
        case 'add_user':
            $username = clean_input($_POST['username'], $conn);
            $nama_lengkap = clean_input($_POST['nama_lengkap'], $conn);
            $desa = clean_input($_POST['desa'], $conn);
            $kecamatan = clean_input($_POST['kecamatan'], $conn);
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            $check_query = "SELECT id FROM users WHERE username = '$username'";
            $check_result = mysqli_query($conn, $check_query);

            if (mysqli_num_rows($check_result) > 0) {
                $_SESSION['error_message'] = "Username sudah digunakan. Silakan pilih username lain.";
            } elseif ($password !== $confirm_password) {
                $_SESSION['error_message'] = "Password dan konfirmasi password tidak cocok.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $insert_query = "INSERT INTO users (username, password, nama_lengkap, desa, kecamatan, role, must_change_password) 
                                VALUES ('$username', '$hashed_password', '$nama_lengkap', '$desa', '$kecamatan', 'kader', TRUE)";

                if (mysqli_query($conn, $insert_query)) {
                    $_SESSION['success_message'] = "User kader berhasil ditambahkan.";

                    $user_id = $_SESSION['user_id'];
                    $log_query = "INSERT INTO logs (user_id, action, table_name, record_id, new_data, ip_address, user_agent) 
                                 VALUES ($user_id, 'CREATE', 'users', LAST_INSERT_ID(), 
                                         '{\"username\": \"$username\", \"nama_lengkap\": \"$nama_lengkap\"}', 
                                         '" . $_SERVER['REMOTE_ADDR'] . "', '" . $_SERVER['HTTP_USER_AGENT'] . "')";
                    mysqli_query($conn, $log_query);
                } else {
                    $_SESSION['error_message'] = "Terjadi kesalahan: " . mysqli_error($conn);
                }
            }
            break;

        case 'edit_user':
            $user_id = intval($_POST['user_id']);
            $nama_lengkap = clean_input($_POST['edit_nama_lengkap'], $conn);
            $desa = clean_input($_POST['edit_desa'], $conn);
            $kecamatan = clean_input($_POST['edit_kecamatan'], $conn);

            $old_data_query = "SELECT nama_lengkap, desa, kecamatan FROM users WHERE id = $user_id";
            $old_data_result = mysqli_query($conn, $old_data_query);
            $old_data = mysqli_fetch_assoc($old_data_result);

            $update_query = "UPDATE users SET 
                            nama_lengkap = '$nama_lengkap',
                            desa = '$desa',
                            kecamatan = '$kecamatan',
                            updated_at = NOW()
                            WHERE id = $user_id";

            if (mysqli_query($conn, $update_query)) {
                $_SESSION['success_message'] = "Data kader berhasil diperbarui.";

                $current_user_id = $_SESSION['user_id'];
                $new_data = json_encode(['nama_lengkap' => $nama_lengkap, 'desa' => $desa, 'kecamatan' => $kecamatan]);
                $old_data_json = json_encode($old_data);

                $log_query = "INSERT INTO logs (user_id, action, table_name, record_id, old_data, new_data, ip_address, user_agent) 
                             VALUES ($current_user_id, 'UPDATE', 'users', $user_id, '$old_data_json', '$new_data', 
                                     '" . $_SERVER['REMOTE_ADDR'] . "', '" . $_SERVER['HTTP_USER_AGENT'] . "')";
                mysqli_query($conn, $log_query);
            } else {
                $_SESSION['error_message'] = "Terjadi kesalahan: " . mysqli_error($conn);
            }
            break;

        case 'change_password':
            $user_id = intval($_POST['user_id']);
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_new_password'];

            if ($new_password !== $confirm_password) {
                $_SESSION['error_message'] = "Password baru dan konfirmasi tidak cocok.";
            } else {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                $update_query = "UPDATE users SET 
                                password = '$hashed_password',
                                must_change_password = FALSE,
                                updated_at = NOW()
                                WHERE id = $user_id";

                if (mysqli_query($conn, $update_query)) {
                    $_SESSION['success_message'] = "Password kader berhasil diubah.";

                    $current_user_id = $_SESSION['user_id'];
                    $log_query = "INSERT INTO logs (user_id, action, table_name, record_id, ip_address, user_agent) 
                                 VALUES ($current_user_id, 'UPDATE', 'users', $user_id, 
                                         '" . $_SERVER['REMOTE_ADDR'] . "', '" . $_SERVER['HTTP_USER_AGENT'] . "')";
                    mysqli_query($conn, $log_query);
                } else {
                    $_SESSION['error_message'] = "Terjadi kesalahan: " . mysqli_error($conn);
                }
            }
            break;

        case 'delete_user':
            $user_id = intval($_POST['user_id']);

            $user_info_query = "SELECT username, nama_lengkap FROM users WHERE id = $user_id";
            $user_info_result = mysqli_query($conn, $user_info_query);
            $user_info = mysqli_fetch_assoc($user_info_result);

            $delete_query = "UPDATE users SET 
                            status = 'inactive',
                            updated_at = NOW()
                            WHERE id = $user_id";

            if (mysqli_query($conn, $delete_query)) {
                $_SESSION['success_message'] = "User kader berhasil dinonaktifkan.";

                $current_user_id = $_SESSION['user_id'];
                $old_data = json_encode($user_info);
                $log_query = "INSERT INTO logs (user_id, action, table_name, record_id, old_data, ip_address, user_agent) 
                             VALUES ($current_user_id, 'DELETE', 'users', $user_id, '$old_data', 
                                     '" . $_SERVER['REMOTE_ADDR'] . "', '" . $_SERVER['HTTP_USER_AGENT'] . "')";
                mysqli_query($conn, $log_query);
            } else {
                $_SESSION['error_message'] = "Terjadi kesalahan: " . mysqli_error($conn);
            }
            break;

        case 'reactivate_user':
            $user_id = intval($_POST['user_id']);

            $reactivate_query = "UPDATE users SET 
                                status = 'active',
                                updated_at = NOW()
                                WHERE id = $user_id";

            if (mysqli_query($conn, $reactivate_query)) {
                $_SESSION['success_message'] = "User kader berhasil diaktifkan kembali.";
            } else {
                $_SESSION['error_message'] = "Terjadi kesalahan: " . mysqli_error($conn);
            }
            break;
    }

    header("Location: settings.php");
    exit();
}

$query = "SELECT * FROM users WHERE role = 'kader' ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

$total_kader = mysqli_num_rows($result);
$active_kader = 0;
$inactive_kader = 0;

mysqli_data_seek($result, 0);
while ($row = mysqli_fetch_assoc($result)) {
    if ($row['status'] == 'active') {
        $active_kader++;
    } else {
        $inactive_kader++;
    }
}

mysqli_data_seek($result, 0);
?>

<?php include 'includes/header.php'; ?>

<div class="fade-in">
    <div class="mb-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Manajemen Pengguna Kader</h1>
                <p class="text-gray-600 mt-2">Kelola akun kader Posyandu</p>
            </div>
            <button onclick="openAddUserModal()" class="btn-primary">
                <i class="fas fa-user-plus mr-2"></i>Tambah Kader Baru
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl p-6 card-shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-blue-100 text-blue-600 mr-4">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Total Kader</p>
                        <p class="text-2xl font-bold"><?php echo $total_kader; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 card-shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-green-100 text-green-600 mr-4">
                        <i class="fas fa-user-check text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Kader Aktif</p>
                        <p class="text-2xl font-bold"><?php echo $active_kader; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 card-shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-gray-100 text-gray-600 mr-4">
                        <i class="fas fa-user-slash text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Kader Nonaktif</p>
                        <p class="text-2xl font-bold"><?php echo $inactive_kader; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-lg">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-3"></i>
                <span><?php echo $_SESSION['success_message'];
                unset($_SESSION['success_message']); ?></span>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-lg">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-3"></i>
                <span><?php echo $_SESSION['error_message'];
                unset($_SESSION['error_message']); ?></span>
            </div>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl card-shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Daftar Kader Posyandu</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Username</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama
                            Lengkap</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Desa
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Kecamatan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Terakhir Login</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($user = mysqli_fetch_assoc($result)): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-semibold"
                                                style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--primary-blue) 100%);">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <div class="font-medium text-gray-900">
                                                <?php echo htmlspecialchars($user['username']); ?></div>
                                            <?php if ($user['must_change_password']): ?>
                                                <span class="text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full">Password
                                                    harus diubah</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-900">
                                    <?php echo htmlspecialchars($user['nama_lengkap']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                                    <?php echo htmlspecialchars($user['desa']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                                    <?php echo htmlspecialchars($user['kecamatan']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($user['status'] == 'active'): ?>
                                        <span class="px-3 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                            <i class="fas fa-circle text-xs mr-1"></i>Aktif
                                        </span>
                                    <?php else: ?>
                                        <span class="px-3 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">
                                            <i class="fas fa-circle text-xs mr-1"></i>Nonaktif
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                                    <?php echo $user['last_login'] ? date('d M Y, H:i', strtotime($user['last_login'])) : 'Belum login'; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button
                                            onclick="openEditUserModal(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['nama_lengkap'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($user['desa'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($user['kecamatan'], ENT_QUOTES); ?>')"
                                            class="text-blue-600 hover:text-blue-900" title="Edit Data">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button
                                            onclick="openChangePasswordModal(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username'], ENT_QUOTES); ?>')"
                                            class="text-yellow-600 hover:text-yellow-900" title="Ubah Password">
                                            <i class="fas fa-key"></i>
                                        </button>
                                        <?php if ($user['status'] == 'active'): ?>
                                            <button
                                                onclick="confirmDelete(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username'], ENT_QUOTES); ?>')"
                                                class="text-red-600 hover:text-red-900" title="Nonaktifkan">
                                                <i class="fas fa-user-slash"></i>
                                            </button>
                                        <?php else: ?>
                                            <button
                                                onclick="confirmReactivate(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username'], ENT_QUOTES); ?>')"
                                                class="text-green-600 hover:text-green-900" title="Aktifkan Kembali">
                                                <i class="fas fa-user-check"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                                    <p class="text-lg">Belum ada kader terdaftar</p>
                                    <p class="text-sm mt-1">Mulai dengan menambahkan kader baru</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <p class="text-sm text-gray-600">
                Menampilkan <span class="font-medium"><?php echo mysqli_num_rows($result); ?></span> kader
            </p>
        </div>
    </div>
</div>

<div id="addUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-xl bg-white">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-gray-900">Tambah Kader Baru</h3>
            <button onclick="closeAddUserModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form method="POST" action="settings.php" class="space-y-4">
            <input type="hidden" name="action" value="add_user">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Username *</label>
                <input type="text" name="username" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-red focus:border-transparent">
                <p class="text-xs text-gray-500 mt-1">Username untuk login (unik)</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap *</label>
                <input type="text" name="nama_lengkap" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-red focus:border-transparent">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kecamatan *</label>
                    <select name="kecamatan" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-red focus:border-transparent"
                        onchange="updateDesaOptions(this.value, 'desa')">
                        <option value="">Pilih Kecamatan</option>
                        <?php foreach ($kecamatan_list as $kec): ?>
                            <option value="<?php echo $kec; ?>"><?php echo $kec; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Desa *</label>
                    <select name="desa" id="desa" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-red focus:border-transparent">
                        <option value="">Pilih Desa</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password *</label>
                <input type="password" name="password" id="newPassword" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-red focus:border-transparent">
                <div class="mt-2 space-y-1">
                    <div class="flex items-center">
                        <input type="checkbox" onclick="togglePasswordVisibility('newPassword')" class="mr-2">
                        <span class="text-xs text-gray-600">Tampilkan Password</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Minimal 8 karakter</p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password *</label>
                <input type="password" name="confirm_password" id="confirmPassword" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-red focus:border-transparent">
                <div class="flex items-center mt-2">
                    <input type="checkbox" onclick="togglePasswordVisibility('confirmPassword')" class="mr-2">
                    <span class="text-xs text-gray-600">Tampilkan Password</span>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeAddUserModal()" class="btn-secondary px-6">Batal</button>
                <button type="submit" class="btn-primary px-6">Tambah Kader</button>
            </div>
        </form>
    </div>
</div>

<div id="editUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-xl bg-white">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-gray-900">Edit Data Kader</h3>
            <button onclick="closeEditUserModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form method="POST" action="settings.php" id="editUserForm" class="space-y-4">
            <input type="hidden" name="action" value="edit_user">
            <input type="hidden" name="user_id" id="edit_user_id">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input type="text" id="edit_username" disabled
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap *</label>
                <input type="text" name="edit_nama_lengkap" id="edit_nama_lengkap" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-red focus:border-transparent">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kecamatan *</label>
                    <select name="edit_kecamatan" id="edit_kecamatan" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-red focus:border-transparent">
                        <option value="">Pilih Kecamatan</option>
                        <?php foreach ($kecamatan_list as $kec): ?>
                            <option value="<?php echo $kec; ?>"><?php echo $kec; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Desa *</label>
                    <select name="edit_desa" id="edit_desa" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-red focus:border-transparent">
                        <option value="">Pilih Desa</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeEditUserModal()" class="btn-secondary px-6">Batal</button>
                <button type="submit" class="btn-primary px-6">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<div id="changePasswordModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-xl bg-white">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-gray-900">Ubah Password Kader</h3>
            <button onclick="closeChangePasswordModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form method="POST" action="settings.php" id="changePasswordForm" class="space-y-4">
            <input type="hidden" name="action" value="change_password">
            <input type="hidden" name="user_id" id="password_user_id">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input type="text" id="password_username" disabled
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru *</label>
                <input type="password" name="new_password" id="change_new_password" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-red focus:border-transparent">
                <div class="mt-2 space-y-1">
                    <div class="flex items-center">
                        <input type="checkbox" onclick="togglePasswordVisibility('change_new_password')" class="mr-2">
                        <span class="text-xs text-gray-600">Tampilkan Password</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Minimal 8 karakter</p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru *</label>
                <input type="password" name="confirm_new_password" id="change_confirm_password" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-red focus:border-transparent">
                <div class="flex items-center mt-2">
                    <input type="checkbox" onclick="togglePasswordVisibility('change_confirm_password')" class="mr-2">
                    <span class="text-xs text-gray-600">Tampilkan Password</span>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeChangePasswordModal()" class="btn-secondary px-6">Batal</button>
                <button type="submit" class="btn-primary px-6">Ubah Password</button>
            </div>
        </form>
    </div>
</div>

<div id="confirmModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-xl bg-white">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2" id="confirmTitle"></h3>
            <p class="text-sm text-gray-500 mb-6" id="confirmMessage"></p>

            <form method="POST" action="settings.php" id="confirmForm">
                <input type="hidden" name="action" id="confirmAction">
                <input type="hidden" name="user_id" id="confirmUserId">

                <div class="flex justify-center space-x-4">
                    <button type="button" onclick="closeConfirmModal()" class="btn-secondary px-6">Batal</button>
                    <button type="submit" class="btn-primary px-6" id="confirmButton"></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openAddUserModal() {
        document.getElementById('addUserModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeAddUserModal() {
        document.getElementById('addUserModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        document.getElementById('addUserModal').querySelector('form').reset();
    }

    function openEditUserModal(userId, nama, desa, kecamatan) {
        document.getElementById('edit_user_id').value = userId;
        document.getElementById('edit_nama_lengkap').value = nama;
        document.getElementById('edit_desa').value = desa;
        document.getElementById('edit_kecamatan').value = kecamatan;

        document.getElementById('edit_username').value = 'Loading...';

        updateDesaOptions(kecamatan, 'edit_desa');

        document.getElementById('editUserModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');

        fetch(`api/get_user.php?id=${userId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('edit_username').value = data.username;
                }
            });
    }

    function closeEditUserModal() {
        document.getElementById('editUserModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        document.getElementById('editUserForm').reset();
    }

    function openChangePasswordModal(userId, username) {
        document.getElementById('password_user_id').value = userId;
        document.getElementById('password_username').value = username;
        document.getElementById('changePasswordModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeChangePasswordModal() {
        document.getElementById('changePasswordModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        document.getElementById('changePasswordForm').reset();
    }

    function togglePasswordVisibility(inputId) {
        const input = document.getElementById(inputId);
        input.type = input.type === 'password' ? 'text' : 'password';
    }

    function confirmDelete(userId, username) {
        document.getElementById('confirmTitle').textContent = 'Nonaktifkan Kader';
        document.getElementById('confirmMessage').textContent = `Anda yakin ingin menonaktifkan kader "${username}"? Kader tidak akan bisa login sampai diaktifkan kembali.`;
        document.getElementById('confirmAction').value = 'delete_user';
        document.getElementById('confirmUserId').value = userId;
        document.getElementById('confirmButton').textContent = 'Nonaktifkan';
        document.getElementById('confirmButton').classList.remove('bg-green-600', 'hover:bg-green-700');
        document.getElementById('confirmButton').classList.add('bg-red-600', 'hover:bg-red-700');
        document.getElementById('confirmModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function confirmReactivate(userId, username) {
        document.getElementById('confirmTitle').textContent = 'Aktifkan Kembali Kader';
        document.getElementById('confirmMessage').textContent = `Anda yakin ingin mengaktifkan kembali kader "${username}"?`;
        document.getElementById('confirmAction').value = 'reactivate_user';
        document.getElementById('confirmUserId').value = userId;
        document.getElementById('confirmButton').textContent = 'Aktifkan';
        document.getElementById('confirmButton').classList.remove('bg-red-600', 'hover:bg-red-700');
        document.getElementById('confirmButton').classList.add('bg-green-600', 'hover:bg-green-700');
        document.getElementById('confirmModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeConfirmModal() {
        document.getElementById('confirmModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        document.getElementById('confirmForm').reset();
    }

    function updateDesaOptions(kecamatan, targetId) {
        const desaSelect = document.getElementById(targetId);
        desaSelect.innerHTML = '<option value="">Pilih Desa</option>';

        const desaMapping = {
            'Pacet': ['Sukarame', 'Cikawao', 'Cikitu']
        };

        if (kecamatan && desaMapping[kecamatan]) {
            desaMapping[kecamatan].forEach(desa => {
                const option = document.createElement('option');
                option.value = desa;
                option.textContent = desa;
                desaSelect.appendChild(option);
            });
        }
    }

    window.onclick = function (event) {
        const modals = ['addUserModal', 'editUserModal', 'changePasswordModal', 'confirmModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (event.target === modal) {
                if (modalId === 'addUserModal') closeAddUserModal();
                if (modalId === 'editUserModal') closeEditUserModal();
                if (modalId === 'changePasswordModal') closeChangePasswordModal();
                if (modalId === 'confirmModal') closeConfirmModal();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const kecamatanSelect = document.querySelector('[name="kecamatan"]');
        if (kecamatanSelect) {
            kecamatanSelect.addEventListener('change', function () {
                updateDesaOptions(this.value, 'desa');
            });
        }

        const editKecamatanSelect = document.getElementById('edit_kecamatan');
        if (editKecamatanSelect) {
            editKecamatanSelect.addEventListener('change', function () {
                updateDesaOptions(this.value, 'edit_desa');
            });
        }
    });
</script>

<?php include 'includes/footer.php'; ?>