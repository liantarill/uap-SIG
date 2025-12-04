<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebGIS - Tugas 2</title>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts - Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        #map {
            width: 100%;
            height: 100%;
        }
    </style>
</head>

<body class="bg-slate-900 text-gray-100">
    <!-- Header -->
    <div class="bg-gradient-to-r from-slate-800 to-slate-900 text-white px-6 py-5 shadow-xl border-b-2 border-cyan-400">
        <a href="/public/index.php">
            <h1 class="text-2xl font-bold mb-1 text-white"><i class="fas fa-map-marked-alt mr-2"></i>UAP Sistem Informasi Geografis</h1>
        </a>
    </div>

    <!-- Container -->
    <div class="flex h-[calc(100vh-80px)]">
        <!-- Sidebar -->
        <div class="w-[350px] bg-slate-800 shadow-2xl overflow-y-auto p-5 border-r border-slate-700">
            <!-- Legend -->
            <div class="bg-slate-700 rounded-lg p-4 shadow-lg border border-slate-600 mb-5">
                <h3 class="text-base font-semibold mb-4 text-cyan-400 border-b-2 border-cyan-400 pb-2"><i class="fas fa-palette mr-2"></i>Legenda</h3>
                <div class="space-y-2">
                    <div class="flex items-center text-sm text-gray-400">
                        <div class="w-5 h-5 rounded-full mr-2.5 border-2 border-slate-600 shadow flex items-center justify-center" style="background: #ef4444;">
                            <i class="fas fa-school text-white text-[10px]"></i>
                        </div>
                        <span>Sekolah</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-400">
                        <div class="w-5 h-5 rounded-full mr-2.5 border-2 border-slate-600 shadow flex items-center justify-center" style="background: #3b82f6;">
                            <i class="fas fa-hospital text-white text-[10px]"></i>
                        </div>
                        <span>Rumah Sakit</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-400">
                        <div class="w-5 h-5 rounded-full mr-2.5 border-2 border-slate-600 shadow flex items-center justify-center" style="background: #10b981;">
                            <i class="fas fa-building text-white text-[10px]"></i>
                        </div>
                        <span>Kantor Pemerintahan</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-400">
                        <div class="w-5 h-5 rounded-full mr-2.5 border-2 border-slate-600 shadow flex items-center justify-center" style="background: #f59e0b;">
                            <i class="fas fa-mosque text-white text-[10px]"></i>
                        </div>
                        <span>Tempat Ibadah</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-400">
                        <div class="w-5 h-5 rounded-full mr-2.5 border-2 border-slate-600 shadow flex items-center justify-center" style="background: #8b5cf6;">
                            <i class="fas fa-map-pin text-white text-[10px]"></i>
                        </div>
                        <span>Lainnya</span>
                    </div>
                </div>
            </div>
            <!-- Upload GeoJSON -->
            <div class="bg-slate-700 rounded-lg p-4 mb-5 shadow-lg border border-slate-600">
                <h3 class="text-base font-semibold mb-4 text-cyan-400 border-b-2 border-cyan-400 pb-2"><i class="fas fa-folder-open mr-2"></i>Kelola GeoJSON</h3>
                <div class="border-2 border-dashed border-cyan-400 rounded-lg p-5 text-center cursor-pointer transition-all hover:bg-cyan-400/10 hover:text-cyan-300 bg-cyan-400/5 text-gray-400 mb-3" onclick="document.getElementById('geojsonFile').click()">
                    <p class="font-medium"><i class="fas fa-upload mr-2"></i>Klik untuk upload file GeoJSON</p>
                    <small class="text-xs">File dari QGIS (Point, Polygon, LineString)</small>
                    <input type="file" id="geojsonFile" accept=".geojson,.json" class="hidden">
                </div>
                <button class="w-full px-5 py-2.5 bg-cyan-400 text-slate-900 font-semibold rounded-lg hover:bg-cyan-500 transition-all hover:shadow-lg hover:shadow-cyan-400/30 mb-2" onclick="loadGeoJSON()">
                    <i class="fas fa-check-circle mr-2"></i>Load GeoJSON
                </button>

            </div>

            <div class="bg-slate">

                <div class=" bg-slate-700 rounded-lg p-4 mb-5 shadow-lg border border-slate-600">
                    <h3 class="text-base font-semibold mb-4 text-cyan-400 border-b-2 border-cyan-400 pb-2">
                        <i class="fas fa-plus-circle mr-2"></i>Tambah Marker Baru
                    </h3>
                    <button class="w-full px-5 py-2.5 bg-cyan-400 text-slate-900 font-semibold rounded-lg hover:bg-cyan-500 transition-all hover:shadow-lg hover:shadow-cyan-400/30" onclick="openAddModal()">
                        <i class="fas fa-map-marker-alt mr-2"></i>Tambah Titik Lokasi
                    </button>
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

            <!-- Region List -->
            <div class="bg-slate-700 rounded-lg p-4 mb-5 shadow-lg border border-slate-600">
                <h3 class="text-base font-semibold mb-4 text-purple-400 border-b-2 border-purple-400 pb-2"><i class="fas fa-draw-polygon mr-2"></i>Daftar Wilayah (<span id="regionCount">0</span>)</h3>
                <ul class="space-y-2.5" id="regionList">
                    <li class="text-center text-gray-500 py-5">
                        Belum ada wilayah. Upload GeoJSON dengan Polygon/LineString.
                    </li>
                </ul>
            </div>


        </div>

        <!-- Map Container -->
        <div class="flex-1 relative">
            <div id="map"></div>
        </div>
    </div>

    <!-- Modal Add Marker -->
    <div id="addModal" class="hidden fixed inset-0 bg-black/70 z-[1000] items-center justify-center">
        <div class="bg-slate-800 p-8 rounded-xl w-[90%] max-w-[500px] max-h-[90vh] overflow-y-auto border border-slate-700">
            <div class="flex justify-between items-center mb-5">
                <h2 class="text-xl font-semibold text-cyan-400"><i class="fas fa-plus-circle mr-2"></i>Tambah Marker Baru</h2>
                <button class="text-2xl text-gray-400 hover:text-cyan-400 transition-colors" onclick="closeAddModal()"><i class="fas fa-times"></i></button>
            </div>
            <form id="addMarkerForm" onsubmit="addMarker(event)">
                <div class="mb-4">
                    <label class="block mb-1.5 text-gray-400 text-sm"><i class="fas fa-tag mr-1"></i>Nama Lokasi *</label>
                    <input type="text" name="name" required placeholder="Contoh: SDN 1 Bandar Lampung" class="w-full px-3 py-2.5 bg-slate-700 border border-slate-600 rounded-lg text-sm text-gray-100 placeholder-gray-500 focus:outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/20">
                </div>
                <div class="mb-4">
                    <label class="block mb-1.5 text-gray-400 text-sm"><i class="fas fa-layer-group mr-1"></i>Jenis/Tipe *</label>
                    <select name="type" required class="w-full px-3 py-2.5 bg-slate-700 border border-slate-600 rounded-lg text-sm text-gray-100 focus:outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/20">
                        <option value="">-- Pilih Jenis --</option>
                        <option value="sekolah"><i class="fas fa-school"></i> Sekolah</option>
                        <option value="rumah_sakit"><i class="fas fa-hospital"></i> Rumah Sakit</option>
                        <option value="kantor"><i class="fas fa-building"></i> Kantor Pemerintahan</option>
                        <option value="tempat_ibadah"><i class="fas fa-mosque"></i> Tempat Ibadah</option>
                        <option value="lainnya"><i class="fas fa-map-pin"></i> Lainnya</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block mb-1.5 text-gray-400 text-sm"><i class="fas fa-map-marker mr-1"></i>Latitude *</label>
                    <input type="number" step="any" name="latitude" required placeholder="-5.450000" class="w-full px-3 py-2.5 bg-slate-700 border border-slate-600 rounded-lg text-sm text-gray-100 placeholder-gray-500 focus:outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/20">
                </div>
                <div class="mb-4">
                    <label class="block mb-1.5 text-gray-400 text-sm"><i class="fas fa-map-marker mr-1"></i>Longitude *</label>
                    <input type="number" step="any" name="longitude" required placeholder="105.266670" class="w-full px-3 py-2.5 bg-slate-700 border border-slate-600 rounded-lg text-sm text-gray-100 placeholder-gray-500 focus:outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/20">
                </div>
                <div class="mb-4">
                    <label class="block mb-1.5 text-gray-400 text-sm"><i class="fas fa-align-left mr-1"></i>Keterangan</label>
                    <textarea name="description" placeholder="Deskripsi tambahan tentang lokasi ini..." class="w-full px-3 py-2.5 bg-slate-700 border border-slate-600 rounded-lg text-sm text-gray-100 placeholder-gray-500 focus:outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/20 resize-y min-h-[60px]"></textarea>
                </div>
                <button type="submit" class="w-full px-5 py-2.5 bg-cyan-400 text-slate-900 font-semibold rounded-lg hover:bg-cyan-500 transition-all hover:shadow-lg hover:shadow-cyan-400/30">
                    <i class="fas fa-save mr-2"></i>Simpan Marker
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>
    <script src="/assets/main.js"></script>
</body>

</html>