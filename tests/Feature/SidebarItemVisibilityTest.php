<?php

namespace SistemasEel\PortalUi\Tests\Feature;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\HtmlString;
use Illuminate\View\ComponentAttributeBag;
use SistemasEel\PortalUi\Tests\TestCase;

class SidebarItemVisibilityTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);
        $app['router']->get('/admin', function () {})->name('admin.dashboard');
    }

    public function test_item_visivel_quando_tem_url_sem_depender_de_rota_ou_auth(): void
    {
        $html = $this->renderItem(['label' => 'Docs', 'url' => '/docs']);

        $this->assertStringContainsString('href="/docs"', $html);
        $this->assertStringContainsString('>Docs</span>', $html);
    }

    public function test_link_externo_recebe_target_rel_e_icone(): void
    {
        $html = $this->renderItem([
            'label' => 'Manual externo',
            'url' => 'https://example.org/manual',
        ]);

        $this->assertStringContainsString('href="https://example.org/manual"', $html);
        $this->assertStringContainsString('target="_blank"', $html);
        $this->assertStringContainsString('rel="noopener noreferrer"', $html);
        $this->assertStringContainsString('fa-up-right-from-square', $html);
    }

    public function test_item_omitido_quando_rota_ausente_e_hide_missing_ativo(): void
    {
        config(['portal-ui.navigation.hide_missing_routes' => true]);

        $html = $this->renderItem(['label' => 'Sem rota', 'route' => 'rota.inexistente']);

        $this->assertSame('', trim($html));
    }

    public function test_item_renderizado_como_desabilitado_quando_rota_ausente_e_hide_missing_inativo(): void
    {
        config(['portal-ui.navigation.hide_missing_routes' => false]);

        $html = $this->renderItem(['label' => 'Sem rota', 'route' => 'rota.inexistente']);

        $this->assertStringContainsString('>Sem rota</span>', $html);
        $this->assertStringContainsString('href="#"', $html);
    }

    public function test_item_com_can_omitido_quando_usuario_nao_autenticado(): void
    {
        $html = $this->renderItem([
            'label' => 'Administração',
            'route' => 'admin.dashboard',
            'can' => 'admin',
        ]);

        $this->assertSame('', trim($html));
    }

    public function test_item_com_can_omitido_quando_usuario_autenticado_sem_permissao(): void
    {
        $this->actingAs($this->fakeUser(false));

        $html = $this->renderItem([
            'label' => 'Administração',
            'route' => 'admin.dashboard',
            'can' => 'admin',
        ]);

        $this->assertSame('', trim($html));
    }

    public function test_item_com_can_renderizado_quando_usuario_autenticado_com_permissao(): void
    {
        $this->actingAs($this->fakeUser(true));

        $html = $this->renderItem([
            'label' => 'Administração',
            'route' => 'admin.dashboard',
            'can' => 'admin',
        ]);

        $this->assertStringContainsString('>Administração</span>', $html);
        $this->assertStringContainsString(route('admin.dashboard', [], false), $html);
    }

    public function test_item_com_guest_renderizado_quando_usuario_nao_autenticado(): void
    {
        $html = $this->renderItem([
            'label' => 'Entrar com Senha Única',
            'route' => 'login',
            'guest' => true,
        ]);

        $this->assertStringContainsString('>Entrar com Senha Única</span>', $html);
    }

    public function test_item_com_guest_omitido_quando_usuario_autenticado(): void
    {
        $this->actingAs($this->fakeUser(false));

        $html = $this->renderItem([
            'label' => 'Entrar com Senha Única',
            'route' => 'login',
            'guest' => true,
        ]);

        $this->assertSame('', trim($html));
    }

    public function test_item_sem_guest_ou_can_renderizado_para_qualquer_usuario(): void
    {
        $html = $this->renderItem([
            'label' => 'Docs',
            'url' => '/docs',
        ]);

        $this->actingAs($this->fakeUser(false));
        $html2 = $this->renderItem([
            'label' => 'Docs',
            'url' => '/docs',
        ]);

        $this->assertStringContainsString('href="/docs"', $html);
        $this->assertStringContainsString('href="/docs"', $html2);
    }

    public function test_submenu_renderiza_pai_e_filhos_visiveis(): void
    {
        $html = $this->renderItem([
            'label' => 'Cadastros',
            'icon' => 'fa-folder',
            'children' => [
                ['label' => 'Admin', 'route' => 'admin.dashboard', 'icon' => 'fa-lock', 'can' => 'admin'],
                ['label' => 'Docs', 'url' => '/docs', 'icon' => 'fa-book'],
            ],
        ]);

        $this->assertStringContainsString('data-portal-submenu-toggle', $html);
        $this->assertStringContainsString('data-portal-submenu', $html);
        $this->assertStringContainsString('>Cadastros</span>', $html);
        $this->assertStringContainsString('>Docs</span>', $html);
        $this->assertStringNotContainsString('>Admin</span>', $html);
    }

    public function test_submenu_omite_pai_quando_nao_tem_filhos_visiveis(): void
    {
        $html = $this->renderItem([
            'label' => 'Administração',
            'icon' => 'fa-lock',
            'children' => [
                ['label' => 'Admin', 'route' => 'admin.dashboard', 'can' => 'admin'],
            ],
        ]);

        $this->assertSame('', trim($html));
    }

    private function fakeUser(bool $allowed): Authenticatable
    {
        return new class($allowed) implements Authenticatable
        {
            private bool $allowed;

            public function __construct(bool $allowed)
            {
                $this->allowed = $allowed;
            }

            public function can(string $ability): bool
            {
                return $this->allowed && $ability === 'admin';
            }

            public function getAuthIdentifierName(): string
            {
                return 'id';
            }

            public function getAuthIdentifier(): int
            {
                return 1;
            }

            public function getAuthPasswordName(): string
            {
                return 'password';
            }

            public function getAuthPassword(): string
            {
                return '';
            }

            public function getRememberToken(): string
            {
                return '';
            }

            public function setRememberToken($value): void {}

            public function getRememberTokenName(): string
            {
                return '';
            }
        };
    }

    private function renderItem(array $props = []): string
    {
        $attributes = new ComponentAttributeBag;
        $slot = new HtmlString('');
        $view = 'portal-ui::components.sidebar-item';

        return view($view, array_merge($props, [
            'attributes' => $attributes,
            'slot' => $slot,
        ]))->render();
    }
}
