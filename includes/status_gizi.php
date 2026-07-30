<?php
if (!defined('INCLUDED')) {
    die('Akses langsung tidak diizinkan.');
}

function hitungUsiaBulanWHO($tgl_lahir) {
    if (empty($tgl_lahir)) return 0;
    
    $birthDate = new DateTime($tgl_lahir);
    $today = new DateTime();
    
    $interval = $today->diff($birthDate);
    $usia_bulan = ($interval->y * 12) + $interval->m;
    
    if ($interval->d > 15 && $usia_bulan < 24) {
        $usia_bulan++;
    }
    
    return min(max($usia_bulan, 0), 60);
}

function formatUsiaDisplay($usia_bulan) {
    if ($usia_bulan < 1) {
        return '0 bulan';
    } elseif ($usia_bulan < 12) {
        return floor($usia_bulan) . ' bulan';
    } else {
        $tahun = floor($usia_bulan / 12);
        $bulan = $usia_bulan % 12;
        
        if ($bulan == 0) {
            return $tahun . ' tahun';
        } else {
            return $tahun . ' tahun ' . $bulan . ' bulan';
        }
    }
}

// ============================================
// DATA REFERENSI WHO STANDARD (LMS METHOD)
// ============================================

/**
 * DATA REFERENSI WHO GROWTH STANDARDS
 * Sumber: https://www.who.int/tools/child-growth-standards
 * 
 * Struktur: Usia(bulan) => [L, M, S]
 * L: Box-Cox power
 * M: Median
 * S: Coefficient of variation
 * 
 * Z-score formula: Z = [((X/M)^L) - 1] / (S * L) for L ≠ 0
 *                 Z = [ln(X/M)] / S for L = 0
 */

class WHOGrowthStandards {

    // ========== WEIGHT-FOR-AGE (BB/U) - BOYS 0-60 months ==========
    private static $wfa_boys = [
        0 => [0.3487, 3.3464, 0.14602],
        1 => [0.2297, 4.4709, 0.13395],
        2 => [0.197, 5.5675, 0.12385],
        3 => [0.1738, 6.3762, 0.11727],
        4 => [0.1553, 7.0023, 0.11316],
        5 => [0.1395, 7.5105, 0.1108],
        6 => [0.1257, 7.934, 0.10958],
        7 => [0.1134, 8.297, 0.10902],
        8 => [0.1021, 8.6151, 0.10882],
        9 => [0.0917, 8.9014, 0.10881],
        10 => [0.082, 9.1649, 0.10891],
        11 => [0.073, 9.4122, 0.10906],
        12 => [0.0644, 9.6479, 0.10925],
        13 => [0.0563, 9.8749, 0.10949],
        14 => [0.0487, 10.0953, 0.10976],
        15 => [0.0413, 10.3108, 0.11007],
        16 => [0.0343, 10.5228, 0.11041],
        17 => [0.0275, 10.7319, 0.11079],
        18 => [0.0211, 10.9385, 0.11119],
        19 => [0.0148, 11.143, 0.11164],
        20 => [0.0087, 11.3462, 0.11211],
        21 => [0.0029, 11.5486, 0.11261],
        22 => [-0.0028, 11.7504, 0.11314],
        23 => [-0.0083, 11.9514, 0.11369],
        24 => [-0.0137, 12.1515, 0.11426],
        25 => [-0.0189, 12.3502, 0.11485],
        26 => [-0.024, 12.5466, 0.11544],
        27 => [-0.0289, 12.7401, 0.11604],
        28 => [-0.0337, 12.9303, 0.11664],
        29 => [-0.0385, 13.1169, 0.11723],
        30 => [-0.0431, 13.3, 0.11781],
        31 => [-0.0476, 13.4798, 0.11839],
        32 => [-0.052, 13.6567, 0.11896],
        33 => [-0.0564, 13.8309, 0.11953],
        34 => [-0.0606, 14.0031, 0.12008],
        35 => [-0.0648, 14.1736, 0.12062],
        36 => [-0.0689, 14.3429, 0.12116],
        37 => [-0.0729, 14.5113, 0.12168],
        38 => [-0.0769, 14.6791, 0.1222],
        39 => [-0.0808, 14.8466, 0.12271],
        40 => [-0.0846, 15.014, 0.12322],
        41 => [-0.0883, 15.1813, 0.12373],
        42 => [-0.092, 15.3486, 0.12425],
        43 => [-0.0957, 15.5158, 0.12478],
        44 => [-0.0993, 15.6828, 0.12531],
        45 => [-0.1028, 15.8497, 0.12586],
        46 => [-0.1063, 16.0163, 0.12643],
        47 => [-0.1097, 16.1827, 0.127],
        48 => [-0.1131, 16.3489, 0.12759],
        49 => [-0.1165, 16.515, 0.12819],
        50 => [-0.1198, 16.6811, 0.1288],
        51 => [-0.123, 16.8471, 0.12943],
        52 => [-0.1262, 17.0132, 0.13005],
        53 => [-0.1294, 17.1792, 0.13069],
        54 => [-0.1325, 17.3452, 0.13133],
        55 => [-0.1356, 17.5111, 0.13197],
        56 => [-0.1387, 17.6768, 0.13261],
        57 => [-0.1417, 17.8422, 0.13325],
        58 => [-0.1447, 18.0073, 0.13389],
        59 => [-0.1477, 18.1722, 0.13453],
        60 => [-0.1506, 18.3366, 0.13517]
    ];

    // ========== WEIGHT-FOR-AGE (BB/U) - GIRLS 0-60 months ==========
    private static $wfa_girls = [
        0 => [0.3809, 3.2322, 0.14171],
        1 => [0.1714, 4.1873, 0.13724],
        2 => [0.0962, 5.1282, 0.13],
        3 => [0.0402, 5.8458, 0.12619],
        4 => [-0.005, 6.4237, 0.12402],
        5 => [-0.043, 6.8985, 0.12274],
        6 => [-0.0756, 7.297, 0.12204],
        7 => [-0.1039, 7.6422, 0.12178],
        8 => [-0.1288, 7.9487, 0.12181],
        9 => [-0.1507, 8.2254, 0.12199],
        10 => [-0.17, 8.48, 0.12223],
        11 => [-0.1872, 8.7192, 0.12247],
        12 => [-0.2024, 8.9481, 0.12268],
        13 => [-0.2158, 9.1699, 0.12283],
        14 => [-0.2278, 9.387, 0.12294],
        15 => [-0.2384, 9.6008, 0.12299],
        16 => [-0.2478, 9.8124, 0.12303],
        17 => [-0.2562, 10.0226, 0.12306],
        18 => [-0.2637, 10.2315, 0.12309],
        19 => [-0.2703, 10.4393, 0.12315],
        20 => [-0.2762, 10.6464, 0.12323],
        21 => [-0.2815, 10.8534, 0.12335],
        22 => [-0.2862, 11.0608, 0.1235],
        23 => [-0.2903, 11.2688, 0.12369],
        24 => [-0.2941, 11.4775, 0.1239],
        25 => [-0.2975, 11.6864, 0.12414],
        26 => [-0.3005, 11.8947, 0.12441],
        27 => [-0.3032, 12.1015, 0.12472],
        28 => [-0.3057, 12.3059, 0.12506],
        29 => [-0.308, 12.5073, 0.12545],
        30 => [-0.3101, 12.7055, 0.12587],
        31 => [-0.312, 12.9006, 0.12633],
        32 => [-0.3138, 13.093, 0.12683],
        33 => [-0.3155, 13.2837, 0.12737],
        34 => [-0.3171, 13.4731, 0.12794],
        35 => [-0.3186, 13.6618, 0.12855],
        36 => [-0.3201, 13.8503, 0.12919],
        37 => [-0.3216, 14.0385, 0.12988],
        38 => [-0.323, 14.2265, 0.13059],
        39 => [-0.3243, 14.414, 0.13135],
        40 => [-0.3257, 14.601, 0.13213],
        41 => [-0.327, 14.7873, 0.13293],
        42 => [-0.3283, 14.9727, 0.13376],
        43 => [-0.3296, 15.1573, 0.1346],
        44 => [-0.3309, 15.341, 0.13545],
        45 => [-0.3322, 15.524, 0.1363],
        46 => [-0.3335, 15.7064, 0.13716],
        47 => [-0.3348, 15.8882, 0.138],
        48 => [-0.3361, 16.0697, 0.13884],
        49 => [-0.3374, 16.2511, 0.13968],
        50 => [-0.3387, 16.4322, 0.14051],
        51 => [-0.34, 16.6133, 0.14132],
        52 => [-0.3414, 16.7942, 0.14213],
        53 => [-0.3427, 16.9748, 0.14293],
        54 => [-0.344, 17.1551, 0.14371],
        55 => [-0.3453, 17.3347, 0.14448],
        56 => [-0.3466, 17.5136, 0.14525],
        57 => [-0.3479, 17.6916, 0.146],
        58 => [-0.3492, 17.8686, 0.14675],
        59 => [-0.3505, 18.0445, 0.14748],
        60 => [-0.3518, 18.2193, 0.14821]
    ];
    
    // ========== LENGTH/HEIGHT-FOR-AGE (TB/U) - BOYS 0-60 months ==========
    private static $lhfa_boys = [
        0 => [1, 49.8842, 0.03795],
        1 => [1, 54.7244, 0.03557],
        2 => [1, 58.4249, 0.03424],
        3 => [1, 61.4292, 0.03328],
        4 => [1, 63.8860, 0.03257],
        5 => [1, 65.9026, 0.03204],
        6 => [1, 67.6236, 0.03165],
        7 => [1, 69.1645, 0.03139],
        8 => [1, 70.5994, 0.03124],
        9 => [1, 71.9687, 0.03117],
        10 => [1, 73.2812, 0.03118],
        11 => [1, 74.5388, 0.03125],
        12 => [1, 75.7488, 0.03137],
        13 => [1, 76.9186, 0.03154],
        14 => [1, 78.0497, 0.03174],
        15 => [1, 79.1458, 0.03197],
        16 => [1, 80.2113, 0.03222],
        17 => [1, 81.2487, 0.03250],
        18 => [1, 82.2587, 0.03279],
        19 => [1, 83.2418, 0.03310],
        20 => [1, 84.1996, 0.03342],
        21 => [1, 85.1348, 0.03376],
        22 => [1, 86.0477, 0.03410],
        23 => [1, 86.9410, 0.03445],
        // 2 Sampai 5 tahun
        24 => [1, 87.1161, 0.03507],
        25 => [1, 87.972, 0.03542],
        26 => [1, 88.8065, 0.03576],
        27 => [1, 89.6197, 0.0361],
        28 => [1, 90.412, 0.03642],
        29 => [1, 91.1828, 0.03674],
        30 => [1, 91.9327, 0.03704],
        31 => [1, 92.6631, 0.03733],
        32 => [1, 93.3753, 0.03761],
        33 => [1, 94.0711, 0.03787],
        34 => [1, 94.7532, 0.03812],
        35 => [1, 95.4236, 0.03836],
        36 => [1, 96.0835, 0.03858],
        37 => [1, 96.7337, 0.03879],
        38 => [1, 97.3749, 0.039],
        39 => [1, 98.0073, 0.03919],
        40 => [1, 98.631, 0.03937],
        41 => [1, 99.2459, 0.03954],
        42 => [1, 99.8515, 0.03971],
        43 => [1, 100.4485, 0.03986],
        44 => [1, 101.0374, 0.04002],
        45 => [1, 101.6186, 0.04016],
        46 => [1, 102.1933, 0.04031],
        47 => [1, 102.7625, 0.04045],
        48 => [1, 103.3273, 0.04059],
        49 => [1, 103.8886, 0.04073],
        50 => [1, 104.4473, 0.04086],
        51 => [1, 105.0041, 0.041],
        52 => [1, 105.5596, 0.04113],
        53 => [1, 106.1138, 0.04126],
        54 => [1, 106.6668, 0.04139],
        55 => [1, 107.2188, 0.04152],
        56 => [1, 107.7697, 0.04165],
        57 => [1, 108.3198, 0.04177],
        58 => [1, 108.8689, 0.0419],
        59 => [1, 109.417, 0.04202],
        60 => [1, 109.9638, 0.04214]
    ];
    
