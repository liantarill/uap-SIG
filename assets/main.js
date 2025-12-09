let map;
let markers = [];
let regions = [];
let markerLayers = new Map();
let regionLayers = new Map();
let geojsonLayer = null;

function initMap() {
  map = L.map("map").setView([-5.45, 105.25], 10);

  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: "© OpenStreetMap contributors",
    maxZoom: 19,
  }).addTo(map);

  loadDataFromServer();
  loadRegionsFromServer();
  loadSavedGeoJSON();
}

function getMarkerColor(type) {
  const colors = {
    sekolah: "#ef4444",
    rumah_sakit: "#3b82f6",
    kantor: "#10b981",
    tempat_ibadah: "#f59e0b",
    pantai: "#06b6d4",
    lainnya: "#8b5cf6",
  };

  return colors[type] || colors["lainnya"];
}

function createMarker(lat, lng, data) {
  const color = getMarkerColor(data.type);

  const icon = L.divIcon({
    html: `<div style="background-color: ${color}; width: 30px; height: 30px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"></div>`,
    className: "",
    iconSize: [30, 30],
    iconAnchor: [15, 15],
  });

  const marker = L.marker([lat, lng], {
    icon: icon,
  }).addTo(map);

  let descriptionHTML = "";
  if (data.description) {
    try {
      const descObj = JSON.parse(data.description); // parse JSON
      descriptionHTML =
        "<p style='margin:5px 0;'><strong>Keterangan:</strong><br>";
      for (const key in descObj) {
        descriptionHTML += `${key}: ${descObj[key]}<br>`;
      }
      descriptionHTML += "</p>";
    } catch (e) {
      // Kalau bukan JSON, tampilkan apa adanya
      descriptionHTML = `<p style="margin:5px 0;"><strong>Keterangan:</strong> ${data.description}</p>`;
    }
  }

  const popupContent = `
    <div style="font-family: 'Poppins', sans-serif;">
        <h3 style="margin:0 0 10px 0; color:${color}; font-weight:600;">${
    data.name
  }</h3>
        <p style="margin:5px 0;"><strong>Jenis:</strong> ${data.type}</p>
        <p style="margin:5px 0;"><strong>Koordinat:</strong> ${lat.toFixed(
          6
        )}, ${lng.toFixed(6)}</p>
        ${descriptionHTML}
    </div>
`;

  marker.bindPopup(popupContent);
  return marker;
}

function loadGeoJSON() {
  const fileInput = document.getElementById("geojsonFile");
  const file = fileInput.files[0];

  if (!file) {
    alert("Pilih file GeoJSON terlebih dahulu!");
    return;
  }

  const reader = new FileReader();
  reader.onload = function (e) {
    try {
      const geojsonData = JSON.parse(e.target.result);

      fetch("api.php?action=save_geojson", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(geojsonData),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            let message = "GeoJSON berhasil dimuat!\n";
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
            fileInput.value = "";
          } else {
            alert("Error: " + data.message);
          }
        })
        .catch((error) => {
          alert("Error: " + error.message);
        });
    } catch (error) {
      alert("Error: File GeoJSON tidak valid!\n" + error.message);
    }
  };
  reader.readAsText(file);
}

function loadSavedGeoJSON() {
  fetch("api.php?action=get_geojson")
    .then((response) => response.json())
    .then((geojsonData) => {
      if (geojsonData && geojsonData.type === "FeatureCollection") {
        displayGeoJSON(geojsonData);
      }
    })
    .catch((error) => {
      console.log("No saved GeoJSON found or error loading:", error);
    });
}

