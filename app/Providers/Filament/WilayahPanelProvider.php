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
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Navigation\MenuItem;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Resma\FilamentAwinTheme\FilamentAwinTheme;

class WilayahPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('wilayah')
            ->path('wilayah')
            ->login(false)
            ->plugins([
                FilamentAwinTheme::make()
                    ->primaryColor(Color::Emerald),
                AuthDesignerPlugin::make()
                    ->login(
                        layout: AuthLayout::Split,
                        media: asset('images/login-bg.svg'),
                        direction: MediaDirection::Right
                    )
                    ->themeToggle(ThemePosition::TopRight),
            ])
            ->discoverResources(in: app_path('Filament/WilayahPanel/Resources'), for: 'App\Filament\WilayahPanel\Resources')
            ->discoverPages(in: app_path('Filament/WilayahPanel/Pages'), for: 'App\Filament\WilayahPanel\Pages')
            ->pages([
                \App\Filament\WilayahPanel\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/WilayahPanel/Widgets'), for: 'App\Filament\WilayahPanel\Widgets')
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
                EnsureUserHasRole::class . ':admin_wilayah',
            ])
            ->profile(\App\Filament\WilayahPanel\Pages\EditProfile::class)
            ->logout(\App\Filament\WilayahPanel\Pages\Auth\Logout::class)
            ->brandName('Portal Admin Wilayah')
            ->authGuard('web');
    }
}
