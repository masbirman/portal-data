<x-filament-panels::page>
    <form wire:submit="save">
        {{-- Status Website --}}
        <x-filament::section style="margin-bottom: 1.5rem;">
            <x-slot name="heading">
                Status Website
            </x-slot>
            <x-slot name="description">
                Kontrol ketersediaan website untuk publik
            </x-slot>

            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer;">
                    <input type="checkbox" wire:model.live="site_active"
                        style="width: 1.25rem; height: 1.25rem; border-radius: 0.25rem;" />
                    <div>
                        <span style="font-weight: 500;">Status Website Aktif</span>
                        <p style="font-size: 0.875rem; color: #6b7280;">Jika dinonaktifkan, pengunjung akan melihat
                            halaman maintenance</p>
                    </div>
                </label>

                @if ($site_active)
                    <div
                        style="padding: 0.75rem 1rem; background: #dcfce7; border-radius: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                        <span style="color: #16a34a;">✓</span>
                        <span style="color: #166534;">Website aktif dan dapat diakses publik</span>
                    </div>
                @else
                    <div
                        style="padding: 0.75rem 1rem; background: #fef3c7; border-radius: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                        <span style="color: #d97706;">⚠️</span>
                        <span style="color: #92400e;">Website dalam mode maintenance</span>
                    </div>
                @endif
            </div>
        </x-filament::section>

        {{-- Pengaturan Maintenance --}}
        <x-filament::section style="margin-bottom: 1.5rem;">
            <x-slot name="heading">
                Pengaturan Maintenance
            </x-slot>
            <x-slot name="description">
                Konfigurasi tampilan halaman maintenance
            </x-slot>

            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <div>
                    <label style="display: block; font-weight: 500; margin-bottom: 0.5rem;">Pesan Maintenance</label>
                    <textarea wire:model="maintenance_message" rows="3"
                        style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; resize: vertical;"
                        placeholder="Masukkan pesan yang akan ditampilkan saat maintenance"></textarea>
                    <p style="font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem;">Pesan yang ditampilkan kepada
                        pengunjung saat maintenance</p>
                </div>

                <div>
                    <label style="display: block; font-weight: 500; margin-bottom: 0.5rem;">Estimasi Waktu
                        Selesai</label>
                    <input type="text" wire:model="maintenance_estimated_time"
                        style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem;"
                        placeholder="Contoh: 2 jam, 30 menit, dll" />
                    <p style="font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem;">Informasi estimasi waktu
                        maintenance selesai (opsional)</p>
                </div>

                <div>
                    <label style="display: block; font-weight: 500; margin-bottom: 0.5rem;">Gambar Ilustrasi</label>
                    @if ($maintenance_image)
                        <div style="margin-bottom: 1rem;">
                            <img src="{{ Storage::disk('public')->url($maintenance_image) }}" alt="Maintenance Image"
                                style="max-width: 200px; border-radius: 0.5rem; border: 1px solid #e5e7eb;" />
                            <button type="button" wire:click="$set('maintenance_image', null)"
                                style="display: block; margin-top: 0.5rem; color: #dc2626; font-size: 0.875rem; cursor: pointer; background: none; border: none;">
                                Hapus gambar
                            </button>
                        </div>
                    @endif
                    <input type="file" wire:model="maintenance_image" accept="image/jpeg,image/png,image/svg+xml"
                        style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.5rem;" />
                    <p style="font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem;">Upload gambar ilustrasi untuk
                        halaman maintenance (max 2MB, JPG/PNG/SVG)</p>
                </div>
            </div>
        </x-filament::section>

        {{-- Save Button --}}
        <div style="display: flex; justify-content: flex-end;">
            <x-filament::button type="submit" color="primary" icon="heroicon-o-check">
                Simpan Perubahan
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
