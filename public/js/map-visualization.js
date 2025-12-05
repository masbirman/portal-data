/**
 * Map Visualization Module for Sulawesi Tengah Assessment Data
 *
 * This module provides functions for initializing and managing an interactive
 * Leaflet map with GeoJSON polygon visualization for kabupaten/kota data.
 */

// Color palette for polygon fills
const POLYGON_COLORS = [
    "#3b82f6", // blue
    "#10b981", // emerald
    "#f59e0b", // amber
    "#ef4444", // red
    "#8b5cf6", // violet
    "#ec4899", // pink
    "#06b6d4", // cyan
    "#84cc16", // lime
    "#f97316", // orange
    "#6366f1", // indigo
    "#14b8a6", // teal
    "#a855f7", // purple
    "#22c55e", // green
];

const UNMATCHED_COLOR = "#9ca3af"; // gray-400

/**
 * Initialize a Leaflet map centered on Sulawesi Tengah
 * @param {string} containerId - The DOM element ID for the map container
 * @param {Object} options - Optional configuration options
 * @returns {L.Map} The initialized Leaflet map instance
 */
function initMap(containerId, options = {}) {
    const defaultOptions = {
        center: [-1.0, 121.0],
        zoom: 7,
        tileUrl: "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
        attribution: "© OpenStreetMap contributors",
    };

    const config = { ...defaultOptions, ...options };

    const map = L.map(containerId).setView(config.center, config.zoom);

    L.tileLayer(config.tileUrl, {
        attribution: config.attribution,
        maxZoom: 18,
    }).addTo(map);

    return map;
}

/**
 * Match a GeoJSON feature to wilayah data using kode_wilayah
 * Supports multiple GeoJSON formats:
 * - Format 1: kode_provinsi + kode_kabkota (e.g., "72" + "01")
 * - Format 2: code with dot notation (e.g., "72.01" -> "7201")
 * @param {Object} feature - GeoJSON feature object
 * @param {Array} wilayahData - Array of wilayah data objects
 * @returns {Object|null} Matched wilayah object or null if no match
 */
function matchWilayahToFeature(feature, wilayahData) {
    if (
        !feature ||
        !feature.properties ||
        !wilayahData ||
        !Array.isArray(wilayahData)
    ) {
        return null;
    }

    const featureProps = feature.properties;

    // Method 1: Build kode_wilayah from kode_provinsi + kode_kabkota (old format)
    let featureKodeWilayah = null;
    if (featureProps.kode_provinsi && featureProps.kode_kabkota) {
        featureKodeWilayah =
            featureProps.kode_provinsi + featureProps.kode_kabkota;
    }

    // Method 2: Convert "code" with dot notation to kode_wilayah (new format)
    // e.g., "72.01" -> "7201", "72.71" -> "7271"
    if (!featureKodeWilayah && featureProps.code) {
        featureKodeWilayah = featureProps.code.replace(".", "");
    }

    // Try matching by kode_wilayah first
    if (featureKodeWilayah) {
        const matchByKode = wilayahData.find(
            (w) => w.kode_wilayah === featureKodeWilayah
        );
        if (matchByKode) {
            return matchByKode;
        }
    }

    // Fallback: try matching by nama/name (case-insensitive, partial match)
    const featureName = (featureProps.nama || featureProps.name || "")
        .toLowerCase()
        .trim();
    if (featureName) {
        const matchByName = wilayahData.find((w) => {
            const wilayahName = (w.nama || "").toLowerCase().trim();
            // Normalize names: remove "kabupaten " or "kota " prefix for comparison
            const normalizedFeatureName = featureName.replace(
                /^(kabupaten|kota)\s+/i,
                ""
            );
            const normalizedWilayahName = wilayahName.replace(
                /^(kabupaten|kota)\s+/i,
                ""
            );
            return (
                normalizedWilayahName === normalizedFeatureName ||
                normalizedWilayahName.includes(normalizedFeatureName) ||
                normalizedFeatureName.includes(normalizedWilayahName)
            );
        });
        if (matchByName) {
            return matchByName;
        }
    }

    return null;
}

/**
 * Get polygon style based on feature and wilayah data
 * @param {Object} _feature - GeoJSON feature object (unused, kept for API consistency)
 * @param {Object|null} wilayah - Matched wilayah data or null
 * @param {number} index - Feature index for color assignment
 * @returns {Object} Leaflet style object
 */
