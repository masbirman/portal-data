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
                Daftar Backup di Google Drive
            </x-slot>

            @if (count($backups) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b dark:border-gray-700">
                                <th class="text-left py-3 px-4 font-medium">Nama File</th>
                                <th class="text-left py-3 px-4 font-medium">Ukuran</th>
                                <th class="text-left py-3 px-4 font-medium">Tanggal</th>
                                <th class="text-right py-3 px-4 font-medium">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($backups as $backup)
                                <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="py-3 px-4 font-mono text-xs">{{ $backup['name'] }}</td>
                                    <td class="py-3 px-4">{{ $this->formatBytes((int) $backup['size']) }}</td>
                                    <td class="py-3 px-4">
                                        {{ \Carbon\Carbon::parse($backup['created_at'])->format('d M Y, H:i') }}</td>
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
                <div class="text-center py-12 text-gray-500">
                    <p class="text-5xl mb-4">☁️</p>
                    <p class="text-sm">Belum ada backup tersimpan di Google Drive</p>
                    <p class="text-xs mt-2 text-gray-400">Klik "Backup Sekarang" untuk membuat backup pertama</p>
                </div>
            @endif
        </x-filament::section>

        {{-- Settings --}}
        <x-filament::section collapsible>
            <x-slot name="heading">
                Pengaturan Backup
            </x-slot>

            <form wire:submit="saveSettings">
                <div class="space-y-6">
                    {{-- Backup Otomatis --}}
                    <div class="border dark:border-gray-700 rounded-lg p-4">
                        <h4 class="font-semibold text-sm mb-4 flex items-center gap-2">
                            <span>🕐</span> Backup Otomatis
                        </h4>
                        <div class="space-y-4">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" wire:model.live="scheduled_backup_enabled"
                                    class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                <span class="text-sm">Aktifkan Backup Terjadwal</span>
                            </label>
                            @if ($scheduled_backup_enabled)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 pl-6">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Jadwal</label>
                                        <select wire:model="backup_schedule"
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm">
                                            <option value="daily">Setiap Hari</option>
                                            <option value="weekly">Setiap Minggu (Senin)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Waktu</label>
                                        <input type="time" wire:model="backup_time"
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm">
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Keamanan --}}
                    <div class="border dark:border-gray-700 rounded-lg p-4">
                        <h4 class="font-semibold text-sm mb-4 flex items-center gap-2">
                            <span>🔒</span> Keamanan
                        </h4>
                        <div class="space-y-4">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" wire:model.live="encryption_enabled"
                                    class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                <span class="text-sm">Enkripsi File Backup</span>
                            </label>
                            @if ($encryption_enabled)
                                <div class="pl-6">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Password
                                        Enkripsi</label>
                                    <input type="password" wire:model="encryption_password"
                                        placeholder="Masukkan password"
                                        class="w-full md:w-1/2 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm">
                                    <p class="text-xs text-gray-400 mt-1">Password ini diperlukan saat restore backup
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Retention --}}
                    <div class="border dark:border-gray-700 rounded-lg p-4">
                        <h4 class="font-semibold text-sm mb-4 flex items-center gap-2">
                            <span>🗑️</span> Retention
                        </h4>
                        <div class="space-y-4">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" wire:model.live="auto_delete_enabled"
                                    class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                <span class="text-sm">Auto-Delete Backup Lama</span>
                            </label>
                            @if ($auto_delete_enabled)
                                <div class="flex items-center gap-2 pl-6">
                                    <span class="text-sm text-gray-600">Simpan backup selama</span>
                                    <input type="number" wire:model="retention_days" min="1" max="365"
                                        class="w-20 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm text-center">
                                    <span class="text-sm text-gray-600">hari</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Notifikasi --}}
                    <div class="border dark:border-gray-700 rounded-lg p-4">
                        <h4 class="font-semibold text-sm mb-4 flex items-center gap-2">
                            <span>📱</span> Notifikasi
                        </h4>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" wire:model="telegram_notification_enabled"
                                class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            <span class="text-sm">Kirim Notifikasi Telegram saat backup selesai</span>
                        </label>
                    </div>

                    {{-- Submit Button --}}
                    <div class="flex justify-end pt-4 border-t dark:border-gray-700">
                        <x-filament::button type="submit" color="success" icon="heroicon-o-check">
                            Simpan Pengaturan
                        </x-filament::button>
                    </div>
                </div>
            </form>
        </x-filament::section>
    </div>
</x-filament-panels::page>
