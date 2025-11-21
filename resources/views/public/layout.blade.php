<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Portal Data AN-TKA Disdik Sulteng')</title>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <!-- Alpine.js (for interactivity) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @livewireStyles
</head>

<body class="bg-gray-50">
    <!-- Navbar -->
    <nav class="bg-white shadow-lg" x-data="{ openDropdown: false, mobileMenu: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-3">
                    <!-- Logo Provinsi Sulawesi Tengah -->
                    <img src="{{ asset('storage/logo-sulteng.png') }}" alt="Logo Sulawesi Tengah" class="h-12 w-auto">
                    <h1 class="text-xl md:text-2xl font-bold text-gray-800">Portal Data AN-TKA</h1>
                </div>
                
                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-4">
                    <a href="{{ route('public.landing') }}"
                        class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        Beranda
                    </a>
                    
                    <!-- Dropdown Data Statistik -->
                    <div class="relative" @mouseenter="openDropdown = true" @mouseleave="openDropdown = false">
                        <button class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium flex items-center transition-colors">
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
                             class="absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
                             style="display: none;">
                            <div class="py-1">
                                <a href="{{ route('asesmen-nasional.index', ['tahun' => 2023]) }}"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors">
                                    📊 Asesmen Nasional 2023
                                </a>
                                <a href="{{ route('asesmen-nasional.index', ['tahun' => 2024]) }}"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors">
                                    📊 Asesmen Nasional 2024
                                </a>
                                <a href="{{ route('asesmen-nasional.index', ['tahun' => 2025]) }}"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors">
                                    📊 Asesmen Nasional 2025
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <a href="/admin"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700 transition-colors">
                        Admin Login
                    </a>
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
                <a href="{{ route('public.landing') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Beranda</a>
                
                <div class="px-4 py-2">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Data Statistik</p>
                </div>
                <a href="{{ route('asesmen-nasional.index', ['tahun' => 2023]) }}" class="block px-6 py-2 text-sm text-gray-700 hover:bg-gray-100">📊 Asesmen Nasional 2023</a>
                <a href="{{ route('asesmen-nasional.index', ['tahun' => 2024]) }}" class="block px-6 py-2 text-sm text-gray-700 hover:bg-gray-100">📊 Asesmen Nasional 2024</a>
                <a href="{{ route('asesmen-nasional.index', ['tahun' => 2025]) }}" class="block px-6 py-2 text-sm text-gray-700 hover:bg-gray-100">📊 Asesmen Nasional 2025</a>
                
                <a href="/admin" class="block mx-4 mt-2 px-4 py-2 bg-blue-600 text-white text-center rounded-md text-sm font-medium hover:bg-blue-700">Admin Login</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white shadow-lg mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <p class="text-center text-gray-600 text-sm">
                © {{ date('Y') }} Portal Data AN-TKA Disdik Sulteng. All rights reserved.
            </p>
        </div>
    </footer>

    @livewireScripts
</body>

</html>
