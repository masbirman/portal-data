<?php

namespace App\Filament\Resources\SiklusAsesmens;

use App\Filament\Resources\SiklusAsesmens\Pages\CreateSiklusAsesmen;
use App\Filament\Resources\SiklusAsesmens\Pages\EditSiklusAsesmen;
use App\Filament\Resources\SiklusAsesmens\Pages\ListSiklusAsesmens;
use App\Filament\Resources\SiklusAsesmens\Pages\ViewSiklusAsesmen;
use App\Filament\Resources\SiklusAsesmens\Schemas\SiklusAsesmenForm;
use App\Filament\Resources\SiklusAsesmens\Schemas\SiklusAsesmenInfolist;
use App\Filament\Resources\SiklusAsesmens\Tables\SiklusAsesmensTable;
use App\Models\SiklusAsesmen;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SiklusAsesmenResource extends Resource
{
    protected static ?string $model = SiklusAsesmen::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-calendar';

    protected static string | \UnitEnum | null $navigationGroup = 'Data Master';

    protected static ?string $modelLabel = 'Tahun Asesmen';

    protected static ?string $pluralModelLabel = 'Data Tahun Asesmen';

    protected static ?string $recordTitleAttribute = 'nama';

    public static function form(Schema $schema): Schema
    {
        return SiklusAsesmenForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SiklusAsesmenInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SiklusAsesmensTable::configure($table);
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
            'index' => ListSiklusAsesmens::route('/'),
            'create' => CreateSiklusAsesmen::route('/create'),
            'edit' => EditSiklusAsesmen::route('/{record}/edit'),
        ];
    }
}
