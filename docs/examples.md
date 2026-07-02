# Exemplos do `portal-ui`

Este documento resume os exemplos visuais incluídos no pacote e mostra snippets mínimos para começar a usar o layout e os componentes.

## Views de exemplo

As seguintes views já existem no pacote:

- `portal-ui::examples.minimal-showcase`
- `portal-ui::examples.simple-showcase`
- `portal-ui::examples.guest-showcase`
- `portal-ui::examples.admin-crud-showcase`

O pacote não registra rotas automaticamente. Para visualizar os exemplos no app consumidor:

```php
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function () {
    Route::view('/portal-ui-demo-minimal', 'portal-ui::examples.minimal-showcase')->name('portal-ui.demo.minimal');
    Route::view('/portal-ui-demo', 'portal-ui::examples.simple-showcase')->name('portal-ui.demo');
    Route::view('/portal-ui-demo-crud', 'portal-ui::examples.admin-crud-showcase')->name('portal-ui.demo.crud');
    Route::view('/portal-ui-demo-guest', 'portal-ui::examples.guest-showcase')->name('portal-ui.demo.guest');
});
```

## Quando usar cada exemplo

- `minimal-showcase`: página interna bem enxuta, com cabeçalho, alerta e card.
- `simple-showcase`: página interna intermediária com cards, tabela e formulário.
- `guest-showcase`: login, recuperação de senha e telas públicas.
- `admin-crud-showcase`: cenário administrativo com filtros, tabela, ações em lote e estado vazio.

## Menus de navegação

O tema monta a sidebar a partir de `config('portal-ui.navigation.groups')`. A topbar já traz o comportamento base de autenticação:

- em layout autenticado, mostra avatar, dropdown do usuário e logout;
- em layout visitante, topbar e sidebar continuam disponíveis para navegação anônima;
- se existir rota `login` e não houver usuário autenticado, a topbar mostra o botão `Entrar`.
- se não houver nenhum item de navegação visível, a sidebar e o botão de abrir menu não são renderizados.

Os stubs publicados pelo pacote ajudam a começar mais rápido:

- `stubs/portal-ui/navigation/minimal.php`: base mínima, com uma única entrada interna.
- `stubs/portal-ui/navigation/simple.php`: evolução natural do minimal, com poucas áreas principais.
- `stubs/portal-ui/navigation/admin.php`: evolução do simple, com seção administrativa e itens protegidos por `can`.
- `stubs/portal-ui/routes/demo.php`: rotas prontas para expor os showcases.

#### Como carregar as rotas de demonstração dos stubs
No seu arquivo `routes/web.php` (no app consumidor), carregue as rotas do stub dinamicamente:
```php
if (file_exists(base_path('stubs/portal-ui/routes/demo.php'))) {
    require base_path('stubs/portal-ui/routes/demo.php');
}
```

#### Como configurar a navegação usando os stubs
Abra um dos stubs de navegação listados acima, copie a estrutura de grupos retornada e cole-a diretamente na chave `groups` do arquivo publicado `config/portal-ui.php`:
```php
'navigation' => [
    'groups' => [
        // Cole a estrutura do stub de navegação copiado aqui
    ],
],
```

Os exemplos visuais seguem a mesma progressão:

- `minimal`: o menor ponto de partida para tela autenticada
- `simple`: evolução natural com blocos de conteúdo, tabela e formulário
- `admin`: evolução do simple para cenários de gestão e CRUD
- `guest`: fluxo público com navegação anônima

### Menu simples

Use quando a aplicação precisa só de uma navegação principal sem regras de permissão:

```php
return [
    'navigation' => [
        'groups' => [
            'main' => [
                'label' => 'Menu Principal',
                'items' => [
                    [
                        'label' => 'Início',
                        'route' => 'dashboard',
                        'icon' => 'fa-home',
                        'active' => 'dashboard',
                    ],
                    [
                        'label' => 'Documentos',
                        'icon' => 'fa-folder-open',
                        'active' => 'documentos.*',
                        'children' => [
                            [
                                'label' => 'Todos os documentos',
                                'route' => 'documentos.index',
                                'icon' => 'fa-list',
                                'active' => 'documentos.index',
                            ],
                            [
                                'label' => 'Novo documento',
                                'route' => 'documentos.create',
                                'icon' => 'fa-plus',
                                'active' => 'documentos.create',
                            ],
                        ],
                    ],
                    [
                        'label' => 'Ajuda',
                        'url' => 'https://example.org/ajuda',
                        'icon' => 'fa-circle-question',
                        'external' => true,
                    ],
                ],
            ],
        ],
    ],
];
```