function getPolygonStyle(_feature, wilayah, index = 0) {
    const isMatched = wilayah !== null;
    const fillColor = isMatched
        ? POLYGON_COLORS[index % POLYGON_COLORS.length]
        : UNMATCHED_COLOR;

    return {
        fillColor: fillColor,
        weight: 2,
        opacity: 1,
        color: "#ffffff",
        fillOpacity: 0.6,
    };
}

/**
 * Get highlight style for polygon on hover
 * Increases border width and fill opacity for visual feedback
 * @returns {Object} Leaflet style object for highlighted state
 */
function getHighlightStyle() {
    return {
        weight: 4, // Increased from default 2 (Requirement 2.1)
        opacity: 1,
        color: "#ffffff", // Keep white border
        fillOpacity: 0.85, // Increased opacity for highlight (Requirement 2.1)
    };
}

/**
 * Bind hover event handlers to a polygon layer
 * Implements mouseover highlight and mouseout restore functionality
 * @param {L.Layer} layer - The Leaflet layer to bind events to
 * @param {L.GeoJSON} geojsonLayer - The parent GeoJSON layer for style reset
 */
function bindHoverEvents(layer, geojsonLayer) {
    layer.on({
        mouseover: function (e) {
            const targetLayer = e.target;
            // Apply highlight style (Requirement 2.1)
            targetLayer.setStyle(getHighlightStyle());
            // Bring to front so borders are visible
            targetLayer.bringToFront();
        },
        mouseout: function (e) {
            // Restore default style (Requirement 2.3)
            geojsonLayer.resetStyle(e.target);
        },
    });
}

/**
 * Bind click event handler to a polygon layer
 * Opens popup and optionally fits bounds
 * @param {L.Layer} layer - The Leaflet layer to bind events to
 * @param {L.Map} map - The Leaflet map instance
 */
function bindClickEvents(layer, map) {
    layer.on({
        click: function (e) {
            // Open popup centered above the polygon (Requirement 3.3)
            // The popup is already bound, so clicking will open it automatically
            // Optionally fit bounds to show the full polygon
            const bounds = e.target.getBounds();
            map.fitBounds(bounds, {
                padding: [50, 50],
                maxZoom: 10, // Don't zoom in too much
            });
        },
    });
}

/**
 * Load GeoJSON data and render polygons on the map
 * Implements fallback behavior for missing GeoJSON or unmatched wilayah (Requirement 1.4)
 * @param {L.Map} map - Leaflet map instance
 * @param {string} geojsonUrl - URL to the GeoJSON file
 * @param {Array} wilayahData - Array of wilayah data objects
 * @param {string} tahun - Current year for navigation links
 * @returns {Promise<L.GeoJSON|null>} GeoJSON layer or null on failure
 */
async function loadGeoJSON(map, geojsonUrl, wilayahData, tahun) {
    try {
        const response = await fetch(geojsonUrl);
        if (!response.ok) {
            throw new Error(`Failed to load GeoJSON: ${response.status}`);
        }

        const geojsonData = await response.json();

        let featureIndex = 0;

        // Track which wilayah IDs have been matched to polygons
        const matchedWilayahIds = new Set();

        // Store layers for post-processing hover events
        const layersToProcess = [];

        const geojsonLayer = L.geoJSON(geojsonData, {
            style: function (feature) {
                const wilayah = matchWilayahToFeature(feature, wilayahData);
                return getPolygonStyle(feature, wilayah, featureIndex++);
            },
            onEachFeature: function (feature, layer) {
                const wilayah = matchWilayahToFeature(feature, wilayahData);

                // Track matched wilayah for fallback rendering
                if (wilayah && wilayah.id) {
                    matchedWilayahIds.add(wilayah.id);
                }

                // Bind tooltip - shows on hover (Requirement 2.2)
                const tooltipContent = wilayah
                    ? createTooltipContent(wilayah)
                    : feature.properties.name ||
                      feature.properties.nama ||
                      "Unknown";
                layer.bindTooltip(tooltipContent, {
                    permanent: false,
                    direction: "top",
                    className: "map-tooltip",
                    sticky: true, // Tooltip follows mouse
                });

                // Bind popup - shows on click (Requirement 3.1)
                if (wilayah) {
                    const popupContent = createPopupContent(wilayah, tahun);
                    layer.bindPopup(popupContent, {
                        maxWidth: 300,
                        className: "map-popup",
                        autoPan: true, // Auto-pan to show popup
                        autoPanPadding: [50, 50], // Padding for auto-pan
                    });
                }

                // Store layer for post-processing (hover events need geojsonLayer reference)
                layersToProcess.push(layer);

                // Bind click events
                bindClickEvents(layer, map);
            },
        }).addTo(map);

        // Bind hover events after geojsonLayer is created
        layersToProcess.forEach((layer) => {
            bindHoverEvents(layer, geojsonLayer);
        });

        // Render markers at ibukota (capital) coordinates for matched wilayah
        const matchedWilayah = wilayahData.filter((w) =>
            matchedWilayahIds.has(w.id)
        );
        renderIbukotaMarkers(map, matchedWilayah, tahun);

        // Render circle markers for unmatched wilayah (Requirement 1.4)
        // Filter wilayah that were not matched to any polygon
        const unmatchedWilayah = wilayahData.filter(
            (w) => !matchedWilayahIds.has(w.id)
        );
        if (unmatchedWilayah.length > 0) {
            console.warn(
                `Rendering ${unmatchedWilayah.length} unmatched wilayah as circle markers`
            );
            renderFallbackMarkers(map, unmatchedWilayah, tahun);
        }

        return geojsonLayer;
    } catch (error) {
        console.error("Error loading GeoJSON:", error);
        // Fallback: render all wilayah as circle markers when GeoJSON fails (Requirement 1.4)
        console.warn(
            "GeoJSON load failed, rendering fallback circle markers for all wilayah"
        );
        renderFallbackMarkers(map, wilayahData, tahun);
        return null;
    }
}

