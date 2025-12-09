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

        $stmt = $conn->prepare("
            INSERT INTO markers (nama_pantai, jam_buka, jam_tutup, harga, rating, latitude, longitude)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $data['NamaPantai'] ?? null,
            $data['JamBuka'] ?? null,
            $data['JamTutup'] ?? null,
            $data['Harga'] ?? null,
            $data['Rating'] ?? null,
            $data['latitude'],
            $data['longitude'],
        ]);

        echo json_encode([
            'success' => true,
            'message' => 'Marker added successfully'
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
    return 'pantai';
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

        $markerStmt = $conn->prepare("
            INSERT INTO markers (name, type, latitude, longitude, description)
            VALUES (?, ?, ?, ?, ?)
        ");

        $markersInserted = 0;

        foreach ($geojson['features'] as $feature) {

            $geometry = $feature['geometry'];
            $props    = $feature['properties'] ?? [];

            if ($geometry['type'] !== 'Point') {
                continue; // Abaikan polygon/line jika GeoJSON hanya berisi point
            }

            // ==========================
            //  SESUAIKAN DENGAN GEOJSON KAMU
            // ==========================
            $name = $props['NamaPantai'] ?? "Pantai";
            $type = "pantai";

            // Siapkan array untuk deskripsi
            $descArray = [];

            // Ambil JamBuka, JamTutup, Harga, Rating jika ada
            if (isset($props["JamBuka"])) $descArray["JamBuka"] = $props["JamBuka"];
            if (isset($props["JamTutup"])) $descArray["JamTutup"] = $props["JamTutup"];
            if (isset($props["Harga"])) $descArray["Harga"] = $props["Harga"];
            if (isset($props["Rating"])) $descArray["Rating"] = $props["Rating"];

            // Jika ada keterangan nested, gabungkan
            if (isset($props['keterangan']) && is_array($props['keterangan'])) {
                $descArray = array_merge($descArray, $props['keterangan']);
            }

            // Jika ada properti description string, simpan juga
            if (isset($props['description'])) {
                $descArray['description'] = $props['description'];
            }

            // Simpan semua sebagai JSON string
            $description = json_encode($descArray, JSON_UNESCAPED_UNICODE);

            // Ambil koordinat
            $coords = $geometry['coordinates'];
            $lon = $coords[0];
            $lat = $coords[1];

            // Cek duplikat
            $check = $conn->prepare("
    SELECT id FROM markers 
    WHERE ABS(latitude - ?) < 0.00001 AND ABS(longitude - ?) < 0.00001
");
            $check->execute([$lat, $lon]);

            if (!$check->fetch()) {
                $markerStmt->execute([$name, $type, $lat, $lon, $description]);
            }
        }

        echo json_encode([
            'success' => true,
            'message' => 'GeoJSON berhasil disimpan ke database',
            'markers_inserted' => $markersInserted,
            'regions_inserted' => 0
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}



$conn = null;
