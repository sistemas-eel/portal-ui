<?php

namespace SistemasEel\PortalUi\Tests\Feature;

use Illuminate\Contracts\Auth\Authenticatable;
use SistemasEel\PortalUi\Tests\TestCase;

class SidebarGroupLabelTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);
        $app['router']->get('/admin', function () {})->name('admin.dashboard');
        $app['router']->get('/public-route', function () {})->name('public.route');
    }

    public function test_group_label_omitido_quando_todos_itens_com_can_filtrados(): void
    {
        config(['portal-ui.navigation.groups' => [
            'admin' => [
                'label' => 'Administração',
                'items' => [
                    ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'fa-home', 'can' => 'admin'],
                ],
            ],
        ]]);

        $html = $this->renderApp();

        $this->assertStringNotContainsString('Administração', $html);
        $this->assertStringNotContainsString('Dashboard', $html);
    }

    public function test_group_label_visivel_quando_pelo_menos_um_item_visivel(): void
    {
        config(['portal-ui.navigation.groups' => [
            'admin' => [
                'label' => 'Administração',
                'items' => [
                    ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'fa-home', 'can' => 'admin'],
                    ['label' => 'Pública', 'route' => 'public.route', 'icon' => 'fa-globe'],
                ],
            ],
        ]]);

        $user = $this->fakeUser(true);
        $this->actingAs($user);

        $html = $this->renderApp();
        $this->assertStringContainsString('Administração', $html);
        $this->assertStringContainsString('Dashboard', $html);
        $this->assertStringContainsString('Pública', $html);
    }

    public function test_group_label_omitido_quando_usuario_autenticado_sem_permissao_e_sem_itens_publicos(): void
    {
        config(['portal-ui.navigation.groups' => [
            'admin' => [
                'label' => 'Administração',
                'items' => [
                    ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'fa-home', 'can' => 'admin'],
                ],
            ],
        ]]);

        $this->actingAs($this->fakeUser(false));
        $html = $this->renderApp();

        $this->assertStringNotContainsString('Administração', $html);
        $this->assertStringNotContainsString('Dashboard', $html);
    }

    public function test_group_label_visivel_apenas_para_guests_quando_item_tem_guest(): void
    {
        config(['portal-ui.navigation.groups' => [
            'public' => [
                'label' => 'Acesso',
                'items' => [
                    ['label' => 'Entrar', 'route' => 'login', 'icon' => 'fa-sign-in-alt', 'guest' => true],
                ],
            ],
        ]]);

        $this->actingAs($this->fakeUser(false));
        $htmlAuth = $this->renderApp();
        $this->assertStringNotContainsString('Acesso', $htmlAuth);
        $this->assertStringNotContainsString('Entrar', $htmlAuth);
    }

    public function test_group_label_visivel_para_guests_quando_item_tem_guest(): void
    {
        config(['portal-ui.navigation.groups' => [
            'public' => [
                'label' => 'Acesso',
                'items' => [
                    ['label' => 'Entrar', 'route' => 'login', 'icon' => 'fa-sign-in-alt', 'guest' => true],
                ],
            ],
        ]]);

        $htmlGuest = $this->renderApp();
        $this->assertStringContainsString('Acesso', $htmlGuest);
        $this->assertStringContainsString('Entrar', $htmlGuest);
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

    private function renderApp(): string
    {
        return view('theme-tests::layout-app')->render();
    }
}
