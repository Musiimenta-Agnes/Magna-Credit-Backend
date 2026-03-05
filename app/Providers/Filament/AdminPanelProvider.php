<?php
namespace App\Providers\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Pages\Dashboard;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('portal')
            ->login()
            ->colors([
                'primary' => [
                    50  => '240 249 255',
                    100 => '224 242 254',
                    200 => '186 230 253',
                    300 => '125 211 252',
                    400 => '56 189 248',
                    500 => '0 118 214',
                    600 => '0 100 181',
                    700 => '0 80 145',
                    800 => '0 60 109',
                    900 => '0 40 73',
                    950 => '0 20 36',
                ],
                'success' => [
                    50  => '240 255 248',
                    100 => '220 255 237',
                    200 => '170 255 210',
                    300 => '100 240 170',
                    400 => '0 220 120',
                    500 => '0 203 94',
                    600 => '0 170 78',
                    700 => '0 135 62',
                    800 => '0 100 46',
                    900 => '0 65 30',
                    950 => '0 35 16',
                ],
            ])
            ->brandName('Magna Credit Admin')
            ->brandLogo(asset('images/magna_logo.jpeg'))
            ->brandLogoHeight('40px')
            ->navigationGroups([
                NavigationGroup::make('Loan Management')->collapsible(false),
                NavigationGroup::make('Finance')->collapsible(false),
            ])
            ->renderHook('panels::head.end', fn () => '<link rel="stylesheet" href="' . asset('css/filament/admin/theme.css') . '">')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([Dashboard::class])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([AccountWidget::class])
            ->authGuard('web')
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
            ->authMiddleware([Authenticate::class]);
    }
}
