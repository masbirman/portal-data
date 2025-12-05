# Implementation Plan

- [x] 1. Prepare GeoJSON data file for Sulawesi Tengah






  - [x] 1.1 Create public/geojson directory and add sulawesi-tengah.geojson file

    - Download or create GeoJSON with polygon boundaries for 13 kabupaten/kota
    - Ensure each feature has kode_wilayah and nama properties
    - Validate GeoJSON structure is correct
    - _Requirements: 6.1, 6.2_

- [x] 2. Create JavaScript map visualization module





  - [x] 2.1 Create resources/js/map-visualization.js with core functions


    - Implement initMap() function for Leaflet initialization
    - Implement loadGeoJSON() function to fetch and render polygons
    - Implement matchWilayahToFeature() for data matching
    - Implement getPolygonStyle() for polygon styling
    - _Requirements: 1.1, 1.2, 1.3, 6.2_
  - [x] 2.2 Write property test for GeoJSON-Wilayah matching


    - **Property 1: GeoJSON-Wilayah Matching Consistency**
    - **Validates: Requirements 6.2**
  - [x] 2.3 Write property test for polygon style determinism

    - **Property 2: Polygon Style Determinism**
    - **Validates: Requirements 1.3, 6.3**
  - [x] 2.4 Implement createPopupContent() and createTooltipContent() functions

    - Generate HTML for popup with all wilayah statistics
    - Generate HTML for tooltip with name and key stats
    - Include navigation link in popup
    - _Requirements: 2.2, 3.1, 3.2_
  - [x] 2.5 Write property test for popup content completeness

    - **Property 3: Popup Content Completeness**
    - **Validates: Requirements 3.1, 3.2**
  - [x] 2.6 Write property test for tooltip content completeness

    - **Property 4: Tooltip Content Completeness**
    - **Validates: Requirements 2.2**
  - [x] 2.7 Implement generateWilayahUrl() function for navigation URLs

    - Generate correct URL pattern for wilayah detail pages
    - Handle tahun and wilayah id parameters
    - _Requirements: 3.2, 4.2_
  - [x] 2.8 Write property test for URL generation

    - **Property 5: URL Generation Correctness**
    - **Validates: Requirements 3.2, 4.2**

- [x] 3. Implement hover and click interactions





  - [x] 3.1 Add hover event handlers for polygon highlighting


    - Implement mouseover handler to highlight polygon
    - Implement mouseout handler to restore default style
    - Bind tooltip to show on hover
    - _Requirements: 2.1, 2.2, 2.3_
  - [x] 3.2 Add click event handlers for popup display


    - Implement click handler to show popup
    - Bind popup with detail content and navigation link
    - _Requirements: 3.1, 3.2, 3.3_

- [x] 4. Implement fallback behavior for missing GeoJSON





  - [x] 4.1 Add circle marker fallback when GeoJSON fails or feature unmatched


    - Detect GeoJSON load failure and render circle markers
    - For wilayah without matching polygon, render circle marker at coordinates
    - _Requirements: 1.4_

- [x] 5. Update Blade view to use new map visualization





  - [x] 5.1 Update peta-data.blade.php to integrate map-visualization.js


    - Add script reference to map-visualization.js
    - Initialize map with GeoJSON loading
    - Pass wilayahData and tahun to JavaScript
    - _Requirements: 1.1, 1.2_
  - [x] 5.2 Update legend section for polygon visualization


    - Update legend to explain polygon colors
    - Add hover and click interaction hints
    - _Requirements: 5.1, 5.2_

- [x] 6. Add responsive styles for mobile support





  - [x] 6.1 Add CSS for responsive map container


    - Adjust map height for mobile viewports
    - Ensure tooltips and popups are readable on small screens
    - _Requirements: 7.1, 7.3_

- [x] 7. Checkpoint - Ensure all tests pass





  - Ensure all tests pass, ask the user if questions arise.

- [x] 8. Write unit tests for map visualization functions





  - [x] 8.1 Write unit tests for initMap function


    - Test map initialization with correct center and zoom
    - _Requirements: 1.1_
  - [x] 8.2 Write unit tests for edge cases

    - Test empty wilayah data array
    - Test GeoJSON with missing properties
    - Test wilayah with missing coordinates
    - _Requirements: 1.4, 6.3_

- [x] 9. Final Checkpoint - Ensure all tests pass





  - Ensure all tests pass, ask the user if questions arise.
