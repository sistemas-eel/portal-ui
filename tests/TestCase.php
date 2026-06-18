<?php

namespace SistemasEel\PortalUi\Tests;

use Illuminate\Support\Facades\View;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use SistemasEel\PortalUi\PortalUiServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            PortalUiServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.name', 'Aplicação de Teste');
        $app['config']->set('app.locale', 'pt_BR');
        $app['config']->set('portal-ui.brand.name', 'Portal Exemplo');
        $app['config']->set('portal-ui.brand.subtitle', 'Atendimento digital');
        $app['config']->set('portal-ui.assets.fontawesome_cdn', false);

        $app['router']->get('/login', function () {})->name('login');
        $app['router']->get('/logout', function () {})->name('logout');
        $app['router']->get('/dashboard', function () {})->name('dashboard');
    }

    protected function setUp(): void
    {
        parent::setUp();

        View::addNamespace('theme-tests', __DIR__.'/Fixtures');
    }
}
