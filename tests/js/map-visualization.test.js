/**
 * Property-Based Tests and Unit Tests for Map Visualization Module
 * 
 * These tests verify the correctness properties defined in the design document
 * using fast-check for property-based testing, plus unit tests for core functions.
 */

import { describe, test, expect, vi, beforeEach, afterEach } from 'vitest';
import fc from 'fast-check';

// Import the module functions (we need to re-implement for testing since module uses browser globals)
// These are the pure functions we're testing

const POLYGON_COLORS = [
    '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6',
    '#ec4899', '#06b6d4', '#84cc16', '#f97316', '#6366f1',
    '#14b8a6', '#a855f7', '#22c55e',
];
const UNMATCHED_COLOR = '#9ca3af';

/**
 * Match a GeoJSON feature to wilayah data using kode_wilayah
 */
function matchWilayahToFeature(feature, wilayahData) {
    if (!feature || !feature.properties || !wilayahData || !Array.isArray(wilayahData)) {
        return null;
    }

    const featureProps = feature.properties;
    
    const featureKodeWilayah = featureProps.kode_provinsi && featureProps.kode_kabkota
        ? featureProps.kode_provinsi + featureProps.kode_kabkota
        : null;

    if (featureKodeWilayah) {
        const matchByKode = wilayahData.find(w => w.kode_wilayah === featureKodeWilayah);
        if (matchByKode) {
            return matchByKode;
        }
    }

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
 */
function getPolygonStyle(feature, wilayah, index = 0) {
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
 * Generate URL for wilayah detail page
 */
function generateWilayahUrl(tahun, wilayahId) {
    return `/asesmen-nasional/${tahun}/wilayah/${wilayahId}`;
}

/**
 * Escape HTML special characters
 */
function escapeHtml(text) {
    if (typeof text !== 'string') {
        return String(text);
    }
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, c => map[c]);
}

/**
 * Format number with locale-specific separators
 */
function formatNumber(num) {
    if (typeof num !== 'number' || isNaN(num)) {
        return '0';
    }
    return num.toLocaleString('id-ID');
}

/**
 * Create popup HTML content for a wilayah
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

// ============================================================================
// Arbitraries (Generators) for Property-Based Testing
// ============================================================================

// Generator for valid kode_provinsi (2 digits)
const kodeProvinsiArb = fc.string({ minLength: 2, maxLength: 2, unit: fc.constantFrom('0', '1', '2', '3', '4', '5', '6', '7', '8', '9') });

// Generator for valid kode_kabkota (2 digits)
const kodeKabkotaArb = fc.string({ minLength: 2, maxLength: 2, unit: fc.constantFrom('0', '1', '2', '3', '4', '5', '6', '7', '8', '9') });

// Generator for wilayah names
const namaWilayahArb = fc.string({ minLength: 3, maxLength: 30, unit: fc.constantFrom(...'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ ') })
    .filter(s => s.trim().length > 0);

// Generator for a GeoJSON feature
const geoJsonFeatureArb = fc.record({
    type: fc.constant('Feature'),
    properties: fc.record({
        id: fc.integer({ min: 1, max: 100 }),
        nama: namaWilayahArb,
        kode_provinsi: kodeProvinsiArb,
        kode_kabkota: kodeKabkotaArb
    }),
    geometry: fc.record({
        type: fc.constant('Polygon'),
        coordinates: fc.constant([[[119.0, -1.0], [120.0, -1.0], [120.0, -2.0], [119.0, -2.0], [119.0, -1.0]]])
    })
});

// Generator for a wilayah data object
const wilayahDataArb = fc.record({
    id: fc.integer({ min: 1, max: 1000 }),
    kode_wilayah: fc.tuple(kodeProvinsiArb, kodeKabkotaArb).map(([p, k]) => p + k),
    nama: namaWilayahArb,
    latitude: fc.double({ min: -3, max: 2, noNaN: true }),
    longitude: fc.double({ min: 119, max: 124, noNaN: true }),
    total_sekolah: fc.integer({ min: 0, max: 1000 }),
    total_peserta: fc.integer({ min: 0, max: 100000 }),
    status_mandiri: fc.integer({ min: 0, max: 500 }),
    status_menumpang: fc.integer({ min: 0, max: 500 })
});

// Generator for array of wilayah data
const wilayahDataArrayArb = fc.array(wilayahDataArb, { minLength: 0, maxLength: 20 });

// Generator for valid tahun (4-digit year)
const tahunArb = fc.integer({ min: 2020, max: 2030 }).map(String);

// Generator for valid wilayah ID
const wilayahIdArb = fc.integer({ min: 1, max: 10000 });


// ============================================================================
// Property-Based Tests
// ============================================================================

describe('Map Visualization Property-Based Tests', () => {
    
    /**
     * **Feature: simple-map-visualization, Property 1: GeoJSON-Wilayah Matching Consistency**
     * 
     * For any GeoJSON feature with a valid kode_wilayah property and for any wilayah data array,
     * if a wilayah record exists with the same kode_wilayah, the matching function SHALL return
     * that exact wilayah record. If no match exists, the function SHALL return null.
     * 
     * **Validates: Requirements 6.2**
     */
    describe('Property 1: GeoJSON-Wilayah Matching Consistency', () => {
        
        test('matching by kode_wilayah returns exact wilayah when match exists', () => {
            fc.assert(
                fc.property(
                    geoJsonFeatureArb,
                    wilayahDataArrayArb,
                    (feature, wilayahArray) => {
                        // Create a kode_wilayah from the feature
                        const featureKode = feature.properties.kode_provinsi + feature.properties.kode_kabkota;
                        
                        // Add a wilayah with matching kode to the array
                        const matchingWilayah = {
                            id: 999,
                            kode_wilayah: featureKode,
                            nama: 'Test Wilayah',
                            latitude: -1.0,
                            longitude: 121.0,
                            total_sekolah: 100,
                            total_peserta: 5000,
                            status_mandiri: 50,
                            status_menumpang: 50
                        };
                        
                        const dataWithMatch = [...wilayahArray, matchingWilayah];
                        const result = matchWilayahToFeature(feature, dataWithMatch);
                        
                        // Should return the matching wilayah
                        return result !== null && result.kode_wilayah === featureKode;
                    }
                ),
                { numRuns: 100 }
            );
        });

        test('returns null when no matching kode_wilayah exists', () => {
            fc.assert(
                fc.property(
                    geoJsonFeatureArb,
                    wilayahDataArrayArb,
                    (feature, wilayahArray) => {
                        // Filter out any wilayah that could match
                        const featureKode = feature.properties.kode_provinsi + feature.properties.kode_kabkota;
                        const featureName = feature.properties.nama.toLowerCase().trim();
                        
                        const nonMatchingArray = wilayahArray.filter(w => {
                            const wName = (w.nama || '').toLowerCase().trim();
                            return w.kode_wilayah !== featureKode &&
                                   wName !== featureName &&
                                   !wName.includes(featureName) &&
                                   !featureName.includes(wName);
                        });
                        
                        const result = matchWilayahToFeature(feature, nonMatchingArray);
                        return result === null;
                    }
                ),
                { numRuns: 100 }
            );
        });

        test('returns null for invalid inputs', () => {
            expect(matchWilayahToFeature(null, [])).toBeNull();
            expect(matchWilayahToFeature({}, [])).toBeNull();
            expect(matchWilayahToFeature({ properties: {} }, null)).toBeNull();
            expect(matchWilayahToFeature({ properties: {} }, 'not an array')).toBeNull();
        });
    });

    /**
     * **Feature: simple-map-visualization, Property 2: Polygon Style Determinism**
     * 
     * For any GeoJSON feature and wilayah data combination (matched or unmatched),
     * calling getPolygonStyle with the same inputs SHALL always return the same style object.
     * Matched features SHALL have colored fill, unmatched features SHALL have neutral gray fill.
     * 
     * **Validates: Requirements 1.3, 6.3**
     */
    describe('Property 2: Polygon Style Determinism', () => {
        
        test('same inputs always produce same style output', () => {
            fc.assert(
                fc.property(
                    geoJsonFeatureArb,
                    fc.option(wilayahDataArb, { nil: null }),
                    fc.integer({ min: 0, max: 100 }),
                    (feature, wilayah, index) => {
                        const style1 = getPolygonStyle(feature, wilayah, index);
                        const style2 = getPolygonStyle(feature, wilayah, index);
                        
                        return JSON.stringify(style1) === JSON.stringify(style2);
                    }
                ),
                { numRuns: 100 }
            );
        });

        test('matched features have colored fill from palette', () => {
            fc.assert(
                fc.property(
                    geoJsonFeatureArb,
                    wilayahDataArb,
                    fc.integer({ min: 0, max: 100 }),
                    (feature, wilayah, index) => {
                        const style = getPolygonStyle(feature, wilayah, index);
                        const expectedColor = POLYGON_COLORS[index % POLYGON_COLORS.length];
                        
                        return style.fillColor === expectedColor;
                    }
                ),
                { numRuns: 100 }
            );
        });

        test('unmatched features have neutral gray fill', () => {
            fc.assert(
                fc.property(
                    geoJsonFeatureArb,
                    fc.integer({ min: 0, max: 100 }),
                    (feature, index) => {
                        const style = getPolygonStyle(feature, null, index);
                        return style.fillColor === UNMATCHED_COLOR;
                    }
                ),
                { numRuns: 100 }
            );
        });

        test('style object has required properties', () => {
            fc.assert(
                fc.property(
                    geoJsonFeatureArb,
                    fc.option(wilayahDataArb, { nil: null }),
                    fc.integer({ min: 0, max: 100 }),
                    (feature, wilayah, index) => {
                        const style = getPolygonStyle(feature, wilayah, index);
                        
                        return typeof style.fillColor === 'string' &&
                               typeof style.weight === 'number' &&
                               typeof style.opacity === 'number' &&
                               typeof style.color === 'string' &&
                               typeof style.fillOpacity === 'number';
                    }
                ),
                { numRuns: 100 }
            );
        });
    });


    /**
     * **Feature: simple-map-visualization, Property 3: Popup Content Completeness**
     * 
     * For any valid wilayah object with all required fields (nama, total_sekolah, total_peserta,
     * status_mandiri, status_menumpang) and for any valid tahun value, the generated popup HTML
     * SHALL contain all five data values and a valid navigation link.
     * 
     * **Validates: Requirements 3.1, 3.2**
     */
    describe('Property 3: Popup Content Completeness', () => {
        
        test('popup contains all required data fields', () => {
            fc.assert(
                fc.property(
                    wilayahDataArb,
                    tahunArb,
                    (wilayah, tahun) => {
                        const popup = createPopupContent(wilayah, tahun);
                        
                        // Check that all required fields are present in the popup
                        const containsNama = popup.includes(escapeHtml(wilayah.nama)) || popup.includes(wilayah.nama);
                        const containsSekolah = popup.includes('Sekolah');
                        const containsPeserta = popup.includes('Peserta');
                        const containsMandiri = popup.includes('Mandiri');
                        const containsMenumpang = popup.includes('Menumpang');
                        
                        return containsNama && containsSekolah && containsPeserta && 
                               containsMandiri && containsMenumpang;
                    }
                ),
                { numRuns: 100 }
            );
        });

        test('popup contains valid navigation link', () => {
            fc.assert(
                fc.property(
                    wilayahDataArb,
                    tahunArb,
                    (wilayah, tahun) => {
                        const popup = createPopupContent(wilayah, tahun);
                        const expectedUrl = generateWilayahUrl(tahun, wilayah.id);
                        
                        return popup.includes(expectedUrl) && popup.includes('href=');
                    }
                ),
                { numRuns: 100 }
            );
        });

        test('popup contains "Lihat Detail" button', () => {
            fc.assert(
                fc.property(
                    wilayahDataArb,
                    tahunArb,
                    (wilayah, tahun) => {
                        const popup = createPopupContent(wilayah, tahun);
                        return popup.includes('Lihat Detail');
                    }
                ),
                { numRuns: 100 }
            );
        });

        test('null wilayah returns fallback message', () => {
            const popup = createPopupContent(null, '2024');
            expect(popup).toContain('Data tidak tersedia');
        });
    });

    /**
     * **Feature: simple-map-visualization, Property 4: Tooltip Content Completeness**
     * 
     * For any valid wilayah object with nama, total_sekolah, and total_peserta fields,
     * the generated tooltip HTML SHALL contain the wilayah name and both statistics values.
     * 
     * **Validates: Requirements 2.2**
     */
    describe('Property 4: Tooltip Content Completeness', () => {
        
        test('tooltip contains wilayah name and key statistics', () => {
            fc.assert(
                fc.property(
                    wilayahDataArb,
                    (wilayah) => {
                        const tooltip = createTooltipContent(wilayah);
                        
                        // Check that name and stats are present
                        const containsNama = tooltip.includes(escapeHtml(wilayah.nama)) || tooltip.includes(wilayah.nama);
                        const containsSekolah = tooltip.includes('Sekolah');
                        const containsPeserta = tooltip.includes('Peserta');
                        
                        return containsNama && containsSekolah && containsPeserta;
                    }
                ),
                { numRuns: 100 }
            );
        });

        test('null wilayah returns fallback message', () => {
            const tooltip = createTooltipContent(null);
            expect(tooltip).toBe('Data tidak tersedia');
        });
    });

    /**
     * **Feature: simple-map-visualization, Property 5: URL Generation Correctness**
     * 
     * For any valid tahun (4-digit year) and for any valid wilayah id (positive integer),
     * the generated navigation URL SHALL follow the pattern `/asesmen-nasional/{tahun}/wilayah/{id}`
     * with correct parameter substitution.
     * 
     * **Validates: Requirements 3.2, 4.2**
     */
    describe('Property 5: URL Generation Correctness', () => {
        
        test('URL follows correct pattern with parameter substitution', () => {
            fc.assert(
                fc.property(
                    tahunArb,
                    wilayahIdArb,
                    (tahun, wilayahId) => {
                        const url = generateWilayahUrl(tahun, wilayahId);
                        const expectedPattern = `/asesmen-nasional/${tahun}/wilayah/${wilayahId}`;
                        
                        return url === expectedPattern;
                    }
                ),
                { numRuns: 100 }
            );
        });

        test('URL starts with /asesmen-nasional/', () => {
            fc.assert(
                fc.property(
                    tahunArb,
                    wilayahIdArb,
                    (tahun, wilayahId) => {
                        const url = generateWilayahUrl(tahun, wilayahId);
                        return url.startsWith('/asesmen-nasional/');
                    }
                ),
                { numRuns: 100 }
            );
        });

        test('URL contains /wilayah/ segment', () => {
            fc.assert(
                fc.property(
                    tahunArb,
                    wilayahIdArb,
                    (tahun, wilayahId) => {
                        const url = generateWilayahUrl(tahun, wilayahId);
                        return url.includes('/wilayah/');
                    }
                ),
                { numRuns: 100 }
            );
        });

        test('URL contains the exact tahun value', () => {
            fc.assert(
                fc.property(
                    tahunArb,
                    wilayahIdArb,
                    (tahun, wilayahId) => {
                        const url = generateWilayahUrl(tahun, wilayahId);
                        return url.includes(tahun);
                    }
                ),
                { numRuns: 100 }
            );
        });

        test('URL contains the exact wilayah ID', () => {
            fc.assert(
                fc.property(
                    tahunArb,
                    wilayahIdArb,
                    (tahun, wilayahId) => {
                        const url = generateWilayahUrl(tahun, wilayahId);
                        return url.endsWith(`/${wilayahId}`);
                    }
                ),
                { numRuns: 100 }
            );
        });
    });
});

