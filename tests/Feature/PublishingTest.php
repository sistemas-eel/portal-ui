<?php

namespace SistemasEel\PortalUi\Tests\Feature;

use Illuminate\Support\Facades\File;
use SistemasEel\PortalUi\PortalUiServiceProvider;
use SistemasEel\PortalUi\Tests\TestCase;

class PublishingTest extends TestCase
{
    private $publishedConfig;

    private $publishedAssets;

    private $publishedStubs;

    protected function setUp(): void
    {
        parent::setUp();

        $this->publishedConfig = config_path('portal-ui.php');
        $this->publishedAssets = public_path('vendor/portal-ui');
        $this->publishedStubs = base_path('stubs/portal-ui');

        File::delete($this->publishedConfig);
        File::deleteDirectory($this->publishedAssets);
        File::deleteDirectory($this->publishedStubs);
    }

    protected function tearDown(): void
    {
        File::delete($this->publishedConfig);
        File::deleteDirectory($this->publishedAssets);
        File::deleteDirectory($this->publishedStubs);

        parent::tearDown();
    }

    public function test_provider_mescla_a_configuracao_padrao(): void
    {
        $this->assertSame('published', config('portal-ui.assets.mode'));
        $this->assertSame('vendor/portal-ui/portal-ui.css', config('portal-ui.assets.css_path'));
        $this->assertSame('vendor/portal-ui/portal-ui.js', config('portal-ui.assets.js_path'));
    }

    public function test_configuracao_pode_ser_publicada(): void
    {
        $this->artisan('vendor:publish', [
            '--provider' => PortalUiServiceProvider::class,
            '--tag' => 'portal-ui-config',
            '--force' => true,
        ])->assertExitCode(0);

        $this->assertFileExists($this->publishedConfig);
        $this->assertStringContainsString("'brand' =>", File::get($this->publishedConfig));
        $this->assertStringContainsString("'assets' =>", File::get($this->publishedConfig));
    }

    public function test_assets_compilados_podem_ser_publicados_sem_codigo_de_dominio(): void
    {
        $this->artisan('vendor:publish', [
            '--provider' => PortalUiServiceProvider::class,
            '--tag' => 'portal-ui-assets',
            '--force' => true,
        ])->assertExitCode(0);

        $css = File::get($this->publishedAssets.'/portal-ui.css');
        $js = File::get($this->publishedAssets.'/portal-ui.js');

        $this->assertFileExists($this->publishedAssets.'/portal-ui.css');
        $this->assertFileExists($this->publishedAssets.'/portal-ui.js');
        $this->assertFileExists($this->publishedAssets.'/fa-solid-900.woff2');
        $this->assertFileExists($this->publishedAssets.'/fa-regular-400.woff2');
        $this->assertFileExists($this->publishedAssets.'/fa-brands-400.woff2');
        $this->assertFileExists($this->publishedAssets.'/fa-v4compatibility.woff2');
        $this->assertStringContainsString('--color-portal', $css);
        $this->assertStringContainsString('Font Awesome 7 Free', $css);
        $this->assertStringContainsString('url(./fa-solid-900.woff2)', $css);
        $this->assertStringNotContainsString('url(/fa-solid-900.woff2)', $css);
        $this->assertStringContainsString('data-portal-sidebar', $js);
        $this->assertStringContainsString('data-portal-modal-open', $js);
        $this->assertStringContainsString('data-portal-person-select', $js);
        $this->assertStringContainsString('portalAutoDismissSignature', $js);
        $this->assertStringContainsString(
            'portalAutoDismissRemaining',
            $js,
        );
        $this->assertStringContainsString('mouseenter', $js);
        $this->assertStringContainsString('mouseleave', $js);
        $this->assertStringContainsString('livewire:init', $js);
        $this->assertStringContainsString('morphed', $js);
        $this->assertStringNotContainsString('data-portal-chat', strtolower($css.$js));
        $this->assertStringNotContainsString('data-portal-agente', strtolower($css.$js));
        $this->assertStringNotContainsString('wire:model', strtolower($css.$js));
    }

    public function test_assets_tambem_podem_ser_publicados_pela_tag_padrao_do_laravel(): void
    {
        $this->artisan('vendor:publish', [
            '--provider' => PortalUiServiceProvider::class,
            '--tag' => 'laravel-assets',
            '--force' => true,
        ])->assertExitCode(0);

        $this->assertFileExists($this->publishedAssets.'/portal-ui.css');
        $this->assertFileExists($this->publishedAssets.'/portal-ui.js');
        $this->assertFileExists($this->publishedAssets.'/fa-solid-900.woff2');
    }

    public function test_stubs_podem_ser_publicados_com_modelos_de_navegacao_e_rotas_demo(): void
    {
        $this->artisan('vendor:publish', [
            '--provider' => PortalUiServiceProvider::class,
            '--tag' => 'portal-ui-stubs',
            '--force' => true,
        ])->assertExitCode(0);

        $this->assertFileExists($this->publishedStubs.'/portal-ui.php');
        $this->assertFileExists($this->publishedStubs.'/navigation/minimal.php');
        $this->assertFileExists($this->publishedStubs.'/navigation/simple.php');
        $this->assertFileExists($this->publishedStubs.'/navigation/admin.php');
        $this->assertFileExists($this->publishedStubs.'/routes/demo.php');
        $this->assertFileExists($this->publishedStubs.'/starter/layouts/app.blade.php');
        $this->assertFileExists($this->publishedStubs.'/starter/portal-ui-starter.blade.php');
        $this->assertFileExists($this->publishedStubs.'/starter/routes/portal-ui-starter.php');

        $this->assertStringContainsString("'groups' =>", File::get($this->publishedStubs.'/navigation/admin.php'));
        $this->assertStringContainsString("Route::view('/portal-ui-demo'", File::get($this->publishedStubs.'/routes/demo.php'));
    }
}
