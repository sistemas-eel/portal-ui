<?php

namespace SistemasEel\PortalUi\Tests\Feature;

use Illuminate\Support\Facades\File;
use SistemasEel\PortalUi\PortalUiServiceProvider;
use SistemasEel\PortalUi\Tests\TestCase;

class PortalUiDoctorTest extends TestCase
{
    private $publishedAssets;

    protected function setUp(): void
    {
        parent::setUp();

        $this->publishedAssets = public_path('vendor/portal-ui');
        File::deleteDirectory($this->publishedAssets);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->publishedAssets);

        parent::tearDown();
    }

    public function test_diagnostico_aprova_uma_instalacao_sincronizada(): void
    {
        $this->publishAssets();

        $this->artisan('portal-ui:doctor')
            ->expectsOutput('Portal UI 0.3.0')
            ->expectsOutput('[OK] Asset sincronizado: fa-solid-900.woff2')
            ->expectsOutput('[OK] Layout SenhaÚnica: portal-ui::layouts.app')
            ->expectsOutput('[OK] Integração SenhaÚnica inativa; dependência opcional não instalada.')
            ->expectsOutput('Instalação consistente.')
            ->assertExitCode(0);
    }

    public function test_diagnostico_avisa_quando_integracao_e_forcada_sem_a_dependencia(): void
    {
        $this->publishAssets();
        config([
            'portal-ui.integrations.senhaunica.enabled' => true,
            'portal-ui.integrations.senhaunica.provider' => 'ProviderAusente',
        ]);

        $this->artisan('portal-ui:doctor')
            ->expectsOutput('[AVISO] Integração SenhaÚnica solicitada, mas a dependência está ausente: ProviderAusente')
            ->assertExitCode(0);
    }

    public function test_diagnostico_reprova_quando_um_asset_de_icone_esta_ausente(): void
    {
        $this->publishAssets();
        File::delete($this->publishedAssets.'/fa-solid-900.woff2');

        $this->artisan('portal-ui:doctor')
            ->expectsOutput('[ERRO] Asset não publicado: fa-solid-900.woff2')
            ->expectsOutput('Foram encontrados problemas que precisam de correção.')
            ->assertExitCode(1);
    }

    public function test_diagnostico_reprova_layout_senhaunica_inexistente(): void
    {
        $this->publishAssets();
        config(['portal-ui.integrations.senhaunica.layout' => 'layouts.inexistente']);

        $this->artisan('portal-ui:doctor')
            ->expectsOutput('[ERRO] Layout SenhaÚnica inexistente: layouts.inexistente')
            ->assertExitCode(1);
    }

    public function test_diagnostico_nao_exige_publicacao_no_modo_fonte(): void
    {
        config(['portal-ui.assets.mode' => 'source']);

        $this->artisan('portal-ui:doctor')
            ->expectsOutput('[OK] Assets publicados não são usados neste modo.')
            ->assertExitCode(0);
    }

    private function publishAssets(): void
    {
        $this->artisan('vendor:publish', [
            '--provider' => PortalUiServiceProvider::class,
            '--tag' => 'portal-ui-assets',
            '--force' => true,
        ])->assertExitCode(0);
    }
}