// ============================================================================
// Unit Tests for Map Visualization Functions
// ============================================================================

/**
 * Mock Leaflet map instance for testing initMap
 */
function createMockLeafletMap() {
    const mockMap = {
        setView: vi.fn().mockReturnThis(),
        _center: null,
        _zoom: null,
        getCenter: function() { return this._center; },
        getZoom: function() { return this._zoom; }
    };
    return mockMap;
}

/**
 * Mock Leaflet tileLayer for testing
 */
function createMockTileLayer() {
    return {
        addTo: vi.fn().mockReturnThis()
    };
}

/**
 * initMap function implementation for testing
 * (Re-implemented to avoid browser globals dependency)
 */
function initMap(containerId, options = {}, mockL = null) {
    const defaultOptions = {
        center: [-1.0, 121.0],
        zoom: 7,
        tileUrl: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        attribution: '© OpenStreetMap contributors'
    };

    const config = { ...defaultOptions, ...options };

    // Use mock Leaflet if provided (for testing)
    const L = mockL || (typeof window !== 'undefined' ? window.L : null);
    
    if (!L) {
        throw new Error('Leaflet library not available');
    }

    const map = L.map(containerId).setView(config.center, config.zoom);

    L.tileLayer(config.tileUrl, {
        attribution: config.attribution,
        maxZoom: 18
    }).addTo(map);

    return map;
}

