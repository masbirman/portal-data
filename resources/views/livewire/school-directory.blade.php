<div>
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white">Direktori Sekolah</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Temukan informasi sekolah di Sulawesi Tengah</p>
    </div>

    <!-- Filters Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 md:p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Search Input -->
            <div class="lg:col-span-2">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cari Sekolah</label>
                <div class="relative">
                    <input type="text" 
                           id="search"
                           wire:model.live.debounce.300ms="search" 
                           placeholder="Nama sekolah atau kode sekolah..."
                           class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
            
            <!-- Wilayah Filter -->
            <div>
                <label for="wilayah" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Wilayah</label>
                <select id="wilayah" 
                        wire:model.live="wilayahId" 
                        class="w-full py-2 px-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Semua Wilayah</option>
                    @foreach($wilayahOptions as $id => $nama)
                        <option value="{{ $id }}">{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Jenjang Filter -->
            <div>
                <label for="jenjang" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jenjang</label>
                <select id="jenjang" 
                        wire:model.live="jenjangId" 
                        class="w-full py-2 px-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Semua Jenjang</option>
                    @foreach($jenjangOptions as $id => $nama)
                        <option value="{{ $id }}">{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Status Filter -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select id="status" 
                        wire:model.live="status" 
                        class="w-full py-2 px-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Semua Status</option>
                    <option value="Negeri">Negeri</option>
                    <option value="Swasta">Swasta</option>
                </select>
            </div>
        </div>
        
        <!-- Reset & Count Row -->
        <div class="mt-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div class="flex items-center text-gray-600 dark:text-gray-400">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span class="font-medium">Total: <span class="text-blue-600 dark:text-blue-400">{{ number_format($totalSchools) }}</span> sekolah</span>
            </div>
            <button wire:click="resetFilters" 
                    class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Reset Filter
            </button>
        </div>
    </div>

    <!-- Loading Indicator -->
    <div wire:loading class="flex justify-center items-center py-8">
        <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600"></div>
        <span class="ml-3 text-gray-600 dark:text-gray-400">Memuat data...</span>
    </div>

    <!-- Schools Grid -->
    <div wire:loading.remove class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse($schools as $school)
            <!-- School Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 flex flex-col h-full">
                <!-- Placeholder Image -->
                <div class="h-32 bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-16 h-16 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                
                <!-- Card Content -->
                <div class="p-4 flex flex-col flex-grow">
                    <h3 class="font-semibold text-gray-800 dark:text-white text-lg leading-tight mb-1 line-clamp-2 min-h-[3.5rem]">
                        {{ $school->nama }}
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">
                        Kode: {{ $school->kode_sekolah }}
                    </p>
                    
                    @if($school->alamat)
                        <p class="text-xs text-gray-400 dark:text-gray-500 mb-3 line-clamp-2">
                            <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ $school->alamat }}
                        </p>
                    @endif
                    
                    <!-- Tags -->
                    <div class="flex flex-wrap gap-2 mb-4">
                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                            {{ $school->wilayah->nama ?? '-' }}
                        </span>
                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full {{ $school->status_sekolah === 'Negeri' ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200' }}">
                            {{ $school->status_sekolah ?? '-' }}
                        </span>
                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200">
                            {{ $school->jenjangPendidikan->nama ?? '-' }}
                        </span>
                    </div>
                    
                    <!-- Action Button - Always at bottom -->
                    <div class="mt-auto">
                        <a href="{{ route('direktori-sekolah.show', $school) }}" 
                           class="block w-full text-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors text-sm font-medium">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <!-- Empty State -->
            <div class="col-span-full">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-8 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-2">Tidak ada sekolah ditemukan</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-4">Coba ubah filter pencarian Anda</p>
                    <button wire:click="resetFilters" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Reset Filter
                    </button>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($schools->hasPages())
        <div class="mt-8">
            {{ $schools->links() }}
        </div>
    @endif
</div>
