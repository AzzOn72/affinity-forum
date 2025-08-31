<?php
/**
 * Affinity Cheat Status API
 * Real-time status updates for the CS2 cheat
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Simulate real cheat status data
$status = [
    'online' => true,
    'version' => 'v2.4.1',
    'last_update' => date('Y-m-d H:i:s', strtotime('-2 hours')),
    'detection_status' => 'UNDETECTED',
    'active_users' => rand(1200, 2500),
    'server_load' => rand(10, 30),
    'features' => [
        'aimbot' => [
            'status' => 'online',
            'accuracy' => rand(90, 98) . '%',
            'settings' => [
                'smoothness' => rand(15, 35),
                'fov' => rand(60, 120),
                'bones' => ['head', 'chest', 'stomach']
            ]
        ],
        'esp' => [
            'status' => 'online',
            'range' => rand(500, 1000) . 'm',
            'features' => ['players', 'weapons', 'items', 'grenades']
        ],
        'triggerbot' => [
            'status' => 'online',
            'delay' => rand(10, 50) . 'ms',
            'hitchance' => rand(85, 95) . '%'
        ],
        'anti_detection' => [
            'status' => 'active',
            'protection_level' => 'maximum',
            'last_scan' => date('Y-m-d H:i:s', strtotime('-5 minutes'))
        ]
    ],
    'safety' => [
        'vac_status' => 'undetected',
        'faceit_status' => 'undetected',
        'esea_status' => 'undetected',
        'total_bans' => 0,
        'days_undetected' => 732
    ],
    'community' => [
        'online_now' => rand(800, 1500),
        'matches_won_today' => rand(15000, 25000),
        'headshots_today' => rand(50000, 100000),
        'satisfaction_rate' => '99.7%'
    ],
    'updates' => [
        [
            'version' => 'v2.4.1',
            'date' => date('Y-m-d H:i:s', strtotime('-2 hours')),
            'changes' => [
                'Improved aimbot smoothness',
                'Enhanced ESP rendering',
                'Fixed minor bugs',
                'Updated anti-detection'
            ]
        ],
        [
            'version' => 'v2.4.0',
            'date' => date('Y-m-d H:i:s', strtotime('-1 day')),
            'changes' => [
                'Major performance improvements',
                'New triggerbot features',
                'UI enhancements',
                'Security updates'
            ]
        ]
    ]
];

// Add timestamp
$status['timestamp'] = time();
$status['response_time'] = rand(8, 25) . 'ms';

echo json_encode($status, JSON_PRETTY_PRINT);
?>