### Menu guest

Para páginas públicas com navegação anônima, use `portal-ui::layouts.guest`. Ele mantém topbar e sidebar, então você pode expor itens públicos com `guest => true`:

```php
return [
    'navigation' => [
        'groups' => [
            'publico' => [
                'label' => 'Acesso Público',
                'items' => [
                    [
                        'label' => 'Entrar',
                        'route' => 'login',
                        'icon' => 'fa-sign-in-alt',
                        'active' => 'login',
                        'guest' => true,
                    ],
                    [
                        'label' => 'Mensagens',
                        'route' => 'mensagens.publicas',
                        'icon' => 'fa-envelope',
                        'active' => 'mensagens.publicas',
                        'guest' => true,
                    ],
                ],
            ],
        ],
    ],
];
```

Na view:

```blade
@extends('portal-ui::layouts.guest')

@section('title', 'Entrar')

@section('content')
    <x-portal::input label="E-mail" name="email" />
    <x-portal::input label="Senha" name="password" type="password" />
    <x-portal::button type="submit" full="true">Entrar</x-portal::button>
@endsection
```

Se existir rota nomeada `login` e não houver usuário autenticado, o botão `Entrar` aparece automaticamente na topbar.

### Menu admin

Use quando a navegação depende de permissões da aplicação:

```php
return [
    'navigation' => [
        'groups' => [
            'principal' => [
                'label' => 'Principal',
                'items' => [
                    [
                        'label' => 'Dashboard',
                        'route' => 'dashboard',
                        'icon' => 'fa-chart-line',
                        'active' => 'dashboard',
                    ],
                ],
            ],
            'admin' => [
                'label' => 'Administração',
                'items' => [
                    [
                        'label' => 'Usuários',
                        'route' => 'admin.users.index',
                        'icon' => 'fa-users',
                        'active' => 'admin.users.*',
                        'can' => 'admin',
                    ],
                    [
                        'label' => 'Configurações',
                        'route' => 'admin.settings.index',
                        'icon' => 'fa-cogs',
                        'active' => 'admin.settings.*',
                        'can' => 'admin',
                    ],
                    [
                        'label' => 'Documentação',
                        'url' => 'https://example.org/docs',
                        'icon' => 'fa-book-open',
                        'external' => true,
                        'can' => 'admin',
                    ],
                ],
            ],
        ],
    ],
];
```

### Submenus e links externos

Use `children` para criar submenus. O item pai aparece quando pelo menos um filho for visível, mesmo que o próprio pai não tenha `route` ou `url`. Regras como `can`, `guest` e `hide_missing_routes` continuam sendo aplicadas em cada filho.

Exemplo isolado de item com submenu:

```php
[
    'label' => 'Cadastros',
    'icon' => 'fa-folder-tree',
    'active' => 'cadastros.*',
    'children' => [
        [
            'label' => 'Unidades',
            'route' => 'unidades.index',
            'icon' => 'fa-building',
            'active' => 'unidades.*',
        ],
        [
            'label' => 'Fontes',
            'route' => 'fontes.index',
            'icon' => 'fa-coins',
            'active' => 'fontes.*',
        ],
        [
            'label' => 'Manual do módulo',
            'url' => 'https://example.org/manual-cadastros',
            'icon' => 'fa-book-open',
            'external' => true,
        ],
    ],
]
```

Links com `url` absoluta (`http`/`https`) são tratados como externos automaticamente. Você também pode forçar o comportamento com `external => true`, ou sobrescrever `target` e `rel` quando necessário.

### Ações da topbar e menu do usuário

Além da sidebar por config, o layout aceita extensões diretamente na view:

```blade
@section('topbar-actions')
    <x-portal::button size="sm" variant="outline" icon="fa-plus">Novo registro</x-portal::button>
@endsection

@section('user-menu')
    <a href="{{ route('profile') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
        <i class="fa fa-user w-4"></i>
        <span>Meu perfil</span>
    </a>
@endsection
```

### Botões da SenhaUnica

