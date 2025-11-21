<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
    @foreach($stats as $jenjang => $jumlah)
        <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-lg shadow-lg p-6 text-white text-center transform hover:scale-105 transition-transform duration-200">
            <h3 class="text-sm font-semibold mb-2 uppercase tracking-wide opacity-90">{{ $jenjang }}</h3>
            <p class="text-4xl font-bold">{{ $jumlah }}</p>
            <p class="text-xs mt-2 opacity-75">Satuan Pendidikan</p>
        </div>
    @endforeach
</div>