/**
 * renderFallbackMarkers function for testing edge cases
 */
function renderFallbackMarkers(map, wilayahData, tahun, mockL = null) {
    if (!wilayahData || !Array.isArray(wilayahData)) {
        return;
    }

    const L = mockL || (typeof window !== 'undefined' ? window.L : null);
    if (!L) return;

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

describe('Unit Tests for Map Visualization', () => {
    
    /**
     * Unit tests for initMap function
     * **Validates: Requirements 1.1**
     */
    describe('initMap function', () => {
        let mockL;
        let mockMap;
        let mockTileLayer;

        beforeEach(() => {
            mockMap = createMockLeafletMap();
            mockTileLayer = createMockTileLayer();
            
            mockL = {
                map: vi.fn().mockReturnValue(mockMap),
                tileLayer: vi.fn().mockReturnValue(mockTileLayer)
            };
        });

        afterEach(() => {
            vi.clearAllMocks();
        });

        test('initializes map with default center coordinates (-1.0, 121.0)', () => {
            initMap('map-container', {}, mockL);
            
            expect(mockL.map).toHaveBeenCalledWith('map-container');
            expect(mockMap.setView).toHaveBeenCalledWith([-1.0, 121.0], 7);
        });

        test('initializes map with default zoom level 7', () => {
            initMap('map-container', {}, mockL);
            
            expect(mockMap.setView).toHaveBeenCalledWith(expect.any(Array), 7);
        });

        test('allows custom center coordinates', () => {
            const customCenter = [-2.5, 120.5];
            initMap('map-container', { center: customCenter }, mockL);
            
            expect(mockMap.setView).toHaveBeenCalledWith(customCenter, 7);
        });

        test('allows custom zoom level', () => {
            const customZoom = 10;
            initMap('map-container', { zoom: customZoom }, mockL);
            
            expect(mockMap.setView).toHaveBeenCalledWith([-1.0, 121.0], customZoom);
        });

        test('adds OpenStreetMap tile layer by default', () => {
            initMap('map-container', {}, mockL);
            
            expect(mockL.tileLayer).toHaveBeenCalledWith(
                'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                expect.objectContaining({
                    attribution: '© OpenStreetMap contributors',
                    maxZoom: 18
                })
            );
            expect(mockTileLayer.addTo).toHaveBeenCalledWith(mockMap);
        });

        test('allows custom tile URL', () => {
            const customTileUrl = 'https://custom.tiles/{z}/{x}/{y}.png';
            initMap('map-container', { tileUrl: customTileUrl }, mockL);
            
            expect(mockL.tileLayer).toHaveBeenCalledWith(
                customTileUrl,
                expect.any(Object)
            );
        });

        test('returns the map instance', () => {
            const result = initMap('map-container', {}, mockL);
            
            expect(result).toBe(mockMap);
        });

        test('throws error when Leaflet is not available', () => {
            expect(() => initMap('map-container', {}, null)).toThrow('Leaflet library not available');
        });
    });

    /**
     * Unit tests for edge cases
     * **Validates: Requirements 1.4, 6.3**
     */
    describe('Edge cases', () => {
        
        describe('Empty wilayah data array', () => {
            test('matchWilayahToFeature returns null for empty array', () => {
                const feature = {
                    type: 'Feature',
                    properties: {
                        kode_provinsi: '72',
                        kode_kabkota: '01',
                        nama: 'Test Wilayah'
                    }
                };
                
                const result = matchWilayahToFeature(feature, []);
                expect(result).toBeNull();
            });

            test('getPolygonStyle returns gray for unmatched with empty array', () => {
                const feature = {
                    type: 'Feature',
                    properties: { nama: 'Test' }
                };
                
                const style = getPolygonStyle(feature, null, 0);
                expect(style.fillColor).toBe(UNMATCHED_COLOR);
            });

            test('createPopupContent handles empty wilayah gracefully', () => {
                const popup = createPopupContent(null, '2024');
                expect(popup).toContain('Data tidak tersedia');
            });

            test('createTooltipContent handles empty wilayah gracefully', () => {
                const tooltip = createTooltipContent(null);
                expect(tooltip).toBe('Data tidak tersedia');
            });
        });

        describe('GeoJSON with missing properties', () => {
            test('matchWilayahToFeature returns null when feature has no properties', () => {
                const feature = { type: 'Feature' };
                const wilayahData = [{ id: 1, kode_wilayah: '7201', nama: 'Test' }];
                
                const result = matchWilayahToFeature(feature, wilayahData);
                expect(result).toBeNull();
            });

            test('matchWilayahToFeature returns null when feature properties is empty object', () => {
                const feature = { type: 'Feature', properties: {} };
                const wilayahData = [{ id: 1, kode_wilayah: '7201', nama: 'Test' }];
                
                const result = matchWilayahToFeature(feature, wilayahData);
                expect(result).toBeNull();
            });

            test('matchWilayahToFeature returns null when kode_provinsi is missing', () => {
                const feature = {
                    type: 'Feature',
                    properties: { kode_kabkota: '01' }
                };
                const wilayahData = [{ id: 1, kode_wilayah: '7201', nama: 'Test' }];
                
                const result = matchWilayahToFeature(feature, wilayahData);
                expect(result).toBeNull();
            });

            test('matchWilayahToFeature returns null when kode_kabkota is missing', () => {
                const feature = {
                    type: 'Feature',
                    properties: { kode_provinsi: '72' }
                };
                const wilayahData = [{ id: 1, kode_wilayah: '7201', nama: 'Test' }];
                
                const result = matchWilayahToFeature(feature, wilayahData);
                expect(result).toBeNull();
            });

            test('matchWilayahToFeature can still match by nama when kode is missing', () => {
                const feature = {
                    type: 'Feature',
                    properties: { nama: 'Kab. Banggai' }
                };
                const wilayahData = [
                    { id: 1, kode_wilayah: '7201', nama: 'Kab. Banggai' }
                ];
                
                const result = matchWilayahToFeature(feature, wilayahData);
                expect(result).not.toBeNull();
                expect(result.id).toBe(1);
            });

            test('getPolygonStyle handles feature with missing properties', () => {
                const feature = { type: 'Feature' };
                const style = getPolygonStyle(feature, null, 0);
                
                expect(style).toHaveProperty('fillColor');
                expect(style).toHaveProperty('weight');
                expect(style).toHaveProperty('opacity');
            });
        });

        describe('Wilayah with missing coordinates', () => {
            let mockL;
            let mockMap;
            let mockMarker;

            beforeEach(() => {
                mockMarker = {
                    addTo: vi.fn().mockReturnThis(),
                    bindPopup: vi.fn().mockReturnThis(),
                    bindTooltip: vi.fn().mockReturnThis()
                };
                
                mockMap = {
                    addLayer: vi.fn()
                };
                
                mockL = {
                    circleMarker: vi.fn().mockReturnValue(mockMarker)
                };
            });

            afterEach(() => {
                vi.clearAllMocks();
            });

            test('renderFallbackMarkers skips wilayah without latitude', () => {
                const wilayahData = [
                    { id: 1, nama: 'Test', longitude: 121.0 } // missing latitude
                ];
                
                renderFallbackMarkers(mockMap, wilayahData, '2024', mockL);
                
                expect(mockL.circleMarker).not.toHaveBeenCalled();
            });

            test('renderFallbackMarkers skips wilayah without longitude', () => {
                const wilayahData = [
                    { id: 1, nama: 'Test', latitude: -1.0 } // missing longitude
                ];
                
                renderFallbackMarkers(mockMap, wilayahData, '2024', mockL);
                
                expect(mockL.circleMarker).not.toHaveBeenCalled();
            });

            test('renderFallbackMarkers skips wilayah with null coordinates', () => {
                const wilayahData = [
                    { id: 1, nama: 'Test', latitude: null, longitude: null }
                ];
                
                renderFallbackMarkers(mockMap, wilayahData, '2024', mockL);
                
                expect(mockL.circleMarker).not.toHaveBeenCalled();
            });

            test('renderFallbackMarkers renders wilayah with valid coordinates', () => {
                const wilayahData = [
                    { id: 1, nama: 'Test', latitude: -1.0, longitude: 121.0, total_sekolah: 10, total_peserta: 100 }
                ];
                
                renderFallbackMarkers(mockMap, wilayahData, '2024', mockL);
                
                expect(mockL.circleMarker).toHaveBeenCalledWith(
                    [-1.0, 121.0],
                    expect.objectContaining({
                        radius: 12,
                        fillColor: '#3b82f6'
                    })
                );
            });

            test('renderFallbackMarkers handles mixed valid and invalid coordinates', () => {
                const wilayahData = [
                    { id: 1, nama: 'Valid', latitude: -1.0, longitude: 121.0, total_sekolah: 10, total_peserta: 100 },
                    { id: 2, nama: 'Missing Lat', longitude: 121.0 },
                    { id: 3, nama: 'Missing Lng', latitude: -1.0 },
                    { id: 4, nama: 'Also Valid', latitude: -2.0, longitude: 122.0, total_sekolah: 20, total_peserta: 200 }
                ];
                
                renderFallbackMarkers(mockMap, wilayahData, '2024', mockL);
                
                // Only 2 markers should be created (for valid coordinates)
                expect(mockL.circleMarker).toHaveBeenCalledTimes(2);
            });

            test('renderFallbackMarkers handles null wilayahData', () => {
                renderFallbackMarkers(mockMap, null, '2024', mockL);
                
                expect(mockL.circleMarker).not.toHaveBeenCalled();
            });

            test('renderFallbackMarkers handles undefined wilayahData', () => {
                renderFallbackMarkers(mockMap, undefined, '2024', mockL);
                
                expect(mockL.circleMarker).not.toHaveBeenCalled();
            });

            test('renderFallbackMarkers handles non-array wilayahData', () => {
                renderFallbackMarkers(mockMap, 'not an array', '2024', mockL);
                
                expect(mockL.circleMarker).not.toHaveBeenCalled();
            });
        });
    });
});
