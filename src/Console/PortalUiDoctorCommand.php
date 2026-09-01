<?php

namespace SistemasEel\PortalUi\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use SistemasEel\PortalUi\PortalUiServiceProvider;
use SistemasEel\PortalUi\Support\SenhaunicaIntegration;

class PortalUiDoctorCommand extends Command
{
    protected $signature = 'portal-ui:doctor';

    protected $description = 'Verifica layout, assets e integração SenhaÚnica do Portal UI';

    public function handle(): int
    {
        $failures = 0;

        $this->info('Portal UI '.PortalUiServiceProvider::VERSION);

        if (config('portal-ui.assets.mode', 'published') === 'published') {
            $assets = [];

            if (config('portal-ui.assets.load_css', true)) {
                $assets = [
                    'portal-ui.css',
                    'fa-solid-900.woff2',
                    'fa-regular-400.woff2',
                    'fa-brands-400.woff2',
                    'fa-v4compatibility.woff2',
                ];
            }

            if (config('portal-ui.assets.load_js', true)) {
                $assets[] = 'portal-ui.js';
            }

            foreach ($assets as $asset) {
                $failures += $this->checkPublishedAsset($asset);
            }
        } else {
            $this->line('[OK] Assets publicados não são usados neste modo.');
        }

        $layout = (string) config('portal-ui.integrations.senhaunica.layout', 'portal-ui::layouts.app');
        if (View::exists($layout)) {
            $this->line("[OK] Layout SenhaÚnica: {$layout}");
        } else {
            $this->error("[ERRO] Layout SenhaÚnica inexistente: {$layout}");
            $failures++;
        }

        if (config('portal-ui.assets.fontawesome_cdn')) {
            $this->warn('[AVISO] Ícones dependem de CDN externo. Prefira os assets locais da versão 0.2.0.');
        } else {
            $this->line('[OK] Font Awesome configurado para os assets locais.');
        }

        if (SenhaunicaIntegration::isRequested() && ! SenhaunicaIntegration::isInstalled()) {
            $provider = SenhaunicaIntegration::providerClass();
            $this->warn("[AVISO] Integração SenhaÚnica solicitada, mas a dependência está ausente: {$provider}");
        } elseif (SenhaunicaIntegration::isEnabled()) {
            foreach (['senhaunica-users.index', 'SenhaunicaLoginAsForm'] as $routeName) {
                if (Route::has($routeName)) {
                    $this->line("[OK] Rota disponível: {$routeName}");
                } else {
                    $this->warn("[AVISO] Rota SenhaÚnica ausente: {$routeName}");
                }
            }

            $this->reportPublishedSenhaunicaOverrides();
        } elseif (SenhaunicaIntegration::isInstalled()) {
            $this->line('[OK] Integração SenhaÚnica desativada por configuração.');
        } else {
            $this->line('[OK] Integração SenhaÚnica inativa; dependência opcional não instalada.');
        }

        if ($failures > 0) {
            $this->newLine();
            $this->error('Foram encontrados problemas que precisam de correção.');

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('Instalação consistente.');

        return self::SUCCESS;
    }

    private function checkPublishedAsset(string $filename): int
    {
        $source = dirname(__DIR__, 2).'/public/'.$filename;
        $published = public_path('vendor/portal-ui/'.$filename);

        if (! is_file($published)) {
            $this->error("[ERRO] Asset não publicado: {$filename}");

            return 1;
        }

        if (! is_file($source) || hash_file('sha256', $source) !== hash_file('sha256', $published)) {
            $this->error("[ERRO] Asset desatualizado: {$filename}. Execute vendor:publish --tag=portal-ui-assets --force.");

            return 1;
        }

        $this->line("[OK] Asset sincronizado: {$filename}");

        return 0;
    }

    private function reportPublishedSenhaunicaOverrides(): void
    {
        $publishedPath = resource_path('views/vendor/senhaunica');

        if (! is_dir($publishedPath)) {
            $this->line('[OK] Sem overrides publicados da SenhaÚnica.');

            return;
        }

        $this->warn('[AVISO] Existem views SenhaÚnica publicadas; elas têm prioridade sobre correções do pacote.');
    }
}
