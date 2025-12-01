<x-filament-panels::page>
    {{-- Action Buttons --}}
    <div style="display: flex; gap: 1rem; margin-bottom: 2rem;">
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
    <x-filament::section style="margin-bottom: 2rem;">
        <x-slot name="heading">
            Daftar Backup di Google Drive
        </x-slot>

        @if (count($backups) > 0)
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                        <th style="text-align: left; padding: 1rem;">Nama File</th>
                        <th style="text-align: left; padding: 1rem;">Ukuran</th>
                        <th style="text-align: left; padding: 1rem;">Tanggal</th>
                        <th style="text-align: right; padding: 1rem;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($backups as $backup)
                        <tr style="border-bottom: 1px solid #e5e7eb;">
                            <td style="padding: 1rem; font-family: monospace; font-size: 0.75rem;">{{ $backup['name'] }}
                            </td>
                            <td style="padding: 1rem;">{{ $this->formatBytes((int) $backup['size']) }}</td>
                            <td style="padding: 1rem;">
                                {{ \Carbon\Carbon::parse($backup['created_at'])->format('d M Y, H:i') }}</td>
                            <td style="padding: 1rem; text-align: right;">
                                <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                                    <x-filament::icon-button
                                        wire:click="downloadBackup('{{ $backup['id'] }}', '{{ $backup['name'] }}')"
                                        icon="heroicon-o-arrow-down-tray" color="info" tooltip="Download" />
                                    <x-filament::icon-button
                                        wire:click="restoreBackup('{{ $backup['id'] }}', '{{ $backup['name'] }}')"
                                        wire:confirm="Restore backup ini?" icon="heroicon-o-arrow-path" color="warning"
                                        tooltip="Restore" />
                                    <x-filament::icon-button wire:click="deleteBackup('{{ $backup['id'] }}')"
                                        wire:confirm="Hapus backup ini?" icon="heroicon-o-trash" color="danger"
                                        tooltip="Hapus" />
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div style="text-align: center; padding: 3rem;">
                <p style="font-size: 3rem; margin-bottom: 1rem;">☁️</p>
                <p style="color: #6b7280;">Belum ada backup tersimpan di Google Drive</p>
                <p style="font-size: 0.875rem; color: #9ca3af; margin-top: 0.5rem;">Klik tombol Backup Sekarang untuk
                    membuat backup pertama</p>
            </div>
        @endif
    </x-filament::section>

    {{-- Settings --}}
    <x-filament::section collapsible>
        <x-slot name="heading">
            Pengaturan Backup
        </x-slot>

        <form wire:submit="saveSettings">
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                {{-- Backup Otomatis --}}
                <x-filament::section>
                    <x-slot name="heading">
                        🕐 Backup Otomatis
                    </x-slot>
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer;">
                            <input type="checkbox" wire:model.live="scheduled_backup_enabled"
                                style="width: 1.25rem; height: 1.25rem; border-radius: 0.25rem;" />
                            <span>Aktifkan Backup Terjadwal</span>
                        </label>
                        @if ($scheduled_backup_enabled)
                            <div style="margin-left: 2rem; display: flex; flex-direction: column; gap: 1rem;">
                                <div>
                                    <label
                                        style="display: block; font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem;">Jadwal</label>
                                    <select wire:model="backup_schedule"
                                        style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.5rem;">
                                        <option value="daily">Setiap Hari</option>
                                        <option value="weekly">Setiap Minggu</option>
                                    </select>
                                </div>
                                <div>
                                    <label
                                        style="display: block; font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem;">Waktu</label>
                                    <input type="time" wire:model="backup_time"
                                        style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.5rem;" />
                                </div>
                            </div>
                        @endif
                    </div>
                </x-filament::section>

                {{-- Keamanan --}}
                <x-filament::section>
                    <x-slot name="heading">
                        🔒 Keamanan
                    </x-slot>
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer;">
                            <input type="checkbox" wire:model.live="encryption_enabled"
                                style="width: 1.25rem; height: 1.25rem; border-radius: 0.25rem;" />
                            <span>Enkripsi File Backup</span>
                        </label>
                        @if ($encryption_enabled)
                            <div style="margin-left: 2rem;">
                                <label
                                    style="display: block; font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem;">Password
                                    Enkripsi</label>
                                <input type="password" wire:model="encryption_password" placeholder="Masukkan password"
                                    style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.5rem;" />
                                <p style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.5rem;">Diperlukan saat
                                    restore</p>
                            </div>
                        @endif
                    </div>
                </x-filament::section>

                {{-- Retention --}}
                <x-filament::section>
                    <x-slot name="heading">
                        🗑️ Retention
                    </x-slot>
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer;">
                            <input type="checkbox" wire:model.live="auto_delete_enabled"
                                style="width: 1.25rem; height: 1.25rem; border-radius: 0.25rem;" />
                            <span>Auto-Delete Backup Lama</span>
                        </label>
                        @if ($auto_delete_enabled)
                            <div style="margin-left: 2rem; display: flex; align-items: center; gap: 0.75rem;">
                                <span>Simpan selama</span>
                                <input type="number" wire:model="retention_days" min="1" max="365"
                                    style="width: 5rem; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.5rem; text-align: center;" />
                                <span>hari</span>
                            </div>
                        @endif
                    </div>
                </x-filament::section>

                {{-- Notifikasi --}}
                <x-filament::section>
                    <x-slot name="heading">
                        📱 Notifikasi
                    </x-slot>
                    <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer;">
                        <input type="checkbox" wire:model="telegram_notification_enabled"
                            style="width: 1.25rem; height: 1.25rem; border-radius: 0.25rem;" />
                        <span>Kirim Notifikasi Telegram</span>
                    </label>
                </x-filament::section>
            </div>

            <div
                style="display: flex; justify-content: flex-end; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                <x-filament::button type="submit" color="success" icon="heroicon-o-check">
                    Simpan Pengaturan
                </x-filament::button>
            </div>
        </form>
    </x-filament::section>
</x-filament-panels::page>