/**
 * Create popup HTML content for a wilayah
 * @param {Object} wilayah - Wilayah data object
 * @param {string} tahun - Current year for navigation link
 * @returns {string} HTML string for popup content
 */
function createPopupContent(wilayah, tahun) {
    if (!wilayah) {
        return '<div style="color: #6b7280;">Data tidak tersedia</div>';
    }

    const nama = wilayah.nama || "Unknown";
    const logo = wilayah.logo || null;
    const totalSekolah = wilayah.total_sekolah ?? 0;
    const totalPeserta = wilayah.total_peserta ?? 0;
    const statusMandiri = wilayah.status_mandiri ?? 0;
    const statusMenumpang = wilayah.status_menumpang ?? 0;
    const url = generateWilayahUrl(tahun, wilayah.id);

    // Logo HTML - small logo (28x28) next to the name
    const logoHtml = logo
        ? `<img src="/storage/${escapeHtml(
              logo
          )}" alt="" style="width: 28px; height: 28px; border-radius: 4px; object-fit: contain; background: white; margin-right: 10px; flex-shrink: 0;">`
        : `<div style="width: 28px; height: 28px; border-radius: 4px; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; margin-right: 10px; flex-shrink: 0; font-size: 11px; font-weight: bold;">${escapeHtml(
              nama.substring(0, 2)
          )}</div>`;

    return `
        <div style="min-width: 220px; padding: 4px; font-family: system-ui, -apple-system, sans-serif;">
            <div style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); color: white; padding: 10px 12px; margin: -12px -12px 12px -12px; border-radius: 8px 8px 0 0; display: flex; align-items: center;">
                ${logoHtml}
                <h3 style="font-weight: bold; font-size: 15px; margin: 0; line-height: 1.2;">${escapeHtml(
                    nama
                )}</h3>
            </div>
            <div style="font-size: 13px; line-height: 1.6;">
                <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #e5e7eb;">
                    <span style="color: #6b7280;">Sekolah</span>
                    <strong style="color: #1f2937;">${formatNumber(
                        totalSekolah
                    )}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #e5e7eb;">
                    <span style="color: #6b7280;">Peserta</span>
                    <strong style="color: #1f2937;">${formatNumber(
                        totalPeserta
                    )}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #e5e7eb;">
                    <span style="color: #6b7280;">Mandiri</span>
                    <strong style="color: #16a34a;">${formatNumber(
                        statusMandiri
                    )}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 6px 0;">
                    <span style="color: #6b7280;">Menumpang</span>
                    <strong style="color: #ca8a04;">${formatNumber(
                        statusMenumpang
                    )}</strong>
                </div>
            </div>
            <a href="${url}"
               style="display: block; text-align: center; background-color: #2563eb; color: white; padding: 10px 12px; border-radius: 6px; font-size: 14px; text-decoration: none; margin-top: 12px; font-weight: 500; transition: background-color 0.2s;"
               onmouseover="this.style.backgroundColor='#1d4ed8'"
               onmouseout="this.style.backgroundColor='#2563eb'">
                Lihat Detail →
            </a>
        </div>
    `;
}

