<?php

namespace App\Filament\Pages\Auth;

use Coderflex\FilamentTurnstile\Forms\Components\Turnstile;
use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BaseLogin;

class Login extends BaseLogin
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
                Turnstile::make('turnstile')
                    ->label('Verifikasi')
                    ->theme('light'),
            ]);
    }
}
