<?php

namespace SistemasEel\PortalUi\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use SistemasEel\PortalUi\PortalUiServiceProvider;

class PortalUiInstallCommand extends Command
{
    private const ROUTE_MARKER = 'Portal UI starter — arquivo gerenciado';

    protected $signature = 'portal-ui:install
        {--starter : Cria layout, view, rota e navegação de demonstração}
        {--force : Sobrescreve configuração e arquivos do starter existentes}';

    protected $description = 'Publica o Portal UI e, opcionalmente, cria uma primeira tela funcional';

    public function handle(): int
    {
        $force = (bool) $this->option('force');
        $withStarter = (bool) $this->option('starter');
        $configPath = config_path('portal-ui.php');
        $configAlreadyExisted = is_file($configPath);

        $this->info('Instalando Portal UI '.PortalUiServiceProvider::VERSION.'...');

        if ($this->publish('portal-ui-config', $force) !== self::SUCCESS) {
            $this->error('Não foi possível publicar a configuração.');

            return self::FAILURE;
        }

        // Assets são inteiramente gerenciados pelo pacote e devem permanecer
        // sincronizados mesmo quando a configuração do consumidor é preservada.
        if ($this->publish('portal-ui-assets', true) !== self::SUCCESS) {
            $this->error('Não foi possível publicar os assets.');

            return self::FAILURE;
        }

        if ($withStarter && ! $this->installStarter($configPath, $configAlreadyExisted, $force)) {
            return self::FAILURE;
        }

        $this->call('optimize:clear');
        $this->newLine();
        $this->info('Portal UI instalado com sucesso.');

        if ($withStarter) {
            $this->line('Abra: '.url('/portal-ui-starter'));
        }

        $this->line('Verifique a instalação com: php artisan portal-ui:doctor');

        return self::SUCCESS;
    }

    private function publish(string $tag, bool $force): int
    {
        return $this->call('vendor:publish', [
            '--provider' => PortalUiServiceProvider::class,
            '--tag' => $tag,
            '--force' => $force,
        ]);
    }

    private function installStarter(string $configPath, bool $configAlreadyExisted, bool $force): bool
    {
        $root = dirname(__DIR__, 2).'/stubs/starter';
        $files = [
            $root.'/layouts/app.blade.php' => resource_path('views/layouts/app.blade.php'),
            $root.'/portal-ui-starter.blade.php' => resource_path('views/portal-ui-starter.blade.php'),
            $root.'/routes/portal-ui-starter.php' => base_path('routes/portal-ui-starter.php'),
        ];

        foreach ($files as $source => $destination) {
            if (! $this->copyStarterFile($source, $destination, $force)) {
                return false;
            }
        }

        if (! $configAlreadyExisted || $force) {
            if (! $this->configureStarterNavigation($configPath)) {
                return false;
            }
        } else {
            $this->warn('Configuração existente preservada; o item do starter não foi adicionado ao menu.');
        }

        return $this->includeStarterRoutes(base_path('routes/web.php'));
    }

    private function copyStarterFile(string $source, string $destination, bool $force): bool
    {
        if (! is_file($source)) {
            $this->error("Stub ausente: {$source}");

            return false;
        }

        $alreadyExisted = is_file($destination);

        if ($alreadyExisted && ! $force) {
            $this->warn('Preservado: '.$this->relativePath($destination));

            return true;
        }

        $directory = dirname($destination);
        if (! is_dir($directory)) {
            File::makeDirectory($directory, 0755, true, true);
        }

        if (! File::copy($source, $destination)) {
            $this->error('Não foi possível criar: '.$this->relativePath($destination));

            return false;
        }

        $status = $alreadyExisted ? 'Atualizado' : 'Criado';
        $this->line("{$status}: ".$this->relativePath($destination));

        return true;
    }

    private function configureStarterNavigation(string $configPath): bool
    {
        if (! is_file($configPath)) {
            $this->error('A configuração portal-ui.php não foi encontrada.');

            return false;
        }

        $contents = File::get($configPath);
        $groupsNeedle = "        'groups' => [],";
        $groupsReplacement = <<<'PHP'
        'groups' => [
            'main' => [
                'label' => 'Menu Principal',
                'items' => [
                    [
                        'label' => 'Início',
                        'route' => 'portal-ui.starter',
                        'icon' => 'fa-house',
                        'active' => 'portal-ui.starter',
                    ],
                ],
            ],
        ],
PHP;

        if (strpos($contents, $groupsNeedle) === false) {
            $this->error('Não foi possível localizar navigation.groups na configuração publicada.');

            return false;
        }

        $contents = str_replace($groupsNeedle, $groupsReplacement, $contents);
        $contents = str_replace("'home' => 'home',", "'home' => 'portal-ui.starter',", $contents);
        File::put($configPath, $contents);
        $this->line('Configurado: '.$this->relativePath($configPath));

        return true;
    }

    private function includeStarterRoutes(string $webRoutesPath): bool
    {
        if (! is_file($webRoutesPath)) {
            $this->error('Arquivo routes/web.php não encontrado.');

            return false;
        }

        $contents = File::get($webRoutesPath);
        if (strpos($contents, self::ROUTE_MARKER) !== false) {
            $this->warn('Preservado: routes/web.php já carrega o starter.');

            return true;
        }

        if (strpos($contents, '<?php') === false) {
            $this->error('routes/web.php não parece ser um arquivo PHP válido.');

            return false;
        }

        $include = "\n// ".self::ROUTE_MARKER."\nrequire __DIR__.'/portal-ui-starter.php';\n";

        if (preg_match('/\?>\s*$/', $contents) === 1) {
            $include = "\n<?php\n// ".self::ROUTE_MARKER."\nrequire __DIR__.'/portal-ui-starter.php';\n";
        }

        File::append($webRoutesPath, $include);
        $this->line('Atualizado: routes/web.php');

        return true;
    }

    private function relativePath(string $path): string
    {
        $base = rtrim(base_path(), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

        return strpos($path, $base) === 0 ? substr($path, strlen($base)) : $path;
    }
}
