<?php

namespace SistemasEel\PortalUi\Tests\Feature;

use Illuminate\Support\Facades\File;
use SistemasEel\PortalUi\Tests\TestCase;

class PortalUiInstallTest extends TestCase
{
    private $assetsPath;

    private $configPath;

    private $layoutPath;

    private $starterRoutePath;

    private $starterViewPath;

    private $webRoutesPath;

    private $backups = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->assetsPath = public_path('vendor/portal-ui');
        $this->configPath = config_path('portal-ui.php');
        $this->layoutPath = resource_path('views/layouts/app.blade.php');
        $this->starterViewPath = resource_path('views/portal-ui-starter.blade.php');
        $this->starterRoutePath = base_path('routes/portal-ui-starter.php');
        $this->webRoutesPath = base_path('routes/web.php');

        foreach ($this->managedFiles() as $path) {
            $this->backups[$path] = is_file($path) ? File::get($path) : null;
            File::delete($path);
        }

        File::deleteDirectory($this->assetsPath);
        File::makeDirectory(dirname($this->webRoutesPath), 0755, true, true);
        File::put($this->webRoutesPath, "<?php\n\n// Rotas existentes do consumidor.\n");
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->assetsPath);

        foreach ($this->managedFiles() as $path) {
            File::delete($path);

            if ($this->backups[$path] !== null) {
                File::makeDirectory(dirname($path), 0755, true, true);
                File::put($path, $this->backups[$path]);
            }
        }

        parent::tearDown();
    }

    public function test_instalacao_basica_publica_configuracao_e_assets_sem_criar_starter(): void
    {
        $this->artisan('portal-ui:install')
            ->expectsOutput('Portal UI instalado com sucesso.')
            ->expectsOutput('Verifique a instalação com: php artisan portal-ui:doctor')
            ->assertExitCode(0);

        $this->assertFileExists($this->configPath);
        $this->assertFileExists($this->assetsPath.'/portal-ui.css');
        $this->assertFileExists($this->assetsPath.'/portal-ui.js');
        $this->assertFileExists($this->assetsPath.'/fa-solid-900.woff2');
        $this->assertFileDoesNotExist($this->layoutPath);
        $this->assertFileDoesNotExist($this->starterViewPath);
        $this->assertFileDoesNotExist($this->starterRoutePath);
        $this->assertStringNotContainsString('Portal UI starter', File::get($this->webRoutesPath));
    }

    public function test_starter_cria_uma_tela_renderizavel_com_menu_e_assets(): void
    {
        $this->artisan('portal-ui:install', ['--starter' => true])
            ->expectsOutput('Portal UI instalado com sucesso.')
            ->expectsOutput('Abra: '.url('/portal-ui-starter'))
            ->assertExitCode(0);

        $this->assertFileExists($this->layoutPath);
        $this->assertFileExists($this->starterViewPath);
        $this->assertFileExists($this->starterRoutePath);
        $this->assertSame("@extends('portal-ui::layouts.app')\n", File::get($this->layoutPath));
        $this->assertSame(1, substr_count(File::get($this->webRoutesPath), 'Portal UI starter — arquivo gerenciado'));

        $publishedConfig = require $this->configPath;
        $this->assertSame('portal-ui.starter', $publishedConfig['routes']['home']);
        $this->assertSame(
            'portal-ui.starter',
            $publishedConfig['navigation']['groups']['main']['items'][0]['route']
        );
        $this->assertFalse($publishedConfig['integrations']['senhaunica']['enabled']);
        $this->assertSame(
            'Uspdev\\SenhaunicaSocialite\\SenhaunicaServiceProvider',
            $publishedConfig['integrations']['senhaunica']['provider']
        );

        config(['portal-ui' => $publishedConfig]);
        require $this->starterRoutePath;
        $this->app['router']->getRoutes()->refreshNameLookups();

        $response = $this->get('/portal-ui-starter');

        $response->assertOk();
        $response->assertSee('Portal UI instalado');
        $response->assertSee('Menu Principal');
        $response->assertSee('fa-house', false);
        $response->assertSee('fa-circle-check', false);
        $response->assertSee('/vendor/portal-ui/portal-ui.css', false);
        $response->assertSee('/vendor/portal-ui/portal-ui.js', false);
    }

    public function test_execucao_repetida_preserva_arquivos_e_nao_duplica_inclusao_de_rotas(): void
    {
        $this->artisan('portal-ui:install', ['--starter' => true])->assertExitCode(0);

        File::put($this->starterViewPath, 'customização do consumidor');

        $this->artisan('portal-ui:install', ['--starter' => true])
            ->expectsOutput('Preservado: resources/views/portal-ui-starter.blade.php')
            ->expectsOutput('Preservado: routes/web.php já carrega o starter.')
            ->assertExitCode(0);

        $this->assertSame('customização do consumidor', File::get($this->starterViewPath));
        $this->assertSame(1, substr_count(File::get($this->webRoutesPath), 'Portal UI starter — arquivo gerenciado'));
    }

    public function test_configuracao_existente_e_preservada_sem_force(): void
    {
        File::makeDirectory(dirname($this->configPath), 0755, true, true);
        File::put($this->configPath, "<?php\n\nreturn ['custom' => true];\n");

        $this->artisan('portal-ui:install', ['--starter' => true])
            ->expectsOutput('Configuração existente preservada; o item do starter não foi adicionado ao menu.')
            ->assertExitCode(0);

        $this->assertSame("<?php\n\nreturn ['custom' => true];\n", File::get($this->configPath));
        $this->assertFileExists($this->starterViewPath);
        $this->assertFileExists($this->starterRoutePath);
    }

    public function test_force_recria_configuracao_e_arquivos_gerenciados_sem_apagar_rotas_existentes(): void
    {
        $this->artisan('portal-ui:install', ['--starter' => true])->assertExitCode(0);

        File::put($this->configPath, "<?php\n\nreturn ['custom' => true];\n");
        File::put($this->layoutPath, 'layout customizado');
        File::put($this->starterViewPath, 'view customizada');
        File::put($this->starterRoutePath, '<?php // rota customizada');

        $this->artisan('portal-ui:install', ['--starter' => true, '--force' => true])
            ->expectsOutput('Atualizado: resources/views/layouts/app.blade.php')
            ->assertExitCode(0);

        $publishedConfig = require $this->configPath;
        $this->assertSame('portal-ui.starter', $publishedConfig['routes']['home']);
        $this->assertSame("@extends('portal-ui::layouts.app')\n", File::get($this->layoutPath));
        $this->assertStringContainsString('Portal UI instalado', File::get($this->starterViewPath));
        $this->assertStringContainsString("Route::view('/portal-ui-starter'", File::get($this->starterRoutePath));
        $this->assertStringContainsString('Rotas existentes do consumidor.', File::get($this->webRoutesPath));
        $this->assertSame(1, substr_count(File::get($this->webRoutesPath), 'Portal UI starter — arquivo gerenciado'));
    }

    private function managedFiles(): array
    {
        return [
            $this->configPath,
            $this->layoutPath,
            $this->starterViewPath,
            $this->starterRoutePath,
            $this->webRoutesPath,
        ];
    }
}
