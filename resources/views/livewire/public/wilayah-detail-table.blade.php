<div>
    <!-- Tabs Filter Jenjang -->
    <div class="mb-6">
        <div class="flex flex-wrap gap-2">
            <button 
                wire:click="setJenjangFilter('all')"
                class="px-4 py-2 rounded-lg font-semibold transition-colors duration-150
                    {{ $jenjangFilter === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                SEMUA
            </button>
            @foreach($jenjangList as $jenjang)
                <button 
                    wire:click="setJenjangFilter('{{ $jenjang->nama }}')"
                    class="px-4 py-2 rounded-lg font-semibold transition-colors duration-150
                        {{ $jenjangFilter === $jenjang->nama ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    {{ strtoupper($jenjang->nama) }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Search Bar -->
    <div class="mb-4">
        <input 
            type="text" 
            wire:model.live.debounce.300ms="search" 
            placeholder="Cari nama sekolah..." 
            class="w-full md:w-1/2 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        >
    </div>

    <!-- Table -->
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-blue-600 text-white">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider">Nama Sekolah</th>
                    <th class="px-6 py-3 text-center text-xs font-bold uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-bold uppercase tracking-wider">Peserta</th>
                    <th class="px-6 py-3 text-center text-xs font-bold uppercase tracking-wider">Partisipasi Literasi (%)</th>
                    <th class="px-6 py-3 text-center text-xs font-bold uppercase tracking-wider">Partisipasi Numerasi (%)</th>
                    <th class="px-6 py-3 text-center text-xs font-bold uppercase tracking-wider">Keterangan</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($data as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $item->sekolah->nama }}</div>
                            <div class="text-xs text-gray-500">{{ $item->sekolah->jenjangPendidikan->nama }} - {{ $item->sekolah->kode_sekolah }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($item->sekolah->status_sekolah)
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $item->sekolah->status_sekolah === 'Negeri' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $item->sekolah->status_sekolah }}
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900 font-semibold">
                            {{ $item->jumlah_peserta ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="text-sm font-semibold
                                {{ $item->partisipasi_literasi >= 100 ? 'text-green-600' : ($item->partisipasi_literasi >= 80 ? 'text-blue-600' : 'text-orange-600') }}">
                                {{ number_format($item->partisipasi_literasi, 2) }}%
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="text-sm font-semibold
                                {{ $item->partisipasi_numerasi >= 100 ? 'text-green-600' : ($item->partisipasi_numerasi >= 80 ? 'text-blue-600' : 'text-orange-600') }}">
                                {{ number_format($item->partisipasi_numerasi, 2) }}%
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center text-sm text-gray-600">
                            {{ $item->keterangan ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="mt-2">Tidak ada data ditemukan</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $data->links() }}
    </div>

    <!-- Loading Indicator -->
    <div wire:loading class="fixed top-0 left-0 right-0 bg-blue-500 h-1 z-50">
        <div class="h-full bg-blue-600 animate-pulse"></div>
    </div>
</div>
