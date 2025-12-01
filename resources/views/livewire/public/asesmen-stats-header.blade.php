<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">
    @foreach ($stats as $jenjang => $jumlah)
        <div
            class="bg-slate-900 rounded-lg shadow-lg p-4 text-white text-center transform hover:scale-105 transition-transform duration-200">
            <h3 class="text-sm font-semibold mb-1 uppercase tracking-wide text-slate-400">{{ $jenjang }}</h3>
            <p class="text-3xl font-bold text-amber-400">{{ $jumlah }}</p>
            <p class="text-xs mt-1 text-slate-500">Satuan Pendidikan</p>
        </div>
    @endforeach
</div>
