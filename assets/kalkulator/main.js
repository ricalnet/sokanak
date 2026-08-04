document.addEventListener('DOMContentLoaded', function () {
    AOS.init({
        once: false,
        mirror: true,
        duration: 600,
        easing: 'ease-out-cubic',
    });

    const defaultDate = new Date();
    defaultDate.setFullYear(defaultDate.getFullYear() - 2);
    document.getElementById('tglLahir').value = defaultDate.toISOString().split('T')[0];

    document.getElementById('calcForm').addEventListener('submit', runCalculator);

    console.log('✅ Kalkulator Gizi Anak Sok!Anak siap digunakan.');
});

// ================================================================
// DATA WHO GROWTH STANDARDS (LMS METHOD)
// ================================================================

const WFA_BOYS = {
    0: [0.3487, 3.3464, 0.14602],
    1: [0.2297, 4.4709, 0.13395],
    2: [0.197, 5.5675, 0.12385],
    3: [0.1738, 6.3762, 0.11727],
    4: [0.1553, 7.0023, 0.11316],
    5: [0.1395, 7.5105, 0.1108],
    6: [0.1257, 7.934, 0.10958],
    7: [0.1134, 8.297, 0.10902],
    8: [0.1021, 8.6151, 0.10882],
    9: [0.0917, 8.9014, 0.10881],
    10: [0.082, 9.1649, 0.10891],
    11: [0.073, 9.4122, 0.10906],
    12: [0.0644, 9.6479, 0.10925],
    13: [0.0563, 9.8749, 0.10949],
    14: [0.0487, 10.0953, 0.10976],
    15: [0.0413, 10.3108, 0.11007],
    16: [0.0343, 10.5228, 0.11041],
    17: [0.0275, 10.7319, 0.11079],
    18: [0.0211, 10.9385, 0.11119],
    19: [0.0148, 11.143, 0.11164],
    20: [0.0087, 11.3462, 0.11211],
    21: [0.0029, 11.5486, 0.11261],
    22: [-0.0028, 11.7504, 0.11314],
    23: [-0.0083, 11.9514, 0.11369],
    24: [-0.0137, 12.1515, 0.11426],
    25: [-0.0189, 12.3502, 0.11485],
    26: [-0.024, 12.5466, 0.11544],
    27: [-0.0289, 12.7401, 0.11604],
    28: [-0.0337, 12.9303, 0.11664],
    29: [-0.0385, 13.1169, 0.11723],
    30: [-0.0431, 13.3, 0.11781],
    31: [-0.0476, 13.4798, 0.11839],
    32: [-0.052, 13.6567, 0.11896],
    33: [-0.0564, 13.8309, 0.11953],
    34: [-0.0606, 14.0031, 0.12008],
    35: [-0.0648, 14.1736, 0.12062],
    36: [-0.0689, 14.3429, 0.12116],
    37: [-0.0729, 14.5113, 0.12168],
    38: [-0.0769, 14.6791, 0.1222],
    39: [-0.0808, 14.8466, 0.12271],
    40: [-0.0846, 15.014, 0.12322],
    41: [-0.0883, 15.1813, 0.12373],
    42: [-0.092, 15.3486, 0.12425],
    43: [-0.0957, 15.5158, 0.12478],
    44: [-0.0993, 15.6828, 0.12531],
    45: [-0.1028, 15.8497, 0.12586],
    46: [-0.1063, 16.0163, 0.12643],
    47: [-0.1097, 16.1827, 0.127],
    48: [-0.1131, 16.3489, 0.12759],
    49: [-0.1165, 16.515, 0.12819],
    50: [-0.1198, 16.6811, 0.1288],
    51: [-0.123, 16.8471, 0.12943],
    52: [-0.1262, 17.0132, 0.13005],
    53: [-0.1294, 17.1792, 0.13069],
    54: [-0.1325, 17.3452, 0.13133],
    55: [-0.1356, 17.5111, 0.13197],
    56: [-0.1387, 17.6768, 0.13261],
    57: [-0.1417, 17.8422, 0.13325],
    58: [-0.1447, 18.0073, 0.13389],
    59: [-0.1477, 18.1722, 0.13453],
    60: [-0.1506, 18.3366, 0.13517]
};

const WFA_GIRLS = {
    0: [0.3809, 3.2322, 0.14171],
    1: [0.1714, 4.1873, 0.13724],
    2: [0.0962, 5.1282, 0.13],
    3: [0.0402, 5.8458, 0.12619],
    4: [-0.005, 6.4237, 0.12402],
    5: [-0.043, 6.8985, 0.12274],
    6: [-0.0756, 7.297, 0.12204],
    7: [-0.1039, 7.6422, 0.12178],
    8: [-0.1288, 7.9487, 0.12181],
    9: [-0.1507, 8.2254, 0.12199],
    10: [-0.17, 8.48, 0.12223],
    11: [-0.1872, 8.7192, 0.12247],
    12: [-0.2024, 8.9481, 0.12268],
    13: [-0.2158, 9.1699, 0.12283],
    14: [-0.2278, 9.387, 0.12294],
    15: [-0.2384, 9.6008, 0.12299],
    16: [-0.2478, 9.8124, 0.12303],
    17: [-0.2562, 10.0226, 0.12306],
    18: [-0.2637, 10.2315, 0.12309],
    19: [-0.2703, 10.4393, 0.12315],
    20: [-0.2762, 10.6464, 0.12323],
    21: [-0.2815, 10.8534, 0.12335],
    22: [-0.2862, 11.0608, 0.1235],
    23: [-0.2903, 11.2688, 0.12369],
    24: [-0.2941, 11.4775, 0.1239],
    25: [-0.2975, 11.6864, 0.12414],
    26: [-0.3005, 11.8947, 0.12441],
    27: [-0.3032, 12.1015, 0.12472],
    28: [-0.3057, 12.3059, 0.12506],
    29: [-0.308, 12.5073, 0.12545],
    30: [-0.3101, 12.7055, 0.12587],
    31: [-0.312, 12.9006, 0.12633],
    32: [-0.3138, 13.093, 0.12683],
    33: [-0.3155, 13.2837, 0.12737],
    34: [-0.3171, 13.473, 0.12794],
    35: [-0.3186, 13.6618, 0.12855],
    36: [-0.3201, 13.8503, 0.12919],
    37: [-0.3216, 14.0385, 0.12988],
    38: [-0.323, 14.2265, 0.13059],
    39: [-0.3243, 14.414, 0.13135],
    40: [-0.3257, 14.601, 0.13213],
    41: [-0.327, 14.7873, 0.13293],
    42: [-0.3283, 14.9727, 0.13376],
    43: [-0.3296, 15.1573, 0.1346],
    44: [-0.3309, 15.341, 0.13545],
    45: [-0.3322, 15.524, 0.1363],
    46: [-0.3335, 15.7064, 0.13716],
    47: [-0.3348, 15.8882, 0.138],
    48: [-0.3361, 16.0697, 0.13884],
    49: [-0.3374, 16.2511, 0.13968],
    50: [-0.3387, 16.4322, 0.14051],
    51: [-0.34, 16.6133, 0.14132],
    52: [-0.3414, 16.7942, 0.14213],
    53: [-0.3427, 16.9748, 0.14293],
    54: [-0.344, 17.1551, 0.14371],
    55: [-0.3453, 17.3347, 0.14448],
    56: [-0.3466, 17.5136, 0.14525],
    57: [-0.3479, 17.6916, 0.146],
    58: [-0.3492, 17.8686, 0.14675],
    59: [-0.3505, 18.0445, 0.14748],
    60: [-0.3518, 18.2193, 0.14821]
};

