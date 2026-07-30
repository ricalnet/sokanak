<?php
require_once '../config/database.php';
check_login();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

function getLatestHeightFromMQTT()
{
    $output = null;
    $return_var = null;

    $command = 'mosquitto_sub -h 192.168.0.50 -t "iot/ultrasonic" -u ultrasonic -P changeme -C 1 -W 3 2>&1';

    exec($command, $output, $return_var);

    if ($return_var === 0 && !empty($output)) {
        $latestData = trim(end($output));

        if (is_numeric($latestData)) {
            $height = floatval($latestData);

            if ($height >= 30 && $height <= 200) {
                return $height;
            } else {
                error_log("Data MQTT diluar rentang normal: " . $height . " cm");
                return null;
            }
        }
    }

    return null;
}

try {
    $height = getLatestHeightFromMQTT();

    if ($height !== null) {
        echo json_encode([
            'success' => true,
            'height' => $height,
            'unit' => 'cm',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Tidak dapat membaca data dari sensor. Pastikan sensor aktif dan terhubung.'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
