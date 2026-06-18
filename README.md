# PortalUi

Biblioteca de UI Laravel reutilizável para sistemas administrativos. O pacote é `Blade-first`: entrega layouts, componentes, configuração e assets compilados sem exigir Livewire, Alpine, Tailwind ou Vite no app consumidor.

Versão inicial: `v0.1.0`.

## Requisitos

```json
{
  "php": "^7.4|^8.0|^8.1|^8.2|^8.3|^8.4",
  "illuminate/support": "^8.0|^9.0|^10.0|^11.0|^12.0|^13.0",
  "illuminate/view": "^8.0|^9.0|^10.0|^11.0|^12.0|^13.0"
}
```

## Instalação

Instale o pacote no app consumidor com Composer:

```bash
composer require sistemas-eel/portal-ui
```

Depois execute:

```bash
php artisan package:discover
```

Se quiser fixar uma faixa de versão, use algo como `sistemas-eel/portal-ui:^0.1`.

## Publicação

```bash
php artisan vendor:publish --tag=portal-ui-config
php artisan vendor:publish --tag=portal-ui-assets
php artisan vendor:publish --tag=portal-ui-stubs
```

Views tematizadas para `uspdev/senhaunica-socialite` podem ser publicadas separadamente quando o app consumidor precisar customizar algum ponto:

```bash
php artisan vendor:publish --tag=portal-ui-senhaunica-views
```

Publique `views` apenas quando precisar sobrescrever Blade no app consumidor:

```bash
php artisan vendor:publish --tag=portal-ui-views
```

Regra prática:

- `assets`: necessários para carregar o CSS/JS compilado do tema.
- `config`: útil para ajustar marca, cores, rotas, navegação e mensagens.
- `stubs`: modelos opcionais de navegação e rotas de demo.
- `portal-ui-senhaunica-views`: opcional; copia apenas os overrides tematizados da SenhaUnica.
- `views`: use somente para customizações locais de layout, partials ou componentes.

Evite publicar `views` sem necessidade. Quando elas existem em `resources/views/vendor/portal-ui`, o Laravel usa a cópia local e deixa de acompanhar automaticamente as views do pacote.

## Uso Rápido

Layout autenticado:

```blade
@extends('portal-ui::layouts.app')

@section('title', 'Painel')

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

Layout visitante:

```blade
@extends('portal-ui::layouts.guest')

@section('title', 'Entrar')

@section('content')
    <x-portal::input label="E-mail" name="email" type="email" />
    <x-portal::input label="Senha" name="password" type="password" />
    <x-portal::button type="submit" full="true">Entrar</x-portal::button>
@endsection
```

Também existem componentes de layout baseados em classe:

```blade
<x-portal-app-layout title="Painel">
    <x-portal::card>
        Conteúdo principal.
    </x-portal::card>
