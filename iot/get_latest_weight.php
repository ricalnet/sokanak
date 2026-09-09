<?php
require_once '../config/database.php';
check_login();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

function getLatestWeightFromMQTT()
{
    $output = null;
    $return_var = null;

    $command = 'mosquitto_sub -h 192.168.0.50 -t "iot/hx711" -u hx711 -P changeme -C 1 -W 3 2>&1';

    exec($command, $output, $return_var);

    if ($return_var === 0 && !empty($output)) {
        $latestData = trim(end($output));

        if (is_numeric($latestData)) {
            $weight = floatval($latestData);

            if ($weight >= 0 && $weight <= 100) {
                return round($weight, 2);
            } else {
                error_log("Data MQTT berat di luar rentang normal: {$weight} kg");
                return null;
            }
        }
    }

    return null;
}

try {
    $weight = getLatestWeightFromMQTT();

    if ($weight !== null) {
        echo json_encode([
            'success' => true,
            'weight' => $weight,
            'unit' => 'kg',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Tidak dapat membaca data berat badan dari sensor. Pastikan HX711 aktif dan terhubung.'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
