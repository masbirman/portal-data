<?php

namespace App\Filament\SekolahPanel\Pages;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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
                        $this->getEmailFormComponent()
                            ->disabled(),
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

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['avatar'] = auth()->user()->avatar;
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $user = auth()->user();
        
        if (array_key_exists('avatar', $data) && $data['avatar'] !== $user->avatar) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
        }

        unset($data['current_password']);
        return $data;
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $data = $this->mutateFormDataBeforeSave($data);

        $user = auth()->user();
        
        if (array_key_exists('avatar', $data)) {
            $user->avatar = $data['avatar'];
        }
        
        if (isset($data['name'])) {
            $user->name = $data['name'];
        }
        
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        
        $user->save();
        $this->afterSave();
    }

    protected function afterSave(): void
    {
        redirect(request()->header('Referer'));
    }
}