/**
 * Create tooltip HTML content for a wilayah
 * @param {Object} wilayah - Wilayah data object
 * @returns {string} HTML string for tooltip content
 */
function createTooltipContent(wilayah) {
    if (!wilayah) {
        return "Data tidak tersedia";
    }

    const nama = wilayah.nama || "Unknown";
    const totalSekolah = wilayah.total_sekolah ?? 0;
    const totalPeserta = wilayah.total_peserta ?? 0;
    const statusMandiri = wilayah.status_mandiri ?? 0;
    const statusMenumpang = wilayah.status_menumpang ?? 0;

    return `
        <div style="min-width: 180px; padding: 8px; font-family: system-ui, -apple-system, sans-serif;">
            <div style="font-weight: bold; font-size: 14px; color: #1f2937; margin-bottom: 8px; border-bottom: 1px solid #e5e7eb; padding-bottom: 6px;">
                ${escapeHtml(nama)}
            </div>
            <div style="font-size: 13px; line-height: 1.5;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                    <span style="color: #6b7280;">Sekolah:</span>
                    <strong style="color: #1f2937;">${formatNumber(
                        totalSekolah
                    )}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                    <span style="color: #6b7280;">Peserta:</span>
                    <strong style="color: #1f2937;">${formatNumber(
                        totalPeserta
                    )}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                    <span style="color: #6b7280;">Mandiri:</span>
                    <strong style="color: #16a34a;">${formatNumber(
                        statusMandiri
                    )}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #6b7280;">Menumpang:</span>
                    <strong style="color: #ca8a04;">${formatNumber(
                        statusMenumpang
                    )}</strong>
                </div>
            </div>
        </div>
    `;
}

/**
 * Generate URL for wilayah detail page
 * @param {string} tahun - Year parameter (4-digit)
 * @param {number|string} wilayahId - Wilayah ID
 * @returns {string} URL string
 */
function generateWilayahUrl(tahun, wilayahId) {
    return `/asesmen-nasional/${tahun}/wilayah/${wilayahId}`;
}

/**
 * Escape HTML special characters to prevent XSS
 * @param {string} text - Text to escape
 * @returns {string} Escaped text
 */
