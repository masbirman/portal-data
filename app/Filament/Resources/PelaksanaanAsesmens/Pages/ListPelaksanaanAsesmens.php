<?php

namespace App\Filament\Resources\PelaksanaanAsesmens\Pages;

use App\Filament\Resources\PelaksanaanAsesmens\PelaksanaanAsesmenResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListPelaksanaanAsesmens extends ListRecords
{
    protected static string $resource = PelaksanaanAsesmenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('download_template')
                ->label('Download Template')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(function () {
                    return response()->download(storage_path('app/templates/template_pelaksanaan_asesmen.xlsx'));
                }),
            Actions\Action::make('import')
                ->label('Import Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->form([
                    \Filament\Forms\Components\Select::make('siklus_asesmen_id')
                        ->label('Siklus Asesmen')
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
                    
                    \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\AsesmenImport($data['siklus_asesmen_id']), $filePath);
                    
                    \Filament\Notifications\Notification::make()
                        ->title('Import Berhasil')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function getHeading(): string
    {
        return 'Data Pelaksanaan Asesmen';
    }

    public function getSubheading(): ?string
    {
        $filters = request()->input('tableFilters', []);
        $hasActiveFilter = !empty($filters['siklus_asesmen_id']['values'] ?? []) ||
                           !empty($filters['jenjang_pendidikan']['values'] ?? []) ||
                           !empty($filters['wilayah_id']['values'] ?? []);
        
        if (!$hasActiveFilter) {
            return '💡 Untuk performa terbaik, gunakan filter Tahun, Jenjang, atau Kota/Kabupaten.';
        }
        
        return null;
    }
}
