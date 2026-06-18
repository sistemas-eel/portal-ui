<?php

namespace SistemasEel\PortalUi\Concerns;

/**
 * Padroniza o envio de mensagens flash compatíveis com os componentes do tema.
 */
trait HandlesFlashMessages
{
    /**
     * Exibe uma mensagem de sucesso no formato
     * "{resource} {nome} {action} com sucesso!".
     *
     * @param string|int|null $id Identificador opcional salvo como "message_id".
     */
    protected function flashSuccess(string $resource, string $action, string $nome = '', bool $persistent = false, $id = null): void
    {
        $message = ucfirst($resource).' '.$nome.' '.$action.' com sucesso!';

        if ($persistent) {
            session()->flash('message_persistent', true);
        }

        session()->flash('message', trim($message));
        $this->flashMessageId('message', $id);
    }

    /**
     * Exibe uma mensagem de status para alternância simples, como ativação e desativação.
     *
     * @param string|int|null $id Identificador opcional salvo como "message_id".
     */
    protected function flashStatus(string $resource, bool $status, string $nome = '', $id = null): void
    {
        $action = $status ? 'ativado' : 'desativado';
        $message = $nome ? "{$resource} {$nome} {$action} com sucesso!" : "{$resource} {$action} com sucesso!";

        session()->flash('message', $message);
        $this->flashMessageId('message', $id);
    }

    /**
     * Exibe uma mensagem de erro na chave flash "error".
     *
     * @param string|int|null $id Identificador opcional salvo como "error_id".
     */
    protected function flashError(string $message, bool $persistent = false, $id = null): void
    {
        if ($persistent) {
            session()->flash('error_persistent', true);
        }

        session()->flash('error', $message);
        $this->flashMessageId('error', $id);
    }

    /**
     * Exibe uma mensagem de aviso com título separado para consumo pelo componente de flash.
     *
     * @param string|int|null $id Identificador opcional salvo como "warning_id".
     */
    protected function flashWarning(string $titulo, string $message, bool $persistent = false, $id = null): void
    {
        if ($persistent) {
            session()->flash('warning_persistent', true);
        }

        session()->flash('warning', $message);
        session()->flash('warning_title', $titulo);
        $this->flashMessageId('warning', $id);
    }

    /**
     * Exibe uma mensagem informativa opcionalmente acompanhada de título.
     *
     * @param string|int|null $id Identificador opcional salvo como "info_id".
     */
    protected function flashInfo(string $message, ?string $title = null, bool $persistent = false, $id = null): void
    {
        if ($persistent) {
            session()->flash('info_persistent', true);
        }

        session()->flash('info', $message);
        $this->flashMessageId('info', $id);

        if ($title !== null && $title !== '') {
            session()->flash('info_title', $title);
        }
    }

    /**
     * Associa um identificador opcional a uma mensagem flash.
     *
     * O identificador é salvo na sessão como "{key}_id" para permitir filtragem
     * ou escopo no frontend.
     *
     * @param string|int|null $id
     */
    protected function flashMessageId(string $key, $id = null): void
    {
        if ($id === null || $id === '') {
            return;
        }

        session()->flash($key.'_id', (string) $id);
    }
}