Quando o sistema usa `uspdev/senhaunica-socialite`, a integração de views do `portal-ui` já tematiza as telas internas da SenhaUnica. Para expor atalhos na UI do sistema, adicione os links pela navegação configurável ou pelos slots da topbar.

Na sidebar, normalmente faz sentido colocar a gestão de usuários dentro do grupo administrativo:

```php
[
    'label' => 'Usuários SenhaUnica',
    'route' => 'senhaunica-users.index',
    'icon' => 'fa-id-card',
    'active' => 'senhaunica-users.*',
    'can' => 'admin',
],
[
    'label' => 'Assumir identidade',
    'route' => 'SenhaunicaLoginAsForm',
    'icon' => 'fa-user-secret',
    'active' => 'SenhaunicaLoginAsForm',
    'can' => 'admin',
],
```

No menu do usuário, prefira proteger os links com `Route::has()` para permitir que apps desabilitem partes da SenhaUnica sem quebrar o layout:

```blade
@section('user-menu')
    @if(session()->has('senhaunica-socialite.undo_loginas') && Route::has('SenhaunicaUndoLoginAs'))
        <a href="{{ route('SenhaunicaUndoLoginAs') }}"
           class="flex items-center gap-3 px-4 py-2 text-sm text-amber-700 hover:bg-amber-50">
            <i class="fa fa-rotate-left w-4"></i>
            <span>Desfazer LoginAs</span>
        </a>
    @endif

    @can('admin')
        @if(Route::has('senhaunica-users.index'))
            <a href="{{ route('senhaunica-users.index') }}"
               class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                <i class="fa fa-id-card w-4"></i>
                <span>Usuários SenhaUnica</span>
            </a>
        @endif

        @if(Route::has('SenhaunicaLoginAsForm'))
            <a href="{{ route('SenhaunicaLoginAsForm') }}"
               class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                <i class="fa fa-user-secret w-4"></i>
                <span>Assumir identidade</span>
            </a>
        @endif
    @endcan
@endsection
```

Se quiser botões compactos no topo da página administrativa:

```blade
@section('topbar-actions')
    @can('admin')
        @if(Route::has('senhaunica-users.index'))
            <x-portal::button
                href="{{ route('senhaunica-users.index') }}"
                size="sm"
                variant="outline"
                icon="fa-id-card"
            >
                SenhaUnica
            </x-portal::button>
        @endif

        @if(Route::has('SenhaunicaLoginAsForm'))
            <x-portal::button
                href="{{ route('SenhaunicaLoginAsForm') }}"
                size="sm"
                variant="ghost"
                icon="fa-user-secret"
            >
                LoginAs
            </x-portal::button>
        @endif
    @endcan
@endsection
```

## CSS e JS customizados

Os layouts também aceitam conteúdo extra por stack:

- `portal-ui-head`: CSS, metas ou links extras no `<head>`
- `portal-ui-before-scripts`: scripts antes do JS do tema
- `portal-ui-after-scripts`: scripts depois do JS do tema

Exemplo:

```blade
@push('portal-ui-head')
    <style>
        .resumo-kpi {
            background-color: color-mix(in srgb, var(--portal-ui-primary) 10%, white);
        }
    </style>
@endpush

@push('portal-ui-before-scripts')
    <script>
        window.portalUiPage = { area: 'relatorios' };
    </script>
@endpush

@push('portal-ui-after-scripts')
    <script>
        console.log('Tela carregada');
    </script>
@endpush
```

## Componentes com Livewire opcional

Os componentes base do tema continuam funcionando sem Livewire, mas agora aceitam atributos `wire:*` quando o app consumidor usa esse stack.

Exemplo:

```blade
<x-portal::input
    label="Nome"
    wire:model.live="form.nome"
/>

<x-portal::select
    label="Setor"
    wire:model.defer="form.setor"
    :options="$setores"
/>

<x-portal::textarea
    label="Descrição"
    wire:model.blur="form.descricao"
/>

<x-portal::switch
    label="Ativo"
    wire:model="form.ativo"
/>

<x-portal::button
    click="save"
    wire:target="save"
    icon="fa-save"
>
    Salvar
</x-portal::button>

<x-portal::modal
    title="Detalhes"
    wire:model="showDetailsModal"
>
    Conteúdo do modal.
</x-portal::modal>
```

Notas práticas:

