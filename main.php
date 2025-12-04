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
        <a href="index.php">
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

    <!-- Leaflet JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>

    <script>
        let map;
        let markers = [];
        let regions = [];
        let markerLayers = new Map();
        let regionLayers = new Map();
        let geojsonLayer = null;

        function initMap() {
            map = L.map('map').setView([-5.45, 105.25], 10);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(map);

            loadDataFromServer();
            loadRegionsFromServer();
            loadSavedGeoJSON();
        }

        function getMarkerColor(type) {
            const colors = {
                'sekolah': '#ef4444',
                'rumah_sakit': '#3b82f6',
                'kantor': '#10b981',
                'tempat_ibadah': '#f59e0b',
                'lainnya': '#8b5cf6'
            };
            return colors[type] || '#8b5cf6';
        }

        function createMarker(lat, lng, data) {
            const color = getMarkerColor(data.type);

            const icon = L.divIcon({
                html: `<div style="background-color: ${color}; width: 30px; height: 30px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"></div>`,
                className: '',
                iconSize: [30, 30],
                iconAnchor: [15, 15]
            });

            const marker = L.marker([lat, lng], {
                icon: icon
            }).addTo(map);

            const popupContent = `
                <div style="font-family: 'Poppins', sans-serif;">
                    <h3 style="margin: 0 0 10px 0; color: ${color}; font-weight: 600;">${data.name}</h3>
                    <p style="margin: 5px 0;"><strong>Jenis:</strong> ${data.type}</p>
                    <p style="margin: 5px 0;"><strong>Koordinat:</strong> ${lat.toFixed(6)}, ${lng.toFixed(6)}</p>
                    ${data.description ? `<p style="margin: 5px 0;"><strong>Keterangan:</strong> ${data.description}</p>` : ''}
                </div>
            `;

            marker.bindPopup(popupContent);
            return marker;
        }

        function loadGeoJSON() {
            const fileInput = document.getElementById('geojsonFile');
            const file = fileInput.files[0];

            if (!file) {
                alert('Pilih file GeoJSON terlebih dahulu!');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                try {
                    const geojsonData = JSON.parse(e.target.result);

                    fetch('api.php?action=save_geojson', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(geojsonData)
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                let message = 'GeoJSON berhasil dimuat!\n';
                                if (data.markers_inserted > 0) {
                                    message += `✓ ${data.markers_inserted} Point markers disimpan ke database.\n`;
                                }
                                if (data.regions_inserted > 0) {
                                    message += `✓ ${data.regions_inserted} Polygon/LineString disimpan ke database.\n`;
                                }
                                message += `\nTotal features: ${data.total_features}`;

                                alert(message);

                                // Reload data
                                loadDataFromServer();
                                loadRegionsFromServer();
                                loadSavedGeoJSON();

                                // Clear file input
                                fileInput.value = '';
                            } else {
                                alert('Error: ' + data.message);
                            }
                        })
                        .catch(error => {
                            alert('Error: ' + error.message);
                        });

                } catch (error) {
                    alert('Error: File GeoJSON tidak valid!\n' + error.message);
                }
            };
            reader.readAsText(file);
        }

        function loadSavedGeoJSON() {
            fetch('api.php?action=get_geojson')
                .then(response => response.json())
                .then(geojsonData => {
                    if (geojsonData && geojsonData.type === 'FeatureCollection') {
                        displayGeoJSON(geojsonData);
                    }
                })
                .catch(error => {
                    console.log('No saved GeoJSON found or error loading:', error);
                });
        }

        function displayGeoJSON(geojsonData) {
            if (geojsonLayer) {
                map.removeLayer(geojsonLayer);
            }

            geojsonLayer = L.geoJSON(geojsonData, {
                style: function(feature) {
                    return {
                        color: feature.properties.color || '#3388ff',
                        weight: 2,
                        opacity: 0.8,
                        fillColor: feature.properties.fillColor || '#3388ff',
                        fillOpacity: 0.4
                    };
                },
                pointToLayer: function(feature, latlng) {
                    return null;
                },
                onEachFeature: function(feature, layer) {
                    const props = feature.properties;

                    if (feature.geometry.type !== 'Point') {
                        let popupContent = '<div style="font-family: \'Poppins\', sans-serif;">';
                        popupContent += `<h3 style="margin: 0 0 10px 0; color: #667eea; font-weight: 600;">${props.nama || props.name || 'Feature'}</h3>`;

                        for (let key in props) {
                            if (props[key] && key !== 'nama' && key !== 'name') {
                                popupContent += `<p style="margin: 5px 0;"><strong>${key}:</strong> ${props[key]}</p>`;
                            }
                        }

                        popupContent += '</div>';
                        layer.bindPopup(popupContent);
                    }
                },
                filter: function(feature) {
                    return feature.geometry.type !== 'Point';
                }
            }).addTo(map);

            if (geojsonLayer.getBounds().isValid()) {
                map.fitBounds(geojsonLayer.getBounds(), {
                    padding: [50, 50]
                });
            }
        }

        function loadDataFromServer() {
            fetch('api.php?action=get_markers')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.markers) {
                        markerLayers.forEach(layer => {
                            map.removeLayer(layer);
                        });
                        markerLayers.clear();
                        markers = [];

                        data.markers.forEach(marker => {
                            const m = createMarker(
                                parseFloat(marker.latitude),
                                parseFloat(marker.longitude),
                                marker
                            );

                            markerLayers.set(marker.id, m);
                            markers.push(marker);
                        });

                        updateMarkerList();

                        if (markers.length > 0) {
                            const bounds = [];
                            markers.forEach(marker => {
                                bounds.push([parseFloat(marker.latitude), parseFloat(marker.longitude)]);
                            });
                            if (bounds.length > 0) {
                                map.fitBounds(bounds, {
                                    padding: [50, 50]
                                });
                            }
                        }
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Load regions from server
        function loadRegionsFromServer() {
            fetch('api.php?action=get_regions')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.regions) {
                        regions = data.regions;
                        updateRegionList();
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Update region list in sidebar
        function updateRegionList() {
            const listContainer = document.getElementById('regionList');
            const countElement = document.getElementById('regionCount');

            countElement.textContent = regions.length;

            if (regions.length === 0) {
                listContainer.innerHTML = '<li class="text-center text-gray-500 py-5">Belum ada wilayah.</li>';
                return;
            }

            listContainer.innerHTML = '';

            regions.forEach((region) => {
                const li = document.createElement('li');
                li.className = 'bg-slate-800 p-3 rounded-lg border-l-4 border-purple-400 cursor-pointer transition-all hover:bg-slate-700 hover:translate-x-1 hover:shadow-lg hover:shadow-purple-400/20 border border-slate-600';

                const geometryIcon = region.geometry_type === 'Polygon' || region.geometry_type === 'MultiPolygon' ?
                    'fa-draw-polygon' : 'fa-route';

                li.innerHTML = `
                    <h4 class="text-sm font-semibold mb-1 text-purple-400"><i class="fas ${geometryIcon} mr-1"></i>${region.name}</h4>
                    <p class="text-xs text-gray-400 mb-0.5"><i class="fas fa-layer-group mr-1"></i><strong>Tipe:</strong> ${region.type}</p>
                    <p class="text-xs text-gray-400 mb-0.5"><i class="fas fa-shapes mr-1"></i><strong>Geometri:</strong> ${region.geometry_type}</p>
                    ${region.description ? `<p class="text-xs text-gray-400 mb-0.5"><i class="fas fa-info-circle mr-1"></i><strong>Ket:</strong> ${region.description}</p>` : ''}
                    <div class="mt-2">
                        <button class="px-2.5 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600 transition-colors" onclick="deleteRegion(${region.id})"><i class="fas fa-trash mr-1"></i>Hapus</button>
                    </div>
                `;

                li.onclick = function(e) {
                    if (e.target.tagName !== 'BUTTON' && !e.target.classList.contains('fa-trash')) {
                        flyToRegion(region);
                    }
                };

                listContainer.appendChild(li);
            });
        }

        // Fly to region
        function flyToRegion(region) {
            const geometry = region.geometry_data;

            // Create temporary layer to get bounds
            const tempLayer = L.geoJSON(geometry);
            const bounds = tempLayer.getBounds();

            map.fitBounds(bounds, {
                padding: [50, 50],
                maxZoom: 15
            });
        }

        // Delete region
        function deleteRegion(id) {
            event.stopPropagation();

            if (!confirm('Hapus wilayah ini?')) return;

            fetch('api.php?action=delete_region', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: id
                    })
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        loadRegionsFromServer();
                        loadSavedGeoJSON();
                        alert('Wilayah berhasil dihapus!');
                    } else {
                        alert('Error: ' + result.message);
                    }
                })
                .catch(error => {
                    alert('Error: ' + error.message);
                });
        }

        function updateMarkerList() {
            const listContainer = document.getElementById('markerList');
            const countElement = document.getElementById('markerCount');

            countElement.textContent = markers.length;

            if (markers.length === 0) {
                listContainer.innerHTML = '<li class="text-center text-gray-500 py-5">Belum ada data.</li>';
                return;
            }

            listContainer.innerHTML = '';

            markers.forEach((marker) => {
                const li = document.createElement('li');
                li.className = 'bg-slate-800 p-3 rounded-lg border-l-4 border-cyan-400 cursor-pointer transition-all hover:bg-slate-700 hover:translate-x-1 hover:shadow-lg hover:shadow-cyan-400/20 border border-slate-600';
                li.innerHTML = `
                    <h4 class="text-sm font-semibold mb-1 text-cyan-400"><i class="fas fa-map-pin mr-1"></i>${marker.name}</h4>
                    <p class="text-xs text-gray-400 mb-0.5"><i class="fas fa-layer-group mr-1"></i><strong>Jenis:</strong> ${marker.type}</p>
                    <p class="text-xs text-gray-400 mb-0.5"><i class="fas fa-crosshairs mr-1"></i><strong>Koordinat:</strong> ${parseFloat(marker.latitude).toFixed(6)}, ${parseFloat(marker.longitude).toFixed(6)}</p>
                    ${marker.description ? `<p class="text-xs text-gray-400 mb-0.5"><i class="fas fa-info-circle mr-1"></i><strong>Ket:</strong> ${marker.description}</p>` : ''}
                    <div class="mt-2">
                        <button class="px-2.5 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600 transition-colors" onclick="deleteMarker(${marker.id})"><i class="fas fa-trash mr-1"></i>Hapus</button>
                    </div>
                `;

                li.onclick = function(e) {
                    if (e.target.tagName !== 'BUTTON') {
                        flyToMarker(marker);
                    }
                };

                listContainer.appendChild(li);
            });
        }

        function flyToMarker(marker) {
            map.setView([parseFloat(marker.latitude), parseFloat(marker.longitude)], 16);
            const layer = markerLayers.get(marker.id);
            if (layer) {
                layer.openPopup();
            }
        }

        function searchMarkers() {
            const query = document.getElementById('searchInput').value.toLowerCase();
            const items = document.querySelectorAll('.marker-item, #markerList > li');

            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(query)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        function openAddModal() {
            const modal = document.getElementById('addModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeAddModal() {
            const modal = document.getElementById('addModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.getElementById('addMarkerForm').reset();
        }

        function addMarker(event) {
            event.preventDefault();

            const formData = new FormData(event.target);
            const data = {
                name: formData.get('name'),
                type: formData.get('type'),
                latitude: parseFloat(formData.get('latitude')),
                longitude: parseFloat(formData.get('longitude')),
                description: formData.get('description')
            };

            fetch('api.php?action=add_marker', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        closeAddModal();
                        loadDataFromServer();
                        alert('Marker berhasil ditambahkan!');
                    } else {
                        alert('Error: ' + result.message);
                    }
                })
                .catch(error => {
                    alert('Error: ' + error.message);
                });
        }

        function deleteMarker(id) {
            if (!confirm('Hapus marker ini?')) return;

            fetch('api.php?action=delete_marker', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: id
                    })
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        loadDataFromServer();
                        alert('Marker berhasil dihapus!');
                    } else {
                        alert('Error: ' + result.message);
                    }
                })
                .catch(error => {
                    alert('Error: ' + error.message);
                });
        }




        window.onload = function() {
            initMap();
        };
    </script>
</body>

</html>