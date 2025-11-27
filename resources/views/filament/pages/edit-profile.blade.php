<x-filament-panels::page>
    @livewire(\Filament\Schemas\Livewire\SchemaComponent::class, [
        'schema' => $this->form($this->makeSchema()),
    ])

    <x-filament-actions::modals />
</x-filament-panels::page>
