<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Portal Data AN-TKA Disdik Sulteng' }}</title>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">

    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <!-- Alpine.js is injected by Livewire 3 automatically -->

    @livewireStyles
    @stack('styles')
    
    <script>
        // Dark mode initialization
        if (localStorage.getItem('darkMode') === 'true' || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
    
    <!-- Cloudflare Turnstile -->
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
</head>

<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-200" 
      x-data="{ 
          showScrollTop: false,
          darkMode: localStorage.getItem('darkMode') === 'true' || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches),
          toggleDarkMode() {
              this.darkMode = !this.darkMode;
              localStorage.setItem('darkMode', this.darkMode);
              if (this.darkMode) {
                  document.documentElement.classList.add('dark');
              } else {
                  document.documentElement.classList.remove('dark');
              }
          }
      }" 
      @scroll.window="showScrollTop = (window.pageYOffset > 300)">
    <!-- Navbar -->
    <nav class="bg-white dark:bg-gray-800 shadow-lg sticky top-0 z-50 transition-colors duration-200" x-data="{ openDropdown: false, mobileMenu: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-3">
                    <!-- Logo Provinsi Sulawesi Tengah -->
                    <img src="{{ asset('storage/logo-sulteng.png') }}" alt="Logo Sulawesi Tengah" class="h-10 w-auto">
                    <h1 class="text-base md:text-lg font-bold text-gray-800 dark:text-white">Portal Data AN-TKA Disdik Sulteng</h1>
                </div>
                
                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-4">
                    <a href="{{ route('public.landing') }}"
                        class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        Beranda
                    </a>
                    
                    <!-- Dashboard Menu -->
                    <a href="{{ route('public.dashboard') }}"
                        class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        Dashboard
                    </a>
                    
                    <!-- Direktori Sekolah Menu -->
                    <a href="{{ route('direktori-sekolah.index') }}"
                        class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        Direktori Sekolah
                    </a>
                    
                    <!-- Dropdown Pengajuan Unduh Data -->
                    <div class="relative" x-data="{ openRequestDropdown: false }" @mouseenter="openRequestDropdown = true" @mouseleave="openRequestDropdown = false">
                        <button class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium flex items-center transition-colors">
                            Pengajuan Unduh Data
                            <svg class="ml-1 h-4 w-4 transition-transform" :class="{'rotate-180': openRequestDropdown}" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div x-show="openRequestDropdown"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute left-0 mt-2 w-64 rounded-md shadow-lg bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5 z-50"
                             style="display: none;">
                            <div class="py-1">
                                <a href="{{ route('download-request.index') }}"
                                   class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-600 hover:text-blue-600 dark:hover:text-white transition-colors">
                                    📝 Ajukan Permintaan
                                </a>
                                <a href="{{ route('download-request.tracking') }}"
                                   class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-600 hover:text-blue-600 dark:hover:text-white transition-colors">
                                    🔍 Tracking Status
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Dropdown Data Statistik -->
                    <div class="relative" @mouseenter="openDropdown = true" @mouseleave="openDropdown = false">
                        <button class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium flex items-center transition-colors">
                            Data Statistik
                            <svg class="ml-1 h-4 w-4 transition-transform" :class="{'rotate-180': openDropdown}" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div x-show="openDropdown"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute left-0 mt-2 w-72 rounded-md shadow-lg bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5 z-50"
                             style="display: none;">
                            <div class="py-1">
                                <a href="{{ route('asesmen-nasional.index', ['tahun' => 2023]) }}"
                                   class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-600 hover:text-blue-600 dark:hover:text-white transition-colors">
                                    📊 Asesmen Nasional 2023
                                </a>
                                <a href="{{ route('asesmen-nasional.index', ['tahun' => 2024]) }}"
                                   class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-600 hover:text-blue-600 dark:hover:text-white transition-colors">
                                    📊 Asesmen Nasional 2024
                                </a>
                                <a href="{{ route('asesmen-nasional.index', ['tahun' => 2025]) }}"
                                   class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-600 hover:text-blue-600 dark:hover:text-white transition-colors">
                                    📊 Asesmen Nasional 2025
                                </a>
                                <div class="border-t border-gray-200 dark:border-gray-600 my-1"></div>
                                <a href="#"
                                   class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-600 hover:text-blue-600 dark:hover:text-white transition-colors">
                                    📊 Survei Lingkungan Belajar 2024
                                </a>
                                <a href="#"
                                   class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-600 hover:text-blue-600 dark:hover:text-white transition-colors">
                                    📊 Tes Kemampuan Akademik 2025
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Dropdown Peta Data -->
                    <div class="relative" x-data="{ openPetaDropdown: false }" @mouseenter="openPetaDropdown = true" @mouseleave="openPetaDropdown = false">
                        <button class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium flex items-center transition-colors">
                            Peta Data
                            <svg class="ml-1 h-4 w-4 transition-transform" :class="{'rotate-180': openPetaDropdown}" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div x-show="openPetaDropdown"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute left-0 mt-2 w-56 rounded-md shadow-lg bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5 z-50"
                             style="display: none;">
                            <div class="py-1">
                                <a href="{{ route('asesmen-nasional.peta', ['tahun' => 2023]) }}"
                                   class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-600 hover:text-blue-600 dark:hover:text-white transition-colors">
                                    Peta Data 2023
                                </a>
                                <a href="{{ route('asesmen-nasional.peta', ['tahun' => 2024]) }}"
                                   class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-600 hover:text-blue-600 dark:hover:text-white transition-colors">
                                    Peta Data 2024
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Dark Mode Toggle -->
                    <button @click="toggleDarkMode()" 
                            class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white p-2 rounded-md transition-colors"
                            aria-label="Toggle dark mode">
                        <!-- Sun Icon (shown in dark mode) -->
                        <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <!-- Moon Icon (shown in light mode) -->
                        <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                        </svg>
                    </button>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center">
                    <button @click="mobileMenu = !mobileMenu" class="text-gray-600 hover:text-gray-900 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="!mobileMenu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path x-show="mobileMenu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div x-show="mobileMenu" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-1"
                 class="md:hidden pb-4"
                 style="display: none;">
                <a href="{{ route('public.landing') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Beranda</a>
                <a href="{{ route('public.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Dashboard</a>
                <a href="{{ route('direktori-sekolah.index') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Direktori Sekolah</a>
                
                <div class="px-4 py-2 mt-2">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Pengajuan Unduh Data</p>
                </div>
                <a href="{{ route('download-request.index') }}" class="block px-6 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">📝 Ajukan Permintaan</a>
                <a href="{{ route('download-request.tracking') }}" class="block px-6 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">🔍 Tracking Status</a>
                
                <div class="px-4 py-2 mt-2">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Data Statistik</p>
                </div>
                <a href="{{ route('asesmen-nasional.index', ['tahun' => 2023]) }}" class="block px-6 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">📊 Asesmen Nasional 2023</a>
                <a href="{{ route('asesmen-nasional.index', ['tahun' => 2024]) }}" class="block px-6 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">📊 Asesmen Nasional 2024</a>
                <a href="{{ route('asesmen-nasional.index', ['tahun' => 2025]) }}" class="block px-6 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">📊 Asesmen Nasional 2025</a>
                
                <div class="px-4 py-2 mt-2">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Peta Data</p>
                </div>
                <a href="{{ route('asesmen-nasional.peta', ['tahun' => 2023]) }}" class="block px-6 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Peta Data 2023</a>
                <a href="{{ route('asesmen-nasional.peta', ['tahun' => 2024]) }}" class="block px-6 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Peta Data 2024</a>
                
            </div>
        </div>
    </nav>

    <!-- Scroll to Top Button -->
    <button 
        x-show="showScrollTop"
        @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        class="fixed bottom-8 right-8 bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-full shadow-lg z-50 transition-colors duration-200"
        style="display: none;"
        aria-label="Scroll to top">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
        </svg>
    </button>

    <!-- Main Content -->
    @hasSection('content')
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @yield('content')
        </main>
    @else
        {{-- For Livewire full-page components --}}
        @if(isset($slot))
            @if(isset($fullWidth) && $fullWidth)
                {{ $slot }}
            @else
                <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    {{ $slot }}
                </main>
            @endif
        @endif
    @endif

    <!-- Footer -->
    @if(!isset($fullWidth) || !$fullWidth)
    <footer class="bg-white dark:bg-gray-800 shadow-lg mt-12 transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <p class="text-center text-gray-600 dark:text-gray-400 text-sm">
                © {{ date('Y') }} Portal Data AN-TKA Disdik Sulteng
            </p>
        </div>
    </footer>
    @endif

    @livewireScripts
    @stack('scripts')
</body>

</html>
