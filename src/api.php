<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Database configuration
$host = 'localhost';
$dbname = 'webgis_db';
$username = 'root';
$password = '';

// Create connection
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Connection failed: ' . $e->getMessage()
    ]);
    exit;
}

// Get action from request
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_markers':
        getMarkers($conn);
        break;

    case 'add_marker':
        addMarker($conn);
        break;

    case 'delete_marker':
        deleteMarker($conn);
        break;

    case 'save_geojson':
        saveGeoJSON($conn);
        break;

    case 'get_geojson':
        getGeoJSON($conn);
        break;

    case 'get_regions':
        getRegions($conn);
        break;

    case 'delete_region':
        deleteRegion($conn);
        break;

    default:
        echo json_encode([
            'success' => false,
            'message' => 'Invalid action'
        ]);
        break;
}

// Get all markers
function getMarkers($conn)
{
    try {
        $stmt = $conn->prepare("SELECT * FROM markers ORDER BY created_at DESC");
        $stmt->execute();
        $markers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'markers' => $markers
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

// Add new marker
function addMarker($conn)
{
    try {
        $data = json_decode(file_get_contents('php://input'), true);

        $stmt = $conn->prepare("INSERT INTO markers (name, type, latitude, longitude, description, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([
            $data['name'],
            $data['type'],
            $data['latitude'],
            $data['longitude'],
            $data['description'] ?? ''
        ]);

        $id = $conn->lastInsertId();

        echo json_encode([
            'success' => true,
            'message' => 'Marker added successfully',
            'id' => $id
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

// Delete marker
function deleteMarker($conn)
{
    try {
        $data = json_decode(file_get_contents('php://input'), true);

        $stmt = $conn->prepare("DELETE FROM markers WHERE id = ?");
        $stmt->execute([$data['id']]);

        echo json_encode([
            'success' => true,
            'message' => 'Marker deleted successfully'
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

// Map REMARK value to our type system
function mapRemarkToType($remark)
{
    if (empty($remark)) {
        return 'lainnya';
    }

    $remark = strtolower(trim($remark));

    // Mapping dictionary
    $typeMap = [
        // Sekolah
        'sekolah' => 'sekolah',
        'sd' => 'sekolah',
        'smp' => 'sekolah',
        'sma' => 'sekolah',
        'smk' => 'sekolah',
        'tk' => 'sekolah',
        'paud' => 'sekolah',
        'universitas' => 'sekolah',
        'kampus' => 'sekolah',
        'perguruan tinggi' => 'sekolah',
        'madrasah' => 'sekolah',
        'pesantren' => 'sekolah',

        // Rumah Sakit
        'rumah sakit' => 'rumah_sakit',
        'rs' => 'rumah_sakit',
        'puskesmas' => 'rumah_sakit',
        'klinik' => 'rumah_sakit',
        'poliklinik' => 'rumah_sakit',
        'apotek' => 'rumah_sakit',
        'posyandu' => 'rumah_sakit',

        // Kantor Pemerintahan
        'kantor' => 'kantor',
        'pemerintah' => 'kantor',
        'balai desa' => 'kantor',
        'kelurahan' => 'kantor',
        'kecamatan' => 'kantor',
        'dinas' => 'kantor',
        'instansi' => 'kantor',
        'balai' => 'kantor',
        'bpbd' => 'kantor',

        // Tempat Ibadah
        'masjid' => 'tempat_ibadah',
        'musholla' => 'tempat_ibadah',
        'mushola' => 'tempat_ibadah',
        'surau' => 'tempat_ibadah',
        'langgar' => 'tempat_ibadah',
        'gereja' => 'tempat_ibadah',
        'pura' => 'tempat_ibadah',
        'vihara' => 'tempat_ibadah',
        'klenteng' => 'tempat_ibadah',
    ];

    // Check if remark contains any keyword
    foreach ($typeMap as $keyword => $type) {
        if (strpos($remark, $keyword) !== false) {
            return $type;
        }
    }

    // Default to 'lainnya'
    return 'lainnya';
}

// Get GeoJSON
function getGeoJSON($conn)
{
    try {

        // ================== GET MARKERS ==================
        $markerStmt = $conn->prepare("SELECT * FROM markers ORDER BY id DESC");
        $markerStmt->execute();
        $markers = $markerStmt->fetchAll(PDO::FETCH_ASSOC);

        $markerFeatures = [];
        foreach ($markers as $m) {
            $markerFeatures[] = [
                "type" => "Feature",
                "geometry" => [
                    "type" => "Point",
                    "coordinates" => [(float)$m['longitude'], (float)$m['latitude']]
                ],
                "properties" => [
                    "id" => $m['id'],
                    "name" => $m['name'],
                    "type" => $m['type'],
                    "description" => $m['description']
                ]
            ];
        }

        // ================== GET REGIONS ==================
        $regionStmt = $conn->prepare("SELECT * FROM regions ORDER BY id DESC");
        $regionStmt->execute();
        $regions = $regionStmt->fetchAll(PDO::FETCH_ASSOC);

        $regionFeatures = [];
        foreach ($regions as $r) {
            $geometry = json_decode($r['geometry_data'], true);
            $properties = json_decode($r['properties'], true) ?? [];

            $regionFeatures[] = [
                "type" => "Feature",
                "geometry" => $geometry,
                "properties" => array_merge($properties, [
                    "id" => $r['id'],
                    "name" => $r['name'],
                    "type" => $r['type'],
                    "description" => $r['description']
                ])
            ];
        }

        // ================== GABUNGKAN ==================
        $allFeatures = array_merge($markerFeatures, $regionFeatures);

        echo json_encode([
            "type" => "FeatureCollection",
            "features" => $allFeatures
        ], JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}





// Fungsi Get Regions
function getRegions($conn)
{
    try {
        $stmt = $conn->prepare("SELECT * FROM regions ORDER BY created_at DESC");
        $stmt->execute();
        $regions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Parse geometry data back to array
        foreach ($regions as &$region) {
            $region['geometry_data'] = json_decode($region['geometry_data'], true);
            $region['properties'] = json_decode($region['properties'], true);
        }

        echo json_encode([
            'success' => true,
            'regions' => $regions
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

// Fungsi Delete Region
function deleteRegion($conn)
{
    try {
        $data = json_decode(file_get_contents('php://input'), true);

        $stmt = $conn->prepare("DELETE FROM regions WHERE id = ?");
        $stmt->execute([$data['id']]);

        echo json_encode([
            'success' => true,
            'message' => 'Region deleted successfully'
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}
// Update fungsi saveGeoJSON - tambahkan penyimpanan polygon ke database
function saveGeoJSON($conn)
{
    try {
        $geojson = json_decode(file_get_contents('php://input'), true);

        if (!$geojson || !isset($geojson['features'])) {
            throw new Exception('Invalid GeoJSON format');
        }


        // Prepare insert statements
        $markerStmt = $conn->prepare("
            INSERT INTO markers (name, type, latitude, longitude, description)
            VALUES (?, ?, ?, ?, ?)
        ");

        $regionStmt = $conn->prepare("
            INSERT INTO regions (name, type, geometry_type, geometry_data, properties, description)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $markersInserted = 0;
        $regionsInserted = 0;

        foreach ($geojson['features'] as $feature) {
            $geometry = $feature['geometry'];
            $props    = $feature['properties'] ?? [];

            $name = $props['NAMOBJ'] ??
                $props['name'] ??
                $props['nama'] ??
                'Unnamed';

            $type = $props['REMARK'] ??
                $props['jenis'] ??
                'lainnya';

            $description = $props['keterangan'] ??
                $props['description'] ??
                '';

            // Insert POINT
            if ($geometry['type'] === 'Point') {

                $coords = $geometry['coordinates'];
                $lon = $coords[0];
                $lat = $coords[1];

                // Cek duplikat
                $check = $conn->prepare("
                    SELECT id FROM markers 
                    WHERE ABS(latitude - ?) < 0.00001 
                      AND ABS(longitude - ?) < 0.00001
                ");
                $check->execute([$lat, $lon]);

                if (!$check->fetch()) {
                    $markerStmt->execute([$name, mapRemarkToType($type), $lat, $lon, $description]);
                    $markersInserted++;
                }
            } else {
                // Insert POLYGON / LINESTRING ke regions table

                $geometry_json = json_encode($geometry);
                $properties_json = json_encode($props);

                // Cek duplikat
                $check = $conn->prepare("SELECT id FROM regions WHERE geometry_data = ?");
                $check->execute([$geometry_json]);

                if (!$check->fetch()) {
                    $regionStmt->execute([
                        $name,
                        $type,
                        $geometry['type'],
                        $geometry_json,
                        $properties_json,
                        $description
                    ]);
                    $regionsInserted++;
                }
            }
        }

        echo json_encode([
            'success' => true,
            'message' => 'GeoJSON berhasil disimpan ke database',
            'markers_inserted' => $markersInserted,
            'regions_inserted' => $regionsInserted
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}


$conn = null;
