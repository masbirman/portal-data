<?php

namespace App\Filament\Resources\Wilayahs;

use App\Filament\Resources\Wilayahs\Pages\CreateWilayah;
use App\Filament\Resources\Wilayahs\Pages\EditWilayah;
use App\Filament\Resources\Wilayahs\Pages\ListWilayahs;
use App\Filament\Resources\Wilayahs\Pages\ViewWilayah;
use App\Filament\Resources\Wilayahs\Schemas\WilayahForm;
use App\Filament\Resources\Wilayahs\Schemas\WilayahInfolist;
use App\Filament\Resources\Wilayahs\Tables\WilayahsTable;
use App\Models\Wilayah;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WilayahResource extends Resource
{
    protected static ?string $model = Wilayah::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-map';

    protected static string | \UnitEnum | null $navigationGroup = 'Data Master';

    protected static ?string $modelLabel = 'Kota/Kabupaten';

    protected static ?string $pluralModelLabel = 'Data Kota/Kabupaten';

    protected static ?string $recordTitleAttribute = 'nama';

    public static function form(Schema $schema): Schema
    {
        return WilayahForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WilayahInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WilayahsTable::configure($table);
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
            'index' => ListWilayahs::route('/'),
            'create' => CreateWilayah::route('/create'),
            'edit' => EditWilayah::route('/{record}/edit'),
        ];
    }
}