function escapeHtml(text) {
    if (typeof text !== "string") {
        return String(text);
    }
    const div = document.createElement("div");
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Format number with locale-specific separators
 * @param {number} num - Number to format
 * @returns {string} Formatted number string
 */
function formatNumber(num) {
    if (typeof num !== "number" || isNaN(num)) {
        return "0";
    }
    return num.toLocaleString("id-ID");
}

/**
 * Create a pulsing pin marker icon
 * @param {string} color - Color for the pin
 * @returns {L.DivIcon} Custom div icon with pulse animation
 */
function createPulsingPinIcon(color = "#3b82f6") {
    return L.divIcon({
        className: "pulsing-pin-container",
        html: `
            <div class="pulsing-pin" style="--pin-color: ${color};">
                <div class="pin-pulse"></div>
                <div class="pin-icon">
                    <svg viewBox="0 0 24 24" width="32" height="32" fill="${color}">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                    </svg>
                </div>
            </div>
        `,
        iconSize: [32, 42],
        iconAnchor: [16, 42],
        popupAnchor: [0, -42],
    });
}

/**
 * Render markers at ibukota (capital city) coordinates for each wilayah
 * These markers are displayed on top of the polygon layer
 * @param {L.Map} map - Leaflet map instance
 * @param {Array} wilayahData - Array of wilayah data objects with latitude/longitude
 * @param {string} tahun - Current year for navigation links
 */
function renderIbukotaMarkers(map, wilayahData, tahun) {
    if (!wilayahData || !Array.isArray(wilayahData)) {
        return;
    }

    // Add CSS for pulsing animation if not already added
    addPulsingPinStyles();

    // Color palette for different wilayah
    const pinColors = [
        "#3b82f6",
        "#10b981",
        "#f59e0b",
        "#ef4444",
        "#8b5cf6",
        "#ec4899",
        "#06b6d4",
        "#84cc16",
        "#f97316",
        "#6366f1",
        "#14b8a6",
        "#a855f7",
        "#22c55e",
    ];

    wilayahData.forEach(function (wilayah, index) {
        if (wilayah.latitude && wilayah.longitude) {
            const color = pinColors[index % pinColors.length];
            const icon = createPulsingPinIcon(color);

            const marker = L.marker([wilayah.latitude, wilayah.longitude], {
                icon: icon,
                zIndexOffset: 1000, // Ensure markers are above polygons
            }).addTo(map);

            marker.bindPopup(createPopupContent(wilayah, tahun), {
                maxWidth: 300,
                className: "map-popup",
            });

            marker.bindTooltip(createTooltipContent(wilayah), {
                permanent: false,
                direction: "top",
                className: "map-tooltip",
                offset: [0, -42],
            });
        }
    });
}

/**
 * Add CSS styles for pulsing pin animation
 */
function addPulsingPinStyles() {
    if (document.getElementById("pulsing-pin-styles")) {
        return; // Already added
    }

    const style = document.createElement("style");
    style.id = "pulsing-pin-styles";
    style.textContent = `
        .pulsing-pin-container {
            background: transparent !important;
            border: none !important;
        }
        .pulsing-pin {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .pin-icon {
            position: relative;
            z-index: 2;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
        }
        .pin-pulse {
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 20px;
            height: 20px;
            background-color: var(--pin-color, #3b82f6);
            border-radius: 50%;
            opacity: 0.6;
            animation: pulse 2s ease-out infinite;
            z-index: 1;
        }
        @keyframes pulse {
            0% {
                transform: translateX(-50%) scale(0.5);
                opacity: 0.8;
            }
            50% {
                opacity: 0.4;
            }
            100% {
                transform: translateX(-50%) scale(2.5);
                opacity: 0;
            }
        }
        .pulsing-pin:hover .pin-icon svg {
            transform: scale(1.1);
            transition: transform 0.2s ease;
        }
    `;
    document.head.appendChild(style);
}

/**
 * Render fallback markers with pulsing pin icons when GeoJSON fails
 * @param {L.Map} map - Leaflet map instance
 * @param {Array} wilayahData - Array of wilayah data objects
 * @param {string} tahun - Current year for navigation links
 */
function renderFallbackMarkers(map, wilayahData, tahun) {
    if (!wilayahData || !Array.isArray(wilayahData)) {
        return;
    }

    // Add CSS for pulsing animation
    addPulsingPinStyles();

    // Color palette for different wilayah
    const pinColors = [
        "#3b82f6",
        "#10b981",
        "#f59e0b",
        "#ef4444",
        "#8b5cf6",
        "#ec4899",
        "#06b6d4",
        "#84cc16",
        "#f97316",
        "#6366f1",
        "#14b8a6",
        "#a855f7",
        "#22c55e",
    ];

    wilayahData.forEach(function (wilayah, index) {
        if (wilayah.latitude && wilayah.longitude) {
            const color = pinColors[index % pinColors.length];
            const icon = createPulsingPinIcon(color);

            const marker = L.marker([wilayah.latitude, wilayah.longitude], {
                icon: icon,
            }).addTo(map);

            marker.bindPopup(createPopupContent(wilayah, tahun), {
                maxWidth: 300,
                className: "map-popup",
            });

            marker.bindTooltip(createTooltipContent(wilayah), {
                permanent: false,
                direction: "top",
                className: "map-tooltip",
                offset: [0, -42],
            });
        }
    });
}

// Export functions for use in other modules and testing
if (typeof module !== "undefined" && module.exports) {
    module.exports = {
        initMap,
        loadGeoJSON,
        matchWilayahToFeature,
        getPolygonStyle,
        getHighlightStyle,
        bindHoverEvents,
        bindClickEvents,
        createPopupContent,
        createTooltipContent,
        generateWilayahUrl,
        renderIbukotaMarkers,
        renderFallbackMarkers,
        addPulsingPinStyles,
        escapeHtml,
        formatNumber,
        POLYGON_COLORS,
        UNMATCHED_COLOR,
    };
}

// Also expose to window for browser usage
if (typeof window !== "undefined") {
    window.MapVisualization = {
        initMap,
        loadGeoJSON,
        matchWilayahToFeature,
        getPolygonStyle,
        getHighlightStyle,
        bindHoverEvents,
        bindClickEvents,
        createPopupContent,
        createTooltipContent,
        generateWilayahUrl,
        renderIbukotaMarkers,
        renderFallbackMarkers,
        addPulsingPinStyles,
        escapeHtml,
        formatNumber,
        POLYGON_COLORS,
        UNMATCHED_COLOR,
    };
}
