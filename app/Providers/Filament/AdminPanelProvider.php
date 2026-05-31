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
            ->passwordReset()
            ->colors([
                'primary' => \Filament\Support\Colors\Color::Blue,
                'success' => \Filament\Support\Colors\Color::Green,
                'warning' => \Filament\Support\Colors\Color::Amber,
                'danger'  => \Filament\Support\Colors\Color::Red,
                'info'    => \Filament\Support\Colors\Color::Sky,
                'gray'    => \Filament\Support\Colors\Color::Slate,
            ])
            ->font('Poppins')
            ->sidebarFullyCollapsibleOnDesktop()
            ->brandName('Magna Credit')
            ->brandLogo(asset('images/magna_logo.jpeg'))
            ->favicon(asset('images/magna_logo.jpeg'))
            ->brandLogoHeight('3rem')
            ->darkMode(false)
            ->maxContentWidth('full')
            ->navigationGroups([
                NavigationGroup::make('Loan Management')->collapsible(false),
                NavigationGroup::make('Finance')->collapsible(false),
                NavigationGroup::make('System')->collapsible(false),
            ])
            ->renderHook('panels::body.end', fn () => '<link rel="stylesheet" href="' . asset('css/filament/admin/theme.css?v=' . time()) . '">')
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