- sem `wire:model`, os componentes seguem o contrato Blade/Laravel tradicional com `name`, `value`, `selected` e `old()`;
- com `wire:model`, os atributos `wire:*` são preservados e o componente evita injetar comportamento que conflite com a atualização do Livewire;
- o tema não carrega assets do Livewire para o consumidor.

### Modal com Livewire

Para modal com Livewire, pense em quatro partes:

1. uma property booleana para controlar abertura;
2. um botão ou ação para definir essa property como `true`;
3. o modal ligado com `wire:model`;
4. um fechamento explícito com `$set(...)` ou método do componente.

Exemplo:

```php
public bool $showCreateModal = false;
public ?int $editingId = null;

public function create(): void
{
    $this->editingId = null;
    $this->showCreateModal = true;
}

public function edit(int $id): void
{
    $this->editingId = $id;
    $this->showCreateModal = true;
}
```

```blade
<x-portal::button click="create" icon="fa-plus">
    Novo registro
</x-portal::button>

<x-portal::modal
    wire:model="showCreateModal"
    :title="$editingId ? 'Editar registro' : 'Novo registro'"
    :icon="$editingId ? 'fa-edit' : 'fa-plus'"
>
    Conteúdo do formulário.

    <x-slot:footer>
        <x-portal::button
            variant="secondary"
            click="$set('showCreateModal', false)"
        >
            Cancelar
        </x-portal::button>

        <x-portal::button click="save" icon="fa-save">
            Salvar
        </x-portal::button>
    </x-slot:footer>
</x-portal::modal>
```

Para título dinâmico, use `:title`, não `title`.

### Padrões para CRUD

Para telas de listagem administrativa, o tema agora oferece componentes menores para padronizar tabela, estado vazio, confirmação e ações por linha.

Exemplo com Livewire:

```blade
<x-portal::confirm-modal
    wire:model="showConfirmModal"
    title="Confirmar exclusão"
    message="O registro selecionado será excluído permanentemente."
    confirm-label="Excluir"
    confirm-variant="danger"
    confirm-icon="fa-trash"
    confirm-action="confirmarExclusao"
    cancel-action="fecharConfirmacao"
/>

<x-portal::table>
    <x-slot:head>
        <tr>
            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nome</th>
            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Ações</th>
        </tr>
    </x-slot:head>

    <x-slot:body>
        @forelse ($registros as $registro)
            <tr>
                <td class="px-4 py-3 text-sm text-gray-700">{{ $registro->nome }}</td>
                <td class="px-4 py-3 text-right">
                    <x-portal::resource-actions
                        :view-click="'visualizar('.$registro->id.')'"
                        :edit-click="'editar('.$registro->id.')'"
                        :delete-click="'abrirConfirmacaoExclusao('.$registro->id.')'"
                        view-title="Visualizar registro"
                        edit-title="Editar registro"
                        delete-title="Excluir registro"
                    />
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="2" class="p-2">
                    <x-portal::empty-state
                        class="m-6"
                        title="Nenhum registro encontrado"
                        message="Assim que houver dados, eles aparecerao aqui."
                        icon="fa-inbox"
                    />
                </td>
            </tr>
        @endforelse
    </x-slot:body>
</x-portal::table>

<x-portal::section-footer align="center" bordered="false">
    <x-portal::button click="create" icon="fa-plus" variant="secondary">
        Adicionar registro
    </x-portal::button>
</x-portal::section-footer>
```

Exemplo sem Livewire:

```blade
<x-portal::table>
    <x-slot:head>
        <tr>
            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nome</th>
            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Ações</th>
        </tr>
    </x-slot:head>

    <x-slot:body>
        @foreach ($registros as $registro)
            <tr>
                <td class="px-4 py-3 text-sm text-gray-700">{{ $registro->nome }}</td>
                <td class="px-4 py-3 text-right">
                    <x-portal::resource-actions
                        :view-href="route('registros.show', $registro)"
                        :edit-href="route('registros.edit', $registro)"
                        :delete-href="route('registros.delete.confirm', $registro)"
                        mode="label"
                    />
                </td>
            </tr>
        @endforeach
    </x-slot:body>
</x-portal::table>
```

Notas práticas:

