/**
 * Map Visualization Module for Sulawesi Tengah Assessment Data
 * 
 * This module provides functions for initializing and managing an interactive
 * Leaflet map with GeoJSON polygon visualization for kabupaten/kota data.
 */

// Color palette for polygon fills
const POLYGON_COLORS = [
    '#3b82f6', // blue
    '#10b981', // emerald
    '#f59e0b', // amber
    '#ef4444', // red
    '#8b5cf6', // violet
    '#ec4899', // pink
    '#06b6d4', // cyan
    '#84cc16', // lime
    '#f97316', // orange
    '#6366f1', // indigo
    '#14b8a6', // teal
    '#a855f7', // purple
    '#22c55e', // green
];

const UNMATCHED_COLOR = '#9ca3af'; // gray-400

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
        tileUrl: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        attribution: '© OpenStreetMap contributors'
    };

    const config = { ...defaultOptions, ...options };

    const map = L.map(containerId).setView(config.center, config.zoom);

    L.tileLayer(config.tileUrl, {
        attribution: config.attribution,
        maxZoom: 18
    }).addTo(map);

    return map;
}

/**
 * Match a GeoJSON feature to wilayah data using kode_wilayah
 * @param {Object} feature - GeoJSON feature object
 * @param {Array} wilayahData - Array of wilayah data objects
 * @returns {Object|null} Matched wilayah object or null if no match
 */
