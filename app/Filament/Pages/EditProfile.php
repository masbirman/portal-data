<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class EditProfile extends Page implements HasForms
{
    use InteractsWithForms;

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
                            ->avatar()
                            ->imageEditor()
                            ->circleCropper()
                            ->disk('public')
                            ->directory('avatars')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                            ->deletable()
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
                            ->dehydrated(false)
                            ->rules(['required_with:new_password']),

                        TextInput::make('new_password')
                            ->label('Password Baru')
                            ->password()
                            ->revealable()
                            ->dehydrated(false)
                            ->rules(['nullable', 'min:8', 'confirmed']),

                        TextInput::make('new_password_confirmation')
                            ->label('Konfirmasi Password Baru')
                            ->password()
                            ->revealable()
                            ->dehydrated(false),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Perubahan')
                ->submit('save'),
        ];
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

        $user->name = $data['name'];
        $user->email = $data['email'];

        if (!empty($data['avatar'])) {
            $user->avatar = $data['avatar'];
        }

        $user->save();

        Notification::make()
            ->title('Profile berhasil diperbarui')
            ->success()
            ->send();
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Pengaturan';
    }
}
