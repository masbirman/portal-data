@extends('public.layout')

@section('title', 'Peta Sebaran Data Pendidikan - Portal Data AN-TKA Disdik Sulteng')

@section('content')
    <!-- Map & Filter Section -->
    <div class="mt-8 bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-100">
        <div class="grid grid-cols-1 lg:grid-cols-12">
            <!-- Left: Map Visualization -->
            <div class="lg:col-span-7 bg-slate-50 relative min-h-[600px] flex items-center justify-center p-8 overflow-hidden">
                <!-- Decorative Elements -->
                <div class="absolute top-0 left-0 w-full h-full opacity-10" style="background-image: radial-gradient(#cbd5e1 1px, transparent 1px); background-size: 20px 20px;"></div>
                <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-amber-100 rounded-full mix-blend-multiply filter blur-3xl opacity-30"></div>
                <div class="absolute -top-24 -right-24 w-64 h-64 bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl opacity-30"></div>
                
                <!-- Map Image Container -->
                <div class="relative z-10 w-full max-w-2xl transform hover:scale-105 transition-transform duration-700">
                    <img src="{{ asset('storage/peta-sulteng.png') }}" alt="Peta Sebaran Data Pendidikan Sulawesi Tengah" class="w-full h-auto drop-shadow-2xl">
                    
                    <!-- Floating Badge (Optional) -->
                    <div class="absolute bottom-4 right-4 bg-white/90 backdrop-blur-sm px-4 py-2 rounded-lg shadow-lg border border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                            <span class="text-xs font-medium text-slate-600">13 Kabupaten/Kota</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Filter Form -->
            <div class="lg:col-span-5 p-8 lg:p-12 flex flex-col justify-center bg-white relative">
                <div class="mb-8">
                    <span class="inline-block py-1 px-3 rounded-full bg-amber-100 text-amber-600 text-xs font-bold tracking-wider mb-4">PETA INTERAKTIF</span>
                    <h2 class="text-3xl font-bold text-slate-800 mb-2">Peta Sebaran Data {{ $tahun ?? 'Terbaru' }}</h2>
                    <p class="text-slate-500 leading-relaxed">Visualisasi data pendidikan berdasarkan wilayah administratif di Sulawesi Tengah. Silakan pilih filter di bawah untuk melihat data spesifik.</p>
                </div>

                <form action="{{ route('public.dashboard') }}" method="GET" class="space-y-6">
                    <!-- Hidden Input for Year if needed -->
                    @if(isset($tahun))
                        <input type="hidden" name="tahun" value="{{ $tahun }}">
                    @endif

                    <!-- Jenjang Dropdown -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Jenjang Pendidikan</label>
                        <div class="relative">
                            <select name="jenjang" class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 py-3 px-4 pr-8 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all cursor-pointer hover:bg-slate-100">
                                <option value="">Semua Jenjang</option>
                                <option value="SD">SD - Sekolah Dasar</option>
                                <option value="SMP">SMP - Sekolah Menengah Pertama</option>
                                <option value="SMA">SMA - Sekolah Menengah Atas</option>
                                <option value="SMK">SMK - Sekolah Menengah Kejuruan</option>
                                <option value="SLB">SLB - Sekolah Luar Biasa</option>
                                <option value="PKBM">PKBM - Pusat Kegiatan Belajar Masyarakat</option>
                                <option value="SKB">SKB - Sanggar Kegiatan Belajar</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Wilayah Dropdown -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Pilih Wilayah</label>
                        <div class="relative">
                            <select name="wilayah" class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 py-3 px-4 pr-8 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all cursor-pointer hover:bg-slate-100">
                                <option value="">Semua Kabupaten/Kota</option>
                                <option value="Palu">Kota Palu</option>
                                <option value="Banggai">Kab. Banggai</option>
                                <option value="Poso">Kab. Poso</option>
                                <option value="Donggala">Kab. Donggala</option>
                                <option value="Toli-Toli">Kab. Toli-Toli</option>
                                <option value="Buol">Kab. Buol</option>
                                <option value="Morowali">Kab. Morowali</option>
                                <option value="Banggai Kepulauan">Kab. Banggai Kepulauan</option>
                                <option value="Parigi Moutong">Kab. Parigi Moutong</option>
                                <option value="Tojo Una-Una">Kab. Tojo Una-Una</option>
                                <option value="Sigi">Kab. Sigi</option>
                                <option value="Banggai Laut">Kab. Banggai Laut</option>
                                <option value="Morowali Utara">Kab. Morowali Utara</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-slate-900 text-white font-bold py-3.5 px-6 rounded-lg hover:bg-slate-800 focus:ring-4 focus:ring-slate-200 transition-all duration-300 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        Telusuri Data
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
