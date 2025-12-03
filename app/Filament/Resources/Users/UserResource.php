<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages;
use App\Models\User;
use App\Models\Permission;
use App\Models\ActivityLog;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $navigationLabel = 'Daftar User';

    protected static ?string $modelLabel = 'User';

    protected static ?string $pluralModelLabel = 'Users';

    protected static string|\UnitEnum|null $navigationGroup = 'Manajemen User';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Dasar')
                    ->schema([
                        FileUpload::make('avatar')
                            ->label('Avatar')
                            ->image()
                            ->avatar()
                            ->disk('public')
                            ->directory('avatars')
                            ->visibility('public')
                            ->maxSize(1024)
                            ->columnSpanFull(),
                        TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('username')
                            ->label('Username')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('Untuk User Sekolah, gunakan kode sekolah'),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->nullable()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => $state ? Hash::make($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->minLength(8),
                        Select::make('role')
                            ->label('Role')
                            ->options([
                                'super_admin' => 'Super Admin',
                                'admin_wilayah' => 'Admin Wilayah',
                                'user_sekolah' => 'User Sekolah',
                            ])
                            ->required()
                            ->live(),
                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ])
                    ->columns(2),

                Section::make('Pengaturan Admin Wilayah')
                    ->schema([
                        Select::make('wilayahs')
                            ->label('Wilayah yang Ditugaskan')
                            ->relationship('wilayahs', 'nama')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                        Select::make('jenjangs')
                            ->label('Jenjang Pendidikan yang Ditugaskan')
                            ->relationship('jenjangs', 'nama')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                    ])
                    ->columns(2)
                    ->visible(fn (Get $get): bool => $get('role') === 'admin_wilayah'),

                Section::make('Pengaturan User Sekolah')
                    ->schema([
                        Select::make('sekolah_id')
                            ->label('Sekolah')
                            ->relationship('sekolah', 'nama')
                            ->searchable()
                            ->preload(),
                        Select::make('permissions')
                            ->label('Permissions')
                            ->relationship('permissions', 'label')
                            ->multiple()
                            ->preload(),
                    ])
                    ->columns(2)
                    ->visible(fn (Get $get): bool => $get('role') === 'user_sekolah'),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->label('Avatar')
                    ->circular()
                    ->disk('public')
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=7F9CF5&background=EBF4FF'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('username')
                    ->label('Username')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin' => 'danger',
                        'admin_wilayah' => 'warning',
                        'user_sekolah' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'super_admin' => 'Super Admin',
                        'admin_wilayah' => 'Admin Wilayah',
                        'user_sekolah' => 'User Sekolah',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('wilayahs.nama')
                    ->label('Kota/Kabupaten')
                    ->badge()
                    ->separator(', ')
                    ->visible(fn () => true),
                //Tables\Columns\TextColumn::make('sekolah.nama')
                    //->label('Sekolah')
                    //->placeholder('-'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Role')
                    ->options([
                        'super_admin' => 'Super Admin',
                        'admin_wilayah' => 'Admin Wilayah',
                        'user_sekolah' => 'User Sekolah',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
            ])
            ->actions([
                Actions\ActionGroup::make([
                    Actions\EditAction::make(),
                    Actions\Action::make('toggle_active')
                        ->label(fn (User $record): string => $record->is_active ? 'Nonaktifkan' : 'Aktifkan')
                        ->icon(fn (User $record): string => $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                        ->color(fn (User $record): string => $record->is_active ? 'danger' : 'success')
                        ->requiresConfirmation()
                        ->action(function (User $record): void {
                            $record->update(['is_active' => !$record->is_active]);

                            ActivityLog::log(
                                $record->is_active ? 'activate' : 'deactivate',
                                ($record->is_active ? 'Mengaktifkan' : 'Menonaktifkan') . " user: {$record->name}",
                                $record
                            );
                        }),
                    Actions\DeleteAction::make()
                        ->before(function (User $record): void {
                            ActivityLog::log(
                                'delete',
                                "Menghapus user: {$record->name} ({$record->email})",
                                $record
                            );
                        }),
                ]),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
