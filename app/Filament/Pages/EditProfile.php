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
                            ->directory('avatars')
                            ->disk('public')
                            ->visibility('public'),

                        TextInput::make('name')
                            ->label('Nama')
                            ->required(),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required(),
                    ]),

                Section::make('Ubah Password')
                    ->description('Kosongkan jika tidak ingin mengubah password')
                    ->schema([
                        TextInput::make('current_password')
                            ->label('Password Saat Ini')
                            ->password()
                            ->dehydrated(false),

                        TextInput::make('new_password')
                            ->label('Password Baru')
                            ->password()
                            ->dehydrated(false)
                            ->confirmed(),

                        TextInput::make('new_password_confirmation')
                            ->label('Konfirmasi Password Baru')
                            ->password()
                            ->dehydrated(false),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $user = auth()->user();

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

        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        if (array_key_exists('avatar', $data) && $data['avatar'] !== $user->avatar) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $data['avatar'];
        }

        $user->save();

        Notification::make()
            ->title('Profile berhasil diperbarui')
            ->success()
            ->send();

        redirect(static::getUrl());
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Pengaturan';
    }
}
