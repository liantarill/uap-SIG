<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'webgis_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Application Configuration
define('APP_NAME', 'WebGIS Application');
define('APP_VERSION', '1.0.0');

// Map Configuration (sesuaikan dengan wilayah Anda)
define('DEFAULT_LAT', -5.45);      // Latitude default (Lampung)
define('DEFAULT_LNG', 105.25);     // Longitude default
define('DEFAULT_ZOOM', 10);         // Zoom level default

// File Upload Configuration
define('UPLOAD_DIR', __DIR__ . '/data/');
define('MAX_FILE_SIZE', 5242880);  // 5MB

// Create upload directory if not exists
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}

// Database Connection Function
function getConnection()
{
    try {
        $conn = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS
        );
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $conn;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// Helper function to sanitize input
function sanitize($data)
{
    return htmlspecialchars(strip_tags(trim($data)));
}

// Helper function to validate coordinates
function validateCoordinates($lat, $lng)
{
    return (
        is_numeric($lat) &&
        is_numeric($lng) &&
        $lat >= -90 &&
        $lat <= 90 &&
        $lng >= -180 &&
        $lng <= 180
    );
}
