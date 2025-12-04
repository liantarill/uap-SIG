<?php
require_once 'config.php';

header('Content-Type: application/json');
header('Content-Disposition: attachment; filename="export_markers_' . date('Y-m-d_H-i-s') . '.geojson"');

try {
    $conn = getConnection();

    // Get all markers from database
    $stmt = $conn->prepare("SELECT * FROM markers ORDER BY id");
    $stmt->execute();
    $markers = $stmt->fetchAll();

    // Build GeoJSON structure
    $geojson = [
        'type' => 'FeatureCollection',
        'features' => []
    ];

    foreach ($markers as $marker) {
        $feature = [
            'type' => 'Feature',
            'geometry' => [
                'type' => 'Point',
                'coordinates' => [
                    floatval($marker['longitude']),
                    floatval($marker['latitude'])
                ]
            ],
            'properties' => [
                'id' => $marker['id'],
                'NAMOBJ' => $marker['name'],
                'nama' => $marker['name'],
                'REMARK' => $marker['type'],
                'jenis' => $marker['type'],
                'keterangan' => $marker['description'],
                'description' => $marker['description'],
                'created_at' => $marker['created_at']
            ]
        ];

        $geojson['features'][] = $feature;
    }

    // Output GeoJSON
    echo json_encode($geojson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    // Save to file if requested
    if (isset($_GET['save'])) {
        if (!file_exists('data')) {
            mkdir('data', 0755, true);
        }
        $filename = 'data/export_' . date('Y-m-d_H-i-s') . '.geojson';
        file_put_contents($filename, json_encode($geojson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
} catch (Exception $e) {
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}

$conn = null;
