# Requirements Document

## Introduction

Fitur Peta Data Visualisasi Sederhana untuk Portal Data AN-TKA Disdik Sulteng. Fitur ini menampilkan peta interaktif Sulawesi Tengah dengan polygon kabupaten/kota yang dapat di-hover untuk melihat ringkasan data dan di-klik untuk navigasi ke halaman statistik detail. Implementasi menggunakan Leaflet.js dengan GeoJSON dari sumber publik untuk menggambar batas wilayah administratif.

## Glossary

- **Peta_Data_System**: Sistem visualisasi peta interaktif yang menampilkan data asesmen nasional per wilayah kabupaten/kota di Sulawesi Tengah
- **GeoJSON**: Format data geografis berbasis JSON untuk merepresentasikan fitur geografis seperti polygon wilayah
- **Polygon**: Bentuk geometri tertutup yang merepresentasikan batas wilayah administratif kabupaten/kota
- **Tooltip**: Informasi popup kecil yang muncul saat hover pada elemen peta
- **Popup**: Panel informasi yang muncul saat klik pada elemen peta
- **Choropleth**: Teknik pewarnaan peta berdasarkan nilai data statistik
- **Wilayah**: Kabupaten atau kota di Provinsi Sulawesi Tengah

## Requirements

### Requirement 1

**User Story:** As a user, I want to see an interactive map of Sulawesi Tengah province, so that I can visualize the geographic distribution of assessment data across districts.

#### Acceptance Criteria

1. WHEN the peta-data page loads THEN the Peta_Data_System SHALL display a Leaflet map centered on Sulawesi Tengah province coordinates (-1.0, 121.0) with zoom level 7
2. WHEN the map initializes THEN the Peta_Data_System SHALL load GeoJSON polygon data for all 13 kabupaten/kota in Sulawesi Tengah
3. WHEN GeoJSON data loads successfully THEN the Peta_Data_System SHALL render polygon boundaries for each wilayah with distinct fill colors
4. IF GeoJSON data fails to load THEN the Peta_Data_System SHALL display fallback circle markers at wilayah coordinates

### Requirement 2

**User Story:** As a user, I want to hover over a district polygon to see summary data, so that I can quickly preview information without clicking.

#### Acceptance Criteria

1. WHEN a user hovers over a wilayah polygon THEN the Peta_Data_System SHALL highlight the polygon with increased opacity and border width
2. WHEN a user hovers over a wilayah polygon THEN the Peta_Data_System SHALL display a tooltip showing wilayah name and key statistics (total sekolah, total peserta)
3. WHEN a user moves mouse away from a wilayah polygon THEN the Peta_Data_System SHALL restore the polygon to its default style

### Requirement 3

**User Story:** As a user, I want to click on a district polygon to navigate to detailed statistics, so that I can access comprehensive data for that area.

#### Acceptance Criteria

1. WHEN a user clicks on a wilayah polygon THEN the Peta_Data_System SHALL display a popup with detailed statistics (nama, total sekolah, total peserta, status mandiri, status menumpang)
2. WHEN a user clicks the "Lihat Detail" button in popup THEN the Peta_Data_System SHALL navigate to the existing wilayah statistics page with correct tahun and wilayah parameters
3. WHEN popup is displayed THEN the Peta_Data_System SHALL position the popup centered above the clicked polygon

### Requirement 4

**User Story:** As a user, I want to filter the map data by year, so that I can view assessment data for different time periods.

#### Acceptance Criteria

1. WHEN the page loads THEN the Peta_Data_System SHALL display a year filter dropdown with available years from database
2. WHEN a user selects a different year THEN the Peta_Data_System SHALL reload the page with the selected year parameter
3. WHEN year changes THEN the Peta_Data_System SHALL update all polygon tooltips and popups with data for the selected year

### Requirement 5

**User Story:** As a user, I want to see a legend explaining the map visualization, so that I can understand what the colors and symbols represent.

#### Acceptance Criteria

1. WHEN the map displays THEN the Peta_Data_System SHALL show a legend panel explaining polygon colors and interaction hints
2. WHEN legend displays THEN the Peta_Data_System SHALL include instructions for hover and click interactions

### Requirement 6

**User Story:** As a developer, I want the GeoJSON data to be loaded from a reliable public source, so that polygon boundaries are accurate and maintainable.

#### Acceptance Criteria

1. WHEN the system initializes THEN the Peta_Data_System SHALL load GeoJSON from a local static file stored in public/geojson directory
2. WHEN GeoJSON is loaded THEN the Peta_Data_System SHALL match polygon features to wilayah data using kode_wilayah or nama_wilayah property
3. WHEN a polygon feature cannot be matched to wilayah data THEN the Peta_Data_System SHALL render the polygon with a neutral gray color

### Requirement 7

**User Story:** As a user, I want the map to work on both desktop and mobile devices, so that I can access the visualization from any device.

#### Acceptance Criteria

1. WHEN viewed on mobile devices THEN the Peta_Data_System SHALL adjust map height to fit viewport appropriately
2. WHEN touch interactions occur on mobile THEN the Peta_Data_System SHALL handle tap as click for popup display
3. WHEN the page is responsive THEN the Peta_Data_System SHALL maintain readable tooltips and popups on smaller screens