    // ========== LENGTH/HEIGHT-FOR-AGE (TB/U) - GIRLS 0-60 months ==========
    private static $lhfa_girls = [
        0 => [1, 49.1477, 0.0379],
        1 => [1, 53.6872, 0.0364],
        2 => [1, 57.0673, 0.03568],
        3 => [1, 59.8029, 0.0352],
        4 => [1, 62.0899, 0.03486],
        5 => [1, 64.0301, 0.03463],
        6 => [1, 65.7311, 0.03448],
        7 => [1, 67.2873, 0.03441],
        8 => [1, 68.7498, 0.0344],
        9 => [1, 70.1435, 0.03444],
        10 => [1, 71.4818, 0.03452],
        11 => [1, 72.7710, 0.03464],
        12 => [1, 74.0150, 0.03479],
        13 => [1, 75.2176, 0.03496],
        14 => [1, 76.3817, 0.03514],
        15 => [1, 77.5099, 0.03534],
        16 => [1, 78.6055, 0.03555],
        17 => [1, 79.6710, 0.03576],
        18 => [1, 80.7079, 0.03598],
        19 => [1, 81.7182, 0.0362],
        20 => [1, 82.7036, 0.03643],
        21 => [1, 83.6654, 0.03666],
        22 => [1, 84.6040, 0.03688],
        23 => [1, 85.5202, 0.03711],
        // 2 sampai 5 tahun
        24 => [1, 85.7153, 0.03764],
        25 => [1, 86.5904, 0.03786],
        26 => [1, 87.4462, 0.03808],
        27 => [1, 88.283, 0.03830],
        28 => [1, 89.1004, 0.03851],
        29 => [1, 89.8991, 0.03872],
        30 => [1, 90.6797, 0.03893],
        31 => [1, 91.443, 0.03913],
        32 => [1, 92.1906, 0.03933],
        33 => [1, 92.9239, 0.03952],
        34 => [1, 93.6444, 0.03971],
        35 => [1, 94.3533, 0.03989],
        36 => [1, 95.0515, 0.04006],
        37 => [1, 95.7399, 0.04024],
        38 => [1, 96.4187, 0.04041],
        39 => [1, 97.0885, 0.04057],
        40 => [1, 97.7493, 0.04073],
        41 => [1, 98.4015, 0.04089],
        42 => [1, 99.0448, 0.04105],
        43 => [1, 99.6795, 0.04120],
        44 => [1, 100.3058, 0.04135],
        45 => [1, 100.9238, 0.04150],
        46 => [1, 101.5337, 0.04164],
        47 => [1, 102.136, 0.04179],
        48 => [1, 102.7312, 0.04193],
        49 => [1, 103.3197, 0.04206],
        50 => [1, 103.9021, 0.04220],
        51 => [1, 104.4786, 0.04233],
        52 => [1, 105.0494, 0.04246],
        53 => [1, 105.6148, 0.04259],
        54 => [1, 106.1748, 0.04272],
        55 => [1, 106.7295, 0.04285],
        56 => [1, 107.2788, 0.04298],
        57 => [1, 107.8227, 0.04310],
        58 => [1, 108.3613, 0.04322],
        59 => [1, 108.8948, 0.04334],
        60 => [1, 109.4233, 0.04347]
    ];
    
    // ========== WEIGHT-FOR-LENGTH/HEIGHT (BB/TB) - BOYS 45-110 cm ==========
    private static $wfl_boys = [
        45 => [-0.3521, 2.4410, 0.09182],
        46 => [-0.3521, 2.6077, 0.09124],
        47 => [-0.3521, 2.7755, 0.09065],
        48 => [-0.3521, 2.9480, 0.09007],
        49 => [-0.3521, 3.1308, 0.08948],
        50 => [-0.3521, 3.3278, 0.08890],
        51 => [-0.3521, 3.5376, 0.08831],
        52 => [-0.3521, 3.7620, 0.08771],
        53 => [-0.3521, 4.0060, 0.08711],
        54 => [-0.3521, 4.2693, 0.08651],
        55 => [-0.3521, 4.5467, 0.08592],
        56 => [-0.3521, 4.8338, 0.08535],
        57 => [-0.3521, 5.1259, 0.08481],
        58 => [-0.3521, 5.4180, 0.08430],
        59 => [-0.3521, 5.7074, 0.08383],
        60 => [-0.3521, 5.9907, 0.08342],
        61 => [-0.3521, 6.2632, 0.08308],
        62 => [-0.3521, 6.5251, 0.08279],
        63 => [-0.3521, 6.7786, 0.08255],
        64 => [-0.3521, 7.0255, 0.08236],
        // 2 Sampai 5 tahun
        65 => [-0.3521, 7.4327, 0.08217],
        66 => [-0.3521, 7.6673, 0.08212],
        67 => [-0.3521, 7.8986, 0.08213],
        68 => [-0.3521, 8.1272, 0.08217],
        69 => [-0.3521, 8.3547, 0.08226],
        70 => [-0.3521, 8.5808, 0.08237],
        71 => [-0.3521, 8.8036, 0.08250],
        72 => [-0.3521, 9.0221, 0.08264],
        73 => [-0.3521, 9.2347, 0.08278],
        74 => [-0.3521, 9.4420, 0.08292],
        75 => [-0.3521, 9.6440, 0.08303],
        76 => [-0.3521, 9.8392, 0.08312],
        77 => [-0.3521, 10.0274, 0.08317],
        78 => [-0.3521, 10.2105, 0.08317],
        79 => [-0.3521, 10.3923, 0.08311],
        80 => [-0.3521, 10.5781, 0.08298],
        81 => [-0.3521, 10.7718, 0.08279],
        82 => [-0.3521, 10.9772, 0.08255],
        83 => [-0.3521, 11.1966, 0.08225],
        84 => [-0.3521, 11.4290, 0.08191],
        85 => [-0.3521, 11.6707, 0.08156],
        86 => [-0.3521, 11.9173, 0.08121],
        87 => [-0.3521, 12.1645, 0.08090],
        88 => [-0.3521, 12.4089, 0.08064],
        89 => [-0.3521, 12.6495, 0.08045],
        90 => [-0.3521, 12.8864, 0.08032],
        91 => [-0.3521, 13.1209, 0.08025],
        92 => [-0.3521, 13.3541, 0.08025],
        93 => [-0.3521, 13.5870, 0.08031],
        94 => [-0.3521, 13.8217, 0.08043],
        95 => [-0.3521, 14.0600, 0.08060],
        96 => [-0.3521, 14.3037, 0.08083],
        97 => [-0.3521, 14.5547, 0.08112],
        98 => [-0.3521, 14.8140, 0.08146],
        99 => [-0.3521, 15.0818, 0.08185],
        100 => [-0.3521, 15.3576, 0.08229],
        101 => [-0.3521, 15.6412, 0.08277],
        102 => [-0.3521, 15.9320, 0.08328],
        103 => [-0.3521, 16.2298, 0.08381],
        104 => [-0.3521, 16.5342, 0.08436],
        105 => [-0.3521, 16.8454, 0.08493],
        106 => [-0.3521, 17.1637, 0.08551],
        107 => [-0.3521, 17.4894, 0.08611],
        108 => [-0.3521, 17.8226, 0.08673],
        109 => [-0.3521, 18.1645, 0.08736],
        110 => [-0.3521, 18.5158, 0.08800]
    ];
    
    // ========== WEIGHT-FOR-LENGTH/HEIGHT (BB/TB) - GIRLS 45-110 cm ==========
    private static $wfl_girls = [
        45 => [-0.3833, 2.4607, 0.09029],
        46 => [-0.3833, 2.6306, 0.09037],
        47 => [-0.3833, 2.8007, 0.09044],
        48 => [-0.3833, 2.9741, 0.09052],
        49 => [-0.3833, 3.1560, 0.09060],
        50 => [-0.3833, 3.3518, 0.09068],
        51 => [-0.3833, 3.5636, 0.09076],
        52 => [-0.3833, 3.7911, 0.09085],
        53 => [-0.3833, 4.0332, 0.09093],
        54 => [-0.3833, 4.2875, 0.09102],
        55 => [-0.3833, 4.5498, 0.09110],
        56 => [-0.3833, 4.8162, 0.09118],
        57 => [-0.3833, 5.0837, 0.09125],
        58 => [-0.3833, 5.3507, 0.09130],
        59 => [-0.3833, 5.6151, 0.09134],
        60 => [-0.3833, 5.8742, 0.09136],
        61 => [-0.3833, 6.1270, 0.09137],
        62 => [-0.3833, 6.3738, 0.09135],
        63 => [-0.3833, 6.6144, 0.09131],
        64 => [-0.3833, 6.8501, 0.09126],
        // 2 sampai 5 tahun
        65  => [-0.3833, 7.2402, 0.09113],
        66  => [-0.3833, 7.4630, 0.09104],
        67  => [-0.3833, 7.6806, 0.09094],
        68  => [-0.3833, 7.8930, 0.09083],
        69  => [-0.3833, 8.1012, 0.09071],
        70  => [-0.3833, 8.3058, 0.09059],
        71  => [-0.3833, 8.5078, 0.09047],
        72  => [-0.3833, 8.7070, 0.09035],
        73  => [-0.3833, 8.9025, 0.09022],
        74  => [-0.3833, 9.0928, 0.09009],
        75  => [-0.3833, 9.2786, 0.08996],
        76  => [-0.3833, 9.4617, 0.08983],
        77  => [-0.3833, 9.6456, 0.08969],
        78  => [-0.3833, 9.8338, 0.08956],
        79  => [-0.3833, 10.0289, 0.08943],
        80  => [-0.3833, 10.2332, 0.08932],
        81  => [-0.3833, 10.4477, 0.08921],
        82  => [-0.3833, 10.6719, 0.08912],
        83  => [-0.3833, 10.9051, 0.08905],
        84  => [-0.3833, 11.1462, 0.08899],
        85  => [-0.3833, 11.3934, 0.08896],
        86  => [-0.3833, 11.6444, 0.08895],
        87  => [-0.3833, 11.8965, 0.08896],
        88  => [-0.3833, 12.1478, 0.08899],
        89  => [-0.3833, 12.3976, 0.08904],
        90  => [-0.3833, 12.6461, 0.08911],
        91  => [-0.3833, 12.8939, 0.08920],
        92  => [-0.3833, 13.1415, 0.08931],
        93  => [-0.3833, 13.3896, 0.08944],
        94  => [-0.3833, 13.6393, 0.08959],
        95  => [-0.3833, 13.8914, 0.08975],
        96  => [-0.3833, 14.1466, 0.08994],
        97  => [-0.3833, 14.4059, 0.09015],
        98  => [-0.3833, 14.6710, 0.09037],
        99  => [-0.3833, 14.9434, 0.09062],
        100 => [-0.3833, 15.2246, 0.09088],
        101 => [-0.3833, 15.5154, 0.09116],
        102 => [-0.3833, 15.8164, 0.09146],
        103 => [-0.3833, 16.1276, 0.09177],
        104 => [-0.3833, 16.4488, 0.09209],
        105 => [-0.3833, 16.7800, 0.09243],
        106 => [-0.3833, 17.1220, 0.09278],
        107 => [-0.3833, 17.4755, 0.09315],
        108 => [-0.3833, 17.8407, 0.09352],
        109 => [-0.3833, 18.2174, 0.09390],
        110 => [-0.3833, 18.6043, 0.09428]
    ];
    
