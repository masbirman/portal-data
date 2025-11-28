<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class EditProfile extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationLabel = 'Profile';

    protected static ?string $title = 'Edit Profile';

    protected static ?string $slug = 'edit-profile';

    public ?array $data = [];

    public function getView(): string
    {
        return 'filament.pages.edit-profile';
    }

    public function mount(): void
    {
        $this->form->fill([
            'name' => auth()->user()->name,
            'email' => auth()->user()->email,
            'avatar' => auth()->user()->avatar,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Profile')
                    ->schema([
                        FileUpload::make('avatar')
                            ->label('Avatar')
                            ->image()
                            ->disk('public')
                            ->directory('avatars')
                            ->visibility('public')
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('1:1')
                            ->imageResizeTargetWidth('200')
                            ->imageResizeTargetHeight('200')
                            ->maxSize(2048)
                            ->nullable()
                            ->columnSpanFull(),

                        TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                    ]),

                Section::make('Ubah Password')
                    ->description('Kosongkan jika tidak ingin mengubah password')
                    ->schema([
                        TextInput::make('current_password')
                            ->label('Password Saat Ini')
                            ->password()
                            ->revealable()
                            ->dehydrated(false),

                        TextInput::make('new_password')
                            ->label('Password Baru')
                            ->password()
                            ->revealable()
                            ->dehydrated(false)
                            ->confirmed(),

                        TextInput::make('new_password_confirmation')
                            ->label('Konfirmasi Password Baru')
                            ->password()
                            ->revealable()
                            ->dehydrated(false),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $user = auth()->user();

        // Validasi password jika diisi
        if (!empty($data['current_password'])) {
            if (!Hash::check($data['current_password'], $user->password)) {
                Notification::make()
                    ->title('Password saat ini tidak sesuai')
                    ->danger()
                    ->send();
                return;
            }

            if (!empty($data['new_password'])) {
                $user->password = Hash::make($data['new_password']);
            }
        }

        // Update data user
        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        // Handle avatar upload
        if (array_key_exists('avatar', $data)) {
            if ($data['avatar'] !== $user->avatar) {
                // Hapus avatar lama jika ada
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $user->avatar = $data['avatar'];
            }
        }

        $user->save();

        Notification::make()
            ->title('Profile berhasil diperbarui')
            ->success()
            ->send();

        // Redirect untuk refresh data
        redirect()->to(static::getUrl());
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Pengaturan';
    }
}