function displayGeoJSON(geojsonData) {
  if (geojsonLayer) {
    map.removeLayer(geojsonLayer);
  }

  geojsonLayer = L.geoJSON(geojsonData, {
    style: function (feature) {
      return {
        color: feature.properties.color || "#3388ff",
        weight: 2,
        opacity: 0.8,
        fillColor: feature.properties.fillColor || "#3388ff",
        fillOpacity: 0.4,
      };
    },
    pointToLayer: function (feature, latlng) {
      return null;
    },
    onEachFeature: function (feature, layer) {
      const props = feature.properties;

      if (feature.geometry.type !== "Point") {
        let popupContent =
          "<div style=\"font-family: 'Poppins', sans-serif;\">";
        popupContent += `<h3 style="margin: 0 0 10px 0; color: #667eea; font-weight: 600;">${
          props.nama || props.name || "Feature"
        }</h3>`;

        for (let key in props) {
          if (props[key] && key !== "nama" && key !== "name") {
            popupContent += `<p style="margin: 5px 0;"><strong>${key}:</strong> ${props[key]}</p>`;
          }
        }

        popupContent += "</div>";
        layer.bindPopup(popupContent);
      }
    },
    filter: function (feature) {
      return feature.geometry.type !== "Point";
    },
  }).addTo(map);

  if (geojsonLayer.getBounds().isValid()) {
    map.fitBounds(geojsonLayer.getBounds(), {
      padding: [50, 50],
    });
  }
}

function loadDataFromServer() {
  fetch("api.php?action=get_markers")
    .then((response) => response.json())
    .then((data) => {
      if (data.success && data.markers) {
        markerLayers.forEach((layer) => {
          map.removeLayer(layer);
        });
        markerLayers.clear();
        markers = [];

        data.markers.forEach((marker) => {
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
          markers.forEach((marker) => {
            bounds.push([
              parseFloat(marker.latitude),
              parseFloat(marker.longitude),
            ]);
          });
          if (bounds.length > 0) {
            map.fitBounds(bounds, {
              padding: [50, 50],
            });
          }
        }
      }
    })
    .catch((error) => console.error("Error:", error));
}

// Load regions from server
function loadRegionsFromServer() {
  fetch("api.php?action=get_regions")
    .then((response) => response.json())
    .then((data) => {
      if (data.success && data.regions) {
        regions = data.regions;
        updateRegionList();
      }
    })
    .catch((error) => console.error("Error:", error));
}

// Update region list in sidebar
function updateRegionList() {
  const listContainer = document.getElementById("regionList");
  const countElement = document.getElementById("regionCount");

  countElement.textContent = regions.length;

  if (regions.length === 0) {
    listContainer.innerHTML =
      '<li class="text-center text-gray-500 py-5">Belum ada wilayah.</li>';
    return;
  }

  listContainer.innerHTML = "";

  regions.forEach((region) => {
    const li = document.createElement("li");
    li.className =
      "bg-slate-800 p-3 rounded-lg border-l-4 border-purple-400 cursor-pointer transition-all hover:bg-slate-700 hover:translate-x-1 hover:shadow-lg hover:shadow-purple-400/20 border border-slate-600";

    const geometryIcon =
      region.geometry_type === "Polygon" ||
      region.geometry_type === "MultiPolygon"
        ? "fa-draw-polygon"
        : "fa-route";

    li.innerHTML = `
                    <h4 class="text-sm font-semibold mb-1 text-purple-400"><i class="fas ${geometryIcon} mr-1"></i>${
      region.name
    }</h4>
                    <p class="text-xs text-gray-400 mb-0.5"><i class="fas fa-layer-group mr-1"></i><strong>Tipe:</strong> ${
                      region.type
                    }</p>
                    <p class="text-xs text-gray-400 mb-0.5"><i class="fas fa-shapes mr-1"></i><strong>Geometri:</strong> ${
                      region.geometry_type
                    }</p>
                    ${
                      region.description
                        ? `<p class="text-xs text-gray-400 mb-0.5"><i class="fas fa-info-circle mr-1"></i><strong>Ket:</strong> ${region.description}</p>`
                        : ""
                    }
                    <div class="mt-2">
                        <button class="px-2.5 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600 transition-colors" onclick="deleteRegion(${
                          region.id
                        })"><i class="fas fa-trash mr-1"></i>Hapus</button>
                    </div>
                `;

    li.onclick = function (e) {
      if (
        e.target.tagName !== "BUTTON" &&
        !e.target.classList.contains("fa-trash")
      ) {
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
    maxZoom: 15,
  });
}

// Delete region
function deleteRegion(id) {
  event.stopPropagation();

  if (!confirm("Hapus wilayah ini?")) return;

  fetch("api.php?action=delete_region", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      id: id,
    }),
  })
    .then((response) => response.json())
    .then((result) => {
      if (result.success) {
        loadRegionsFromServer();
        loadSavedGeoJSON();
        alert("Wilayah berhasil dihapus!");
      } else {
        alert("Error: " + result.message);
      }
    })
    .catch((error) => {
      alert("Error: " + error.message);
    });
}