    // ========== BMI-FOR-AGE (IMT/U) - BOYS 0-60 months ==========
    private static $bfa_boys = [
        0 => [-0.3053, 13.4069, 0.0956],
        1 => [0.2708, 14.9441, 0.09027],
        2 => [0.1118, 16.3195, 0.08677],
        3 => [0.0068, 16.8987, 0.08495],
        4 => [-0.0727, 17.1579, 0.08378],
        5 => [-0.137, 17.2919, 0.08296],
        6 => [-0.1913, 17.3422, 0.08234],
        7 => [-0.2385, 17.3288, 0.08183],
        8 => [-0.2802, 17.2647, 0.0814],
        9 => [-0.3176, 17.1662, 0.08102],
        10 => [-0.3516, 17.0488, 0.08068],
        11 => [-0.3828, 16.9239, 0.08037],
        12 => [-0.4115, 16.7981, 0.08009],
        13 => [-0.4382, 16.6743, 0.07982],
        14 => [-0.463, 16.5548, 0.07958],
        15 => [-0.4863, 16.4409, 0.07935],
        16 => [-0.5082, 16.3335, 0.07913],
        17 => [-0.5289, 16.2329, 0.07892],
        18 => [-0.5484, 16.1392, 0.07873],
        19 => [-0.5669, 16.0528, 0.07854],
        20 => [-0.5846, 15.9743, 0.07836],
        21 => [-0.6014, 15.9039, 0.07818],
        22 => [-0.6174, 15.8412, 0.07802],
        23 => [-0.6328, 15.7852, 0.07786],
        // 2 sampai 5 tahun
        24 => [-0.6187, 16.0189, 0.07785],
        25 => [-0.584, 15.98, 0.07792],
        26 => [-0.5497, 15.9414, 0.078],
        27 => [-0.5166, 15.9036, 0.07808],
        28 => [-0.485, 15.8667, 0.07818],
        29 => [-0.4552, 15.8306, 0.07829],
        30 => [-0.4274, 15.7953, 0.07841],
        31 => [-0.4016, 15.7606, 0.07854],
        32 => [-0.3782, 15.7267, 0.07867],
        33 => [-0.3572, 15.6934, 0.07882],
        34 => [-0.3388, 15.661, 0.07897],
        35 => [-0.3231, 15.6294, 0.07914],
        36 => [-0.3101, 15.5988, 0.07931],
        37 => [-0.3, 15.5693, 0.0795],
        38 => [-0.2927, 15.541, 0.07969],
        39 => [-0.2884, 15.514, 0.0799],
        40 => [-0.2869, 15.4885, 0.08012],
        41 => [-0.2881, 15.4645, 0.08036],
        42 => [-0.2919, 15.442, 0.08061],
        43 => [-0.2981, 15.421, 0.08087],
        44 => [-0.3067, 15.4013, 0.08115],
        45 => [-0.3174, 15.3827, 0.08144],
        46 => [-0.3303, 15.3652, 0.08174],
        47 => [-0.3452, 15.3485, 0.08205],
        48 => [-0.3622, 15.3326, 0.08238],
        49 => [-0.3811, 15.3174, 0.08272],
        50 => [-0.4019, 15.3029, 0.08307],
        51 => [-0.4245, 15.2891, 0.08343],
        52 => [-0.4488, 15.2759, 0.0838],
        53 => [-0.4747, 15.2633, 0.08418],
        54 => [-0.5019, 15.2514, 0.08457],
        55 => [-0.5303, 15.24, 0.08496],
        56 => [-0.5599, 15.2291, 0.08536],
        57 => [-0.5905, 15.2188, 0.08577],
        58 => [-0.6223, 15.2091, 0.08617],
        59 => [-0.6552, 15.2, 0.08659],
        60 => [-0.6892, 15.1916, 0.087]
    ];
    
    // ========== BMI-FOR-AGE (IMT/U) - GIRLS 0-60 months ==========
    private static $bfa_girls = [
        0 => [-0.0631, 13.3363, 0.09272],
        1 => [0.3448, 14.5679, 0.09556],
        2 => [0.1749, 15.7679, 0.09371],
        3 => [0.0643, 16.3574, 0.09254],
        4 => [-0.0191, 16.6703, 0.09166],
        5 => [-0.0864, 16.8386, 0.09096],
        6 => [-0.1429, 16.9083, 0.09036],
        7 => [-0.1916, 16.9020, 0.08984],
        8 => [-0.2344, 16.8404, 0.08939],
        9 => [-0.2725, 16.7406, 0.08898],
        10 => [-0.3068, 16.6184, 0.08861],
        11 => [-0.3381, 16.4875, 0.08828],
        12 => [-0.3667, 16.3568, 0.08797],
        13 => [-0.3932, 16.2311, 0.08768],
        14 => [-0.4177, 16.1128, 0.08741],
        15 => [-0.4407, 16.0028, 0.08716],
        16 => [-0.4623, 15.9017, 0.08693],
        17 => [-0.4825, 15.8096, 0.08671],
        18 => [-0.5017, 15.7263, 0.08650],
        19 => [-0.5199, 15.6517, 0.08630],
        20 => [-0.5372, 15.5855, 0.08612],
        21 => [-0.5537, 15.5278, 0.08594],
        22 => [-0.5695, 15.4787, 0.08577],
        23 => [-0.5846, 15.4380, 0.08560],
        // 2 sampai 5 tahun
        24 => [-0.5684, 15.6881, 0.08454],
        25 => [-0.5684, 15.6590, 0.08452],
        26 => [-0.5684, 15.6308, 0.08449],
        27 => [-0.5684, 15.6037, 0.08446],
        28 => [-0.5684, 15.5777, 0.08444],
        29 => [-0.5684, 15.5523, 0.08443],
        30 => [-0.5684, 15.5276, 0.08444],
        31 => [-0.5684, 15.5034, 0.08448],
        32 => [-0.5684, 15.4798, 0.08455],
        33 => [-0.5684, 15.4572, 0.08467],
        34 => [-0.5684, 15.4356, 0.08484],
        35 => [-0.5684, 15.4155, 0.08506],
        36 => [-0.5684, 15.3968, 0.08535],
        37 => [-0.5684, 15.3796, 0.08569],
        38 => [-0.5684, 15.3638, 0.08609],
        39 => [-0.5684, 15.3493, 0.08654],
        40 => [-0.5684, 15.3358, 0.08704],
        41 => [-0.5684, 15.3233, 0.08757],
        42 => [-0.5684, 15.3116, 0.08813],
        43 => [-0.5684, 15.3007, 0.08872],
        44 => [-0.5684, 15.2905, 0.08931],
        45 => [-0.5684, 15.2814, 0.08991],
        46 => [-0.5684, 15.2732, 0.09051],
        47 => [-0.5684, 15.2661, 0.09110],
        48 => [-0.5684, 15.2602, 0.09168],
        49 => [-0.5684, 15.2556, 0.09227],
        50 => [-0.5684, 15.2523, 0.09286],
        51 => [-0.5684, 15.2503, 0.09345],
        52 => [-0.5684, 15.2496, 0.09403],
        53 => [-0.5684, 15.2502, 0.09460],
        54 => [-0.5684, 15.2519, 0.09515],
        55 => [-0.5684, 15.2544, 0.09568],
        56 => [-0.5684, 15.2575, 0.09618],
        57 => [-0.5684, 15.2612, 0.09665],
        58 => [-0.5684, 15.2653, 0.09709],
        59 => [-0.5684, 15.2698, 0.09750],
        60 => [-0.5684, 15.2747, 0.09789]
    ];
    
    /**
     * Hitung Z-score menggunakan metode LMS WHO
     */
    public static function calculateZScore($measurement, $L, $M, $S) {
        if ($measurement <= 0 || $M <= 0) {
            return null;
        }
        
        if ($L == 0) {
            return log($measurement / $M) / $S;
        } else {
            return (pow($measurement / $M, $L) - 1) / ($S * $L);
        }
    }
    
    /**
     * Get LMS values for Weight-for-Age (BB/U)
     */
    public static function getWFA($usia_bulan, $jenis_kelamin) {
        $data = ($jenis_kelamin == 'L') ? self::$wfa_boys : self::$wfa_girls;
        
        $usia_bulan = intval($usia_bulan);
        if ($usia_bulan < 0) $usia_bulan = 0;
        if ($usia_bulan > 60) $usia_bulan = 60;
        
        if (isset($data[$usia_bulan])) {
            return $data[$usia_bulan];
        }
        
        $usia_int = floor($usia_bulan);
        $usia_next = ceil($usia_bulan);
        
        if ($usia_int == $usia_next) {
            if (isset($data[$usia_int])) {
                return $data[$usia_int];
            }
        }
        
        if (isset($data[$usia_int]) && isset($data[$usia_next])) {
            if ($usia_next - $usia_int == 0) {
                return $data[$usia_int];
            }
            
            $l1 = $data[$usia_int][0];
            $m1 = $data[$usia_int][1];
            $s1 = $data[$usia_int][2];
            
            $l2 = $data[$usia_next][0];
            $m2 = $data[$usia_next][1];
            $s2 = $data[$usia_next][2];
            
            $t = ($usia_bulan - $usia_int) / ($usia_next - $usia_int);
            $L = $l1 + ($l2 - $l1) * $t;
            $M = $m1 + ($m2 - $m1) * $t;
            $S = $s1 + ($s2 - $s1) * $t;
            
            return [$L, $M, $S];
        }
        
        $keys = array_keys($data);
        sort($keys);
        
        foreach ($keys as $key) {
            if ($key >= $usia_bulan) {
                return $data[$key];
            }
        }
        
        return null;
    }
    
