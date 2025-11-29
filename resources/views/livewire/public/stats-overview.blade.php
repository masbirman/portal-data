<div>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Statistik Utama</h2>
            <p class="text-slate-500 text-sm mt-1">Ringkasan data terkini pelaksanaan asesmen</p>
        </div>
        <span class="inline-flex items-center gap-1.5 bg-slate-100 text-slate-700 text-sm font-medium px-4 py-1.5 rounded-full border border-slate-200">
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            Periode: {{ $latestYear }}
        </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Satuan Pendidikan -->
        <div class="group bg-white rounded-xl p-6 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-slate-100">
            <div class="flex items-start justify-between mb-4">
                <div class="p-3 rounded-lg bg-teal-50 text-teal-600 group-hover:bg-teal-600 group-hover:text-white transition-colors duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <span class="text-xs font-medium text-teal-600 bg-teal-50 px-2 py-1 rounded-full">Sekolah</span>
            </div>
            <div>
                <h3 class="text-3xl font-bold text-slate-800 mb-1">{{ number_format($stats['total_sekolah']) }}</h3>
                <p class="text-sm text-slate-500">Satuan Pendidikan Melaksanakan Asesmen</p>
            </div>
        </div>

        <!-- Peserta Asesmen -->
        <div class="group bg-white rounded-xl p-6 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-slate-100">
            <div class="flex items-start justify-between mb-4">
                <div class="p-3 rounded-lg bg-amber-50 text-amber-600 group-hover:bg-amber-600 group-hover:text-white transition-colors duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <span class="text-xs font-medium text-amber-600 bg-amber-50 px-2 py-1 rounded-full">Siswa</span>
            </div>
            <div>
                <h3 class="text-3xl font-bold text-slate-800 mb-1">{{ number_format($stats['total_peserta']) }}</h3>
                <p class="text-sm text-slate-500">Total Peserta Terdaftar</p>
            </div>
        </div>

        <!-- Kemandirian -->
        <div class="group bg-white rounded-xl p-6 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-slate-100">
            <div class="flex items-start justify-between mb-4">
                <div class="p-3 rounded-lg bg-indigo-50 text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                    </svg>
                </div>
                <span class="text-xs font-medium text-indigo-600 bg-indigo-50 px-2 py-1 rounded-full">Status</span>
            </div>
            <div>
                <h3 class="text-3xl font-bold text-slate-800 mb-1">{{ $stats['kemandirian'] }}%</h3>
                <p class="text-sm text-slate-500">Satuan Pendidikan Mandiri</p>
            </div>
        </div>

        <!-- Keikutsertaan -->
        <div class="group bg-white rounded-xl p-6 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-slate-100">
            <div class="flex items-start justify-between mb-4">
                <div class="p-3 rounded-lg bg-rose-50 text-rose-600 group-hover:bg-rose-600 group-hover:text-white transition-colors duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <span class="text-xs font-medium text-rose-600 bg-rose-50 px-2 py-1 rounded-full">Partisipasi</span>
            </div>
            <div>
                <h3 class="text-3xl font-bold text-slate-800 mb-1">{{ $stats['keikutsertaan'] }}%</h3>
                <p class="text-sm text-slate-500">Rata-rata Partisipasi</p>
            </div>
        </div>
    </div>
</div>
