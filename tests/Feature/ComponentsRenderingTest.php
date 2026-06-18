<?php

namespace SistemasEel\PortalUi\Tests\Feature;

use SistemasEel\PortalUi\Tests\TestCase;

class ComponentsRenderingTest extends TestCase
{
    public function test_componentes_genericos_do_lote_seguro_renderizam_sem_livewire(): void
    {
        $html = view('theme-tests::components-safe')->render();

        $this->assertStringContainsString('Painel de indicadores', $html);
        $this->assertStringContainsString('Conteúdo do card', $html);
        $this->assertStringContainsString('Ativo', $html);
        $this->assertStringContainsString('Atenção', $html);
        $this->assertStringContainsString('data-portal-dismissible', $html);
        $this->assertStringContainsString('Seção de exemplo', $html);

        // Novos componentes
        $this->assertStringContainsString('Sucesso!', $html);
        $this->assertStringContainsString('Operação realizada.', $html);
        $this->assertStringContainsString('Opção Ativa', $html);
        $this->assertStringContainsString('name="opcao"', $html);
        $this->assertStringContainsString('Título da Página', $html);
        $this->assertStringContainsString('Subtítulo da página', $html);
        $this->assertStringContainsString('Cabeçalho Hero', $html);
        $this->assertStringContainsString('Subtítulo Hero', $html);
        $this->assertStringContainsString('fa-user', $html);

        $this->assertStringNotContainsString('wire:', $html);
        $this->assertStringNotContainsString('@entangle', $html);
    }

    public function test_componentes_de_formulario_renderizam_com_contrato_laravel_padrao(): void
    {
        $html = view('theme-tests::components-form')->render();

        $this->assertStringContainsString('name="nome"', $html);
        $this->assertStringContainsString('value="Maria"', $html);
        $this->assertStringContainsString('name="setor"', $html);
        $this->assertStringContainsString('selected', $html);
        $this->assertStringContainsString('name="observacao"', $html);
        $this->assertStringContainsString('Salvar', $html);
        $this->assertStringContainsString('Abrir detalhes', $html);
        $this->assertStringContainsString('wire:model.live="form.nome"', $html);
        $this->assertStringContainsString('wire:model.defer="form.setor"', $html);
        $this->assertStringContainsString('wire:model.blur="form.observacao"', $html);
        $this->assertStringContainsString('wire:model="form.ativo"', $html);
        $this->assertStringContainsString('wire:click="save"', $html);
        $this->assertStringContainsString('wire:target="save"', $html);
        $this->assertStringContainsString('Salvar Livewire', $html);
        $this->assertStringContainsString('x-data="{ show: $wire.entangle(\'showDetails\') }"', $html);
        $this->assertStringNotContainsString('@entangle', $html);
    }

    public function test_componentes_de_crud_renderizam_tabela_estado_vazio_rodape_e_acoes(): void
    {
        $html = view('theme-tests::components-crud')->render();

        $this->assertStringContainsString('Confirmar remoção', $html);
        $this->assertStringContainsString('wire:model="showDeleteModal"', $html);
        $this->assertStringContainsString('lista-crud', $html);
        $this->assertStringContainsString('Registro Livewire', $html);
        $this->assertStringContainsString('wire:click="viewItem(10)"', $html);
        $this->assertStringContainsString('wire:click="editItem(10)"', $html);
        $this->assertStringContainsString('wire:click="confirmDelete(10)"', $html);
        $this->assertStringContainsString('Registro tradicional', $html);
        $this->assertStringContainsString('href="/registros/11"', $html);
        $this->assertStringContainsString('href="/registros/11/editar"', $html);
        $this->assertStringContainsString('href="/registros/11/excluir"', $html);
        $this->assertMatchesRegularExpression('~>\s*Ver\s*<~', $html);
        $this->assertMatchesRegularExpression('~>\s*Editar\s*<~', $html);
        $this->assertMatchesRegularExpression('~>\s*Excluir\s*<~', $html);
        $this->assertStringContainsString('Nada por aqui', $html);
        $this->assertStringContainsString('Cadastre um item para começar.', $html);
        $this->assertStringContainsString('Mostrando 1 de 1', $html);
        $this->assertStringContainsString('window.alert(&amp;quot;visualizar&amp;quot;)', $html);
        $this->assertStringContainsString('window.alert(&amp;quot;excluir&amp;quot;)', $html);
    }

    public function test_componentes_de_crud_honram_booleanos_string_em_props_do_tema(): void
    {
        $html = view('theme-tests::components-crud')->render();

        $this->assertStringNotContainsString('border-t border-gray-200', $html);
        $this->assertStringContainsString('text-sm text-gray-500', $html);
        $this->assertDoesNotMatchRegularExpression('~class="[^"]*botao-nao-full[^"]*w-full~', $html);
        $this->assertStringNotContainsString('disabled', $html);
    }