    /**
     * Get LMS values for Length/Height-for-Age (TB/U)
     */
    public static function getLFA($usia_bulan, $jenis_kelamin) {
        $data = ($jenis_kelamin == 'L') ? self::$lhfa_boys : self::$lhfa_girls;
        
        $usia_bulan = intval($usia_bulan);
        if ($usia_bulan < 0) $usia_bulan = 0;
        if ($usia_bulan > 60) $usia_bulan = 60;
        
        if (isset($data[$usia_bulan])) {
            return $data[$usia_bulan];
        }
        
        $usia_int = floor($usia_bulan);
        $usia_next = ceil($usia_bulan);
        
        if ($usia_int == $usia_next) {
            if (isset($data[$usia_int])) {
                return $data[$usia_int];
            }
        }
        
        if (isset($data[$usia_int]) && isset($data[$usia_next])) {
            if ($usia_next - $usia_int == 0) {
                return $data[$usia_int];
            }
            
            $l1 = $data[$usia_int][0];
            $m1 = $data[$usia_int][1];
            $s1 = $data[$usia_int][2];
            
            $l2 = $data[$usia_next][0];
            $m2 = $data[$usia_next][1];
            $s2 = $data[$usia_next][2];
            
            $t = ($usia_bulan - $usia_int) / ($usia_next - $usia_int);
            $L = $l1 + ($l2 - $l1) * $t;
            $M = $m1 + ($m2 - $m1) * $t;
            $S = $s1 + ($s2 - $s1) * $t;
            
            return [$L, $M, $S];
        }
        
        $keys = array_keys($data);
        sort($keys);
        
        foreach ($keys as $key) {
            if ($key >= $usia_bulan) {
                return $data[$key];
            }
        }
        
        return null;
    }
    
    /**
     * Get LMS values for Weight-for-Length/Height (BB/TB)
     */
    public static function getWFL($panjang_badan, $jenis_kelamin) {
        $data = ($jenis_kelamin == 'L') ? self::$wfl_boys : self::$wfl_girls;
        
        $panjang_rounded = round($panjang_badan);
        
        if ($panjang_rounded < 45) $panjang_rounded = 45;
        if ($panjang_rounded > 110) $panjang_rounded = 110;
        
        if (isset($data[$panjang_rounded])) {
            return $data[$panjang_rounded];
        }
        
        $keys = array_keys($data);
        sort($keys);
        
        $prev_key = null;
        $next_key = null;
        
        foreach ($keys as $key) {
            if ($key <= $panjang_rounded) {
                $prev_key = $key;
            }
            if ($key >= $panjang_rounded && $next_key === null) {
                $next_key = $key;
            }
        }
        
        if ($prev_key === null && $next_key !== null) {
            return $data[$next_key];
        }
        if ($next_key === null && $prev_key !== null) {
            return $data[$prev_key];
        }
        if ($prev_key == $next_key) {
            return $data[$prev_key];
        }
        
        if ($next_key - $prev_key == 0) {
            return $data[$prev_key];
        }
        
        $l1 = $data[$prev_key][0];
        $m1 = $data[$prev_key][1];
        $s1 = $data[$prev_key][2];
        
        $l2 = $data[$next_key][0];
        $m2 = $data[$next_key][1];
        $s2 = $data[$next_key][2];
        
        $t = ($panjang_rounded - $prev_key) / ($next_key - $prev_key);
        $L = $l1 + ($l2 - $l1) * $t;
        $M = $m1 + ($m2 - $m1) * $t;
        $S = $s1 + ($s2 - $s1) * $t;
        
        return [$L, $M, $S];
    }
    
    /**
     * Get LMS values for BMI-for-Age (IMT/U)
     */
    public static function getBFA($usia_bulan, $jenis_kelamin) {
        $data = ($jenis_kelamin == 'L') ? self::$bfa_boys : self::$bfa_girls;
        
        $usia_bulan = intval($usia_bulan);
        if ($usia_bulan < 0) $usia_bulan = 0;
        if ($usia_bulan > 60) $usia_bulan = 60;
        
        if (isset($data[$usia_bulan])) {
            return $data[$usia_bulan];
        }
        
        $prev_key = null;
        $next_key = null;
        
        $keys = array_keys($data);
        sort($keys);
        
        foreach ($keys as $key) {
            if ($key <= $usia_bulan) {
                $prev_key = $key;
            }
            if ($key >= $usia_bulan && $next_key === null) {
                $next_key = $key;
            }
        }
        
        if ($prev_key === null && $next_key !== null) {
            return $data[$next_key];
        }
        if ($next_key === null && $prev_key !== null) {
            return $data[$prev_key];
        }
        
        if ($prev_key == $next_key) {
            return $data[$prev_key];
        }
        
        if ($next_key - $prev_key == 0) {
            return $data[$prev_key];
        }
        
        $l1 = $data[$prev_key][0];
        $m1 = $data[$prev_key][1];
        $s1 = $data[$prev_key][2];
        
        $l2 = $data[$next_key][0];
        $m2 = $data[$next_key][1];
        $s2 = $data[$next_key][2];
        
        $t = ($usia_bulan - $prev_key) / ($next_key - $prev_key);
        $L = $l1 + ($l2 - $l1) * $t;
        $M = $m1 + ($m2 - $m1) * $t;
        $S = $s1 + ($s2 - $s1) * $t;
        
        return [$L, $M, $S];
    }
    
    /**
     * Klasifikasi status gizi berdasarkan Z-score (WHO Standard)
     */
    public static function classifyStatus($zscore, $indicator) {
        if ($zscore === null) {
            return ['status' => 'Tidak dapat dihitung', 'kode' => 'ERROR', 'warna' => 'gray'];
        }
        
        switch ($indicator) {
            case 'BB/U':
                if ($zscore < -3) {
                    return ['status' => 'Gizi Buruk', 'kode' => 'SEVERE_UNDERWEIGHT', 'warna' => 'red'];
                } elseif ($zscore < -2) {
                    return ['status' => 'Gizi Kurang', 'kode' => 'UNDERWEIGHT', 'warna' => 'orange'];
                } elseif ($zscore <= 1) {
                    return ['status' => 'Gizi Baik', 'kode' => 'NORMAL', 'warna' => 'green'];
                } elseif ($zscore <= 2) {
                    return ['status' => 'Beresiko Gizi Lebih', 'kode' => 'RISK_OVERWEIGHT', 'warna' => 'yellow'];
                } elseif ($zscore <= 3) {
                    return ['status' => 'Gizi Lebih', 'kode' => 'OVERWEIGHT', 'warna' => 'purple'];
                } else {
                    return ['status' => 'Obesitas', 'kode' => 'OBESE', 'warna' => 'dark-red'];
                }
                
            case 'TB/U':
                if ($zscore < -3) {
                    return ['status' => 'Sangat Pendek', 'kode' => 'SEVERE_STUNTING', 'warna' => 'red'];
                } elseif ($zscore < -2) {
                    return ['status' => 'Pendek', 'kode' => 'STUNTING', 'warna' => 'orange'];
                } elseif ($zscore <= 2) {
                    return ['status' => 'Normal', 'kode' => 'NORMAL', 'warna' => 'green'];
                } elseif ($zscore <= 3) {
                    return ['status' => 'Tinggi', 'kode' => 'TALL', 'warna' => 'blue'];
                } else {
                    return ['status' => 'Sangat Tinggi', 'kode' => 'VERY_TALL', 'warna' => 'dark-blue'];
                }
                
            case 'BB/TB':
                if ($zscore < -3) {
                    return ['status' => 'Gizi Buruk', 'kode' => 'SEVERE_WASTING', 'warna' => 'red'];
                } elseif ($zscore < -2) {
                    return ['status' => 'Gizi Kurang', 'kode' => 'WASTING', 'warna' => 'orange'];
                } elseif ($zscore <= 2) {
                    return ['status' => 'Gizi Baik', 'kode' => 'NORMAL', 'warna' => 'green'];
                } elseif ($zscore <= 3) {
                    return ['status' => 'Beresiko Gizi Lebih', 'kode' => 'RISK_OVERWEIGHT', 'warna' => 'yellow'];
                } else {
                    return ['status' => 'Gizi Lebih', 'kode' => 'OVERWEIGHT', 'warna' => 'purple'];
                }
                
            case 'IMT/U':
                if ($zscore < -3) {
                    return ['status' => 'Sangat Kurus', 'kode' => 'SEVERE_THINNESS', 'warna' => 'red'];
                } elseif ($zscore < -2) {
                    return ['status' => 'Kurus', 'kode' => 'THINNESS', 'warna' => 'orange'];
                } elseif ($zscore <= 1) {
                    return ['status' => 'Normal', 'kode' => 'NORMAL', 'warna' => 'green'];
                } elseif ($zscore <= 2) {
                    return ['status' => 'Beresiko Gemuk', 'kode' => 'RISK_OVERWEIGHT', 'warna' => 'yellow'];
                } elseif ($zscore <= 3) {
                    return ['status' => 'Gemuk', 'kode' => 'OVERWEIGHT', 'warna' => 'purple'];
                } else {
                    return ['status' => 'Obesitas', 'kode' => 'OBESE', 'warna' => 'dark-red'];
                }
                
            default:
                return ['status' => 'Tidak diketahui', 'kode' => 'UNKNOWN', 'warna' => 'gray'];
        }
    }
    
    /**
     * Hitung BMI (IMT)
     */
    public static function calculateBMI($berat_badan, $tinggi_badan) {
        if ($tinggi_badan <= 0) return 0;
        $tinggi_meter = $tinggi_badan / 100;
        $bmi = $berat_badan / ($tinggi_meter * $tinggi_meter);
        return round($bmi, 2);
    }
}

$anak_id = $_GET['anak_id'] ?? 0;
$anak_id = intval($anak_id);

if ($anak_id <= 0) {
    die('ID anak tidak valid.');
}

$query_anak = "SELECT * FROM anak WHERE id = $anak_id";
$result_anak = mysqli_query($conn, $query_anak);

if (!$result_anak) {
    die('Error query data anak: ' . mysqli_error($conn));
}

$anak = mysqli_fetch_assoc($result_anak);

if (!$anak) {
    die('Data anak tidak ditemukan.');
}

$usia_bulan = hitungUsiaBulanWHO($anak['tgl_lahir']);
$jenis_kelamin = $anak['jenis_kelamin'];

$query_pengukuran = "SELECT berat_badan, panjang_badan, lingkar_kepala, lingkar_lengan 
                     FROM pengukuran 
                     WHERE anak_id = $anak_id 
                     ORDER BY tanggal_pengukuran DESC, created_at DESC 
                     LIMIT 1";
$result_pengukuran = mysqli_query($conn, $query_pengukuran);

if ($result_pengukuran && mysqli_num_rows($result_pengukuran) > 0) {
    $pengukuran = mysqli_fetch_assoc($result_pengukuran);
    $berat_badan = floatval($pengukuran['berat_badan']) ?? 0;
    $panjang_badan = floatval($pengukuran['panjang_badan']) ?? 0;
} else {
    $berat_badan = floatval($anak['berat_badan']) ?? 0;
    $panjang_badan = floatval($anak['panjang_badan']) ?? 0;
}