const LHFA_BOYS = {
    0: [1, 49.8842, 0.03795],
    1: [1, 54.7244, 0.03557],
    2: [1, 58.4249, 0.03424],
    3: [1, 61.4292, 0.03328],
    4: [1, 63.886, 0.03257],
    5: [1, 65.9026, 0.03204],
    6: [1, 67.6236, 0.03165],
    7: [1, 69.1645, 0.03139],
    8: [1, 70.5994, 0.03124],
    9: [1, 71.9687, 0.03117],
    10: [1, 73.2812, 0.03118],
    11: [1, 74.5388, 0.03125],
    12: [1, 75.7488, 0.03137],
    13: [1, 76.9186, 0.03154],
    14: [1, 78.0497, 0.03174],
    15: [1, 79.1458, 0.03197],
    16: [1, 80.2113, 0.03222],
    17: [1, 81.2487, 0.0325],
    18: [1, 82.2587, 0.03279],
    19: [1, 83.2418, 0.0331],
    20: [1, 84.1996, 0.03342],
    21: [1, 85.1348, 0.03376],
    22: [1, 86.0477, 0.0341],
    23: [1, 86.941, 0.03445],
    24: [1, 87.1161, 0.03507],
    25: [1, 87.972, 0.03542],
    26: [1, 88.8065, 0.03576],
    27: [1, 89.6197, 0.0361],
    28: [1, 90.412, 0.03642],
    29: [1, 91.1828, 0.03674],
    30: [1, 91.9327, 0.03704],
    31: [1, 92.6631, 0.03733],
    32: [1, 93.3753, 0.03761],
    33: [1, 94.0711, 0.03787],
    34: [1, 94.7532, 0.03812],
    35: [1, 95.4236, 0.03836],
    36: [1, 96.0835, 0.03858],
    37: [1, 96.7337, 0.03879],
    38: [1, 97.3749, 0.039],
    39: [1, 98.0073, 0.03919],
    40: [1, 98.631, 0.03937],
    41: [1, 99.2459, 0.03954],
    42: [1, 99.8515, 0.03971],
    43: [1, 100.4485, 0.03986],
    44: [1, 101.0374, 0.04002],
    45: [1, 101.6186, 0.04016],
    46: [1, 102.1933, 0.04031],
    47: [1, 102.7625, 0.04045],
    48: [1, 103.3273, 0.04059],
    49: [1, 103.8886, 0.04073],
    50: [1, 104.4473, 0.04086],
    51: [1, 105.0041, 0.041],
    52: [1, 105.5596, 0.04113],
    53: [1, 106.1138, 0.04126],
    54: [1, 106.6668, 0.04139],
    55: [1, 107.2188, 0.04152],
    56: [1, 107.7697, 0.04165],
    57: [1, 108.3198, 0.04177],
    58: [1, 108.8689, 0.0419],
    59: [1, 109.417, 0.04202],
    60: [1, 109.9638, 0.04214]
};

const LHFA_GIRLS = {
    0: [1, 49.1477, 0.0379],
    1: [1, 53.6872, 0.0364],
    2: [1, 57.0673, 0.03568],
    3: [1, 59.8029, 0.0352],
    4: [1, 62.0899, 0.03486],
    5: [1, 64.0301, 0.03463],
    6: [1, 65.7311, 0.03448],
    7: [1, 67.2873, 0.03441],
    8: [1, 68.7498, 0.0344],
    9: [1, 70.1435, 0.03444],
    10: [1, 71.4818, 0.03452],
    11: [1, 72.771, 0.03464],
    12: [1, 74.015, 0.03479],
    13: [1, 75.2176, 0.03496],
    14: [1, 76.3817, 0.03514],
    15: [1, 77.5099, 0.03534],
    16: [1, 78.6055, 0.03555],
    17: [1, 79.671, 0.03576],
    18: [1, 80.7079, 0.03598],
    19: [1, 81.7182, 0.0362],
    20: [1, 82.7036, 0.03643],
    21: [1, 83.6654, 0.03666],
    22: [1, 84.604, 0.03688],
    23: [1, 85.5202, 0.03711],
    24: [1, 85.7153, 0.03764],
    25: [1, 86.5904, 0.03786],
    26: [1, 87.4462, 0.03808],
    27: [1, 88.283, 0.0383],
    28: [1, 89.1004, 0.03851],
    29: [1, 89.8991, 0.03872],
    30: [1, 90.6797, 0.03893],
    31: [1, 91.443, 0.03913],
    32: [1, 92.1906, 0.03933],
    33: [1, 92.9239, 0.03952],
    34: [1, 93.6444, 0.03971],
    35: [1, 94.3533, 0.03989],
    36: [1, 95.0515, 0.04006],
    37: [1, 95.7399, 0.04024],
    38: [1, 96.4187, 0.04041],
    39: [1, 97.0885, 0.04057],
    40: [1, 97.7493, 0.04073],
    41: [1, 98.4015, 0.04089],
    42: [1, 99.0448, 0.04105],
    43: [1, 99.6795, 0.0412],
    44: [1, 100.3058, 0.04135],
    45: [1, 100.9238, 0.0415],
    46: [1, 101.5337, 0.04164],
    47: [1, 102.136, 0.04179],
    48: [1, 102.7312, 0.04193],
    49: [1, 103.3197, 0.04206],
    50: [1, 103.9021, 0.0422],
    51: [1, 104.4786, 0.04233],
    52: [1, 105.0494, 0.04246],
    53: [1, 105.6148, 0.04259],
    54: [1, 106.1748, 0.04272],
    55: [1, 106.7295, 0.04285],
    56: [1, 107.2788, 0.04298],
    57: [1, 107.8227, 0.0431],
    58: [1, 108.3613, 0.04322],
    59: [1, 108.8948, 0.04334],
    60: [1, 109.4233, 0.04347]
};

