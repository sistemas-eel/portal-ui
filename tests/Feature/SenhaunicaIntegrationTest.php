<?php

namespace SistemasEel\PortalUi\Tests\Feature;

use Illuminate\Support\Facades\File;
use ReflectionMethod;
use SistemasEel\PortalUi\PortalUiServiceProvider;
use SistemasEel\PortalUi\Tests\TestCase;

class SenhaunicaIntegrationTest extends TestCase
{
    public function test_integracao_senhaunica_fica_habilitada_por_padrao(): void
    {
        $this->assertTrue(config('portal-ui.integrations.senhaunica.enabled'));
    }

    public function test_namespace_senhaunica_preserva_override_local_e_views_originais(): void
    {
        $originalPath = base_path('original-senhaunica-views');

        File::ensureDirectoryExists($originalPath);
        view()->addNamespace('senhaunica', $originalPath);

        $provider = new PortalUiServiceProvider($this->app);
        $method = new ReflectionMethod($provider, 'registerSenhaunicaViews');
        $method->setAccessible(true);
        $method->invoke($provider);

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

        File::deleteDirectory($publishedViews);
    }
}