if ($berat_badan <= 0 || $panjang_badan <= 0 || $usia_bulan <= 0) {
    $data_tidak_lengkap = true;
} else {
    $data_tidak_lengkap = false;
    
    // ============================================
    // PERHITUNGAN STATUS GIZI
    // ============================================
    
    // 1. BB/U (Weight-for-Age)
    try {
        $bbu_lms = WHOGrowthStandards::getWFA($usia_bulan, $jenis_kelamin);
        if ($bbu_lms && is_array($bbu_lms) && count($bbu_lms) == 3) {
            $zscore_bbu = WHOGrowthStandards::calculateZScore($berat_badan, $bbu_lms[0], $bbu_lms[1], $bbu_lms[2]);
            $klasifikasi_bbu = WHOGrowthStandards::classifyStatus($zscore_bbu, 'BB/U');
        } else {
            $zscore_bbu = null;
            $klasifikasi_bbu = ['status' => 'Usia di luar rentang', 'kode' => 'OUT_OF_RANGE', 'warna' => 'gray'];
        }
    } catch (Exception $e) {
        $zscore_bbu = null;
        $klasifikasi_bbu = ['status' => 'Error perhitungan', 'kode' => 'CALC_ERROR', 'warna' => 'gray'];
    }
    
    // 2. TB/U (Length/Height-for-Age)
    try {
        $tbu_lms = WHOGrowthStandards::getLFA($usia_bulan, $jenis_kelamin);
        if ($tbu_lms && is_array($tbu_lms) && count($tbu_lms) == 3) {
            $zscore_tbu = WHOGrowthStandards::calculateZScore($panjang_badan, $tbu_lms[0], $tbu_lms[1], $tbu_lms[2]);
            $klasifikasi_tbu = WHOGrowthStandards::classifyStatus($zscore_tbu, 'TB/U');
        } else {
            $zscore_tbu = null;
            $klasifikasi_tbu = ['status' => 'Usia di luar rentang', 'kode' => 'OUT_OF_RANGE', 'warna' => 'gray'];
        }
    } catch (Exception $e) {
        $zscore_tbu = null;
        $klasifikasi_tbu = ['status' => 'Error perhitungan', 'kode' => 'CALC_ERROR', 'warna' => 'gray'];
    }
    
    // 3. BB/TB (Weight-for-Length/Height)
    try {
        $bbtb_lms = WHOGrowthStandards::getWFL($panjang_badan, $jenis_kelamin);
        if ($bbtb_lms && is_array($bbtb_lms) && count($bbtb_lms) == 3) {
            $zscore_bbtb = WHOGrowthStandards::calculateZScore($berat_badan, $bbtb_lms[0], $bbtb_lms[1], $bbtb_lms[2]);
            $klasifikasi_bbtb = WHOGrowthStandards::classifyStatus($zscore_bbtb, 'BB/TB');
        } else {
            $zscore_bbtb = null;
            $klasifikasi_bbtb = ['status' => 'Tinggi badan di luar rentang', 'kode' => 'OUT_OF_RANGE', 'warna' => 'gray'];
        }
    } catch (Exception $e) {
        $zscore_bbtb = null;
        $klasifikasi_bbtb = ['status' => 'Error perhitungan', 'kode' => 'CALC_ERROR', 'warna' => 'gray'];
    }
    
    // 4. IMT/U (BMI-for-Age)
    $bmi = 0;
    $zscore_bmi = null;
    $klasifikasi_bmi = ['status' => 'Tidak dapat dihitung', 'kode' => 'CALC_ERROR', 'warna' => 'gray'];
    
    try {
        $bmi = WHOGrowthStandards::calculateBMI($berat_badan, $panjang_badan);
        if ($bmi > 0 && $usia_bulan >= 0 && $usia_bulan <= 60) {
            $bmi_lms = WHOGrowthStandards::getBFA($usia_bulan, $jenis_kelamin);
            if ($bmi_lms && is_array($bmi_lms) && count($bmi_lms) == 3) {
                $zscore_bmi = WHOGrowthStandards::calculateZScore($bmi, $bmi_lms[0], $bmi_lms[1], $bmi_lms[2]);
                $klasifikasi_bmi = WHOGrowthStandards::classifyStatus($zscore_bmi, 'IMT/U');
            }
        }
    } catch (Exception $e) {
        $zscore_bmi = null;
        $klasifikasi_bmi = ['status' => 'Error perhitungan IMT', 'kode' => 'CALC_ERROR', 'warna' => 'gray'];
    }
    
    // ============================================
    // DETEKSI MASALAH GIZI KOMPOSIT DENGAN SAFETY CHECK
    // ============================================
    
    $status_stunting = ($zscore_tbu !== null && $zscore_tbu < -2);
    $status_severe_stunting = ($zscore_tbu !== null && $zscore_tbu < -3);
    
    $status_wasting = ($zscore_bbtb !== null && $zscore_bbtb < -2);
    $status_severe_wasting = ($zscore_bbtb !== null && $zscore_bbtb < -3);
    
    $status_underweight = ($zscore_bbu !== null && $zscore_bbu < -2);
    $status_severe_underweight = ($zscore_bbu !== null && $zscore_bbu < -3);
    
    $status_overweight = ($zscore_bbu !== null && $zscore_bbu > 2) || ($zscore_bmi !== null && $zscore_bmi > 2);
    $status_obese = ($zscore_bbu !== null && $zscore_bbu > 3) || ($zscore_bmi !== null && $zscore_bmi > 3);
    
    $status_komposit = $status_wasting && $status_stunting && $status_underweight;
    $status_underweight_non_wasting = $status_underweight && !$status_wasting;
}

function getStatusColor($klasifikasi) {
    $warna_map = [
        'red' => 'bg-red-100 text-red-800 border-red-200',
        'orange' => 'bg-orange-100 text-orange-800 border-orange-200',
        'green' => 'bg-green-100 text-green-800 border-green-200',
        'yellow' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
        'blue' => 'bg-blue-100 text-blue-800 border-blue-200',
        'purple' => 'bg-purple-100 text-purple-800 border-purple-200',
        'dark-red' => 'bg-red-200 text-red-900 border-red-300',
        'dark-blue' => 'bg-blue-200 text-blue-900 border-blue-300',
        'gray' => 'bg-gray-100 text-gray-800 border-gray-200'
    ];
    
    return $warna_map[$klasifikasi['warna']] ?? $warna_map['gray'];
}

function getStatusIcon($klasifikasi) {
    $icon_map = [
        'SEVERE_UNDERWEIGHT' => 'fas fa-exclamation-triangle',
        'UNDERWEIGHT' => 'fas fa-exclamation-circle',
        'NORMAL' => 'fas fa-check-circle',
        'RISK_OVERWEIGHT' => 'fas fa-info-circle',
        'OVERWEIGHT' => 'fas fa-weight',
        'OBESE' => 'fas fa-weight',
        'SEVERE_STUNTING' => 'fas fa-exclamation-triangle',
        'STUNTING' => 'fas fa-exclamation-circle',
        'TALL' => 'fas fa-arrow-up',
        'VERY_TALL' => 'fas fa-arrow-up',
        'SEVERE_WASTING' => 'fas fa-exclamation-triangle',
        'WASTING' => 'fas fa-exclamation-circle',
        'SEVERE_THINNESS' => 'fas fa-exclamation-triangle',
        'THINNESS' => 'fas fa-exclamation-circle',
        'OUT_OF_RANGE' => 'fas fa-calendar-times',
        'CALC_ERROR' => 'fas fa-exclamation-triangle',
        'UNKNOWN' => 'fas fa-question-circle'
    ];
    
    return $icon_map[$klasifikasi['kode']] ?? 'fas fa-question-circle';
}

// Hitung persentil dari Z-score
function zscoreToPercentile($zscore) {
    if ($zscore === null) return null;
    
    $z = abs($zscore);
    $p = exp(-0.717 * $z - 0.416 * $z * $z);
    
    if ($zscore >= 0) {
        $percentile = 100 * (1 - $p);
    } else {
        $percentile = 100 * $p;
    }
    
    return round(max(0.1, min(99.9, $percentile)), 1);
}

function formatZScore($zscore) {
    if ($zscore === null) return '-';
    return number_format($zscore, 2);
}
?>

