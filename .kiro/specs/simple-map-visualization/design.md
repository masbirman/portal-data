# Design Document

## Overview

Implementasi peta data visualisasi sederhana untuk Portal Data AN-TKA Disdik Sulteng menggunakan Leaflet.js dengan GeoJSON polygon. Sistem ini menampilkan peta interaktif Sulawesi Tengah dengan batas wilayah kabupaten/kota yang dapat di-hover dan di-klik untuk melihat data asesmen nasional.

Pendekatan yang dipilih adalah implementasi frontend-only dengan data GeoJSON statis dan data statistik dari controller PHP yang sudah ada. Tidak memerlukan API endpoint baru atau komponen Livewire kompleks.

## Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    Browser (Frontend)                        │
├─────────────────────────────────────────────────────────────┤
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────┐ │
│  │   Leaflet Map   │  │  GeoJSON Layer  │  │   Popups/   │ │
│  │   Container     │  │   (Polygons)    │  │   Tooltips  │ │
│  └────────┬────────┘  └────────┬────────┘  └──────┬──────┘ │
│           │                    │                   │        │
│           └────────────────────┼───────────────────┘        │
│                                │                            │
│  ┌─────────────────────────────┴─────────────────────────┐ │
│  │              map-visualization.js                      │ │
│  │  - Initialize map                                      │ │
│  │  - Load GeoJSON                                        │ │
│  │  - Merge with wilayah data                            │ │
│  │  - Handle interactions                                 │ │
│  └───────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                    Server (Backend)                          │
├─────────────────────────────────────────────────────────────┤
│  ┌─────────────────────────────────────────────────────┐   │
│  │           PetaDataController (existing)              │   │
│  │  - Load wilayah data with statistics                 │   │
│  │  - Pass data to Blade view via @json                 │   │
│  └─────────────────────────────────────────────────────┘   │
│                              │                              │
│  ┌─────────────────────────────────────────────────────┐   │
│  │              Static Files                            │   │
│  │  - public/geojson/sulawesi-tengah.geojson           │   │
│  └─────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
```

## Components and Interfaces

### 1. GeoJSON Data File

**Location:** `public/geojson/sulawesi-tengah.geojson`

**Structure:**
```json
{
  "type": "FeatureCollection",
  "features": [
    {
      "type": "Feature",
      "properties": {
        "kode_wilayah": "7201",
        "nama": "Kab. Banggai Kepulauan"
      },
      "geometry": {
        "type": "Polygon",
        "coordinates": [[[lon, lat], ...]]
      }
    }
  ]
}
```

### 2. Map Initialization Module

**File:** `resources/js/map-visualization.js`

**Interface:**
```javascript
// Initialize map with configuration
function initMap(containerId, options) {
  // Returns Leaflet map instance
}

// Load and render GeoJSON with wilayah data
function loadGeoJSON(map, geojsonUrl, wilayahData, tahun) {
  // Returns GeoJSON layer
}

// Style function for polygons
function getPolygonStyle(feature, wilayahData) {
  // Returns Leaflet style object
}

// Create popup content
function createPopupContent(wilayah, tahun) {
  // Returns HTML string
}

// Create tooltip content
function createTooltipContent(wilayah) {
  // Returns HTML string
}
```

### 3. Blade View Updates

**File:** `resources/views/public/peta-data.blade.php`

Updates needed:
- Add GeoJSON fetch and rendering
- Update legend for polygon visualization
- Maintain existing table and filter functionality

## Data Models

### Wilayah Data (from PHP)
```php
[
  'id' => int,
  'kode_wilayah' => string,  // e.g., "7201"
  'nama' => string,
  'latitude' => float,
  'longitude' => float,
  'total_sekolah' => int,
  'total_peserta' => int,
  'status_mandiri' => int,
  'status_menumpang' => int
]
```

### GeoJSON Feature Properties
```json
{
  "kode_wilayah": "7201",
  "nama": "Kab. Banggai Kepulauan",
  "kode_provinsi": "72",
  "kode_kabkota": "01"
}
```

### Merged Data for Rendering
```javascript
{
  feature: GeoJSON.Feature,
  wilayah: {
    id, kode_wilayah, nama,
    total_sekolah, total_peserta,
    status_mandiri, status_menumpang
  },
  matched: boolean
}
```

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: GeoJSON-Wilayah Matching Consistency

*For any* GeoJSON feature with a valid kode_wilayah property and *for any* wilayah data array, if a wilayah record exists with the same kode_wilayah, the matching function SHALL return that exact wilayah record. If no match exists, the function SHALL return null.

**Validates: Requirements 6.2**

### Property 2: Polygon Style Determinism

*For any* GeoJSON feature and wilayah data combination (matched or unmatched), calling getPolygonStyle with the same inputs SHALL always return the same style object. Matched features SHALL have colored fill, unmatched features SHALL have neutral gray fill.

**Validates: Requirements 1.3, 6.3**

### Property 3: Popup Content Completeness

*For any* valid wilayah object with all required fields (nama, total_sekolah, total_peserta, status_mandiri, status_menumpang) and *for any* valid tahun value, the generated popup HTML SHALL contain all five data values and a valid navigation link.

**Validates: Requirements 3.1, 3.2**

### Property 4: Tooltip Content Completeness

*For any* valid wilayah object with nama, total_sekolah, and total_peserta fields, the generated tooltip HTML SHALL contain the wilayah name and both statistics values.

**Validates: Requirements 2.2**

### Property 5: URL Generation Correctness

*For any* valid tahun (4-digit year) and *for any* valid wilayah id (positive integer), the generated navigation URL SHALL follow the pattern `/asesmen-nasional/{tahun}/wilayah/{id}` with correct parameter substitution.

**Validates: Requirements 3.2, 4.2**

## Error Handling

| Error Scenario | Handling Strategy |
|----------------|-------------------|
| GeoJSON file not found (404) | Log warning, render circle markers as fallback |
| GeoJSON parse error | Log error, render circle markers as fallback |
| Wilayah data missing coordinates | Skip marker/polygon for that wilayah |
| No matching kode_wilayah | Render polygon with neutral gray color |
| Network timeout loading GeoJSON | Retry once, then fallback to markers |

## Testing Strategy

### Unit Testing

Framework: **Jest** for JavaScript unit tests

Test coverage:
- `matchWilayahToFeature()` - matching logic
- `getPolygonStyle()` - style generation
- `createPopupContent()` - HTML generation
- `createTooltipContent()` - tooltip generation
- URL generation for navigation

### Property-Based Testing

Framework: **fast-check** for JavaScript property-based testing

Properties to test:
1. Matching consistency (Property 1)
2. Style determinism (Property 2)
3. Popup completeness (Property 3)
4. Fallback behavior (Property 4)
5. URL generation (Property 5)

Each property-based test MUST:
- Run minimum 100 iterations
- Use comment format: `**Feature: simple-map-visualization, Property {number}: {property_text}**`
- Reference the correctness property from this design document

### Integration Testing

Manual testing checklist:
- Map loads and centers correctly
- All 13 kabupaten/kota polygons render
- Hover highlights polygon and shows tooltip
- Click shows popup with correct data
- "Lihat Detail" navigates to correct page
- Year filter changes data correctly
- Mobile touch interactions work