</x-portal-app-layout>
```

## Configuração

O arquivo publicado é:

```text
config/portal-ui.php
```

Principais seções:

- `brand`: nome, subtítulo, logo e favicon.
- `colors`: tokens visuais principais.
- `layout`: comportamento geral do layout.
- `assets`: carregamento de CSS/JS.
- `navigation`: grupos e itens da sidebar.
- `routes`: nomes de rotas comuns, como `login`, `logout` e `home`.
- `integrations`: integrações opcionais com outros pacotes, como `uspdev/senhaunica-socialite`.
- `flash`: chaves de sessão usadas por mensagens.

## Integração SenhaUnica

Quando `portal-ui.integrations.senhaunica.enabled` está ativo, o pacote registra views tematizadas no namespace `senhaunica`.

A ordem de resolução fica:

1. `resources/views/vendor/senhaunica` do sistema consumidor.
2. `resources/views/integrations/senhaunica` do `portal-ui`.
3. Views originais registradas por `uspdev/senhaunica-socialite`.

Isso permite que os sistemas usem a lista de usuários, permissões, LoginAs, modais e login local da SenhaUnica já adaptados ao tema, sem copiar todas as views para cada aplicação.

Para desabilitar:

```env
PORTAL_UI_SENHAUNICA_VIEWS=false
```

## Navegação

A sidebar é montada por `config('portal-ui.navigation.groups')`. Se não houver item visível, a sidebar e o botão de menu não são renderizados.

Exemplo mínimo:

```php
'navigation' => [
    'hide_missing_routes' => true,
    'groups' => [
        'main' => [
            'label' => 'Menu Principal',
            'items' => [
                [
                    'label' => 'Início',
                    'route' => 'home',
                    'icon' => 'fa-home',
                    'active' => 'home',
                ],
            ],
        ],
    ],
],
```

Itens podem usar `route`, `url`, `icon`, `active`, `can`, `guest`, `children`, `external`, `target` e `rel`. Quando `url` for absoluta (`http`/`https`) ou `external` for `true`, o item abre em nova aba com `rel="noopener noreferrer"` por padrão.

Submenus usam `children` e o item pai fica visível quando tiver pelo menos um filho visível:

```php
[
    'label' => 'Cadastros',
    'icon' => 'fa-folder',
    'active' => 'cadastros.*',
    'children' => [
        [
            'label' => 'Unidades',
            'route' => 'unidades.index',
            'active' => 'unidades.*',
        ],
        [
            'label' => 'Manual',
            'url' => 'https://example.org/manual',
            'external' => true,
        ],
    ],
]
```

Menus mais completos estão em [docs/examples.md](docs/examples.md).

## Componentes

Componentes disponíveis com prefixo `x-portal::`:

- `alert`
- `badge`
- `button`
- `card`
- `confirm-modal`
- `empty-state`
- `flash-messages`
- `input`
- `modal`
- `page-header`
- `resource-actions`
- `section-footer`
- `section-header`
- `select`
- `flash-alert`
- `sidebar-item`
- `switch`
- `table-actions`
- `table`
- `textarea`

Exemplo:

```blade
<x-portal::alert variant="success" title="Sucesso">
    Operação realizada.
</x-portal::alert>

<x-portal::button type="submit" icon="fa-save">
    Salvar
</x-portal::button>

<x-portal::confirm-modal
    wire:model="showConfirmModal"
    title="Confirmar exclusao"
    message="Este registro sera excluido permanentemente."
    confirm-label="Excluir"
    confirm-variant="danger"
    confirm-icon="fa-trash"
    confirm-action="confirmarExclusao"
    cancel-action="fecharConfirmacaoExclusao"
/>

<x-portal::empty-state
    title="Nenhum registro encontrado"
    message="Assim que houver dados, eles aparecerao aqui."
    icon="fa-inbox"
/>

<x-portal::table>
    <x-slot:head>
        <tr>
            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nome</th>
        </tr>
    </x-slot:head>

    <x-slot:body>
        <tr>
            <td class="px-4 py-3 text-sm text-gray-600">Exemplo</td>
        </tr>
    </x-slot:body>
</x-portal::table>

<x-portal::section-footer align="center">
    <x-portal::button variant="secondary" icon="fa-plus">Adicionar item</x-portal::button>
</x-portal::section-footer>

<x-portal::resource-actions
    :view-click="'visualizar('.$item->id.')'"
    :edit-click="'editar('.$item->id.')'"
    :delete-click="'confirmarExclusao('.$item->id.')'"
    mode="icon"
/>

<x-portal::resource-actions
    :view-href="route('items.show', $item)"
    :edit-href="route('items.edit', $item)"
    only="view,edit"
    mode="label"
/>
```

## Assets

O pacote distribui:

- `public/portal-ui.css`
- `public/portal-ui.js`

O app consumidor deve publicar esses assets para `public/vendor/portal-ui`:

```bash
php artisan vendor:publish --tag=portal-ui-assets
```

Ao alterar CSS ou JavaScript do pacote, gere novamente os arquivos distribuíveis:

```bash
npm install
npm run build
```

## Extensão de CSS e JS

Os layouts expõem stacks para CSS, metas e scripts customizados:

- `portal-ui-head`: conteúdo extra no `<head>`.
- `portal-ui-before-scripts`: scripts antes do `portal-ui.js`.
- `portal-ui-after-scripts`: scripts depois do `portal-ui.js`.

Exemplo:

```blade
@push('portal-ui-head')
    <style>
        .painel-kpi {
            border-color: var(--portal-ui-primary);
        }
    </style>
