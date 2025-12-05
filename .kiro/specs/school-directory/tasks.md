# Implementation Plan

- [x] 1. Set up routes and controller




  - [x] 1.1 Create SchoolDirectoryController with index and show methods


    - Create controller at `app/Http/Controllers/SchoolDirectoryController.php`
    - Implement `index()` method returning Livewire component view
    - Implement `show(Sekolah $sekolah)` method with eager loading for relations
    - _Requirements: 3.1, 6.2_
  - [x] 1.2 Add routes to web.php


    - Add `/sekolah` route for index page
    - Add `/sekolah/{sekolah}` route for detail page
    - _Requirements: 6.2_

- [x] 2. Create SchoolStatisticsService





  - [x] 2.1 Implement statistics calculation methods


    - Create service at `app/Services/SchoolStatisticsService.php`
    - Implement `getStatistics(Sekolah $sekolah)` returning total_peserta, avg_literasi, avg_numerasi
    - Implement `getAssessmentHistory(Sekolah $sekolah)` returning assessment records by siklus
    - Implement `getNearbySchools(Sekolah $sekolah, int $limit = 5)` returning schools in same wilayah
    - _Requirements: 4.2, 4.3, 4.4, 5.1, 5.2_

  - [x] 2.2 Write property tests for SchoolStatisticsService


    - **Property 7: Statistics calculation - Total peserta**
    - **Property 8: Statistics calculation - Averages**
    - **Property 9: Nearby schools - Same wilayah**
    - **Property 10: Nearby schools - Exclusion and limit**
    - **Validates: Requirements 4.2, 4.3, 5.1, 5.2**

- [x] 3. Create SchoolDirectory Livewire Component




  - [x] 3.1 Implement Livewire component with filter logic


    - Create component at `app/Livewire/SchoolDirectory.php`
    - Add properties: search, wilayahId, jenjangId, status, perPage
    - Implement `getSchoolsProperty()` with query builder and filters
    - Implement `resetFilters()` method
    - _Requirements: 1.1, 2.1, 2.2, 2.3, 2.4, 2.5, 2.6_
  - [x] 3.2 Write property tests for filter logic




    - **Property 2: Filter correctness - Search**
    - **Property 3: Filter correctness - Wilayah**
    - **Property 4: Filter correctness - Jenjang**
    - **Property 5: Filter correctness - Status**
    - **Property 6: School count matches filtered results**
    - **Validates: Requirements 2.1, 2.2, 2.3, 2.4, 1.5**

- [x] 4. Create Blade views for school directory listing





  - [x] 4.1 Create Livewire component view


    - Create view at `resources/views/livewire/school-directory.blade.php`
    - Implement search input field
    - Implement filter dropdowns (wilayah, jenjang, status)
    - Implement reset button
    - Display total school count
    - _Requirements: 1.5, 2.1, 2.2, 2.3, 2.4, 2.5_
  - [x] 4.2 Create school card component

    - Create card layout with placeholder image
    - Display school name, kode_sekolah, wilayah, status, jenjang
    - Conditionally display alamat if available
    - Add "Lihat" button linking to detail page
    - _Requirements: 1.2, 1.3, 1.4, 3.1_
  - [x] 4.3 Implement responsive grid and pagination

    - Add responsive grid (1 col mobile, 2 col tablet, 4 col desktop)
    - Implement Livewire pagination
    - Add loading indicator
    - _Requirements: 1.1, 1.6, 7.1, 7.2, 7.3, 7.5_
  - [x] 4.4 Write property test for card rendering


    - **Property 1: Card displays all required fields**
    - **Validates: Requirements 1.2**

- [x] 5. Checkpoint - Ensure all tests pass



  - Ensure all tests pass, ask the user if questions arise.

- [x] 6. Create school detail page

  - [x] 6.1 Create detail page Blade view


    - Create view at `resources/views/public/sekolah-detail.blade.php`
    - Implement breadcrumb navigation
    - Display school header with name, wilayah, status, jenjang, kode_sekolah
    - Conditionally display alamat if available
    - _Requirements: 3.2, 3.3, 3.4_
  - [x] 6.2 Implement statistics section

    - Display total jumlah_peserta
    - Display average partisipasi_literasi and partisipasi_numerasi
    - Display assessment history table/chart by siklus
    - Show "Belum ada data asesmen" message if no data
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_
  - [x] 6.3 Implement map section with Leaflet.js

    - Add Leaflet.js CSS and JS via CDN
    - Initialize map with OpenStreetMap tiles
    - Add marker at school coordinates with popup
    - Add "Buka di Google Maps" link
    - Show "Lokasi tidak tersedia" if no coordinates
    - _Requirements: 3.5, 3.6, 3.7, 3.8_
  - [x] 6.4 Write property test for Google Maps link


    - **Property 11: Google Maps link correctness**
    - **Validates: Requirements 3.7**

  - [x] 6.5 Implement nearby schools section
    - Display up to 5 schools from same wilayah
    - Exclude current school from list
    - Add links to each nearby school's detail page
    - _Requirements: 5.1, 5.2, 5.3_

- [x] 7. Update navigation menu

  - [x] 7.1 Add "Sekolah" menu item to public layout


    - Add menu item to desktop navigation in layout.blade.php
    - Add menu item to mobile navigation
    - Link to sekolah.index route
    - _Requirements: 6.1, 6.2, 6.3_

- [x] 8. Final Checkpoint - Ensure all tests pass



  - Ensure all tests pass, ask the user if questions arise.
