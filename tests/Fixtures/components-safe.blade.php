<x-portal::card class="painel">
    <x-slot name="header">
        Painel de indicadores
    </x-slot>
    Conteúdo do card
</x-portal::card>

<x-portal::badge variant="success">Ativo</x-portal::badge>

<x-portal::alert variant="warning" title="Atenção" :dismissible="true">
    Revise as informações.
</x-portal::alert>

<x-portal::section-header title="Seção de exemplo" subtitle="Informações gerais" icon="fa-folder">
    <x-slot name="actions">
        <button type="button">Nova ação</button>
    </x-slot>
</x-portal::section-header>

<x-portal::flash-alert variant="success" title="Sucesso!" :autoDismiss="false">
    Operação realizada.
</x-portal::flash-alert>

<x-portal::switch label="Opção Ativa" name="opcao" :checked="true" />

<x-portal::page-header title="Título da Página" subtitle="Subtítulo da página" backRoute="login" />

<x-portal::hero-header icon="fa-user" title="Cabeçalho Hero" subtitle="Subtítulo Hero" />