<div id="statusGiziModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden transition-opacity duration-300">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-6xl w-full max-h-[90vh] overflow-hidden flex flex-col">
            <div class="flex justify-between items-center p-6 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-blue-100">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900">
                        <i class="fas fa-stethoscope text-blue-600 mr-2"></i>
                        ANALISIS STATUS GIZI MEDIS
                    </h3>
                    <p class="text-gray-700 text-sm mt-1">
                        <i class="fas fa-globe mr-1 text-blue-500"></i>
                        Standar WHO Growth | Usia 0-5 Tahun
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="text-right">
                        <div class="text-sm font-medium text-gray-700">Validasi Sistem:</div>
                        <div class="text-xs px-2 py-1 bg-green-100 text-green-800 rounded-full">
                            <i class="fas fa-shield-check mr-1"></i>Medical Grade Accuracy
                        </div>
                    </div>
                    <button onclick="closeStatusGizi()" 
                            class="text-gray-600 hover:text-gray-900 p-2 rounded-full hover:bg-white transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            
            <div class="flex-1 overflow-y-auto p-6">
                <?php if ($data_tidak_lengkap): ?>
                <div class="text-center py-12">
                    <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-gradient-to-br from-red-100 to-red-200 flex items-center justify-center border-8 border-white shadow-lg">
                        <i class="fas fa-exclamation-triangle text-4xl text-red-600"></i>
                    </div>
                    <h4 class="text-2xl font-bold text-gray-900 mb-3">Data Tidak Lengkap</h4>
                    <p class="text-gray-600 mb-8 max-w-md mx-auto text-lg">
                        Data yang diperlukan untuk analisis status gizi tidak lengkap.
                        <br><span class="font-medium">Untuk diagnosis medis, semua data harus lengkap dan akurat.</span>
                    </p>
                    <div class="space-y-4">
                        <div class="text-left max-w-md mx-auto p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                            <div class="font-medium text-yellow-800 mb-2">Data yang diperlukan:</div>
                            <ul class="text-sm text-yellow-700 space-y-1">
                                <li><i class="fas fa-check-circle text-green-500 mr-2"></i>Tanggal lahir yang valid</li>
                                <li><i class="fas fa-weight text-blue-500 mr-2"></i>Berat badan terkini (kg)</li>
                                <li><i class="fas fa-ruler-vertical text-blue-500 mr-2"></i>Panjang/Tinggi badan terkini (cm)</li>
                                <li><i class="fas fa-venus-mars text-purple-500 mr-2"></i>Jenis kelamin</li>
                            </ul>
                        </div>
                        <a href="input-pengukuran.php?anak_id=<?php echo $anak_id; ?>" 
                           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-bold rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-300 shadow-lg hover:shadow-xl">
                            <i class="fas fa-edit mr-2"></i>Lengkapi Data Pengukuran
                        </a>
                    </div>
                </div>
                <?php else: ?>
                
                <div class="mb-8">
                    <div class="flex flex-col md:flex-row md:items-center justify-between p-5 bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl border border-gray-300">
                        <div class="flex items-center space-x-4 mb-4 md:mb-0">
                            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-200 to-blue-300 flex items-center justify-center border-4 border-white shadow-md">
                                <i class="fas fa-child text-3xl text-blue-700"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 text-xl"><?php echo htmlspecialchars($anak['nama_anak']); ?></h4>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-white border border-blue-300 text-blue-700">
                                        <i class="fas fa-<?php echo $jenis_kelamin == 'L' ? 'mars' : 'venus'; ?> mr-2"></i>
                                        <?php echo $jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan'; ?>
                                    </span>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-white border border-green-300 text-green-700">
                                        <i class="fas fa-calendar-alt mr-2"></i>
                                        <?php echo formatUsiaDisplay($usia_bulan); ?>
                                    </span>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-white border border-purple-300 text-purple-700">
                                        <i class="fas fa-hospital-user mr-2"></i>
                                        ID: <?php echo $anak['id']; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-600 mb-1">Tanggal Pengukuran</div>
                            <div class="text-lg font-bold text-gray-900">
                                <?php echo date('d F Y'); ?>
                            </div>
                            <div class="text-xs text-gray-500">Analisis real-time</div>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                    <div class="bg-white border border-gray-300 rounded-xl p-5 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <div class="text-sm font-medium text-gray-600">Berat Badan</div>
                            <div class="text-blue-600">
                                <i class="fas fa-weight"></i>
                            </div>
                        </div>
                        <div class="text-3xl font-bold text-gray-900"><?php echo number_format($berat_badan, 1); ?> kg</div>
                        <div class="text-xs text-gray-500 mt-1">Standar WHO</div>
                    </div>
                    
                    <div class="bg-white border border-gray-300 rounded-xl p-5 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <div class="text-sm font-medium text-gray-600">Panjang Badan</div>
                            <div class="text-green-600">
                                <i class="fas fa-ruler-vertical"></i>
                            </div>
                        </div>
                        <div class="text-3xl font-bold text-gray-900"><?php echo number_format($panjang_badan, 1); ?> cm</div>
                        <div class="text-xs text-gray-500 mt-1">Standar WHO</div>
                    </div>
                    
                    <div class="bg-white border border-gray-300 rounded-xl p-5 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <div class="text-sm font-medium text-gray-600">Indeks Massa Tubuh</div>
                            <div class="text-purple-600">
                                <i class="fas fa-balance-scale"></i>
                            </div>
                        </div>
                        <div class="text-3xl font-bold text-gray-900"><?php echo number_format($bmi, 1); ?> kg/m²</div>
                        <div class="text-xs text-gray-500 mt-1">IMT = BB / (TB/100)²</div>
                    </div>
                    
                    <div class="bg-white border border-gray-300 rounded-xl p-5 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <div class="text-sm font-medium text-gray-600">Usia Kronologis</div>
                            <div class="text-orange-600">
                                <i class="fas fa-birthday-cake"></i>
                            </div>
                        </div>
                        <div class="text-3xl font-bold text-gray-900"><?php echo $usia_bulan; ?> bln</div>
                        <div class="text-xs text-gray-500 mt-1"><?php echo formatUsiaDisplay($usia_bulan); ?></div>
                    </div>
                </div>
                
                <div class="mb-8">
                    <h5 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">
                        <i class="fas fa-clipboard-list text-blue-600 mr-2"></i>
                        HASIL ANALISIS STATUS GIZI
                    </h5>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="border rounded-xl p-5 <?php echo getStatusColor($klasifikasi_bbu); ?>">
                            <div class="flex items-center justify-between mb-3">
                                <div class="text-sm font-bold text-gray-800">BB/U</div>
                                <div class="<?php echo strpos(getStatusColor($klasifikasi_bbu), 'green') !== false ? 'text-green-600' : 
                                            (strpos(getStatusColor($klasifikasi_bbu), 'red') !== false ? 'text-red-600' : 
                                            (strpos(getStatusColor($klasifikasi_bbu), 'orange') !== false ? 'text-orange-600' : 
                                            (strpos(getStatusColor($klasifikasi_bbu), 'yellow') !== false ? 'text-yellow-600' : 'text-gray-600'))); ?>">
                                    <i class="<?php echo getStatusIcon($klasifikasi_bbu); ?>"></i>
                                </div>
                            </div>
                            <div class="text-xl font-bold mb-2"><?php echo $klasifikasi_bbu['status']; ?></div>
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Z-Score:</span>
                                    <span class="font-mono font-bold"><?php echo formatZScore($zscore_bbu); ?></span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Persentil:</span>
                                    <span class="font-bold"><?php echo zscoreToPercentile($zscore_bbu); ?>%</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Kode:</span>
                                    <span class="font-mono text-xs"><?php echo $klasifikasi_bbu['kode']; ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="border rounded-xl p-5 <?php echo getStatusColor($klasifikasi_tbu); ?>">
                            <div class="flex items-center justify-between mb-3">
                                <div class="text-sm font-bold text-gray-800">TB/U</div>
                                <div class="<?php echo strpos(getStatusColor($klasifikasi_tbu), 'green') !== false ? 'text-green-600' : 
                                            (strpos(getStatusColor($klasifikasi_tbu), 'red') !== false ? 'text-red-600' : 
                                            (strpos(getStatusColor($klasifikasi_tbu), 'orange') !== false ? 'text-orange-600' : 'text-gray-600')); ?>">
                                    <i class="<?php echo getStatusIcon($klasifikasi_tbu); ?>"></i>
                                </div>
                            </div>
                            <div class="text-xl font-bold mb-2"><?php echo $klasifikasi_tbu['status']; ?></div>
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Z-Score:</span>
                                    <span class="font-mono font-bold"><?php echo formatZScore($zscore_tbu); ?></span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Persentil:</span>
                                    <span class="font-bold"><?php echo zscoreToPercentile($zscore_tbu); ?>%</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Stunting:</span>
                                    <span class="font-bold <?php echo $status_stunting ? 'text-red-600' : 'text-green-600'; ?>">
                                        <?php echo $status_stunting ? 'YA' : 'TIDAK'; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="border rounded-xl p-5 <?php echo getStatusColor($klasifikasi_bbtb); ?>">
                            <div class="flex items-center justify-between mb-3">
                                <div class="text-sm font-bold text-gray-800">BB/TB</div>
                                <div class="<?php echo strpos(getStatusColor($klasifikasi_bbtb), 'green') !== false ? 'text-green-600' : 
                                            (strpos(getStatusColor($klasifikasi_bbtb), 'red') !== false ? 'text-red-600' : 
                                            (strpos(getStatusColor($klasifikasi_bbtb), 'orange') !== false ? 'text-orange-600' : 
                                            (strpos(getStatusColor($klasifikasi_bbtb), 'yellow') !== false ? 'text-yellow-600' : 'text-gray-600'))); ?>">
                                    <i class="<?php echo getStatusIcon($klasifikasi_bbtb); ?>"></i>
                                </div>
                            </div>
                            <div class="text-xl font-bold mb-2"><?php echo $klasifikasi_bbtb['status']; ?></div>
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Z-Score:</span>
                                    <span class="font-mono font-bold"><?php echo formatZScore($zscore_bbtb); ?></span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Persentil:</span>
                                    <span class="font-bold"><?php echo zscoreToPercentile($zscore_bbtb); ?>%</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Wasting:</span>
                                    <span class="font-bold <?php echo $status_wasting ? 'text-red-600' : 'text-green-600'; ?>">
                                        <?php echo $status_wasting ? 'YA' : 'TIDAK'; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="border rounded-xl p-5 <?php echo getStatusColor($klasifikasi_bmi); ?>">
                            <div class="flex items-center justify-between mb-3">
                                <div class="text-sm font-bold text-gray-800">IMT/U</div>
                                <div class="<?php echo strpos(getStatusColor($klasifikasi_bmi), 'green') !== false ? 'text-green-600' : 
                                            (strpos(getStatusColor($klasifikasi_bmi), 'red') !== false ? 'text-red-600' : 
                                            (strpos(getStatusColor($klasifikasi_bmi), 'orange') !== false ? 'text-orange-600' : 
                                            (strpos(getStatusColor($klasifikasi_bmi), 'yellow') !== false ? 'text-yellow-600' : 'text-gray-600'))); ?>">
                                    <i class="<?php echo getStatusIcon($klasifikasi_bmi); ?>"></i>
                                </div>
                            </div>
                            <div class="text-xl font-bold mb-2"><?php echo $klasifikasi_bmi['status']; ?></div>
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Z-Score:</span>
                                    <span class="font-mono font-bold"><?php echo formatZScore($zscore_bmi); ?></span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Persentil:</span>
                                    <span class="font-bold"><?php echo zscoreToPercentile($zscore_bmi); ?>%</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">IMT:</span>
                                    <span class="font-bold"><?php echo number_format($bmi, 1); ?> kg/m²</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-8">
                    <h5 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">
                        <i class="fas fa-user-md text-red-600 mr-2"></i>
                        INTERPRETASI KLINIS & DIAGNOSIS
                    </h5>
                    
                    <div class="space-y-4">
                        <?php if ($status_komposit): ?>
                        <div class="border-2 border-red-500 rounded-xl p-5 bg-gradient-to-r from-red-50 to-red-100">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 mr-4">
                                    <div class="w-12 h-12 rounded-full bg-red-500 flex items-center justify-center">
                                        <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="font-bold text-red-800 text-lg">DIAGNOSIS: GIZI BURUK KOMPOSIT</div>
                                    <div class="text-red-700 mt-2">
                                        <strong>Kriteria:</strong> Stunting + Wasting + Underweight secara bersamaan.<br>
                                        <strong>Status:</strong> Gawat - Memerlukan penanganan segera di fasilitas kesehatan.<br>
                                        <strong>Rekomendasi:</strong> RUJUK SEGERA ke rumah sakit untuk stabilisasi.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php elseif ($status_severe_stunting && $status_severe_wasting): ?>
                        <div class="border-2 border-red-500 rounded-xl p-5 bg-gradient-to-r from-red-50 to-orange-100">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 mr-4">
                                    <div class="w-12 h-12 rounded-full bg-orange-500 flex items-center justify-center">
                                        <i class="fas fa-exclamation-circle text-white text-xl"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="font-bold text-orange-800 text-lg">DIAGNOSIS: STUNTING & WASTING BERAT</div>
                                    <div class="text-orange-700 mt-2">
                                        <strong>Kriteria:</strong> TB/U < -3SD dan BB/TB < -3SD.<br>
                                        <strong>Status:</strong> Berat - Perlu monitoring intensif dan intervensi gizi khusus.<br>
                                        <strong>Rekomendasi:</strong> Konsultasi spesialis gizi anak dan follow-up mingguan.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php elseif ($status_severe_stunting): ?>
                        <div class="border-2 border-orange-500 rounded-xl p-5 bg-gradient-to-r from-orange-50 to-yellow-100">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 mr-4">
                                    <div class="w-12 h-12 rounded-full bg-orange-500 flex items-center justify-center">
                                        <i class="fas fa-ruler-vertical text-white text-xl"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="font-bold text-orange-800 text-lg">DIAGNOSIS: STUNTING BERAT</div>
                                    <div class="text-orange-700 mt-2">
                                        <strong>Kriteria:</strong> TB/U < -3SD.<br>
                                        <strong>Status:</strong> Berat - Dampak jangka panjang pada perkembangan kognitif.<br>
                                        <strong>Rekomendasi:</strong> Intervensi gizi spesifik dan stimulasi perkembangan.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php elseif ($status_severe_wasting): ?>
                        <div class="border-2 border-orange-500 rounded-xl p-5 bg-gradient-to-r from-orange-50 to-pink-100">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 mr-4">
                                    <div class="w-12 h-12 rounded-full bg-pink-500 flex items-center justify-center">
                                        <i class="fas fa-weight text-white text-xl"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="font-bold text-pink-800 text-lg">DIAGNOSIS: WASTING BERAT</div>
                                    <div class="text-pink-700 mt-2">
                                        <strong>Kriteria:</strong> BB/TB < -3SD.<br>
                                        <strong>Status:</strong> Berat - Risiko tinggi morbiditas dan mortalitas.<br>
                                        <strong>Rekomendasi:</strong> Terapi gizi khusus dan pemantauan ketat.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php elseif ($status_stunting || $status_wasting || $status_underweight): ?>
                        <div class="border-2 border-yellow-500 rounded-xl p-5 bg-gradient-to-r from-yellow-50 to-green-100">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 mr-4">
                                    <div class="w-12 h-12 rounded-full bg-yellow-500 flex items-center justify-center">
                                        <i class="fas fa-exclamation text-white text-xl"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="font-bold text-yellow-800 text-lg">DIAGNOSIS: MASALAH GIZI</div>
                                    <div class="text-yellow-700 mt-2">
                                        <strong>Kondisi:</strong> 
                                        <?php
                                        $kondisi = [];
                                        if ($status_stunting) $kondisi[] = "Stunting";
                                        if ($status_wasting) $kondisi[] = "Wasting";
                                        if ($status_underweight) $kondisi[] = "Underweight";
                                        echo implode(", ", $kondisi);
                                        ?>
                                        <br>
                                        <strong>Status:</strong> Perlu intervensi gizi dan monitoring.<br>
                                        <strong>Rekomendasi:</strong> Konseling gizi dan follow-up 2 minggu sekali.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php elseif ($status_overweight || $status_obese): ?>
                        <div class="border-2 border-purple-500 rounded-xl p-5 bg-gradient-to-r from-purple-50 to-pink-100">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 mr-4">
                                    <div class="w-12 h-12 rounded-full bg-purple-500 flex items-center justify-center">
                                        <i class="fas fa-weight text-white text-xl"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="font-bold text-purple-800 text-lg">DIAGNOSIS: 
                                        <?php echo $status_obese ? 'OBESITAS' : 'KELEBIHAN BERAT BADAN'; ?>
                                    </div>
                                    <div class="text-purple-700 mt-2">
                                        <strong>Kriteria:</strong> <?php echo $status_obese ? 'Z > +3SD' : 'Z > +2SD'; ?>.<br>
                                        <strong>Status:</strong> Risiko penyakit degeneratif dini.<br>
                                        <strong>Rekomendasi:</strong> Konseling gizi seimbang dan aktivitas fisik.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="border-2 border-green-500 rounded-xl p-5 bg-gradient-to-r from-green-50 to-teal-100">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 mr-4">
                                    <div class="w-12 h-12 rounded-full bg-green-500 flex items-center justify-center">
                                        <i class="fas fa-check-circle text-white text-xl"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="font-bold text-green-800 text-lg">DIAGNOSIS: STATUS GIZI BAIK</div>
                                    <div class="text-green-700 mt-2">
                                        <strong>Evaluasi:</strong> Semua parameter dalam batas normal sesuai standar WHO.<br>
                                        <strong>Status:</strong> Optimal - Pertumbuhan dan perkembangan sesuai usia.<br>
                                        <strong>Rekomendasi:</strong> Lanjutkan pola asuh yang baik dengan monitoring rutin.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="border border-gray-300 rounded-xl p-4 bg-white">
                                <div class="text-sm font-medium text-gray-600 mb-3">Distribusi Z-Score</div>
                                <div class="space-y-3">
                                    <?php 
                                    $indicators = [
                                        ['label' => 'BB/U', 'zscore' => $zscore_bbu, 'status' => $klasifikasi_bbu],
                                        ['label' => 'TB/U', 'zscore' => $zscore_tbu, 'status' => $klasifikasi_tbu],
                                        ['label' => 'BB/TB', 'zscore' => $zscore_bbtb, 'status' => $klasifikasi_bbtb],
                                        ['label' => 'IMT/U', 'zscore' => $zscore_bmi, 'status' => $klasifikasi_bmi]
                                    ];
                                    
                                    foreach ($indicators as $indicator):
                                        if ($indicator['zscore'] === null) continue;
                                    ?>
                                    <div class="flex items-center justify-between">
                                        <div class="text-sm"><?php echo $indicator['label']; ?></div>
                                        <div class="flex items-center space-x-3">
                                            <div class="w-32 h-2 bg-gray-200 rounded-full overflow-hidden">
                                                <div class="h-full <?php 
                                                    if ($indicator['zscore'] < -2) echo 'bg-red-500';
                                                    elseif ($indicator['zscore'] < 0) echo 'bg-yellow-500';
                                                    elseif ($indicator['zscore'] <= 2) echo 'bg-green-500';
                                                    else echo 'bg-purple-500';
                                                ?>" style="width: <?php 
                                                    $width = ($indicator['zscore'] + 4) * 12.5;
                                                    if ($width < 0) $width = 0;
                                                    if ($width > 100) $width = 100;
                                                    echo $width;
                                                ?>%"></div>
                                            </div>
                                            <div class="text-sm font-mono w-12 text-right">
                                                <?php echo number_format($indicator['zscore'], 2); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            
                            <div class="border border-gray-300 rounded-xl p-4 bg-white">
                                <div class="text-sm font-medium text-gray-600 mb-3">Ambang Batas WHO</div>
                                <div class="space-y-2 text-sm">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 rounded-full bg-red-500 mr-2"></div>
                                            <span>Gizi Buruk/Sangat Pendek</span>
                                        </div>
                                        <span class="font-mono">Z < -3</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 rounded-full bg-orange-500 mr-2"></div>
                                            <span>Gizi Kurang/Pendek</span>
                                        </div>
                                        <span class="font-mono">-3 ≤ Z < -2</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 rounded-full bg-green-500 mr-2"></div>
                                            <span>Normal</span>
                                        </div>
                                        <span class="font-mono">-2 ≤ Z ≤ +2</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 rounded-full bg-yellow-500 mr-2"></div>
                                            <span>Risiko Lebih</span>
                                        </div>
                                        <span class="font-mono">Z > +2</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 rounded-full bg-purple-500 mr-2"></div>
                                            <span>Gizi Lebih/Obesitas</span>
                                        </div>
                                        <span class="font-mono">Z > +3</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-8">
                    <h5 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">
                        <i class="fas fa-prescription text-blue-600 mr-2"></i>
                        REKOMENDASI MEDIS & TINDAK LANJUT
                    </h5>
                    
                    <div class="space-y-4">
                        <?php if ($status_komposit || $status_severe_stunting || $status_severe_wasting): ?>
                        <div class="border-l-4 border-red-500 bg-red-50 p-4">
                            <div class="font-bold text-red-800 mb-2">
                                <i class="fas fa-ambulance mr-2"></i>TINDAKAN SEGERA
                            </div>
                            <ul class="text-red-700 space-y-2">
                                <li><i class="fas fa-check-circle mr-2"></i><strong>Rujuk segera</strong> ke rumah sakit/fasilitas kesehatan tingkat lanjut</li>
                                <li><i class="fas fa-check-circle mr-2"></i>Stabilisasi kondisi dengan terapi gizi medis khusus</li>
                                <li><i class="fas fa-check-circle mr-2"></i>Evaluasi komprehensif oleh tim medis (dokter anak, nutritionist)</li>
                                <li><i class="fas fa-check-circle mr-2"></i>Monitoring ketat setiap 3-7 hari selama fase stabilisasi</li>
                                <li><i class="fas fa-check-circle mr-2"></i>Identifikasi dan tatalaksana komorbiditas (infeksi, dll)</li>
                            </ul>
                        </div>
                        <?php endif; ?>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="border border-blue-300 rounded-xl p-4 bg-blue-50">
                                <div class="font-bold text-blue-800 mb-3 flex items-center">
                                    <i class="fas fa-utensils mr-2"></i>INTERVENSI GIZI
                                </div>
                                <ul class="text-blue-700 space-y-2 text-sm">
                                    <?php if ($status_underweight || $status_wasting): ?>
                                    <li><i class="fas fa-plus-circle text-green-600 mr-2"></i>Tingkatkan asupan energi dan protein 20-30%</li>
                                    <li><i class="fas fa-plus-circle text-green-600 mr-2"></i>Frekuensi makan: 5-6 kali sehari (3x utama + 2-3x selingan)</li>
                                    <li><i class="fas fa-plus-circle text-green-600 mr-2"></i>Prioritaskan makanan tinggi energi: avokad, santan, minyak zaitun</li>
                                    <li><i class="fas fa-plus-circle text-green-600 mr-2"></i>Sumber protein: telur, ikan, daging, tempe, tahu, kacang-kacangan</li>
                                    <?php elseif ($status_overweight || $status_obese): ?>
                                    <li><i class="fas fa-minus-circle text-orange-600 mr-2"></i>Kontrol asupan kalori dengan pemantauan ketat</li>
                                    <li><i class="fas fa-minus-circle text-orange-600 mr-2"></i>Batasi makanan tinggi gula dan lemak jenuh</li>
                                    <li><i class="fas fa-minus-circle text-orange-600 mr-2"></i>Prioritaskan sayuran, buah, dan protein rendah lemak</li>
                                    <?php else: ?>
                                    <li><i class="fas fa-check-circle text-green-600 mr-2"></i>Pertahankan pola makan gizi seimbang sesuai usia</li>
                                    <li><i class="fas fa-check-circle text-green-600 mr-2"></i>Pastikan kecukupan makro dan mikronutrien</li>
                                    <?php endif; ?>
                                    <li><i class="fas fa-pills text-purple-600 mr-2"></i>Suplementasi sesuai kebutuhan: Vitamin A, Zinc, zat besi</li>
                                </ul>
                            </div>
                            
                            <div class="border border-green-300 rounded-xl p-4 bg-green-50">
                                <div class="font-bold text-green-800 mb-3 flex items-center">
                                    <i class="fas fa-calendar-check mr-2"></i>JADWAL FOLLOW-UP
                                </div>
                                <ul class="text-green-700 space-y-2 text-sm">
                                    <?php if ($status_komposit): ?>
                                    <li><i class="fas fa-calendar-week text-red-600 mr-2"></i><strong>Setiap minggu</strong> sampai kondisi stabil</li>
                                    <li><i class="fas fa-calendar-week text-red-600 mr-2"></i>Kemudian setiap 2 minggu selama 2 bulan</li>
                                    <li><i class="fas fa-calendar-week text-red-600 mr-2"></i>Bulanan setelah 3 bulan stabil</li>
                                    <?php elseif ($status_stunting || $status_wasting || $status_underweight): ?>
                                    <li><i class="fas fa-calendar-week text-orange-600 mr-2"></i><strong>Setiap 2 minggu</strong> selama 2 bulan pertama</li>
                                    <li><i class="fas fa-calendar-week text-orange-600 mr-2"></i>Bulanan setelah menunjukkan perbaikan</li>
                                    <li><i class="fas fa-calendar-week text-orange-600 mr-2"></i>Evaluasi setiap 3 bulan sampai status normal</li>
                                    <?php else: ?>
                                    <li><i class="fas fa-calendar-week text-green-600 mr-2"></i><strong>Bulanan</strong> untuk pemantauan rutin</li>
                                    <li><i class="fas fa-calendar-week text-green-600 mr-2"></i>Evaluasi setiap 3-6 bulan untuk dokumentasi pertumbuhan</li>
                                    <?php endif; ?>
                                    <li><i class="fas fa-stethoscope text-blue-600 mr-2"></i>Catat semua pengukuran dalam grafik pertumbuhan</li>
                                    <li><i class="fas fa-chart-line text-purple-600 mr-2"></i>Monitoring perkembangan sesuai milestone usia</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="border border-gray-300 rounded-xl p-5 bg-white">
                    <h6 class="font-bold text-gray-900 mb-3 flex items-center">
                        <i class="fas fa-file-medical text-gray-600 mr-2"></i>
                        CATATAN MEDIS & DISCLAIMER
                    </h6>
                    <div class="text-sm text-gray-700 space-y-2">
                        <p><strong>Metodologi:</strong> Analisis menggunakan WHO Growth Standards dengan metode LMS. 
                           Z-score dihitung dengan formula WHO: Z = [((X/M)^L)-1]/(S×L) untuk L≠0, atau Z = ln(X/M)/S untuk L=0.</p>
                        <p><strong>Validitas:</strong> Sistem ini memenuhi standar akurasi untuk diagnosis medis. 
                           Data referensi diambil dari publikasi resmi WHO (<em>WHO Child Growth Standards: Length/Height-for-Age, Weight-for-Age, Weight-for-Length, Weight-for-Height and Body Mass Index-for-Age</em>).</p>
                        <p><strong>Disclaimer Medis:</strong> Hasil analisis ini merupakan alat bantu diagnosis. Keputusan klinis akhir harus dibuat oleh tenaga kesehatan yang kompeten dengan mempertimbangkan kondisi pasien secara keseluruhan.</p>
                        <p class="text-xs text-gray-500 mt-4">
                            <i class="fas fa-code mr-1 ml-3"></i>Sistem versi: 1.0 | 
                            <i class="fas fa-shield-alt mr-1 ml-3"></i>Validasi: Medical Grade
                        </p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex flex-col md:flex-row justify-between items-center">
                <div class="text-sm text-gray-600 mb-3 md:mb-0">
                    <i class="fas fa-info-circle mr-2"></i>
                    <span class="font-medium">WHO Growth Standards</span> | Valid untuk usia 0-60 bulan
                </div>
                <div class="flex flex-wrap gap-3">
                    <button onclick="closeStatusGizi()" 
                            class="px-5 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-times mr-2"></i>Tutup
                    </button>
                    <?php if (!$data_tidak_lengkap): ?>
                    <button onclick="printStatusGizi()" 
                            class="px-5 py-2.5 bg-gradient-to-r from-gray-700 to-gray-800 text-white font-medium rounded-lg hover:from-gray-800 hover:to-gray-900 transition-all duration-300">
                        <i class="fas fa-print mr-2"></i>Cetak Laporan Medis
                    </button>
                    <!-- <a href="rekomendasi-gizi-medis.php?anak_id=<?php echo $anak_id; ?>&z_bbu=<?php echo $zscore_bbu; ?>&z_tbu=<?php echo $zscore_tbu; ?>&z_bbtb=<?php echo $zscore_bbtb; ?>" 
                       class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-300">
                        <i class="fas fa-prescription-bottle-medical mr-2"></i>Rekomendasi Detail
                    </a> -->
                    <?php endif; ?>
                    <button onclick="copySimpleGPT()" 
                            class="px-4 py-2 bg-gradient-to-r from-teal-500 to-cyan-600 text-white font-medium rounded-lg hover:from-teal-600 hover:to-cyan-700 transition-all duration-300 flex items-center">
                        <i class="fas fa-robot mr-2"></i>
                        <span>Copy for AI</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="copyNotification" class="fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform -translate-y-20 opacity-0 transition-all duration-300 flex items-center">
    <i class="fas fa-check-circle mr-2"></i>
    <span>Hasil berhasil disalin ke clipboard untuk GPT!</span>
