<div>
    <!-- Search Bar -->
    <div class="mb-4">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari Kabupaten/Kota..."
            class="w-full md:w-1/2 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
    </div>

    <!-- Table -->
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-slate-900 text-white">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider">Kabupaten/Kota</th>
                    @foreach ($jenjangList as $jenjang)
                        <th class="px-6 py-3 text-center text-xs font-bold uppercase tracking-wider">
                            {{ $jenjang->nama }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($wilayahData as $index => $wilayah)
                    <tr class="hover:bg-blue-50 cursor-pointer transition-colors duration-150"
                        onclick="window.location='{{ route('asesmen-nasional.wilayah', ['tahun' => $tahun, 'wilayah' => $wilayah->id]) }}'">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $wilayahData->firstItem() + $index }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if ($wilayah->logo)
                                    <img src="{{ asset('storage/' . $wilayah->logo) }}" alt="{{ $wilayah->nama }}"
                                        class="h-8 w-8 rounded-full mr-3 object-cover"
                                        onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="h-8 w-8 rounded-full mr-3 bg-blue-100 items-center justify-center"
                                        style="display:none;">
                                        <span class="text-blue-600 font-bold text-xs">
                                            {{ substr($wilayah->nama, 0, 2) }}
                                        </span>
                                    </div>
                                @else
                                    <div class="h-8 w-8 rounded-full mr-3 bg-blue-100 flex items-center justify-center">
                                        <span class="text-blue-600 font-bold text-xs">
                                            {{ substr($wilayah->nama, 0, 2) }}
                                        </span>
                                    </div>
                                @endif
                                <span class="text-sm font-medium text-gray-900">{{ $wilayah->nama }}</span>
                            </div>
                        </td>
                        @foreach ($jenjangList as $jenjang)
                            <td
                                class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium {{ $wilayah->stats[$jenjang->nama] > 0 ? 'text-gray-900' : 'text-gray-400' }}">
                                {{ $wilayah->stats[$jenjang->nama] }}
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($jenjangList) + 2 }}" class="px-6 py-8 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
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
        {{ $wilayahData->links() }}
    </div>
</div>