- `x-portal::table` cuida apenas da estrutura visual da listagem; o conteúdo continua explícito na view.
- `x-portal::section-footer` funciona bem tanto para totais quanto para botões de ação ou paginação.
- `x-portal::table-actions` é o contêiner base para agrupar ações de linha.
- `x-portal::resource-actions` atende os casos comuns de `visualizar`, `editar` e `excluir`, aceitando `view-click`, `edit-click` e `delete-click` para Livewire, `view-href`, `edit-href` e `delete-href` para navegação tradicional, e `mode="icon"` ou `mode="label"` para variar a apresentação.
- quando houver uma quarta ação específica do domínio, como enviar e-mail, use `x-portal::table-actions` e combine `resource-actions` com botões avulsos.

## Exemplo mínimo de layout autenticado

```blade
@extends('portal-ui::layouts.app')

@section('title', 'Painel')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <span>/</span>
    <span>Painel</span>
@endsection

@section('content')
    <x-portal::page-header
        title="Painel"
        subtitle="Resumo da operação"
    />

    <x-portal::card>
        <x-slot:header>
            Indicadores
        </x-slot:header>

        Conteúdo principal da página.
    </x-portal::card>
@endsection
```

## Exemplo mínimo de layout visitante

```blade
@extends('portal-ui::layouts.guest')

@section('title', 'Entrar')

@section('content')
    <x-portal::input label="E-mail" name="email" />
    <x-portal::input label="Senha" name="password" type="password" />
    <x-portal::button type="submit" full="true">Entrar</x-portal::button>
@endsection
```

## Cabeçalhos

### `x-portal::page-header`

Use para cabeçalho principal da página, com breadcrumbs e ações:

```blade
<x-portal::page-header
    title="Usuários"
    subtitle="Gerencie os usuários do sistema"
    :breadcrumbs="[
        ['label' => 'Dashboard', 'route' => 'dashboard'],
        ['label' => 'Usuários', 'route' => ''],
    ]"
>
    <x-slot:actions>
        <x-portal::button icon="fa-plus">Novo usuário</x-portal::button>
    </x-slot:actions>
</x-portal::page-header>
```

### `x-portal::section-header`

Use dentro de cards e blocos internos:

```blade
<x-portal::section-header
    title="Tabela de registros"
    subtitle="Últimas atualizações"
    icon="fa-table"
/>
```

## Cards

```blade
<x-portal::card>
    <x-slot:header>
        Resumo
    </x-slot:header>

    Conteúdo do card.

    <x-slot:footer>
        Rodapé opcional.
    </x-slot:footer>
</x-portal::card>
```

## Tabela simples

```blade
<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-800/50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nome</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Ações</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white dark:divide-gray-700 dark:bg-gray-900/20">
            <tr>
                <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">Portal de Atendimento</td>
                <td class="px-4 py-3">
                    <x-portal::badge variant="success">Ativo</x-portal::badge>
                </td>
                <td class="px-4 py-3 text-right">
                    <x-portal::resource-actions
                        :view-href="route('registros.show', $registro)"
                        only="view"
                    />
                </td>
            </tr>
        </tbody>
    </table>
</div>
```

## Formulário simples

```blade
<form class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <x-portal::input label="Nome" name="nome" />
    <x-portal::select
        label="Status"
        name="status"
        :options="['ativo' => 'Ativo', 'inativo' => 'Inativo']"
    />
    <div class="md:col-span-2">
        <x-portal::textarea label="Descrição" name="descricao" />
    </div>
    <div class="md:col-span-2 flex flex-col gap-3 sm:flex-row sm:justify-end">
        <x-portal::button variant="secondary" full="true">Cancelar</x-portal::button>
        <x-portal::button full="true">Salvar</x-portal::button>
    </div>
</form>
```

## Feedback visual

### Alert

```blade
<x-portal::alert variant="success" title="Sucesso">
    Operação concluída com sucesso.
</x-portal::alert>
```

### Badge

```blade
<x-portal::badge variant="warning">Pendente</x-portal::badge>
```

### Switch

```blade
<x-portal::switch name="publicado" label="Publicado?" checked="true" />
```

## Referência rápida

Os melhores arquivos para copiar como base são:

- `resources/views/examples/minimal-showcase.blade.php`
- `resources/views/examples/simple-showcase.blade.php`
- `resources/views/examples/guest-showcase.blade.php`
- `resources/views/examples/admin-crud-showcase.blade.php`

Se a ideia for criar uma tela nova no consumidor, normalmente vale começar copiando o exemplo mais próximo e ir removendo o excesso.
