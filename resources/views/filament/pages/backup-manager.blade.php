<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Action Buttons --}}
        <div class="flex gap-3">
            <x-filament::button wire:click="createBackup" wire:loading.attr="disabled" icon="heroicon-o-cloud-arrow-up"
                color="primary">
                <span wire:loading.remove wire:target="createBackup">Backup Sekarang</span>
                <span wire:loading wire:target="createBackup">Memproses...</span>
            </x-filament::button>

            <x-filament::button wire:click="loadBackups" wire:loading.attr="disabled" icon="heroicon-o-arrow-path"
                color="gray">
                Refresh
            </x-filament::button>
        </div>

        {{-- Backup List --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-cloud class="w-5 h-5" />
                    Daftar Backup di Google Drive
                </div>
            </x-slot>

            @if (count($backups) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-3 px-4">Nama File</th>
                                <th class="text-left py-3 px-4">Ukuran</th>
                                <th class="text-left py-3 px-4">Tanggal</th>
                                <th class="text-right py-3 px-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($backups as $backup)
                                <tr class="border-b hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="py-3 px-4 font-mono text-xs">
                                        {{ $backup['name'] }}
                                    </td>
                                    <td class="py-3 px-4">
                                        {{ $this->formatBytes((int) $backup['size']) }}
                                    </td>
                                    <td class="py-3 px-4">
                                        {{ \Carbon\Carbon::parse($backup['created_at'])->format('d M Y, H:i') }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex justify-end gap-2">
                                            <x-filament::icon-button
                                                wire:click="downloadBackup('{{ $backup['id'] }}', '{{ $backup['name'] }}')"
                                                icon="heroicon-o-arrow-down-tray" color="info" tooltip="Download" />
                                            <x-filament::icon-button
                                                wire:click="restoreBackup('{{ $backup['id'] }}', '{{ $backup['name'] }}')"
                                                wire:confirm="Apakah Anda yakin ingin me-restore backup ini? Data saat ini akan ditimpa!"
                                                icon="heroicon-o-arrow-path" color="warning" tooltip="Restore" />
                                            <x-filament::icon-button wire:click="deleteBackup('{{ $backup['id'] }}')"
                                                wire:confirm="Apakah Anda yakin ingin menghapus backup ini?"
                                                icon="heroicon-o-trash" color="danger" tooltip="Hapus" />
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <x-heroicon-o-cloud class="w-12 h-12 mx-auto mb-3 opacity-50" />
                    <p>Belum ada backup tersimpan di Google Drive</p>
                </div>
            @endif
        </x-filament::section>

        {{-- Settings --}}
        <x-filament::section collapsible collapsed>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-cog-6-tooth class="w-5 h-5" />
                    Pengaturan Backup
                </div>
            </x-slot>

            <form wire:submit="saveSettings" class="space-y-6">
                {{-- Backup Otomatis --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div class="md:col-span-3">
                        <h4 class="font-medium mb-3">Backup Otomatis</h4>
                    </div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" wire:model.live="scheduled_backup_enabled" class="rounded">
                        <span>Aktifkan Backup Terjadwal</span>
                    </label>
                    @if ($scheduled_backup_enabled)
                        <select wire:model="backup_schedule"
                            class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                            <option value="daily">Setiap Hari</option>
                            <option value="weekly">Setiap Minggu</option>
                        </select>
                        <input type="time" wire:model="backup_time"
                            class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                    @endif
                </div>

                {{-- Keamanan --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div class="md:col-span-2">
                        <h4 class="font-medium mb-3">Keamanan</h4>
                    </div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" wire:model.live="encryption_enabled" class="rounded">
                        <span>Enkripsi File Backup</span>
                    </label>
                    @if ($encryption_enabled)
                        <input type="password" wire:model="encryption_password" placeholder="Password Enkripsi"
                            class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                    @endif
                </div>

                {{-- Retention --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div class="md:col-span-2">
                        <h4 class="font-medium mb-3">Retention</h4>
                    </div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" wire:model.live="auto_delete_enabled" class="rounded">
                        <span>Auto-Delete Backup Lama</span>
                    </label>
                    @if ($auto_delete_enabled)
                        <div class="flex items-center gap-2">
                            <span>Simpan backup selama</span>
                            <input type="number" wire:model="retention_days" min="1" max="365"
                                class="w-20 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                            <span>hari</span>
                        </div>
                    @endif
                </div>

                {{-- Notifikasi --}}
                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <h4 class="font-medium mb-3">Notifikasi</h4>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" wire:model="telegram_notification_enabled" class="rounded">
                        <span>Kirim Notifikasi Telegram</span>
                    </label>
                </div>

                <div class="flex justify-end">
                    <x-filament::button type="submit" color="success">
                        Simpan Pengaturan
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>
    </div>
</x-filament-panels::page>