const WFL_BOYS = {
    45: [-0.3521, 2.441, 0.09182],
    46: [-0.3521, 2.6077, 0.09124],
    47: [-0.3521, 2.7755, 0.09065],
    48: [-0.3521, 2.948, 0.09007],
    49: [-0.3521, 3.1308, 0.08948],
    50: [-0.3521, 3.3278, 0.0889],
    51: [-0.3521, 3.5376, 0.08831],
    52: [-0.3521, 3.762, 0.08771],
    53: [-0.3521, 4.006, 0.08711],
    54: [-0.3521, 4.2693, 0.08651],
    55: [-0.3521, 4.5467, 0.08592],
    56: [-0.3521, 4.8338, 0.08535],
    57: [-0.3521, 5.1259, 0.08481],
    58: [-0.3521, 5.418, 0.0843],
    59: [-0.3521, 5.7074, 0.08383],
    60: [-0.3521, 5.9907, 0.08342],
    61: [-0.3521, 6.2632, 0.08308],
    62: [-0.3521, 6.5251, 0.08279],
    63: [-0.3521, 6.7786, 0.08255],
    64: [-0.3521, 7.0255, 0.08236],
    65: [-0.3521, 7.4327, 0.08217],
    66: [-0.3521, 7.6673, 0.08212],
    67: [-0.3521, 7.8986, 0.08213],
    68: [-0.3521, 8.1272, 0.08217],
    69: [-0.3521, 8.3547, 0.08226],
    70: [-0.3521, 8.5808, 0.08237],
    71: [-0.3521, 8.8036, 0.0825],
    72: [-0.3521, 9.0221, 0.08264],
    73: [-0.3521, 9.2347, 0.08278],
    74: [-0.3521, 9.442, 0.08292],
    75: [-0.3521, 9.644, 0.08303],
    76: [-0.3521, 9.8392, 0.08312],
    77: [-0.3521, 10.0274, 0.08317],
    78: [-0.3521, 10.2105, 0.08317],
    79: [-0.3521, 10.3923, 0.08311],
    80: [-0.3521, 10.5781, 0.08298],
    81: [-0.3521, 10.7718, 0.08279],
    82: [-0.3521, 10.9772, 0.08255],
    83: [-0.3521, 11.1966, 0.08225],
    84: [-0.3521, 11.429, 0.08191],
    85: [-0.3521, 11.6707, 0.08156],
    86: [-0.3521, 11.9173, 0.08121],
    87: [-0.3521, 12.1645, 0.0809],
    88: [-0.3521, 12.4089, 0.08064],
    89: [-0.3521, 12.6495, 0.08045],
    90: [-0.3521, 12.8864, 0.08032],
    91: [-0.3521, 13.1209, 0.08025],
    92: [-0.3521, 13.3541, 0.08025],
    93: [-0.3521, 13.587, 0.08031],
    94: [-0.3521, 13.8217, 0.08043],
    95: [-0.3521, 14.06, 0.0806],
    96: [-0.3521, 14.3037, 0.08083],
    97: [-0.3521, 14.5547, 0.08112],
    98: [-0.3521, 14.814, 0.08146],
    99: [-0.3521, 15.0818, 0.08185],
    100: [-0.3521, 15.3576, 0.08229],
    101: [-0.3521, 15.6412, 0.08277],
    102: [-0.3521, 15.932, 0.08328],
    103: [-0.3521, 16.2298, 0.08381],
    104: [-0.3521, 16.5342, 0.08436],
    105: [-0.3521, 16.8454, 0.08493],
    106: [-0.3521, 17.1637, 0.08551],
    107: [-0.3521, 17.4894, 0.08611],
    108: [-0.3521, 17.8226, 0.08673],
    109: [-0.3521, 18.1645, 0.08736],
    110: [-0.3521, 18.5158, 0.088]
};

const WFL_GIRLS = {
    45: [-0.3833, 2.4607, 0.09029],
    46: [-0.3833, 2.6306, 0.09037],
    47: [-0.3833, 2.8007, 0.09044],
    48: [-0.3833, 2.9741, 0.09052],
    49: [-0.3833, 3.156, 0.0906],
    50: [-0.3833, 3.3518, 0.09068],
    51: [-0.3833, 3.5636, 0.09076],
    52: [-0.3833, 3.7911, 0.09085],
    53: [-0.3833, 4.0332, 0.09093],
    54: [-0.3833, 4.2875, 0.09102],
    55: [-0.3833, 4.5498, 0.0911],
    56: [-0.3833, 4.8162, 0.09118],
    57: [-0.3833, 5.0837, 0.09125],
    58: [-0.3833, 5.3507, 0.0913],
    59: [-0.3833, 5.6151, 0.09134],
    60: [-0.3833, 5.8742, 0.09136],
    61: [-0.3833, 6.127, 0.09137],
    62: [-0.3833, 6.3738, 0.09135],
    63: [-0.3833, 6.6144, 0.09131],
    64: [-0.3833, 6.8501, 0.09126],
    65: [-0.3833, 7.2402, 0.09113],
    66: [-0.3833, 7.463, 0.09104],
    67: [-0.3833, 7.6806, 0.09094],
    68: [-0.3833, 7.893, 0.09083],
    69: [-0.3833, 8.1012, 0.09071],
    70: [-0.3833, 8.3058, 0.09059],
    71: [-0.3833, 8.5078, 0.09047],
    72: [-0.3833, 8.707, 0.09035],
    73: [-0.3833, 8.9025, 0.09022],
    74: [-0.3833, 9.0928, 0.09009],
    75: [-0.3833, 9.2786, 0.08996],
    76: [-0.3833, 9.4617, 0.08983],
    77: [-0.3833, 9.6456, 0.08969],
    78: [-0.3833, 9.8338, 0.08956],
    79: [-0.3833, 10.0289, 0.08943],
    80: [-0.3833, 10.2332, 0.08932],
    81: [-0.3833, 10.4477, 0.08921],
    82: [-0.3833, 10.6719, 0.08912],
    83: [-0.3833, 10.9051, 0.08905],
    84: [-0.3833, 11.1462, 0.08899],
    85: [-0.3833, 11.3934, 0.08896],
    86: [-0.3833, 11.6444, 0.08895],
    87: [-0.3833, 11.8965, 0.08896],
    88: [-0.3833, 12.1478, 0.08899],
    89: [-0.3833, 12.3976, 0.08904],
    90: [-0.3833, 12.6461, 0.08911],
    91: [-0.3833, 12.8939, 0.0892],
    92: [-0.3833, 13.1415, 0.08931],
    93: [-0.3833, 13.3896, 0.08944],
    94: [-0.3833, 13.6393, 0.08959],
    95: [-0.3833, 13.8914, 0.08975],
    96: [-0.3833, 14.1466, 0.08994],
    97: [-0.3833, 14.4059, 0.09015],
    98: [-0.3833, 14.671, 0.09037],
    99: [-0.3833, 14.9434, 0.09062],
    100: [-0.3833, 15.2246, 0.09088],
    101: [-0.3833, 15.5154, 0.09116],
    102: [-0.3833, 15.8164, 0.09146],
    103: [-0.3833, 16.1276, 0.09177],
    104: [-0.3833, 16.4488, 0.09209],
    105: [-0.3833, 16.78, 0.09243],
    106: [-0.3833, 17.122, 0.09278],
    107: [-0.3833, 17.4755, 0.09315],
    108: [-0.3833, 17.8407, 0.09352],
    109: [-0.3833, 18.2174, 0.0939],
    110: [-0.3833, 18.6043, 0.09428]
};

