<?php

namespace SistemasEel\PortalUi;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\Component;
use SistemasEel\PortalUi\View\Components\AppLayout;
use SistemasEel\PortalUi\View\Components\GuestLayout;

class PortalUiServiceProvider extends ServiceProvider
{
    /**
     * Componentes anônimos expostos pelo pacote.
     *
     * @var string[]
     */
    protected $anonymousComponents = [
        'card',
        'badge',
        'alert',
        'flash-alert',
        'flash-messages',
        'confirm-modal',
        'empty-state',
        'section-header',
        'section-footer',
        'page-header',
        'hero-header',
        'button',
        'table-actions',
        'resource-actions',
        'table',
        'input',
        'switch',
        'select',
        'textarea',
        'modal',
        'sidebar-item',
    ];

    /**
     * Componentes Blade baseados em classe expostos pelo pacote.
     *
     * @var array<int, class-string<Component>>
     */
    protected $classComponents = [
        AppLayout::class,
        GuestLayout::class,
    ];

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/portal-ui.php', 'portal-ui');
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'portal-ui');
        $this->registerSenhaunicaViews();

        $bladeCompiler = $this->app->make('blade.compiler');
        $hasAnonymousRegistration = false;

        if (method_exists($bladeCompiler, 'anonymousComponentNamespace')) {
            $bladeCompiler->anonymousComponentNamespace('portal-ui::components', 'portal');
            $hasAnonymousRegistration = true;
        } elseif (method_exists($bladeCompiler, 'anonymousComponentPath')) {
            $bladeCompiler->anonymousComponentPath(__DIR__.'/../resources/views/components', 'portal');
            $hasAnonymousRegistration = true;
        }

        if (! $hasAnonymousRegistration && method_exists($bladeCompiler, 'component')) {
            foreach ($this->anonymousComponents as $component) {
                $bladeCompiler->component('portal-ui::components.'.$component, 'portal::'.$component);
            }
        }

        if (method_exists($this, 'loadViewComponentsAs')) {
            $this->loadViewComponentsAs('portal', $this->classComponents);
        } elseif (method_exists($bladeCompiler, 'component')) {
            $bladeCompiler->component(AppLayout::class, 'portal::app-layout');
            $bladeCompiler->component(GuestLayout::class, 'portal::guest-layout');
        }

        $this->publishes([
            __DIR__.'/../config/portal-ui.php' => config_path('portal-ui.php'),
        ], 'portal-ui-config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/portal-ui'),
        ], 'portal-ui-views');

        $this->publishes([
            __DIR__.'/../resources/views/integrations/senhaunica' => resource_path('views/vendor/senhaunica'),
        ], 'portal-ui-senhaunica-views');

        $this->publishes([
            __DIR__.'/../public' => public_path('vendor/portal-ui'),
        ], 'portal-ui-assets');

        $this->publishes([
            __DIR__.'/../stubs' => base_path('stubs/portal-ui'),
        ], 'portal-ui-stubs');
    }

    protected function registerSenhaunicaViews(): void
    {
        if (! config('portal-ui.integrations.senhaunica.enabled', true)) {
            return;
        }

        $this->app->booted(function (): void {
            $finder = $this->app['view']->getFinder();

            if (! method_exists($finder, 'getHints') || ! method_exists($finder, 'replaceNamespace')) {
                return;
            }

            $hints = $finder->getHints();
            $originalPaths = $hints['senhaunica'] ?? [];

            $paths = array_values(array_unique(array_filter(array_merge([
                resource_path('views/vendor/senhaunica'),
                __DIR__.'/../resources/views/integrations/senhaunica',
            ], $originalPaths))));

            $finder->replaceNamespace('senhaunica', $paths);
        });
    }
}
