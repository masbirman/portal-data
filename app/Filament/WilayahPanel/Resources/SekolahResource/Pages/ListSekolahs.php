<?php

namespace App\Filament\WilayahPanel\Resources\SekolahResource\Pages;

use App\Filament\WilayahPanel\Resources\SekolahResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListSekolahs extends ListRecords
{
    protected static string $resource = SekolahResource::class;

    public function getTabs(): array
    {
        $user = auth()->user();
        $tabs = [];

        // Tab "Semua" untuk menampilkan semua data (default aktif)
        $tabs['semua'] = Tab::make('Semua')
            ->icon('heroicon-o-building-library');

        // Buat tab untuk setiap jenjang yang ditugaskan
        if ($user && $user->isAdminWilayah()) {
            foreach ($user->jenjangs as $jenjang) {
                $jenjangId = $jenjang->id;
                $tabs["jenjang-{$jenjangId}"] = Tab::make($jenjang->nama)
                    ->icon('heroicon-o-academic-cap')
                    ->modifyQueryUsing(fn (Builder $query) => $query->where('jenjang_pendidikan_id', $jenjangId))
                    ->badge(\App\Models\Sekolah::query()
                        ->whereIn('wilayah_id', $user->getWilayahIds())
                        ->where('jenjang_pendidikan_id', $jenjangId)
                        ->count()
                    );
            }
        }

        return $tabs;
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'semua';
    }
}
