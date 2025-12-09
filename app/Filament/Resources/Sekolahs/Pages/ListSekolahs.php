<?php

namespace App\Filament\Resources\Sekolahs\Pages;

use App\Filament\Resources\Sekolahs\SekolahResource;
use App\Models\JenjangPendidikan;
use App\Models\Sekolah;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListSekolahs extends ListRecords
{
    protected static string $resource = SekolahResource::class;

    public function getTabs(): array
    {
        $tabs = [];

        // Tab "Semua"
        $tabs['semua'] = Tab::make('Semua')
            ->icon('heroicon-o-building-library')
            ->badge(Sekolah::count());

        // Tab untuk setiap jenjang
        foreach (JenjangPendidikan::orderBy('id')->get() as $jenjang) {
            $tabs["jenjang-{$jenjang->id}"] = Tab::make($jenjang->nama)
                ->icon('heroicon-o-academic-cap')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('jenjang_pendidikan_id', $jenjang->id))
                ->badge(Sekolah::where('jenjang_pendidikan_id', $jenjang->id)->count());
        }

        return $tabs;
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'semua';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('download_template')
                ->label('Download Template')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(function () {
                    return response()->download(storage_path('app/templates/template_sekolah.xlsx'));
                }),
            Actions\Action::make('import')
                ->label('Import Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->form([
                    \Filament\Forms\Components\Select::make('siklus_asesmen_id')
                        ->label('Tahun Asesmen')
                        ->options(\App\Models\SiklusAsesmen::pluck('nama', 'id'))
                        ->required(),
                    \Filament\Forms\Components\FileUpload::make('file')
                        ->label('File Excel')
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                        ->disk('public')
                        ->directory('imports')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $filePath = \Illuminate\Support\Facades\Storage::disk('public')->path($data['file']);

                    \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\SekolahImport($data['siklus_asesmen_id']), $filePath);

                    Notification::make()
                        ->title('Import Berhasil')
                        ->success()
                        ->send();
                }),
            Actions\Action::make('sync_status')
                ->label('Sync Status')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Sinkronisasi Status Sekolah')
                ->modalDescription('Ini akan mengupdate status sekolah (Negeri/Swasta) dari data CSV Kemendikdasmen berdasarkan NPSN. Lanjutkan?')
                ->action(function () {
                    $result = $this->syncStatusFromCsv();

                    if ($result['updated'] > 0) {
                        Notification::make()
                            ->title('Sync Status Selesai')
                            ->body("Updated: {$result['updated']}, Skipped: {$result['skipped']}, Not Found: {$result['not_found']}")
                            ->success()
                            ->persistent()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Tidak Ada Perubahan')
                            ->body("Semua status sudah sinkron. Skipped: {$result['skipped']}, Not Found: {$result['not_found']}")
                            ->info()
                            ->persistent()
                            ->send();
                    }
                }),
        ];
    }

    protected function syncStatusFromCsv(): array
    {
        $csvDir = base_path('kabupaten_csv');
        $files = glob($csvDir . '/*.csv');

        // Load status from CSV indexed by NPSN
        $csvStatus = [];
        foreach ($files as $file) {
            $handle = fopen($file, 'r');
            $header = fgetcsv($handle);
            $columns = array_flip($header);

            while (($row = fgetcsv($handle)) !== false) {
                $npsn = isset($columns['npsn']) && isset($row[$columns['npsn']])
                    ? trim($row[$columns['npsn']]) : null;
                $status = isset($columns['status_sekolah']) && isset($row[$columns['status_sekolah']])
                    ? trim($row[$columns['status_sekolah']]) : null;

                if ($npsn && $status) {
                    // Normalize status: NEGERI -> Negeri, SWASTA -> Swasta
                    $csvStatus[$npsn] = ucfirst(strtolower($status));
                }
            }
            fclose($handle);
        }

        // Update schools
        $stats = ['updated' => 0, 'skipped' => 0, 'not_found' => 0];

        $schools = Sekolah::withoutGlobalScopes()
            ->whereNotNull('npsn')
            ->where('npsn', '!=', '')
            ->get();

        foreach ($schools as $school) {
            if (!isset($csvStatus[$school->npsn])) {
                $stats['not_found']++;
                continue;
            }

            $newStatus = $csvStatus[$school->npsn];

            if ($school->status_sekolah === $newStatus) {
                $stats['skipped']++;
                continue;
            }

            $school->status_sekolah = $newStatus;
            $school->save();
            $stats['updated']++;
        }

        return $stats;
    }
}
