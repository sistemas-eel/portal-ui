<?php

namespace SistemasEel\PortalUi\Tests\Feature;

use Illuminate\Support\Facades\Session;
use SistemasEel\PortalUi\Tests\TestCase;

class FlashMessagesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Session::start();
    }

    public function test_nao_renderiza_nada_sem_mensagens_de_sessao(): void
    {
        $html = view('theme-tests::flash-messages')->render();

        $this->assertStringNotContainsString('data-portal-flash-messages', $html);
        $this->assertStringNotContainsString('role="alert"', $html);
    }

    public function test_renderiza_mensagens_de_sucesso_das_chaves_configuradas(): void
    {
        Session::flash('success', 'Operação concluída');
        Session::flash('message', 'Mensagem genérica');

        $html = view('theme-tests::flash-messages')->render();

        $this->assertStringContainsString('data-portal-flash-messages', $html);
        $this->assertStringContainsString('Operação concluída', $html);
        $this->assertStringContainsString('Mensagem genérica', $html);
        $this->assertStringContainsString('role="alert"', $html);
        $this->assertStringContainsString('Sucesso', $html);
    }

    public function test_renderiza_mensagens_de_erro_warning_e_info(): void
    {
        Session::flash('error', 'Falha ao processar');
        Session::flash('warning', 'Atenção requerida');
        Session::flash('info', 'Aviso informativo');

        $html = view('theme-tests::flash-messages')->render();

        $this->assertStringContainsString('Falha ao processar', $html);
        $this->assertStringContainsString('Atenção requerida', $html);
        $this->assertStringContainsString('Aviso informativo', $html);
        $this->assertStringContainsString('Erro', $html);
        $this->assertStringContainsString('Atenção', $html);
        $this->assertStringContainsString('Informação', $html);
    }

    public function test_honra_chaves_de_flash_personalizadas(): void
    {
        config(['portal-ui.flash.success_keys' => ['meu_success']]);
        Session::flash('meu_success', 'Sucesso customizado');
        Session::flash('success', 'Sucesso padrão');

        $html = view('theme-tests::flash-messages')->render();

        $this->assertStringContainsString('Sucesso customizado', $html);
        $this->assertStringNotContainsString('Sucesso padrão', $html);
    }

    public function test_aceita_classes_adicionais_no_container(): void
    {
        Session::flash('success', 'ok');

        $html = view('theme-tests::flash-messages')->render();

        $this->assertStringContainsString('class="custom-flash"', $html);
    }

    public function test_pode_filtrar_mensagem_por_identificador(): void
    {
        Session::flash('message', 'Mensagem do item 42');
        Session::flash('message_id', '42');

        $html = view('theme-tests::flash-messages')->render();

        $this->assertStringContainsString('Mensagem do item 42', $html);
        $this->assertStringContainsString('class="flash-item-42"', $html);
    }

    public function test_nao_renderiza_mensagem_quando_identificador_nao_corresponde(): void
    {
        Session::flash('message', 'Mensagem do item 99');
        Session::flash('message_id', '99');

        $html = view('theme-tests::flash-messages')->render();

        $this->assertStringNotContainsString('class="flash-item-42"', $html);
    }

    public function test_nao_renderiza_quando_sessao_tem_apenas_valores_vazios(): void
    {
        Session::flash('success', '');
        Session::flash('info', null);

        $html = view('theme-tests::flash-messages')->render();

        $this->assertStringNotContainsString('data-portal-flash-messages', $html);
    }

    public function test_desativa_auto_dismiss_quando_chave_persistente_esta_presente(): void
    {
        Session::flash('error', 'Falha crítica');
        Session::flash('error_persistent', true);

        $html = view('theme-tests::flash-messages')->render();

        $this->assertStringContainsString('Falha crítica', $html);
        $this->assertStringNotContainsString('data-portal-auto-dismiss', $html);
        $this->assertStringNotContainsString('portal-alert-progress', $html);
    }

    public function test_usa_titulo_personalizado_quando_chave_de_titulo_esta_presente(): void
    {
        Session::flash('warning', 'Sessão expirou');
        Session::flash('warning_title', 'Sessão Expirada');

        $html = view('theme-tests::flash-messages')->render();

        $this->assertStringContainsString('Sessão expirou', $html);
        $this->assertStringContainsString('Sessão Expirada', $html);
        $this->assertStringNotContainsString('Atenção', $html);
    }

    public function test_persistencia_e_titulo_personalizado_podem_coexistir_para_o_mesmo_variant(): void
    {
        Session::flash('error', 'Você não tem permissão para acessar este sistema.');
        Session::flash('error_persistent', true);
        Session::flash('error_title', 'Acesso Negado');

        $html = view('theme-tests::flash-messages')->render();

        $this->assertStringContainsString('Acesso Negado', $html);
        $this->assertStringContainsString('Você não tem permissão', $html);
        $this->assertStringNotContainsString('data-portal-auto-dismiss', $html);
        $this->assertStringNotContainsString('Erro', $html);
    }

    public function test_persistencia_e_titulo_personalizado_sao_configuraveis(): void
    {
        config(['portal-ui.flash.persistent_keys.error' => ['minha_persistente']]);
        config(['portal-ui.flash.title_keys.success' => ['meu_titulo']]);

        Session::flash('error', 'Erro custom');
        Session::flash('minha_persistente', true);

        $html = view('theme-tests::flash-messages')->render();

        $this->assertStringNotContainsString('data-portal-auto-dismiss', $html);
    }

    public function test_titulo_personalizado_sao_configuraveis_pela_config(): void
    {
        config(['portal-ui.flash.title_keys.success' => ['meu_titulo']]);

        Session::flash('success', 'OK');
        Session::flash('meu_titulo', 'Título custom');

        $html = view('theme-tests::flash-messages')->render();

        $this->assertStringContainsString('Título custom', $html);
        $this->assertStringNotContainsString('Sucesso', $html);
    }
}