@endpush

@push('portal-ui-after-scripts')
    <script>
        console.log('JS extra da tela');
    </script>
@endpush
```

## Livewire

Livewire não é dependência obrigatória. O pacote não registra componentes Livewire nem inclui `@livewireStyles` ou `@livewireScripts` no layout.

Apps consumidores que usam Livewire continuam responsáveis por carregar seus próprios assets.

Os componentes de formulário aceitam atributos `wire:*` normalmente, então o mesmo componente pode ser usado tanto com Blade tradicional quanto com Livewire:

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
    label="Observação"
    wire:model.blur="form.observacao"
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
```

O modal também aceita `wire:model` de forma opcional. Quando esse atributo está presente, o pacote renderiza apenas atributos Alpine compatíveis com o `$wire.entangle(...)` do próprio Livewire, sem usar a diretiva Blade `@entangle`. Em aplicações sem Livewire, basta continuar usando o modal com a prop `show`.

Exemplo mínimo com Livewire:

```php
<?php

namespace App\Livewire\Admin\Exemplo;

use Livewire\Component;

class ExemploModal extends Component
{
    public bool $showFormModal = false;
    public ?int $editingId = null;

    public function create(): void
    {
        $this->editingId = null;
        $this->showFormModal = true;
    }

    public function edit(int $id): void
    {
        $this->editingId = $id;
        $this->showFormModal = true;
    }

    public function closeModal(): void
    {
        $this->showFormModal = false;
    }
}
```

```blade
<x-portal::button click="create" icon="fa-plus">
    Novo registro
</x-portal::button>

<x-portal::modal
    wire:model="showFormModal"
    :title="$editingId ? 'Editar registro' : 'Novo registro'"
    :icon="$editingId ? 'fa-edit' : 'fa-plus'"
>
    Conteúdo do formulário aqui.

    <x-slot:footer>
        <x-portal::button
            variant="secondary"
            click="$set('showFormModal', false)"
        >
            Cancelar
        </x-portal::button>

        <x-portal::button click="save" icon="fa-save">
            Salvar
        </x-portal::button>
    </x-slot:footer>
</x-portal::modal>
```

Fluxo esperado:

- crie uma property booleana, como `showFormModal`, iniciando com `false`;
- abra o modal alterando essa property para `true`;
- ligue o componente com `wire:model="showFormModal"`;
- para título dinâmico, use `:title` com uma expressão Livewire/Blade;
- para fechar, use `$set('showFormModal', false)` ou um método do componente.

## Exemplos

Views de exemplo incluídas:

- `portal-ui::examples.minimal-showcase`
- `portal-ui::examples.simple-showcase`
- `portal-ui::examples.admin-crud-showcase`
- `portal-ui::examples.guest-showcase`

Rotas sugeridas:

```php
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function () {
    Route::view('/portal-ui-demo-minimal', 'portal-ui::examples.minimal-showcase')->name('portal-ui.demo.minimal');
    Route::view('/portal-ui-demo', 'portal-ui::examples.simple-showcase')->name('portal-ui.demo');
    Route::view('/portal-ui-demo-crud', 'portal-ui::examples.admin-crud-showcase')->name('portal-ui.demo.crud');
    Route::view('/portal-ui-demo-guest', 'portal-ui::examples.guest-showcase')->name('portal-ui.demo.guest');
});
```

Mais exemplos de layout, menus e componentes estão em [docs/examples.md](docs/examples.md).

## Desenvolvimento

Instale dependências e rode os testes dentro da pasta do pacote:

```bash
composer install
composer test
```

Para validar assets:

```bash
npm install
npm run build
```

## Versões

O pacote segue Versionamento Semântico (`MAJOR.MINOR.PATCH`):

- `PATCH`: correções compatíveis.
- `MINOR`: novos recursos compatíveis.
- `MAJOR`: mudanças incompatíveis em layouts, componentes, configuração ou assets.

Consulte [CHANGELOG.md](CHANGELOG.md) antes de atualizar consumidores. Republique `assets` sempre que houver mudanças visuais ou de JavaScript no tema.