const BFA_BOYS = {
    0: [-0.3053, 13.4069, 0.0956],
    1: [0.2708, 14.9441, 0.09027],
    2: [0.1118, 16.3195, 0.08677],
    3: [0.0068, 16.8987, 0.08495],
    4: [-0.0727, 17.1579, 0.08378],
    5: [-0.137, 17.2919, 0.08296],
    6: [-0.1913, 17.3422, 0.08234],
    7: [-0.2385, 17.3288, 0.08183],
    8: [-0.2802, 17.2647, 0.0814],
    9: [-0.3176, 17.1662, 0.08102],
    10: [-0.3516, 17.0488, 0.08068],
    11: [-0.3828, 16.9239, 0.08037],
    12: [-0.4115, 16.7981, 0.08009],
    13: [-0.4382, 16.6743, 0.07982],
    14: [-0.463, 16.5548, 0.07958],
    15: [-0.4863, 16.4409, 0.07935],
    16: [-0.5082, 16.3335, 0.07913],
    17: [-0.5289, 16.2329, 0.07892],
    18: [-0.5484, 16.1392, 0.07873],
    19: [-0.5669, 16.0528, 0.07854],
    20: [-0.5846, 15.9743, 0.07836],
    21: [-0.6014, 15.9039, 0.07818],
    22: [-0.6174, 15.8412, 0.07802],
    23: [-0.6328, 15.7852, 0.07786],
    24: [-0.6187, 16.0189, 0.07785],
    25: [-0.584, 15.98, 0.07792],
    26: [-0.5497, 15.9414, 0.078],
    27: [-0.5166, 15.9036, 0.07808],
    28: [-0.485, 15.8667, 0.07818],
    29: [-0.4552, 15.8306, 0.07829],
    30: [-0.4274, 15.7953, 0.07841],
    31: [-0.4016, 15.7606, 0.07854],
    32: [-0.3782, 15.7267, 0.07867],
    33: [-0.3572, 15.6934, 0.07882],
    34: [-0.3388, 15.661, 0.07897],
    35: [-0.3231, 15.6294, 0.07914],
    36: [-0.3101, 15.5988, 0.07931],
    37: [-0.3, 15.5693, 0.0795],
    38: [-0.2927, 15.541, 0.07969],
    39: [-0.2884, 15.514, 0.0799],
    40: [-0.2869, 15.4885, 0.08012],
    41: [-0.2881, 15.4645, 0.08036],
    42: [-0.2919, 15.442, 0.08061],
    43: [-0.2981, 15.421, 0.08087],
    44: [-0.3067, 15.4013, 0.08115],
    45: [-0.3174, 15.3827, 0.08144],
    46: [-0.3303, 15.3652, 0.08174],
    47: [-0.3452, 15.3485, 0.08205],
    48: [-0.3622, 15.3326, 0.08238],
    49: [-0.3811, 15.3174, 0.08272],
    50: [-0.4019, 15.3029, 0.08307],
    51: [-0.4245, 15.2891, 0.08343],
    52: [-0.4488, 15.2759, 0.0838],
    53: [-0.4747, 15.2633, 0.08418],
    54: [-0.5019, 15.2514, 0.08457],
    55: [-0.5303, 15.24, 0.08496],
    56: [-0.5599, 15.2291, 0.08536],
    57: [-0.5905, 15.2188, 0.08577],
    58: [-0.6223, 15.2091, 0.08617],
    59: [-0.6552, 15.2, 0.08659],
    60: [-0.6892, 15.1916, 0.087]
};

