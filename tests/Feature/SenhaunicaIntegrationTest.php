<?php

namespace SistemasEel\PortalUi\Tests\Feature;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ViewErrorBag;
use ReflectionMethod;
use SistemasEel\PortalUi\PortalUiServiceProvider;
use SistemasEel\PortalUi\Support\SenhaunicaIntegration;
use SistemasEel\PortalUi\Tests\TestCase;

class SenhaunicaIntegrationTest extends TestCase
{
    public function test_integracao_senhaunica_fica_inativa_quando_a_dependencia_nao_esta_instalada(): void
    {
        $this->assertFalse(class_exists(SenhaunicaIntegration::DEFAULT_PROVIDER));
        $this->assertFalse(config('portal-ui.integrations.senhaunica.enabled'));
        $this->assertFalse(SenhaunicaIntegration::isInstalled());
        $this->assertFalse(SenhaunicaIntegration::isRequested());
        $this->assertFalse(SenhaunicaIntegration::isEnabled());
        $this->assertSame('portal-ui::layouts.app', config('portal-ui.integrations.senhaunica.layout'));
    }

    public function test_ativacao_forcada_sem_dependencia_nao_registra_namespace_fantasma(): void
    {
        config(['portal-ui.integrations.senhaunica.enabled' => true]);

        $this->invokeIntegrationRegistration();

        $this->assertTrue(SenhaunicaIntegration::isRequested());
        $this->assertFalse(SenhaunicaIntegration::isInstalled());
        $this->assertFalse(SenhaunicaIntegration::isEnabled());
        $this->assertArrayNotHasKey('senhaunica', view()->getFinder()->getHints());
    }

    public function test_views_de_pagina_usam_o_layout_configuravel(): void
    {
        $viewsPath = realpath(__DIR__.'/../../resources/views/integrations/senhaunica');

        foreach (['users.blade.php', 'loginas.blade.php', 'unavailable.blade.php', 'local/login.blade.php'] as $view) {
            $contents = File::get($viewsPath.'/'.$view);

            $this->assertStringContainsString("config('portal-ui.integrations.senhaunica.layout'", $contents);
            $this->assertStringNotContainsString('layouts.portal-app', $contents);
        }
    }

    public function test_integracao_nao_exige_alpine_ou_jquery_para_suas_interacoes(): void
    {
        $viewsPath = realpath(__DIR__.'/../../resources/views/integrations/senhaunica');
        $contents = collect(File::allFiles($viewsPath))
            ->map(fn ($file) => File::get($file->getPathname()))
            ->implode("\n");

        foreach (['x-data', 'x-show', 'x-cloak', '@click', 'x-on:', '$.ajax', 'jQuery'] as $dependency) {
            $this->assertStringNotContainsString($dependency, $contents);
        }

        foreach ([
            'data-portal-modal-open',
            'data-portal-person-select',
            'data-portal-local-user-edit',
            'data-portal-permissions-open',
            'data-portal-json-open',
        ] as $hook) {
            $this->assertStringContainsString($hook, $contents);
        }
    }

    public function test_login_as_renderiza_dentro_do_layout_configurado(): void
    {
        $this->enableInstalledIntegration();
        config(['portal-ui.integrations.senhaunica.layout' => 'theme-tests::senhaunica-layout']);
        $this->app['router']->post('/login-as', function () {})->name('SenhaunicaLoginAs');

        $html = view('senhaunica::loginas', ['errors' => new ViewErrorBag])->render();

        $this->assertStringContainsString('Layout consumidor', $html);
        $this->assertStringContainsString('Login as', $html);
        $this->assertStringContainsString('action="'.route('SenhaunicaLoginAs').'"', $html);
    }

    public function test_tela_indisponivel_renderiza_dentro_do_layout_configurado(): void
    {
        $this->enableInstalledIntegration();
        config(['portal-ui.integrations.senhaunica.layout' => 'theme-tests::senhaunica-layout']);

        $html = view('senhaunica::unavailable', ['reason' => 'noLocalUser'])->render();

        $this->assertStringContainsString('Layout consumidor', $html);
        $this->assertStringContainsString('Usuário sem acesso!', $html);
    }

    public function test_namespace_senhaunica_preserva_override_local_e_views_originais(): void
    {
        $originalPath = base_path('original-senhaunica-views');

        File::ensureDirectoryExists($originalPath);
        view()->addNamespace('senhaunica', $originalPath);
        $this->enableInstalledIntegration();

        $paths = view()->getFinder()->getHints()['senhaunica'];

        $this->assertSame(resource_path('views/vendor/senhaunica'), $paths[0]);
        $this->assertSame(realpath(__DIR__.'/../../resources/views/integrations/senhaunica'), realpath($paths[1]));
        $this->assertContains($originalPath, $paths);

        File::deleteDirectory($originalPath);
    }

    public function test_views_senhaunica_podem_ser_publicadas(): void
    {
        $publishedViews = resource_path('views/vendor/senhaunica');

        File::deleteDirectory($publishedViews);

        $this->artisan('vendor:publish', [
            '--provider' => PortalUiServiceProvider::class,
            '--tag' => 'portal-ui-senhaunica-views',
            '--force' => true,
        ])->assertExitCode(0);

        $this->assertFileExists($publishedViews.'/users.blade.php');
        $this->assertFileExists($publishedViews.'/partials/users-list.blade.php');
        $this->assertFileExists($publishedViews.'/users/partials/permissoes-modal.blade.php');
        $this->assertStringContainsString(
            "config('portal-ui.integrations.senhaunica.layout'",
            File::get($publishedViews.'/loginas.blade.php')
        );
        $this->assertStringContainsString(
            'data-portal-local-user-edit',
            File::get($publishedViews.'/partials/users-list.blade.php')
        );

        File::deleteDirectory($publishedViews);
    }

    private function enableInstalledIntegration(): void
    {
        config([
            'portal-ui.integrations.senhaunica.provider' => PortalUiServiceProvider::class,
            'portal-ui.integrations.senhaunica.enabled' => true,
        ]);

        $this->invokeIntegrationRegistration();
    }

    private function invokeIntegrationRegistration(): void
    {
        $provider = new PortalUiServiceProvider($this->app);
        $method = new ReflectionMethod($provider, 'registerSenhaunicaViews');
        $method->setAccessible(true);
        $method->invoke($provider);
    }
}
