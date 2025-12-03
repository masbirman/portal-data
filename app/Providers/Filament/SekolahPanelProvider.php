<?php

namespace App\Providers\Filament;

use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\EnsureUserIsActive;
use Caresome\FilamentAuthDesigner\AuthDesignerPlugin;
use Caresome\FilamentAuthDesigner\Enums\AuthLayout;
use Caresome\FilamentAuthDesigner\Enums\MediaDirection;
use Caresome\FilamentAuthDesigner\Enums\ThemePosition;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Resma\FilamentAwinTheme\FilamentAwinTheme;

class SekolahPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('sekolah')
            ->path('sekolah')
            ->login(\App\Filament\SekolahPanel\Pages\Auth\Login::class)
            ->plugins([
                FilamentAwinTheme::make()
                    ->primaryColor(Color::Blue),
                AuthDesignerPlugin::make()
                    ->login(
                        layout: AuthLayout::Split,
                        media: asset('images/login-bg.svg'),
                        direction: MediaDirection::Right
                    )
                    ->themeToggle(ThemePosition::TopRight),
            ])
            ->discoverResources(in: app_path('Filament/SekolahPanel/Resources'), for: 'App\Filament\SekolahPanel\Resources')
            ->discoverPages(in: app_path('Filament/SekolahPanel/Pages'), for: 'App\Filament\SekolahPanel\Pages')
            ->pages([
                \App\Filament\SekolahPanel\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/SekolahPanel/Widgets'), for: 'App\Filament\SekolahPanel\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                EnsureUserIsActive::class,
                EnsureUserHasRole::class . ':user_sekolah',
            ])
            ->profile(\App\Filament\SekolahPanel\Pages\EditProfile::class)
            ->brandName('Portal Sekolah');
    }
}