const BFA_GIRLS = {
    0: [-0.0631, 13.3363, 0.09272],
    1: [0.3448, 14.5679, 0.09556],
    2: [0.1749, 15.7679, 0.09371],
    3: [0.0643, 16.3574, 0.09254],
    4: [-0.0191, 16.6703, 0.09166],
    5: [-0.0864, 16.8386, 0.09096],
    6: [-0.1429, 16.9083, 0.09036],
    7: [-0.1916, 16.902, 0.08984],
    8: [-0.2344, 16.8404, 0.08939],
    9: [-0.2725, 16.7406, 0.08898],
    10: [-0.3068, 16.6184, 0.08861],
    11: [-0.3381, 16.4875, 0.08828],
    12: [-0.3667, 16.3568, 0.08797],
    13: [-0.3932, 16.2311, 0.08768],
    14: [-0.4177, 16.1128, 0.08741],
    15: [-0.4407, 16.0028, 0.08716],
    16: [-0.4623, 15.9017, 0.08693],
    17: [-0.4825, 15.8096, 0.08671],
    18: [-0.5017, 15.7263, 0.0865],
    19: [-0.5199, 15.6517, 0.0863],
    20: [-0.5372, 15.5855, 0.08612],
    21: [-0.5537, 15.5278, 0.08594],
    22: [-0.5695, 15.4787, 0.08577],
    23: [-0.5846, 15.438, 0.0856],
    24: [-0.5684, 15.6881, 0.08454],
    25: [-0.5684, 15.659, 0.08452],
    26: [-0.5684, 15.6308, 0.08449],
    27: [-0.5684, 15.6037, 0.08446],
    28: [-0.5684, 15.5777, 0.08444],
    29: [-0.5684, 15.5523, 0.08443],
    30: [-0.5684, 15.5276, 0.08444],
    31: [-0.5684, 15.5034, 0.08448],
    32: [-0.5684, 15.4798, 0.08455],
    33: [-0.5684, 15.4572, 0.08467],
    34: [-0.5684, 15.4356, 0.08484],
    35: [-0.5684, 15.4155, 0.08506],
    36: [-0.5684, 15.3968, 0.08535],
    37: [-0.5684, 15.3796, 0.08569],
    38: [-0.5684, 15.3638, 0.08609],
    39: [-0.5684, 15.3493, 0.08654],
    40: [-0.5684, 15.3358, 0.08704],
    41: [-0.5684, 15.3233, 0.08757],
    42: [-0.5684, 15.3116, 0.08813],
    43: [-0.5684, 15.3007, 0.08872],
    44: [-0.5684, 15.2905, 0.08931],
    45: [-0.5684, 15.2814, 0.08991],
    46: [-0.5684, 15.2732, 0.09051],
    47: [-0.5684, 15.2661, 0.0911],
    48: [-0.5684, 15.2602, 0.09168],
    49: [-0.5684, 15.2556, 0.09227],
    50: [-0.5684, 15.2523, 0.09286],
    51: [-0.5684, 15.2503, 0.09345],
    52: [-0.5684, 15.2496, 0.09403],
    53: [-0.5684, 15.2502, 0.0946],
    54: [-0.5684, 15.2519, 0.09515],
    55: [-0.5684, 15.2544, 0.09568],
    56: [-0.5684, 15.2575, 0.09618],
    57: [-0.5684, 15.2612, 0.09665],
    58: [-0.5684, 15.2653, 0.09709],
    59: [-0.5684, 15.2698, 0.0975],
    60: [-0.5684, 15.2747, 0.09789]
};

function hitungUsiaBulanWHO(tglLahir) {
    if (!tglLahir) return 0;
    const birth = new Date(tglLahir);
    const today = new Date();
    let years = today.getFullYear() - birth.getFullYear();
    let months = today.getMonth() - birth.getMonth();
    let days = today.getDate() - birth.getDate();
    if (days < 0) {
        months--;
        const prevMonth = new Date(today.getFullYear(), today.getMonth(), 0);
        days += prevMonth.getDate();
    }
    if (months < 0) {
        years--;
        months += 12;
    }
    let usia = years * 12 + months;
    if (days > 15 && usia < 24) {
        usia++;
    }
    usia = Math.min(Math.max(usia, 0), 60);
    return usia;
}

function formatUsia(usiaBulan) {
    if (usiaBulan < 1) return '0 bulan';
    if (usiaBulan < 12) return Math.floor(usiaBulan) + ' bulan';
    const th = Math.floor(usiaBulan / 12);
    const bl = usiaBulan % 12;
    if (bl === 0) return th + ' tahun';
    return th + ' tahun ' + bl + ' bulan';
}

function getLMS(data, key) {
    const keys = Object.keys(data).map(Number).sort((a, b) => a - b);
    if (key < keys[0]) key = keys[0];
    if (key > keys[keys.length - 1]) key = keys[keys.length - 1];
    if (data[key]) return data[key];

    let prev = null,
        next = null;
    for (const k of keys) {
        if (k <= key) prev = k;
        if (k >= key && next === null) next = k;
    }
    if (prev === null) return data[next];
    if (next === null) return data[prev];
    if (prev === next) return data[prev];
    const t = (key - prev) / (next - prev);
    const d1 = data[prev],
        d2 = data[next];
    return [
        d1[0] + (d2[0] - d1[0]) * t,
        d1[1] + (d2[1] - d1[1]) * t,
        d1[2] + (d2[2] - d1[2]) * t
    ];
}

function calculateZScore(measurement, L, M, S) {
    if (measurement <= 0 || M <= 0) return null;
    if (L === 0) {
        return Math.log(measurement / M) / S;
    } else {
        return (Math.pow(measurement / M, L) - 1) / (S * L);
    }
}

function classifyStatus(zscore, indicator) {
    if (zscore === null || zscore === undefined || isNaN(zscore)) {
        return { status: 'Tidak dapat dihitung', kode: 'ERROR', warna: 'gray' };
    }
    switch (indicator) {
        case 'BB/U':
            if (zscore < -3) return { status: 'Gizi Buruk', kode: 'SEVERE_UNDERWEIGHT', warna: 'red' };
            if (zscore < -2) return { status: 'Gizi Kurang', kode: 'UNDERWEIGHT', warna: 'orange' };
            if (zscore <= 1) return { status: 'Gizi Baik', kode: 'NORMAL', warna: 'green' };
            if (zscore <= 2) return { status: 'Beresiko Gizi Lebih', kode: 'RISK_OVERWEIGHT', warna: 'yellow' };
            if (zscore <= 3) return { status: 'Gizi Lebih', kode: 'OVERWEIGHT', warna: 'purple' };
            return { status: 'Obesitas', kode: 'OBESE', warna: 'dark-red' };
        case 'TB/U':
            if (zscore < -3) return { status: 'Sangat Pendek', kode: 'SEVERE_STUNTING', warna: 'red' };
            if (zscore < -2) return { status: 'Pendek', kode: 'STUNTING', warna: 'orange' };
            if (zscore <= 2) return { status: 'Normal', kode: 'NORMAL', warna: 'green' };
            if (zscore <= 3) return { status: 'Tinggi', kode: 'TALL', warna: 'blue' };
            return { status: 'Sangat Tinggi', kode: 'VERY_TALL', warna: 'dark-blue' };
        case 'BB/TB':
            if (zscore < -3) return { status: 'Gizi Buruk', kode: 'SEVERE_WASTING', warna: 'red' };
            if (zscore < -2) return { status: 'Gizi Kurang', kode: 'WASTING', warna: 'orange' };
            if (zscore <= 2) return { status: 'Gizi Baik', kode: 'NORMAL', warna: 'green' };
            if (zscore <= 3) return { status: 'Beresiko Gizi Lebih', kode: 'RISK_OVERWEIGHT', warna: 'yellow' };
            return { status: 'Gizi Lebih', kode: 'OVERWEIGHT', warna: 'purple' };
        case 'IMT/U':
            if (zscore < -3) return { status: 'Sangat Kurus', kode: 'SEVERE_THINNESS', warna: 'red' };
            if (zscore < -2) return { status: 'Kurus', kode: 'THINNESS', warna: 'orange' };
            if (zscore <= 1) return { status: 'Normal', kode: 'NORMAL', warna: 'green' };
            if (zscore <= 2) return { status: 'Beresiko Gemuk', kode: 'RISK_OVERWEIGHT', warna: 'yellow' };
            if (zscore <= 3) return { status: 'Gemuk', kode: 'OVERWEIGHT', warna: 'purple' };
            return { status: 'Obesitas', kode: 'OBESE', warna: 'dark-red' };
        default:
            return { status: 'Tidak diketahui', kode: 'UNKNOWN', warna: 'gray' };
    }
}

