<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua')
                ->icon('heroicon-o-users')
                ->badge(User::count()),

            'super_admin' => Tab::make('Super Admin')
                ->icon('heroicon-o-shield-check')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('role', 'super_admin'))
                ->badge(User::where('role', 'super_admin')->count()),

            'admin_wilayah' => Tab::make('Admin Wilayah')
                ->icon('heroicon-o-map')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('role', 'admin_wilayah'))
                ->badge(User::where('role', 'admin_wilayah')->count()),

            'user_sekolah' => Tab::make('User Sekolah')
                ->icon('heroicon-o-building-library')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('role', 'user_sekolah'))
                ->badge(User::where('role', 'user_sekolah')->count()),
        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'semua';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