    public function test_icones_genericos_renderizam_com_atributos_e_titulo(): void
    {
        $html = view('theme-tests::components-icons')->render();

        $this->assertStringContainsString('fa-tachometer-alt', $html);
        $this->assertStringContainsString('fa-book', $html);
        $this->assertStringContainsString('data-portal-tooltip', $html);
        $this->assertStringNotContainsString('wire:', $html);
    }

    public function test_sidebar_item_renderiza_link_label_icon_e_tooltip(): void
    {
        $html = view('theme-tests::components-icons')->render();

        $this->assertStringContainsString('data-portal-nav-item', $html);
        $this->assertStringContainsString('data-portal-hide-collapsed', $html);
        $this->assertStringContainsString('data-portal-tooltip', $html);
        $this->assertStringContainsString('>Painel</span>', $html);
        $this->assertStringContainsString('>Documentos</span>', $html);
        $this->assertStringContainsString('fa-tachometer-alt', $html);
        $this->assertStringContainsString('fa-book', $html);
        $this->assertStringContainsString(route('dashboard', [], false), $html);
        $this->assertStringContainsString('href="/docs"', $html);
    }

    public function test_sidebar_item_omite_item_quando_rota_nao_existe_e_hide_missing_esta_ativo(): void
    {
        config(['portal-ui.navigation.hide_missing_routes' => true]);

        $html = view('theme-tests::components-icons')->render();

        $this->assertStringNotContainsString('>Inexistente</span>', $html);
        $this->assertStringContainsString('>Painel</span>', $html);
    }

    public function test_modal_renderiza_sem_dependencia_de_livewire(): void
    {
        $html = view('theme-tests::modal')->render();

        $this->assertStringContainsString('id="detalhes-modal"', $html);
        $this->assertStringContainsString('data-portal-modal', $html);
        $this->assertStringContainsString('role="dialog"', $html);
        $this->assertStringContainsString('aria-modal="true"', $html);
        $this->assertStringContainsString('Detalhes do item', $html);
        $this->assertStringContainsString('Conteúdo do modal sem Livewire.', $html);
        $this->assertStringContainsString('data-portal-modal-close', $html);
        $this->assertStringContainsString('Fechar', $html);
        $this->assertStringContainsString('max-w-lg', $html);
        $this->assertStringNotContainsString('wire:', $html);
        $this->assertStringNotContainsString('@entangle', $html);
    }

    public function test_views_de_exemplo_do_pacote_renderizam_cards_tabelas_e_cabecalhos(): void
    {
        $minimalHtml = view('portal-ui::examples.minimal-showcase')->render();
        $simpleHtml = view('portal-ui::examples.simple-showcase')->render();
        $crudHtml = view('portal-ui::examples.admin-crud-showcase')->render();
        $guestHtml = view('portal-ui::examples.guest-showcase')->render();

        $this->assertStringContainsString('Showcase Minimal', $minimalHtml);
        $this->assertStringContainsString('Layout base', $minimalHtml);
        $this->assertStringContainsString('O showcase minimal reúne só o essencial', $minimalHtml);

        $this->assertStringContainsString('Showcase Simple', $simpleHtml);
        $this->assertStringContainsString('Tabela de Exemplo', $simpleHtml);
        $this->assertStringContainsString('Formulário de Exemplo', $simpleHtml);
        $this->assertStringContainsString('Portal de Atendimento', $simpleHtml);
        $this->assertStringContainsString('Salvar alterações', $simpleHtml);

        $this->assertStringContainsString('CRUD Administrativo', $crudHtml);
        $this->assertStringContainsString('Ações em lote', $crudHtml);
        $this->assertStringContainsString('Tabela de registros', $crudHtml);
        $this->assertStringContainsString('Nenhum item encontrado', $crudHtml);
        $this->assertStringContainsString('Sincronização pendente', $crudHtml);

        $this->assertStringContainsString('Exemplo de Tela Visitante', $guestHtml);
        $this->assertStringContainsString('Acesso público', $guestHtml);
        $this->assertStringContainsString('Entrar', $guestHtml);
    }

    public function test_session_alert_renderiza_com_variantes_e_progresso(): void
    {
        $html = view('theme-tests::components-safe')->render();

        $this->assertStringContainsString('Sucesso!', $html);
        $this->assertStringContainsString('Operação realizada.', $html);
        $this->assertStringContainsString('data-portal-dismissible', $html);
        $this->assertStringContainsString('border-green-200', $html);
    }

    public function test_session_alert_com_auto_dismiss_false_omite_progress_e_atributo_auto_dismiss(): void
    {
        $html = view('theme-tests::components-safe')->render();

        $this->assertStringNotContainsString('data-portal-auto-dismiss', $html);
        $this->assertStringNotContainsString('portal-alert-progress', $html);
    }

    public function test_session_alert_renderiza_com_titulo_e_dismiss_desabilitado(): void
    {
        $html = view('theme-tests::components-safe')->render();

        $this->assertStringContainsString('Atenção', $html);
        $this->assertStringContainsString('Revise as informações.', $html);
        $this->assertStringContainsString('border-yellow-200', $html);
    }
}