function calculateBMI(bb, tb) {
    if (tb <= 0) return 0;
    const meter = tb / 100;
    return bb / (meter * meter);
}

function zscoreToPercentile(z) {
    if (z === null || isNaN(z)) return null;
    const za = Math.abs(z);
    const p = Math.exp(-0.717 * za - 0.416 * za * za);
    let percentile = (z >= 0) ? 100 * (1 - p) : 100 * p;
    percentile = Math.max(0.1, Math.min(99.9, percentile));
    return Math.round(percentile * 10) / 10;
}

function getStatusColorClass(klas) {
    const map = {
        'red': 'status-red',
        'orange': 'status-orange',
        'green': 'status-green',
        'yellow': 'status-yellow',
        'purple': 'status-purple',
        'blue': 'status-blue',
        'dark-red': 'status-red',
        'dark-blue': 'status-blue',
        'gray': 'status-gray'
    };
    return map[klas.warna] || 'status-gray';
}

function getStatusIcon(klas) {
    const map = {
        'SEVERE_UNDERWEIGHT': 'fa-exclamation-triangle',
        'UNDERWEIGHT': 'fa-exclamation-circle',
        'NORMAL': 'fa-check-circle',
        'RISK_OVERWEIGHT': 'fa-info-circle',
        'OVERWEIGHT': 'fa-weight',
        'OBESE': 'fa-weight',
        'SEVERE_STUNTING': 'fa-exclamation-triangle',
        'STUNTING': 'fa-exclamation-circle',
        'TALL': 'fa-arrow-up',
        'VERY_TALL': 'fa-arrow-up',
        'SEVERE_WASTING': 'fa-exclamation-triangle',
        'WASTING': 'fa-exclamation-circle',
        'SEVERE_THINNESS': 'fa-exclamation-triangle',
        'THINNESS': 'fa-exclamation-circle',
        'ERROR': 'fa-question-circle',
        'UNKNOWN': 'fa-question-circle'
    };
    return map[klas.kode] || 'fa-question-circle';
}