function matchWilayahToFeature(feature, wilayahData) {
    if (!feature || !feature.properties || !wilayahData || !Array.isArray(wilayahData)) {
        return null;
    }

    const featureProps = feature.properties;
    
    // Build kode_wilayah from kode_provinsi + kode_kabkota
    const featureKodeWilayah = featureProps.kode_provinsi && featureProps.kode_kabkota
        ? featureProps.kode_provinsi + featureProps.kode_kabkota
        : null;

    // Try matching by kode_wilayah first
    if (featureKodeWilayah) {
        const matchByKode = wilayahData.find(w => w.kode_wilayah === featureKodeWilayah);
        if (matchByKode) {
            return matchByKode;
        }
    }

    // Fallback: try matching by nama (case-insensitive, partial match)
    const featureName = (featureProps.nama || '').toLowerCase().trim();
    if (featureName) {
        const matchByName = wilayahData.find(w => {
            const wilayahName = (w.nama || '').toLowerCase().trim();
            return wilayahName === featureName || 
                   wilayahName.includes(featureName) || 
                   featureName.includes(wilayahName);
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
        color: '#ffffff',
        fillOpacity: 0.6
    };
}


/**
 * Get highlight style for polygon on hover
 * Increases border width and fill opacity for visual feedback
 * @returns {Object} Leaflet style object for highlighted state
 */
function getHighlightStyle() {
    return {
        weight: 4,           // Increased from default 2 (Requirement 2.1)
        opacity: 1,
        color: '#ffffff',    // Keep white border
        fillOpacity: 0.85    // Increased opacity for highlight (Requirement 2.1)
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
        mouseover: function(e) {
            const targetLayer = e.target;
            // Apply highlight style (Requirement 2.1)
            targetLayer.setStyle(getHighlightStyle());
            // Bring to front so borders are visible
            targetLayer.bringToFront();
        },
        mouseout: function(e) {
            // Restore default style (Requirement 2.3)
            geojsonLayer.resetStyle(e.target);
        }
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
        click: function(e) {
            // Open popup centered above the polygon (Requirement 3.3)
            // The popup is already bound, so clicking will open it automatically
            // Optionally fit bounds to show the full polygon
            const bounds = e.target.getBounds();
            map.fitBounds(bounds, { 
                padding: [50, 50],
                maxZoom: 10  // Don't zoom in too much
            });
        }
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
        
        const geojsonLayer = L.geoJSON(geojsonData, {
            style: function(feature) {
                const wilayah = matchWilayahToFeature(feature, wilayahData);
                return getPolygonStyle(feature, wilayah, featureIndex++);
            },
            onEachFeature: function(feature, layer) {
                const wilayah = matchWilayahToFeature(feature, wilayahData);
                
                // Track matched wilayah for fallback rendering
                if (wilayah && wilayah.id) {
                    matchedWilayahIds.add(wilayah.id);
                }
                
                // Bind tooltip - shows on hover (Requirement 2.2)
                const tooltipContent = wilayah 
                    ? createTooltipContent(wilayah)
                    : feature.properties.nama || 'Unknown';
                layer.bindTooltip(tooltipContent, {
                    permanent: false,
                    direction: 'top',
                    className: 'map-tooltip',
                    sticky: true  // Tooltip follows mouse
                });

                // Bind popup - shows on click (Requirement 3.1)
                if (wilayah) {
                    const popupContent = createPopupContent(wilayah, tahun);
                    layer.bindPopup(popupContent, {
                        maxWidth: 300,
                        className: 'map-popup',
                        autoPan: true,           // Auto-pan to show popup
                        autoPanPadding: [50, 50] // Padding for auto-pan
                    });
                }

                // Bind hover events using helper function (Requirements 2.1, 2.3)
                bindHoverEvents(layer, geojsonLayer);
                
                // Bind click events using helper function (Requirements 3.1, 3.2, 3.3)
                bindClickEvents(layer, map);
            }
        }).addTo(map);

        // Render circle markers for unmatched wilayah (Requirement 1.4)
        // Filter wilayah that were not matched to any polygon
        const unmatchedWilayah = wilayahData.filter(w => !matchedWilayahIds.has(w.id));
        if (unmatchedWilayah.length > 0) {
            console.warn(`Rendering ${unmatchedWilayah.length} unmatched wilayah as circle markers`);
            renderFallbackMarkers(map, unmatchedWilayah, tahun);
        }

        return geojsonLayer;
    } catch (error) {
        console.error('Error loading GeoJSON:', error);
        // Fallback: render all wilayah as circle markers when GeoJSON fails (Requirement 1.4)
        console.warn('GeoJSON load failed, rendering fallback circle markers for all wilayah');
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
        return '<div class="text-gray-500">Data tidak tersedia</div>';
    }

    const nama = wilayah.nama || 'Unknown';
    const totalSekolah = wilayah.total_sekolah ?? 0;
    const totalPeserta = wilayah.total_peserta ?? 0;
    const statusMandiri = wilayah.status_mandiri ?? 0;
    const statusMenumpang = wilayah.status_menumpang ?? 0;
    const url = generateWilayahUrl(tahun, wilayah.id);

    return `
        <div class="min-w-[200px]">
            <h3 class="font-bold text-base mb-2">${escapeHtml(nama)}</h3>
            <div class="text-sm space-y-1">
                <p><span class="text-gray-600">Sekolah:</span> <strong>${formatNumber(totalSekolah)}</strong></p>
                <p><span class="text-gray-600">Peserta:</span> <strong>${formatNumber(totalPeserta)}</strong></p>
                <p><span class="text-gray-600">Mandiri:</span> <strong class="text-green-600">${formatNumber(statusMandiri)}</strong></p>
                <p><span class="text-gray-600">Menumpang:</span> <strong class="text-yellow-600">${formatNumber(statusMenumpang)}</strong></p>
            </div>
            <a href="${url}" 
               class="mt-3 block text-center bg-blue-600 text-white px-3 py-2 rounded text-sm hover:bg-blue-700">
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
        return 'Data tidak tersedia';
    }

    const nama = wilayah.nama || 'Unknown';
    const totalSekolah = wilayah.total_sekolah ?? 0;
    const totalPeserta = wilayah.total_peserta ?? 0;

    return `
        <div class="text-sm">
            <strong>${escapeHtml(nama)}</strong><br>
            Sekolah: ${formatNumber(totalSekolah)} | Peserta: ${formatNumber(totalPeserta)}
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
    if (typeof text !== 'string') {
        return String(text);
    }
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Format number with locale-specific separators
 * @param {number} num - Number to format
 * @returns {string} Formatted number string
 */
function formatNumber(num) {
    if (typeof num !== 'number' || isNaN(num)) {
        return '0';
    }
    return num.toLocaleString('id-ID');
}

/**
 * Render fallback circle markers when GeoJSON fails
 * @param {L.Map} map - Leaflet map instance
 * @param {Array} wilayahData - Array of wilayah data objects
 * @param {string} tahun - Current year for navigation links
 */
function renderFallbackMarkers(map, wilayahData, tahun) {
    if (!wilayahData || !Array.isArray(wilayahData)) {
        return;
    }

    wilayahData.forEach(function(wilayah) {
        if (wilayah.latitude && wilayah.longitude) {
            const marker = L.circleMarker([wilayah.latitude, wilayah.longitude], {
                radius: 12,
                fillColor: '#3b82f6',
                color: '#fff',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.8
            }).addTo(map);

            marker.bindPopup(createPopupContent(wilayah, tahun));
            marker.bindTooltip(wilayah.nama, {
                permanent: false,
                direction: 'top'
            });
        }
    });
}

// Export functions for use in other modules and testing
if (typeof module !== 'undefined' && module.exports) {
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
        renderFallbackMarkers,
        escapeHtml,
        formatNumber,
        POLYGON_COLORS,
        UNMATCHED_COLOR
    };
}

// Also expose to window for browser usage
if (typeof window !== 'undefined') {
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
        renderFallbackMarkers,
        escapeHtml,
        formatNumber,
        POLYGON_COLORS,
        UNMATCHED_COLOR
    };
}
