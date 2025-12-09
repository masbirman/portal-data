<x-filament-widgets::widget>
    <x-filament::section class="!p-0 overflow-hidden">
        <div class="relative flex items-center justify-between px-6 py-4" style="background: linear-gradient(135deg, #5D87FF 0%, #49BEFF 100%); min-height: 100px;">
            {{-- Content --}}
            <div class="z-10">
                <h2 class="text-xl font-semibold text-white mb-1">
                    {{ $this->getGreeting() }}, {{ $this->getUserName() }}! 👋
                </h2>
                <p class="text-white/80 text-sm">
                    Kelola data asesmen dengan mudah dan efisien
                </p>
            </div>
            
            {{-- Character Image --}}
            <div class="hidden md:block absolute right-4 bottom-0 z-0">
                <img 
                    src="{{ asset('images/welcome-character.png') }}" 
                    alt="Welcome" 
                    class="h-28 object-contain"
                    style="filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));"
                />
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