function runCalculator(e) {
    e.preventDefault();

    const tglLahir = document.getElementById('tglLahir').value;
    const kelamin = document.getElementById('jenisKelamin').value;
    const bb = parseFloat(document.getElementById('beratBadan').value);
    const tb = parseFloat(document.getElementById('panjangBadan').value);

    if (!tglLahir || !kelamin || isNaN(bb) || isNaN(tb) || bb <= 0 || tb <= 0) {
        showToast('Mohon lengkapi semua data dengan benar!', 'warning');
        return;
    }
    if (bb < 0.5 || bb > 35) {
        showToast('Berat badan harus antara 0.5 – 35 kg', 'warning');
        return;
    }
    if (tb < 30 || tb > 120) {
        showToast('Panjang badan harus antara 30 – 120 cm', 'warning');
        return;
    }

    const usia = hitungUsiaBulanWHO(tglLahir);
    if (usia < 0 || usia > 60) {
        showToast('Usia harus 0–60 bulan (0–5 tahun)', 'warning');
        return;
    }

    const isBoy = (kelamin === 'L');
    const genderLabel = isBoy ? 'Laki-laki' : 'Perempuan';

    const wfaData = isBoy ? WFA_BOYS : WFA_GIRLS;
    const lhfaData = isBoy ? LHFA_BOYS : LHFA_GIRLS;
    const wflData = isBoy ? WFL_BOYS : WFL_GIRLS;
    const bfaData = isBoy ? BFA_BOYS : BFA_GIRLS;

    // 1. BB/U
    let lmsBBU = getLMS(wfaData, Math.round(usia));
    let zBBU = calculateZScore(bb, lmsBBU[0], lmsBBU[1], lmsBBU[2]);
    let klasBBU = classifyStatus(zBBU, 'BB/U');

    // 2. TB/U
    let lmsTBU = getLMS(lhfaData, Math.round(usia));
    let zTBU = calculateZScore(tb, lmsTBU[0], lmsTBU[1], lmsTBU[2]);
    let klasTBU = classifyStatus(zTBU, 'TB/U');

    // 3. BB/TB
    let tbRounded = Math.round(tb);
    if (tbRounded < 45) tbRounded = 45;
    if (tbRounded > 110) tbRounded = 110;
    let lmsBBTB = getLMS(wflData, tbRounded);
    let zBBTB = calculateZScore(bb, lmsBBTB[0], lmsBBTB[1], lmsBBTB[2]);
    let klasBBTB = classifyStatus(zBBTB, 'BB/TB');

    // 4. IMT/U
    let bmi = calculateBMI(bb, tb);
    let lmsBMI = getLMS(bfaData, Math.round(usia));
    let zBMI = calculateZScore(bmi, lmsBMI[0], lmsBMI[1], lmsBMI[2]);
    let klasBMI = classifyStatus(zBMI, 'IMT/U');

    // Diagnosis flags
    const stunting = (zTBU !== null && zTBU < -2);
    const severeStunting = (zTBU !== null && zTBU < -3);
    const wasting = (zBBTB !== null && zBBTB < -2);
    const severeWasting = (zBBTB !== null && zBBTB < -3);
    const underweight = (zBBU !== null && zBBU < -2);
    const overweight = (zBBU !== null && zBBU > 2) || (zBMI !== null && zBMI > 2);
    const obese = (zBBU !== null && zBBU > 3) || (zBMI !== null && zBMI > 3);
    const komposit = wasting && stunting && underweight;

    // === TAMPILKAN HASIL ===

    // Summary
    document.getElementById('sUsia').textContent = formatUsia(usia);
    document.getElementById('sBB').textContent = bb.toFixed(1) + ' kg';
    document.getElementById('sTB').textContent = tb.toFixed(1) + ' cm';
    document.getElementById('sIMT').textContent = bmi.toFixed(1) + ' kg/m²';
    document.getElementById('sKelamin').textContent = genderLabel;

    // Indicator cards
    const indicators = [
        { label: 'BB/U', z: zBBU, klas: klasBBU, icon: 'fa-weight' },
        { label: 'TB/U', z: zTBU, klas: klasTBU, icon: 'fa-ruler-vertical' },
        { label: 'BB/TB', z: zBBTB, klas: klasBBTB, icon: 'fa-weight-scale' },
        { label: 'IMT/U', z: zBMI, klas: klasBMI, icon: 'fa-circle' }
    ];

    const grid = document.getElementById('indicatorGrid');
    grid.innerHTML = '';
    for (const ind of indicators) {
        const colorClass = getStatusColorClass(ind.klas);
        const icon = getStatusIcon(ind.klas);
        const pct = zscoreToPercentile(ind.z);
        const zStr = (ind.z !== null && !isNaN(ind.z)) ? ind.z.toFixed(2) : '—';
        const pctStr = (pct !== null) ? pct + '%' : '—';

        const card = document.createElement('div');
        card.className = 'indicator-card-enhanced ' + colorClass;
        card.setAttribute('data-aos', 'fade-up');
        card.setAttribute('data-aos-delay', '100');
        card.innerHTML = `
            <div class="head">
              <span class="name"><i class="fas ${ind.icon}" style="margin-right:6px;"></i>${ind.label}</span>
              <span class="icon"><i class="fas ${icon}"></i></span>
            </div>
            <div class="status-text">${ind.klas.status}</div>
            <div class="meta">
              <span>Z-Score: ${zStr}</span>
              <span>Persentil: ${pctStr}</span>
            </div>
          `;
        grid.appendChild(card);
    }

    AOS.refresh();

    // Z-Score bar
    const zValues = [zBBU, zTBU, zBBTB, zBMI].filter(v => v !== null && !isNaN(v));
    let avgZ = 0;
    if (zValues.length > 0) {
        avgZ = zValues.reduce((a, b) => a + b, 0) / zValues.length;
    }
    let barPercent = (avgZ + 4) / 8 * 100;
    barPercent = Math.max(0, Math.min(100, barPercent));
    const barFill = document.getElementById('zscoreBarFill');
    barFill.style.width = barPercent + '%';
    if (avgZ < -2) barFill.style.background = 'linear-gradient(90deg, #dc2626, #ef4444)';
    else if (avgZ < 0) barFill.style.background = 'linear-gradient(90deg, #ea580c, #f97316)';
    else if (avgZ <= 2) barFill.style.background = 'linear-gradient(90deg, #16a34a, #22c55e)';
    else if (avgZ <= 3) barFill.style.background = 'linear-gradient(90deg, #ca8a04, #eab308)';
    else barFill.style.background = 'linear-gradient(90deg, #9333ea, #a855f7)';

    // Diagnosis
    const diagContainer = document.getElementById('diagnosisContainer');
    let diagHTML = '';

    if (komposit) {
        diagHTML = `
            <div class="diagnosis-box-enhanced diagnosis-critical" data-aos="fade-up" data-aos-delay="200">
              <div class="icon-box"><i class="fas fa-exclamation-triangle"></i></div>
              <div class="content">
                <div class="title">⚠️ DIAGNOSIS: GIZI BURUK KOMPOSIT</div>
                <div class="desc"><strong>Kriteria:</strong> Stunting + Wasting + Underweight bersamaan.<br>
                <strong>Status:</strong> Gawat – rujuk segera ke fasilitas kesehatan tingkat lanjut.<br>
                <strong>Rekomendasi:</strong> RUJUK SEGERA untuk stabilisasi dan terapi gizi medis.</div>
              </div>
            </div>
          `;
    } else if (severeStunting && severeWasting) {
        diagHTML = `
            <div class="diagnosis-box-enhanced diagnosis-warning" data-aos="fade-up" data-aos-delay="200">
              <div class="icon-box"><i class="fas fa-exclamation-circle"></i></div>
              <div class="content">
                <div class="title">⚠️ DIAGNOSIS: STUNTING &amp; WASTING BERAT</div>
                <div class="desc"><strong>Kriteria:</strong> TB/U &lt; -3SD dan BB/TB &lt; -3SD.<br>
                <strong>Status:</strong> Berat – perlu monitoring intensif dan intervensi gizi khusus.<br>
                <strong>Rekomendasi:</strong> Konsultasi spesialis gizi anak, follow-up mingguan.</div>
              </div>
            </div>
          `;
    } else if (severeStunting) {
        diagHTML = `
            <div class="diagnosis-box-enhanced diagnosis-warning" data-aos="fade-up" data-aos-delay="200">
              <div class="icon-box"><i class="fas fa-ruler-vertical"></i></div>
              <div class="content">
                <div class="title">📏 DIAGNOSIS: STUNTING BERAT</div>
                <div class="desc"><strong>Kriteria:</strong> TB/U &lt; -3SD.<br>
                <strong>Status:</strong> Berat – berdampak pada perkembangan kognitif.<br>
                <strong>Rekomendasi:</strong> Intervensi gizi spesifik dan stimulasi perkembangan.</div>
              </div>
            </div>
          `;
    } else if (severeWasting) {
        diagHTML = `
            <div class="diagnosis-box-enhanced diagnosis-warning" data-aos="fade-up" data-aos-delay="200">
              <div class="icon-box"><i class="fas fa-weight"></i></div>
              <div class="content">
                <div class="title">⚖️ DIAGNOSIS: WASTING BERAT</div>
                <div class="desc"><strong>Kriteria:</strong> BB/TB &lt; -3SD.<br>
                <strong>Status:</strong> Berat – risiko tinggi morbiditas dan mortalitas.<br>
                <strong>Rekomendasi:</strong> Terapi gizi khusus dan pemantauan ketat.</div>
              </div>
            </div>
          `;
    } else if (stunting || wasting || underweight) {
        const kondisi = [];
        if (stunting) kondisi.push('Stunting');
        if (wasting) kondisi.push('Wasting');
        if (underweight) kondisi.push('Underweight');
        diagHTML = `
            <div class="diagnosis-box-enhanced diagnosis-info" data-aos="fade-up" data-aos-delay="200">
              <div class="icon-box"><i class="fas fa-info-circle"></i></div>
              <div class="content">
                <div class="title">📋 DIAGNOSIS: MASALAH GIZI</div>
                <div class="desc"><strong>Kondisi:</strong> ${kondisi.join(', ')}.<br>
                <strong>Status:</strong> Perlu intervensi gizi dan monitoring.<br>
                <strong>Rekomendasi:</strong> Konseling gizi dan follow-up 2 minggu sekali.</div>
              </div>
            </div>
          `;
    } else if (overweight || obese) {
        diagHTML = `
            <div class="diagnosis-box-enhanced diagnosis-warning" data-aos="fade-up" data-aos-delay="200">
              <div class="icon-box"><i class="fas fa-weight"></i></div>
              <div class="content">
                <div class="title">⚖️ DIAGNOSIS: ${obese ? 'OBESITAS' : 'KELEBIHAN BERAT BADAN'}</div>
                <div class="desc"><strong>Kriteria:</strong> ${obese ? 'Z &gt; +3SD' : 'Z &gt; +2SD'}.<br>
                <strong>Status:</strong> Risiko penyakit degeneratif dini.<br>
                <strong>Rekomendasi:</strong> Konseling gizi seimbang dan aktivitas fisik.</div>
              </div>
            </div>
          `;
    } else {
        diagHTML = `
            <div class="diagnosis-box-enhanced diagnosis-success" data-aos="fade-up" data-aos-delay="200">
              <div class="icon-box"><i class="fas fa-check-circle"></i></div>
              <div class="content">
                <div class="title">✅ DIAGNOSIS: STATUS GIZI BAIK</div>
                <div class="desc"><strong>Evaluasi:</strong> Semua parameter dalam batas normal.<br>
                <strong>Status:</strong> Optimal – pertumbuhan sesuai usia.<br>
                <strong>Rekomendasi:</strong> Lanjutkan pola asuh yang baik, monitoring rutin.</div>
              </div>
            </div>
          `;
    }
    diagContainer.innerHTML = diagHTML;
    AOS.refresh();

    document.getElementById('resultSection').classList.remove('hidden');
    document.getElementById('resultSection').scrollIntoView({ behavior: 'smooth', block: 'start' });

    window._lastResult = {
        usia,
        usiaFormatted: formatUsia(usia),
        bb,
        tb,
        bmi,
        kelamin: genderLabel,
        zBBU,
        zTBU,
        zBBTB,
        zBMI,
        klasBBU,
        klasTBU,
        klasBBTB,
        klasBMI,
        stunting,
        wasting,
        underweight,
        overweight,
        obese,
        komposit,
        severeStunting,
        severeWasting
    };

    showToast('✅ Perhitungan selesai!', 'success');
}

