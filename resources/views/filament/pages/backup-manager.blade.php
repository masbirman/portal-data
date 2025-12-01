<x-filament-panels::page>
    <div class="space-y-8">
        {{-- Action Buttons --}}
        <div class="flex gap-4">
            <x-filament::button wire:click="createBackup" wire:loading.attr="disabled" icon="heroicon-o-cloud-arrow-up"
                color="primary" size="lg">
                <span wire:loading.remove wire:target="createBackup">Backup Sekarang</span>
                <span wire:loading wire:target="createBackup">Memproses...</span>
            </x-filament::button>

            <x-filament::button wire:click="loadBackups" wire:loading.attr="disabled" icon="heroicon-o-arrow-path"
                color="gray" size="lg">
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
                                <th class="text-left py-4 px-4 font-medium">Nama File</th>
                                <th class="text-left py-4 px-4 font-medium">Ukuran</th>
                                <th class="text-left py-4 px-4 font-medium">Tanggal</th>
                                <th class="text-right py-4 px-4 font-medium">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($backups as $backup)
                                <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="py-4 px-4 font-mono text-xs">{{ $backup['name'] }}</td>
                                    <td class="py-4 px-4">{{ $this->formatBytes((int) $backup['size']) }}</td>
                                    <td class="py-4 px-4">
                                        {{ \Carbon\Carbon::parse($backup['created_at'])->format('d M Y, H:i') }}</td>
                                    <td class="py-4 px-4">
                                        <div class="flex justify-end gap-2">
                                            <x-filament::icon-button
                                                wire:click="downloadBackup('{{ $backup['id'] }}', '{{ $backup['name'] }}')"
                                                icon="heroicon-o-arrow-down-tray" color="info" tooltip="Download" />
                                            <x-filament::icon-button
                                                wire:click="restoreBackup('{{ $backup['id'] }}', '{{ $backup['name'] }}')"
                                                wire:confirm="Restore backup ini? Data akan ditimpa!"
                                                icon="heroicon-o-arrow-path" color="warning" tooltip="Restore" />
                                            <x-filament::icon-button wire:click="deleteBackup('{{ $backup['id'] }}')"
                                                wire:confirm="Hapus backup ini?" icon="heroicon-o-trash" color="danger"
                                                tooltip="Hapus" />
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-16">
                    <p class="text-6xl mb-6">☁️</p>
                    <p class="text-gray-600 dark:text-gray-400">Belum ada backup tersimpan di Google Drive</p>
                    <p class="text-sm mt-2 text-gray-400">Klik tombol Backup Sekarang untuk membuat backup pertama</p>
                </div>
            @endif
        </x-filament::section>

        {{-- Settings --}}
        <x-filament::section collapsible>
            <x-slot name="heading">
                Pengaturan Backup
            </x-slot>

            <form wire:submit="saveSettings">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    {{-- Backup Otomatis --}}
                    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-6">
                        <h4 class="font-semibold mb-6 flex items-center gap-3 text-base">
                            <span class="text-2xl">🕐</span>
                            <span>Backup Otomatis</span>
                        </h4>
                        <div class="space-y-5">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" wire:model.live="scheduled_backup_enabled"
                                    class="w-5 h-5 rounded border-gray-300 text-primary-600">
                                <span>Aktifkan Backup Terjadwal</span>
                            </label>
                            @if ($scheduled_backup_enabled)
                                <div class="space-y-4 mt-4 ml-8 p-4 bg-white dark:bg-gray-900 rounded-lg">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-2">Jadwal</label>
                                        <select wire:model="backup_schedule"
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800">
                                            <option value="daily">Setiap Hari</option>
                                            <option value="weekly">Setiap Minggu</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-2">Waktu</label>
                                        <input type="time" wire:model="backup_time"
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800">
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Keamanan --}}
                    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-6">
                        <h4 class="font-semibold mb-6 flex items-center gap-3 text-base">
                            <span class="text-2xl">🔒</span>
                            <span>Keamanan</span>
                        </h4>
                        <div class="space-y-5">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" wire:model.live="encryption_enabled"
                                    class="w-5 h-5 rounded border-gray-300 text-primary-600">
                                <span>Enkripsi File Backup</span>
                            </label>
                            @if ($encryption_enabled)
                                <div class="mt-4 ml-8 p-4 bg-white dark:bg-gray-900 rounded-lg">
                                    <label class="block text-sm font-medium text-gray-600 mb-2">Password</label>
                                    <input type="password" wire:model="encryption_password"
                                        placeholder="Masukkan password"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800">
                                    <p class="text-xs text-gray-400 mt-2">Diperlukan saat restore</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Retention --}}
                    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-6">
                        <h4 class="font-semibold mb-6 flex items-center gap-3 text-base">
                            <span class="text-2xl">🗑️</span>
                            <span>Retention</span>
                        </h4>
                        <div class="space-y-5">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" wire:model.live="auto_delete_enabled"
                                    class="w-5 h-5 rounded border-gray-300 text-primary-600">
                                <span>Auto-Delete Backup Lama</span>
                            </label>
                            @if ($auto_delete_enabled)
                                <div class="flex items-center gap-3 mt-4 ml-8">
                                    <span class="text-gray-600">Simpan selama</span>
                                    <input type="number" wire:model="retention_days" min="1" max="365"
                                        class="w-20 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-center">
                                    <span class="text-gray-600">hari</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Notifikasi --}}
                    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-6">
                        <h4 class="font-semibold mb-6 flex items-center gap-3 text-base">
                            <span class="text-2xl">📱</span>
                            <span>Notifikasi</span>
                        </h4>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" wire:model="telegram_notification_enabled"
                                class="w-5 h-5 rounded border-gray-300 text-primary-600">
                            <span>Kirim Notifikasi Telegram</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end mt-8 pt-6 border-t dark:border-gray-700">
                    <x-filament::button type="submit" color="success" icon="heroicon-o-check" size="lg">
                        Simpan Pengaturan
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>
    </div>
</x-filament-panels::page>