function updateMarkerList() {
  const listContainer = document.getElementById("markerList");
  const countElement = document.getElementById("markerCount");

  countElement.textContent = markers.length;

  if (markers.length === 0) {
    listContainer.innerHTML =
      '<li class="text-center text-gray-500 py-5">Belum ada data.</li>';
    return;
  }

  listContainer.innerHTML = "";

  markers.forEach((marker) => {
    const li = document.createElement("li");
    li.className =
      "bg-slate-800 p-3 rounded-lg border-l-4 border-cyan-400 cursor-pointer transition-all hover:bg-slate-700 hover:translate-x-1 hover:shadow-lg hover:shadow-cyan-400/20 border border-slate-600";
    let descriptionText = "";
    if (marker.description) {
      try {
        const descObj = JSON.parse(marker.description); // parse JSON
        for (const key in descObj) {
          descriptionText += `${key}: ${descObj[key]} | `;
        }
        // hapus trailing " | "
        descriptionText = descriptionText.slice(0, -3);
      } catch (e) {
        // kalau bukan JSON, tampilkan apa adanya
        descriptionText = marker.description;
      }
    }

    li.innerHTML = `
        <h4 class="text-sm font-semibold mb-1 text-cyan-400">
            <i class="fas fa-map-pin mr-1"></i>${marker.name}
        </h4>
        <p class="text-xs text-gray-400 mb-0.5">
            <i class="fas fa-layer-group mr-1"></i><strong>Jenis:</strong> ${
              marker.type
            }
        </p>
        <p class="text-xs text-gray-400 mb-0.5">
            <i class="fas fa-crosshairs mr-1"></i><strong>Koordinat:</strong> ${parseFloat(
              marker.latitude
            ).toFixed(6)}, ${parseFloat(marker.longitude).toFixed(6)}
        </p>
        ${
          descriptionText
            ? `<p class="text-xs text-gray-400 mb-0.5"><i class="fas fa-info-circle mr-1"></i><strong>Ket:</strong> ${descriptionText}</p>`
            : ""
        }
        <div class="mt-2">
            <button class="px-2.5 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600 transition-colors" onclick="deleteMarker(${
              marker.id
            })">
                <i class="fas fa-trash mr-1"></i>Hapus
            </button>
        </div>
    `;

    li.onclick = function (e) {
      if (e.target.tagName !== "BUTTON") {
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
  const query = document.getElementById("searchInput").value.toLowerCase();
  const items = document.querySelectorAll(".marker-item, #markerList > li");

  items.forEach((item) => {
    const text = item.textContent.toLowerCase();
    if (text.includes(query)) {
      item.style.display = "block";
    } else {
      item.style.display = "none";
    }
  });
}

function openAddModal() {
  const modal = document.getElementById("addModal");
  modal.classList.remove("hidden");
  modal.classList.add("flex");
}

function closeAddModal() {
  const modal = document.getElementById("addModal");
  modal.classList.add("hidden");
  modal.classList.remove("flex");
  document.getElementById("addMarkerForm").reset();
}

function addMarker(event) {
  event.preventDefault();

  const formData = new FormData(event.target);
  const data = {
    name: formData.get("name"),
    type: formData.get("type"),
    latitude: parseFloat(formData.get("latitude")),
    longitude: parseFloat(formData.get("longitude")),
    description: formData.get("description"),
  };

  fetch("api.php?action=add_marker", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  })
    .then((response) => response.json())
    .then((result) => {
      if (result.success) {
        closeAddModal();
        loadDataFromServer();
        alert("Marker berhasil ditambahkan!");
      } else {
        alert("Error: " + result.message);
      }
    })
    .catch((error) => {
      alert("Error: " + error.message);
    });
}

function deleteMarker(id) {
  if (!confirm("Hapus marker ini?")) return;

  fetch("api.php?action=delete_marker", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      id: id,
    }),
  })
    .then((response) => response.json())
    .then((result) => {
      if (result.success) {
        loadDataFromServer();
        alert("Marker berhasil dihapus!");
      } else {
        alert("Error: " + result.message);
      }
    })
    .catch((error) => {
      alert("Error: " + error.message);
    });
}

window.onload = function () {
  initMap();
};
