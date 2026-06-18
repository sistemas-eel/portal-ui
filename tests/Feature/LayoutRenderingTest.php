<?php

namespace SistemasEel\PortalUi\Tests\Feature;

use Illuminate\Contracts\Auth\Authenticatable;
use SistemasEel\PortalUi\Tests\TestCase;
use SistemasEel\PortalUi\View\Components\AppLayout;
use SistemasEel\PortalUi\View\Components\GuestLayout;

class LayoutRenderingTest extends TestCase
{
    public function test_layout_autenticavel_renderiza_slots_e_assets_publicados(): void
    {
        $html = view('theme-tests::layout-app')->render();

        $this->assertStringContainsString('<title>Painel - Portal Exemplo</title>', $html);
        $this->assertStringContainsString('Página inicial', $html);
        $this->assertStringContainsString('Ação rápida', $html);
        $this->assertStringContainsString('Conteúdo principal', $html);
        $this->assertStringContainsString('/vendor/portal-ui/portal-ui.css', $html);
        $this->assertStringContainsString('/vendor/portal-ui/portal-ui.js', $html);
        $this->assertStringContainsString('Alternar tema escuro', $html);
        $this->assertStringContainsString('data-portal-has-sidebar="false"', $html);
        $this->assertStringNotContainsString('@livewireStyles', $html);
        $this->assertStringNotContainsString('@livewireScripts', $html);
        $this->assertStringNotContainsString('wire:', $html);
    }

    public function test_layout_visitante_renderiza_configuracao_de_marca_sem_livewire(): void
    {
        $html = view('theme-tests::layout-guest')->render();

        $this->assertStringContainsString('<title>Entrar - Portal Exemplo</title>', $html);
        $this->assertStringContainsString('Portal Exemplo', $html);
        $this->assertStringContainsString('Formulário de acesso', $html);
        $this->assertStringContainsString('data-portal-ui="guest"', $html);
        $this->assertStringContainsString('data-portal-has-sidebar="false"', $html);
        $this->assertStringNotContainsString('data-portal-sidebar', $html);
        $this->assertStringNotContainsString('data-portal-sidebar-open', $html);
        $this->assertStringContainsString('Alternar tema escuro', $html);
        $this->assertStringNotContainsString('wire:', $html);
    }

    public function test_alias_do_componente_de_classe_usa_hifen_e_nao_dois_pontos(): void
    {
        $blade = $this->app->make('blade.compiler');
        $ref = new \ReflectionClass($blade);
        $prop = $ref->getProperty('classComponentAliases');
        $prop->setAccessible(true);
        $aliases = $prop->getValue($blade);

        $this->assertArrayHasKey('portal-app-layout', $aliases);
        $this->assertArrayHasKey('portal-guest-layout', $aliases);
        $this->assertSame(AppLayout::class, $aliases['portal-app-layout']);
        $this->assertSame(GuestLayout::class, $aliases['portal-guest-layout']);
    }

    public function test_componente_app_layout_renderiza_slot_e_suporta_secoes_de_extensao(): void
    {
        $html = view('theme-tests::layout-app-component')->render();

        $this->assertStringContainsString('data-portal-ui="app"', $html);
        $this->assertStringContainsString('Conteúdo via slot', $html);
        $this->assertStringContainsString('Breadcrumb via slot', $html);
    }

    public function test_css_compilado_usa_translate_property_para_garantir_sidebar_responsivo(): void
    {
        $cssPath = __DIR__.'/../../public/portal-ui.css';

        $this->assertFileExists($cssPath, 'O CSS compilado do pacote deve existir em public/portal-ui.css');

        $css = file_get_contents($cssPath);

        $this->assertStringContainsString(
            '[data-portal-layout].is-sidebar-open [data-portal-sidebar]',
            $css,
            'A regra para abrir o sidebar no mobile deve existir no CSS compilado'
        );

        $this->assertMatchesRegularExpression(
            '/\[data-portal-layout\]\.is-sidebar-open\s+\[data-portal-sidebar\][^{]*\{[^}]*translate:\s*0\s*!important/s',
            $css,
            'A regra precisa usar `translate: 0 !important` (e não apenas `transform: translate(0)`) para sobrepor a propriedade `translate` que Tailwind v4 aplica via `.-translate-x-full`'
        );

        $this->assertMatchesRegularExpression(
            '/\[data-portal-layout\]\.is-sidebar-open\s+\[data-portal-sidebar\][^{]*\{[^}]*--tw-translate-x:\s*0\s*!important/s',
            $css,
            'A regra precisa resetar a custom property `--tw-translate-x` que Tailwind v4 usa internamente'
        );
    }

