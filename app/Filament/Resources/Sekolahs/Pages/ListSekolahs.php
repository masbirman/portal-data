<?php

namespace App\Filament\Resources\Sekolahs\Pages;

use App\Filament\Resources\Sekolahs\SekolahResource;
use App\Models\JenjangPendidikan;
use App\Models\Sekolah;
use Filament\Actions;
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

                    \Filament\Notifications\Notification::make()
                        ->title('Import Berhasil')
                        ->success()
                        ->send();
                }),
        ];
    }
}
