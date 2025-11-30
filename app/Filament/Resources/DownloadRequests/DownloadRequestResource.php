<?php

namespace App\Filament\Resources\DownloadRequests;

use App\Filament\Resources\DownloadRequests\Pages\CreateDownloadRequest;
use App\Filament\Resources\DownloadRequests\Pages\EditDownloadRequest;
use App\Filament\Resources\DownloadRequests\Pages\ListDownloadRequests;
use App\Filament\Resources\DownloadRequests\Schemas\DownloadRequestForm;
use App\Filament\Resources\DownloadRequests\Tables\DownloadRequestsTable;
use App\Models\DownloadRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DownloadRequestResource extends Resource
{
    protected static ?string $model = DownloadRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Request Download';

    protected static ?string $modelLabel = 'Request Download';

    protected static ?string $pluralModelLabel = 'Request Download';

    protected static ?int $navigationSort = 99;

    public static function form(Schema $schema): Schema
    {
        return DownloadRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DownloadRequestsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDownloadRequests::route('/'),
            'create' => CreateDownloadRequest::route('/create'),
            'edit' => EditDownloadRequest::route('/{record}/edit'),
        ];
    }
}