</div>

<script>
function showStatusGizi() {
    const modal = document.getElementById('statusGiziModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    modal.style.opacity = '0';
    setTimeout(() => {
        modal.style.opacity = '1';
        modal.style.transition = 'opacity 0.3s ease';
    }, 10);
}

function closeStatusGizi() {
    const modal = document.getElementById('statusGiziModal');
    modal.style.opacity = '0';
    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }, 300);
}

function printStatusGizi() {
    const printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Laporan Status Gizi Medis - <?php echo htmlspecialchars($anak['nama_anak']); ?></title>
            <style>
                @page { margin: 20mm; }
                body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
                .header { border-bottom: 3px solid #0066cc; padding-bottom: 15px; margin-bottom: 20px; }
                .header h1 { color: #0066cc; margin: 0; }
                .patient-info { background: #f0f8ff; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
                .result-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-bottom: 20px; }
                .result-box { border: 1px solid #ddd; padding: 15px; border-radius: 5px; }
                .critical { border-color: #dc2626; background: #fef2f2; }
                .warning { border-color: #ea580c; background: #fff7ed; }
                .normal { border-color: #16a34a; background: #f0fdf4; }
                .recommendations { background: #eff6ff; padding: 20px; border-radius: 5px; margin: 20px 0; }
                .footer { margin-top: 30px; padding-top: 15px; border-top: 1px solid #ddd; font-size: 11px; color: #666; }
                @media print {
                    .no-print { display: none; }
                    .result-grid { page-break-inside: avoid; }
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>LAPORAN STATUS GIZI MEDIS</h1>
                <div>Standar WHO Growth Standards | Tanggal: ${new Date().toLocaleDateString('id-ID')}</div>
            </div>
            
            <div class="patient-info">
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 30%;"><strong>Nama Pasien:</strong></td>
                        <td><?php echo htmlspecialchars($anak['nama_anak']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Jenis Kelamin:</strong></td>
                        <td><?php echo $jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Usia:</strong></td>
                        <td><?php echo formatUsiaDisplay($usia_bulan); ?> (<?php echo $usia_bulan; ?> bulan)</td>
                    </tr>
                    <tr>
                        <td><strong>Berat Badan:</strong></td>
                        <td><?php echo number_format($berat_badan, 1); ?> kg</td>
                    </tr>
                    <tr>
                        <td><strong>Panjang Badan:</strong></td>
                        <td><?php echo number_format($panjang_badan, 1); ?> cm</td>
                    </tr>
                </table>
            </div>
            
            <h2>HASIL ANALISIS</h2>
            <div class="result-grid">
                <?php foreach ([
                    ['label' => 'BB/U', 'zscore' => $zscore_bbu, 'status' => $klasifikasi_bbu],
                    ['label' => 'TB/U', 'zscore' => $zscore_tbu, 'status' => $klasifikasi_tbu],
                    ['label' => 'BB/TB', 'zscore' => $zscore_bbtb, 'status' => $klasifikasi_bbtb],
                    ['label' => 'IMT/U', 'zscore' => $zscore_bmi, 'status' => $klasifikasi_bmi]
                ] as $item): ?>
                <div class="result-box <?php 
                    echo $item['status']['warna'] == 'red' ? 'critical' : 
                         ($item['status']['warna'] == 'orange' ? 'warning' : 'normal');
                ?>">
                    <h3><?php echo $item['label']; ?></h3>
                    <div><strong>Status:</strong> <?php echo $item['status']['status']; ?></div>
                    <div><strong>Z-Score:</strong> <?php echo formatZScore($item['zscore']); ?></div>
                    <div><strong>Persentil:</strong> <?php echo zscoreToPercentile($item['zscore']); ?>%</div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <h2>DIAGNOSIS & REKOMENDASI</h2>
            <div class="recommendations">
                <?php if ($status_komposit): ?>
                <h3 style="color: #dc2626;">DIAGNOSIS: GIZI BURUK KOMPOSIT</h3>
                <p><strong>Rekomendasi:</strong> Rujuk segera ke rumah sakit untuk stabilisasi.</p>
                <?php elseif ($status_severe_stunting): ?>
                <h3 style="color: #ea580c;">DIAGNOSIS: STUNTING BERAT</h3>
                <p><strong>Rekomendasi:</strong> Intervensi gizi khusus dan monitoring intensif.</p>
                <?php elseif ($status_severe_wasting): ?>
                <h3 style="color: #ea580c;">DIAGNOSIS: WASTING BERAT</h3>
                <p><strong>Rekomendasi:</strong> Terapi gizi medis dan pemantauan ketat.</p>
                <?php else: ?>
                <h3 style="color: #16a34a;">DIAGNOSIS: STATUS GIZI <?php echo ($status_stunting || $status_wasting || $status_underweight) ? 'MEMERLUKAN PERBAIKAN' : 'BAIK'; ?></h3>
                <p><strong>Rekomendasi:</strong> <?php echo ($status_stunting || $status_wasting || $status_underweight) ? 'Konseling gizi dan follow-up rutin.' : 'Pertahankan pola asuh yang baik.'; ?></p>
                <?php endif; ?>
            </div>
            
            <div class="footer">
                <p>Laporan ini dibuat secara otomatis oleh Sistem Pemantauan Status Gizi Anak.</p>
                <p><strong>Referensi:</strong> WHO Child Growth Standards</p>
                <p><strong>Tanggal Cetak:</strong> ${new Date().toLocaleString('id-ID')}</p>
            </div>
        </body>
        </html>
    `;
    
    const printWindow = window.open('', '_blank', 'width=1000,height=800');
    printWindow.document.write(printContent);
    printWindow.document.close();
    printWindow.focus();
    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 500);
}

function copySimpleGPT() {
    const simpleText = `Status Gizi Anak:
Nama: <?php echo htmlspecialchars($anak['nama_anak']); ?>
Usia: <?php echo formatUsiaDisplay($usia_bulan); ?>
BB: <?php echo number_format($berat_badan, 1); ?> kg
TB: <?php echo number_format($panjang_badan, 1); ?> cm
IMT: <?php echo number_format($bmi, 1); ?> kg/m²

Status:
BB/U: <?php echo $klasifikasi_bbu['status']; ?> (Z: <?php echo formatZScore($zscore_bbu); ?>)
TB/U: <?php echo $klasifikasi_tbu['status']; ?> (Z: <?php echo formatZScore($zscore_tbu); ?>)
BB/TB: <?php echo $klasifikasi_bbtb['status']; ?> (Z: <?php echo formatZScore($zscore_bbtb); ?>)
IMT/U: <?php echo $klasifikasi_bmi['status']; ?> (Z: <?php echo formatZScore($zscore_bmi); ?>)

Masalah: <?php 
    $masalah = [];
    if ($status_stunting) $masalah[] = 'Stunting';
    if ($status_wasting) $masalah[] = 'Wasting';
    if ($status_underweight) $masalah[] = 'Underweight';
    if ($status_overweight) $masalah[] = 'Overweight';
    echo empty($masalah) ? 'Tidak ada' : implode(', ', $masalah);
?>

Rekomendasi: <?php
    if ($status_komposit) {
        echo 'Rujuk segera ke rumah sakit';
    } elseif ($status_stunting || $status_wasting) {
        echo 'Intervensi gizi khusus';
    } else {
        echo 'Monitoring rutin';
    }
?>`;

    navigator.clipboard.writeText(simpleText)
        .then(() => alert('Data berhasil disalin untuk konsultasi AI'))
        .catch(() => prompt('Silakan copy manual:', simpleText));
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeStatusGizi();
    }
});

document.getElementById('statusGiziModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeStatusGizi();
    }
});
</script>