<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebGIS - Geographic Information System</title>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts - Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="/assets/style.css">
    <style>
        body {

            overflow: hidden;
        }
    </style>
</head>

<body>
    <!-- Star field background like index.php -->
    <div class="star-field" id="starField"></div>

    <!-- Top Navigation -->
    <nav class="top-nav">
        <div class="logo">
            <i class="fas fa-map-marked-alt text-cyan-400 text-xl"></i>
            <span class="logo-text">GIS UAP</span>
        </div>
        <div class="nav-actions">
            <a href="./index.php" class="btn-ghost">
                <i class="fas fa-home mr-1"></i> Home
            </a>
            <button class="btn-glow" onclick="openAddModal()">
                <i class="fas fa-plus mr-1"></i> Add Marker
            </button>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Left Panel -->
        <aside class="side-panel left">
            <!-- Stats -->
            <div class="stat-card">
                <div class="stat-value"><span id="totalLocations">0</span></div>
                <div class="stat-label">Total Locations</div>
                <i class="fas fa-map-marker-alt stat-icon"></i>
            </div>

            <div class="stat-card">
                <div class="stat-value"><span id="totalRegions">0</span></div>
                <div class="stat-label">Regions Mapped</div>
                <i class="fas fa-draw-polygon stat-icon"></i>
            </div>

            <!-- Legend Panel -->
            <!-- <div class="panel-card">
                <div class="panel-header">
                    <i class="fas fa-palette"></i> Legend
                </div>
                <div>
                    <div class="legend-item color-sekolah" style="border-left-color: #ef4444;">
                        <div class="legend-dot" style="background: #ef4444;"></div>
                        <span class="legend-text">Sekolah</span>
                        <span class="legend-count">0</span>
                    </div>
                    <div class="legend-item color-rumah_sakit" style="border-left-color: #3b82f6;">
                        <div class="legend-dot" style="background: #3b82f6;"></div>
                        <span class="legend-text">Rumah Sakit</span>
                        <span class="legend-count">0</span>
                    </div>
                    <div class="legend-item color-kantor" style="border-left-color: #10b981;">
                        <div class="legend-dot" style="background: #10b981;"></div>
                        <span class="legend-text">Kantor Pemerintahan</span>
                        <span class="legend-count">0</span>
                    </div>
                    <div class="legend-item color-tempat_ibadah" style="border-left-color: #f59e0b;">
                        <div class="legend-dot" style="background: #f59e0b;"></div>
                        <span class="legend-text">Tempat Ibadah</span>
                        <span class="legend-count">0</span>
                    </div>
                    <div class="legend-item color-lainnya" style="border-left-color: #8b5cf6;">
                        <div class="legend-dot" style="background: #8b5cf6;"></div>
                        <span class="legend-text">Lainnya</span>
                        <span class="legend-count">0</span>
                    </div>
                </div>
            </div> -->

            <div class="panel-card">
                <div class="panel-header">
                    <i class="fas fa-bolt"></i> Quick Actions
                </div>
                <button class="action-btn" onclick="openAddModal()">
                    <i class="fas fa-plus-circle"></i> Add Marker
                </button>
                <button class="action-btn secondary" onclick="document.getElementById('geojsonFile').click()">
                    <i class="fas fa-upload"></i> Upload GeoJSON
                </button>
                <input type="file" id="geojsonFile" accept=".geojson,.json" class="hidden" onchange="loadGeoJSON()">
            </div>
        </aside>

        <!-- Map -->
        <main>
            <div id="map"></div>
        </main>

        <!-- Right Panel -->
        <aside class="side-panel right">
            <!-- Search -->
            <div class="panel-card">
                <div class="panel-header">
                    <i class="fas fa-search"></i> Search
                </div>
                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="searchInput" placeholder="Search locations..." class="search-input" onkeyup="searchMarkers()">
                </div>
            </div>
            <!-- Search -->
            <div class="bg-slate-700 rounded-lg p-4 mb-5 shadow-lg border border-slate-600">
                <h3 class="text-base font-semibold mb-4 text-cyan-400 border-b-2 border-cyan-400 pb-2"><i class="fas fa-search mr-2"></i>Pencarian</h3>
                <input type="text" id="searchInput" placeholder="Cari nama lokasi..." onkeyup="searchMarkers()" class="w-full px-3 py-2.5 bg-slate-800 border border-slate-600 rounded-lg text-sm text-gray-100 placeholder-gray-500 focus:outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/20">
            </div>

            <!-- Marker List -->
            <div class="bg-slate-700 rounded-lg p-4 mb-5 shadow-lg border border-slate-600">
                <h3 class="text-base font-semibold mb-4 text-cyan-400 border-b-2 border-cyan-400 pb-2"><i class="fas fa-map-marker-alt mr-2"></i>Daftar Lokasi (<span id="markerCount">0</span>)</h3>
                <ul class="space-y-2.5" id="markerList">
                    <li class="text-center text-gray-500 py-5">
                        Belum ada data. Upload GeoJSON atau tambah marker baru.
                    </li>
                </ul>
            </div>

            <!-- Location List -->
            <div class="panel-card">
                <div class="panel-header">
                    <i class="fas fa-map-marker-alt"></i> Locations
                </div>
                <div id="markerList">
                    <div class="empty-state">
                        <i class="fas fa-inbox" style="font-size: 1.5rem; margin-bottom: 8px; display: block;"></i>
                        No locations yet
                    </div>
                </div>

            </div>

            <!-- Region List -->
            <div class="panel-card">
                <h3 class="panel-header">
                    <i class="fas fa-draw-polygon mr-2"></i>Daftar Wilayah (<span id="regionCount">0</span>)
                </h3>

                <ul class="space-y-2.5" id="regionList">
                    <li class="text-center text-gray-500 py-5">
                        No regions mapped
                    </li>
                </ul>
            </div>


        </aside>
    </div>

    <!-- Modal Add Marker -->
    <div id="addModal" class="hidden fixed inset-0 z-[1000] flex items-center justify-center modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Add New Marker</h2>
                <button class="close-btn" onclick="closeAddModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="addMarkerForm" onsubmit="addMarker(event)">
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-tag mr-1"></i> Location Name</label>
                    <input type="text" name="name" class="form-input" required placeholder="Enter location name">
                </div>
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-layer-group mr-1"></i> Category</label>
                    <select name="type" class="form-select" required>
                        <option value="">-- Select Category --</option>
                        <option value="sekolah">Sekolah</option>
                        <option value="rumah_sakit">Rumah Sakit</option>
                        <option value="kantor">Kantor Pemerintahan</option>
                        <option value="tempat_ibadah">Tempat Ibadah</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-map-marker mr-1"></i> Latitude</label>
                    <input type="number" step="any" name="latitude" class="form-input" required placeholder="-5.450000">
                </div>
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-map-marker mr-1"></i> Longitude</label>
                    <input type="number" step="any" name="longitude" class="form-input" required placeholder="105.266670">
                </div>
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-align-left mr-1"></i> Description</label>
                    <textarea name="description" class="form-textarea" placeholder="Additional information..."></textarea>
                </div>
                <button type="submit" class="action-btn">
                    <i class="fas fa-save"></i> Save Marker
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>
    <!-- <script>
        // Generate random stars like index.php
        function generateStars() {
            const starField = document.getElementById('starField');
            const starCount = Math.min(60, window.innerWidth / 10);

            for (let i = 0; i < starCount; i++) {
                const star = document.createElement('div');
                star.className = 'star';
                star.style.left = Math.random() * 100 + '%';
                star.style.top = Math.random() * 100 + '%';
                star.style.animationDelay = Math.random() * 4 + 's';
                starField.appendChild(star);
            }
        }
        generateStars();
    </script> -->
    <script src="/assets/main.js"></script>
</body>

</html>