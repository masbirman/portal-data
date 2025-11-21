<?php

namespace App\Filament\Resources\PelaksanaanAsesmens;

use App\Filament\Resources\PelaksanaanAsesmens\Pages\CreatePelaksanaanAsesmen;
use App\Filament\Resources\PelaksanaanAsesmens\Pages\EditPelaksanaanAsesmen;
use App\Filament\Resources\PelaksanaanAsesmens\Pages\ListPelaksanaanAsesmens;
use App\Filament\Resources\PelaksanaanAsesmens\Schemas\PelaksanaanAsesmenForm;
use App\Filament\Resources\PelaksanaanAsesmens\Schemas\PelaksanaanAsesmenInfolist;
use App\Filament\Resources\PelaksanaanAsesmens\Tables\PelaksanaanAsesmensTable;
use App\Models\PelaksanaanAsesmen;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PelaksanaanAsesmenResource extends Resource
{
    protected static ?string $model = PelaksanaanAsesmen::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static string | \UnitEnum | null $navigationGroup = 'Data Master';

    protected static ?string $modelLabel = 'Pelaksanaan Asesmen';

    protected static ?string $pluralModelLabel = 'Data Pelaksanaan Asesmen';

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return PelaksanaanAsesmenForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PelaksanaanAsesmenInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PelaksanaanAsesmensTable::configure($table);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'siklusAsesmen',
                'sekolah.jenjangPendidikan',
                'sekolah.wilayah',
                'wilayah'
            ]);
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
            'index' => ListPelaksanaanAsesmens::route('/'),
            'create' => CreatePelaksanaanAsesmen::route('/create'),
            'edit' => EditPelaksanaanAsesmen::route('/{record}/edit'),
        ];
    }
}