function copyToClipboard() {
    const r = window._lastResult;
    if (!r) {
        showToast('Belum ada hasil perhitungan.', 'warning');
        return;
    }
    const text =
        `📊 STATUS GIZI ANAK (WHO)\n──────────────────────────────\nUsia     : ${r.usiaFormatted} (${r.usia} bln)\nJenis    : ${r.kelamin}\nBB       : ${r.bb.toFixed(1)} kg\nTB       : ${r.tb.toFixed(1)} cm\nIMT      : ${r.bmi.toFixed(1)} kg/m²\n\n─── HASIL Z-SCORE ───\nBB/U  : ${r.klasBBU.status}  (Z=${r.zBBU !== null ? r.zBBU.toFixed(2) : '—'})\nTB/U  : ${r.klasTBU.status}  (Z=${r.zTBU !== null ? r.zTBU.toFixed(2) : '—'})\nBB/TB : ${r.klasBBTB.status} (Z=${r.zBBTB !== null ? r.zBBTB.toFixed(2) : '—'})\nIMT/U : ${r.klasBMI.status}  (Z=${r.zBMI !== null ? r.zBMI.toFixed(2) : '—'})\n\n─── DIAGNOSIS ───\n${r.komposit ? '⚡ GIZI BURUK KOMPOSIT – RUJUK SEGERA' :
            r.severeStunting ? '📏 STUNTING BERAT' :
                r.severeWasting ? '⚖️ WASTING BERAT' :
                    (r.stunting || r.wasting || r.underweight) ? '⚠️ MASALAH GIZI' :
                        (r.overweight || r.obese) ? '⚖️ KELEBIHAN BERAT' :
                            '✅ STATUS GIZI BAIK'}\n\nRekomendasi: ${r.komposit ? 'Rujuk ke rumah sakit' :
                                (r.stunting || r.wasting) ? 'Intervensi gizi khusus' :
                                    (r.overweight || r.obese) ? 'Konseling gizi & aktivitas fisik' :
                                        'Monitoring rutin'}\n──────────────────────────────\n*Dihitung dengan standar WHO Growth (LMS)`;

    navigator.clipboard.writeText(text)
        .then(() => showToast('📋 Data berhasil disalin ke clipboard!', 'success'))
        .catch(() => {
            const ta = document.createElement('textarea');
            ta.value = text;
            document.body.appendChild(ta);
            ta.select();
            document.execCommand('copy');
            ta.remove();
            showToast('📋 Data berhasil disalin!', 'success');
        });
}

function resetForm() {
    document.getElementById('calcForm').reset();
    document.getElementById('resultSection').classList.add('hidden');
    window._lastResult = null;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

let toastTimer = null;

function showToast(msg, type) {
    const toast = document.getElementById('toast');
    const toastMsg = document.getElementById('toastMessage');
    toastMsg.textContent = msg;
    toast.className = 'toast-enhanced';
    if (type === 'warning') {
        toast.classList.add('warning');
    } else {
        toast.classList.remove('warning');
    }
    clearTimeout(toastTimer);
    toast.classList.add('show');
    toastTimer = setTimeout(() => {
        toast.classList.remove('show');
    }, 4000);
}