    public function test_layout_autenticavel_renderiza_slot_modals_apos_os_scripts(): void
    {
        $html = view('theme-tests::layout-app-with-modals')->render();

        $this->assertStringContainsString('Modal global do consumidor', $html);
        $posContent = strpos($html, 'Conteúdo principal do consumidor');
        $posModals = strpos($html, 'Modal global do consumidor');
        $this->assertNotFalse($posContent);
        $this->assertNotFalse($posModals);
        $this->assertGreaterThan(
            $posContent,
            $posModals,
            'O slot @yield(modals) deve ser renderizado depois do @yield(content), não antes'
        );
    }

    public function test_layout_visitante_tambem_expoe_slot_modals_via_secao_classica(): void
    {
        $html = view('theme-tests::layout-guest-with-modal')->render();

        $this->assertStringContainsString('data-portal-ui="guest"', $html);
        $this->assertStringContainsString('Modal do consumidor (visitante)', $html);
    }

    public function test_layout_omite_sidebar_e_botao_de_menu_quando_nao_ha_itens_visiveis(): void
    {
        config(['portal-ui.navigation.groups' => []]);

        $html = view('theme-tests::layout-guest')->render();

        $this->assertStringContainsString('data-portal-has-sidebar="false"', $html);
        $this->assertStringNotContainsString('data-portal-sidebar', $html);
        $this->assertStringNotContainsString('data-portal-sidebar-open', $html);
    }

    public function test_layout_renderiza_submenu_e_link_externo_da_configuracao(): void
    {
        config(['portal-ui.navigation.groups' => [
            'main' => [
                'label' => 'Principal',
                'items' => [
                    [
                        'label' => 'Cadastros',
                        'icon' => 'fa-folder',
                        'children' => [
                            [
                                'label' => 'Dashboard',
                                'route' => 'dashboard',
                                'active' => 'dashboard',
                            ],
                        ],
                    ],
                    [
                        'label' => 'Manual',
                        'url' => 'https://example.org/manual',
                    ],
                ],
            ],
        ]]);

        $html = view('theme-tests::layout-app')->render();

        $this->assertStringContainsString('data-portal-has-sidebar="true"', $html);
        $this->assertStringContainsString('data-portal-submenu-toggle', $html);
        $this->assertStringContainsString('>Cadastros</span>', $html);
        $this->assertStringContainsString('>Dashboard</span>', $html);
        $this->assertStringContainsString('href="https://example.org/manual"', $html);
        $this->assertStringContainsString('target="_blank"', $html);
        $this->assertStringContainsString('rel="noopener noreferrer"', $html);
    }

    public function test_topbar_user_menu_dropdown_renderiza_avatar_e_form_de_logout_quando_autenticado(): void
    {
        $this->actingAs($this->fakeUser());

        $html = view('portal-ui::layouts.app')->render();

        $this->assertStringContainsString('data-portal-dropdown', $html);
        $this->assertStringContainsString('data-portal-dropdown-toggle', $html);
        $this->assertStringContainsString('aria-expanded="false"', $html);
        $this->assertStringContainsString('data-portal-dropdown-menu', $html);
        $this->assertStringContainsString('Sair', $html);
        $this->assertMatchesRegularExpression(
            '#action="[^"]*logout"#',
            $html,
            'O formulário de logout no dropdown do user menu deve apontar para a rota logout (URL absoluta ou relativa)'
        );
        $this->assertStringContainsString('name="_token"', $html);
        $this->assertStringContainsString('Maria de Teste', $html);
    }

    public function test_topbar_aceita_slot_user_menu_via_secao_classica(): void
    {
        $this->actingAs($this->fakeUser());

        $html = view('theme-tests::layout-app-with-user-menu')->render();

        $this->assertStringContainsString('Item customizado do user menu', $html);
        $this->assertStringContainsString('Sair', $html);
    }

    private function fakeUser(): Authenticatable
    {
        return new class implements Authenticatable
        {
            public ?string $name = 'Maria de Teste';

            public ?string $email = 'maria@example.org';

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

            public function getAuthIdentifierValue(): mixed
            {
                return 1;
            }

            public function getNameAttribute(): string
            {
                return 'Maria de Teste';
            }
        };
    }
}
