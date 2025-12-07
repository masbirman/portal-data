<?php

namespace App\Filament\Pages;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class EditProfile extends BaseEditProfile
{
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
                            ->visibility('public')
                            ->maxSize(2048)
                            ->nullable()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/gif'])
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('1:1')
                            ->imageResizeTargetWidth('200')
                            ->imageResizeTargetHeight('200'),
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                    ]),
                Section::make('Ubah Password')
                    ->description('Kosongkan jika tidak ingin mengubah password. Password lama wajib diisi untuk mengubah password.')
                    ->schema([
                        TextInput::make('current_password')
                            ->label('Password Lama')
                            ->password()
                            ->revealable()
                            ->requiredWith('password')
                            ->currentPassword()
                            ->dehydrated(false),
                        $this->getPasswordFormComponent()
                            ->label('Password Baru'),
                        $this->getPasswordConfirmationFormComponent()
                            ->label('Konfirmasi Password Baru'),
                    ]),
            ]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Handle avatar deletion if old avatar exists and new one is uploaded
        if (array_key_exists('avatar', $data) && $data['avatar'] !== auth()->user()->avatar) {
            if (auth()->user()->avatar && Storage::disk('public')->exists(auth()->user()->avatar)) {
                Storage::disk('public')->delete(auth()->user()->avatar);
            }
        }

        // Remove current_password from data as it's only for validation
        unset($data['current_password']);

        return parent::mutateFormDataBeforeSave($data);
    }

    protected function afterSave(): void
    {
        $this->js('window.location.reload()');
    }
}
