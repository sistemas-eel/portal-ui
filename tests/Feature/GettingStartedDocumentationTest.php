<?php

namespace SistemasEel\PortalUi\Tests\Feature;

use Illuminate\Support\Facades\File;
use SistemasEel\PortalUi\Tests\TestCase;

class GettingStartedDocumentationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        view()->addLocation(__DIR__.'/../Fixtures/getting-started');

        $this->app['router']->view('/', 'home')->name('home');

        config([
            'portal-ui.brand.name' => 'Meu Sistema',
            'portal-ui.brand.subtitle' => 'Sistema administrativo',
            'portal-ui.navigation.hide_missing_routes' => true,
            'portal-ui.navigation.groups' => [
                'main' => [
                    'label' => 'Menu Principal',
                    'items' => [
                        [
                            'label' => 'Início',
                            'route' => 'home',
                            'icon' => 'fa-house',
                            'active' => 'home',
                        ],
                    ],
                ],
            ],
            'portal-ui.routes.home' => 'home',
        ]);
    }

    public function test_primeira_tela_documentada_renderiza_layout_menu_assets_e_icone(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('<title>Página inicial - Meu Sistema</title>', false);
        $response->assertSee('Meu primeiro sistema');
        $response->assertSee('O Portal UI está funcionando corretamente.');
        $response->assertSee('Layout, estilos, JavaScript e ícones carregados.');
        $response->assertSee('data-portal-ui="app"', false);
        $response->assertSee('data-portal-has-sidebar="true"', false);
        $response->assertSee('Menu Principal');
        $response->assertSee('Início');
        $response->assertSee('fa-house', false);
        $response->assertSee('fa-circle-check', false);
        $response->assertSee('/vendor/portal-ui/portal-ui.css', false);
        $response->assertSee('/vendor/portal-ui/portal-ui.js', false);
    }

    public function test_guia_contem_os_contratos_exercitados_pelo_teste(): void
    {
        $guide = File::get(__DIR__.'/../../docs/getting-started.md');

        $this->assertStringContainsString("@extends('portal-ui::layouts.app')", $guide);
        $this->assertStringContainsString("@extends('layouts.app')", $guide);
        $this->assertStringContainsString("Route::view('/', 'home')->name('home');", $guide);
        $this->assertStringContainsString("'route' => 'home'", $guide);
        $this->assertStringContainsString('php artisan portal-ui:doctor', $guide);
        $this->assertStringContainsString('PORTAL_UI_SENHAUNICA_VIEWS=false', $guide);
    }
}
