# PortalUi

Biblioteca de UI Laravel reutilizável para sistemas administrativos. O pacote é `Blade-first`: entrega layouts, componentes, configuração e assets compilados sem exigir Livewire, Alpine, Tailwind ou Vite no app consumidor.

Versão atual: `v0.2.1`.

## Comece aqui

Se esta é sua primeira instalação, siga o tutorial [Primeira tela com Portal UI](docs/getting-started.md). Ele começa em um projeto Laravel novo e termina com uma página acessível no navegador, contendo layout, topbar, menu, card, ícones e modo escuro.

O caminho automático recomendado é:

```bash
composer require sistemas-eel/portal-ui:^0.2
php artisan portal-ui:install --starter
php artisan serve
```

Depois, abra `/portal-ui-starter`. O instalador preserva arquivos existentes por padrão.

Guias complementares:

- [Autenticação](docs/authentication.md): como transformar a primeira tela em uma área protegida.
- [Integração SenhaÚnica](docs/senhaunica.md): layout, rotas administrativas, LoginAs e gate `admin`.
- [Solução de problemas](docs/troubleshooting.md): diagnóstico por sintoma.
- [Exemplos de componentes](docs/examples.md): navegação e telas mais completas.

## Requisitos

```json
{
  "php": "^7.4 || ^8.0",
  "illuminate/support": "^8.0 || ^9.0 || ^10.0 || ^11.0 || ^12.0 || ^13.0",
  "illuminate/view": "^8.0 || ^9.0 || ^10.0 || ^11.0 || ^12.0 || ^13.0"
}
```

Essas combinações são verificadas continuamente no GitHub Actions com uma matriz que cobre PHP 7.4 a 8.4 e Laravel 8 a 13, usando a geração correspondente do Orchestra Testbench.

## Instalação

Instale o pacote no app consumidor com Composer:

```bash
composer require sistemas-eel/portal-ui
```

O Laravel executa o package discovery automaticamente durante a instalação. Se a descoberta automática estiver desabilitada no projeto, execute manualmente:

```bash
php artisan package:discover
```

Se quiser fixar uma faixa de versão, use `sistemas-eel/portal-ui:^0.2`.

## Publicação

O instalador publica configuração e assets automaticamente:

```bash
php artisan portal-ui:install
```

Para também gerar uma primeira tela funcional:

```bash
php artisan portal-ui:install --starter
```

Os comandos de publicação equivalentes são:

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

O instalador não sobrescreve configuração, layout, view ou rota do starter já existentes. A opção `--force` permite a sobrescrita explícita dos arquivos gerenciados e da configuração; use-a somente depois de revisar customizações locais. A inclusão criada em `routes/web.php` é marcada e nunca é duplicada.

### Atualização para 0.2

Depois de atualizar o pacote, republique os assets e limpe os caches:

```bash
composer require sistemas-eel/portal-ui:^0.2 -W
php artisan vendor:publish --tag=portal-ui-assets --force
php artisan optimize:clear
php artisan portal-ui:doctor
```

O `--force` é necessário porque o Composer atualiza `vendor`, mas não substitui automaticamente arquivos que já estão em `public/vendor/portal-ui`. Se o sistema publicou views da SenhaÚnica, compare suas customizações antes de republicá-las; essas cópias locais têm prioridade sobre as correções do pacote.

## Uso Rápido

Para uma instalação nova, prefira o [tutorial completo da primeira tela](docs/getting-started.md). Os exemplos abaixo servem como referência rápida para projetos que já possuem rotas e layouts organizados.

Layout de área interna, normalmente usado depois da autenticação:

```blade
@extends('portal-ui::layouts.app')

@section('title', 'Painel')

@section('content')
    <x-portal::page-header
        title="Painel"
        subtitle="Resumo da operação"
    />

    <x-portal::card>
        <x-slot name="header">
            Indicadores
        </x-slot>

        Conteúdo principal da página.
    </x-portal::card>
@endsection
```

Se o sistema já padroniza suas páginas com `@extends('layouts.app')`, crie um layout intermediário mínimo:

```blade
{{-- resources/views/layouts/app.blade.php --}}
@extends('portal-ui::layouts.app')
```

As páginas da aplicação continuam simples:

```blade
@extends('layouts.app')

@section('title', 'Teste autenticado')

@section('content')
    <x-portal::card>Usuário autenticado: {{ auth()->user()->name }}</x-portal::card>
@endsection
```

Não copie o conteúdo interno de `portal-ui::layouts.app` para esse arquivo. O layout do pacote já monta o `<head>`, topbar, sidebar, stacks e referências ao CSS/JS; manter apenas a extensão evita que o consumidor fique preso a uma versão antiga.

Se a aplicação utiliza classes Tailwind próprias, carregue também o CSS compilado pelo Vite:

```blade
@extends('portal-ui::layouts.app')

@push('styles')
    @vite('resources/css/app.css')
@endpush
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

O Portal UI detecta `uspdev/senhaunica-socialite` automaticamente. Quando o service provider da dependência está disponível e `portal-ui.integrations.senhaunica.enabled` está ativo, o pacote registra views tematizadas no namespace `senhaunica`.

Sem a biblioteca instalada, a integração permanece inativa e não registra um namespace incompleto. Ainda é seguro executar `vendor:publish --tag=portal-ui-senhaunica-views`: a publicação apenas copia arquivos e não os renderiza.

A ordem de resolução fica:

1. `resources/views/vendor/senhaunica` do sistema consumidor.
2. `resources/views/integrations/senhaunica` do `portal-ui`.
3. Views originais registradas por `uspdev/senhaunica-socialite`.

Isso permite que os sistemas usem a lista de usuários, permissões, LoginAs, modais e login local da SenhaUnica já adaptados ao tema, sem copiar todas as views para cada aplicação.

Para desabilitar:

```env
PORTAL_UI_SENHAUNICA_VIEWS=false
```

Para forçar a ativação, use `PORTAL_UI_SENHAUNICA_VIEWS=true`, mas a dependência ainda precisa estar instalada. Se ela estiver ausente, `portal-ui:doctor` apresentará um aviso e as views não serão registradas.

Por padrão, as páginas da integração estendem diretamente `portal-ui::layouts.app`. Para fazê-las passar pelo layout intermediário do sistema:

```env
PORTAL_UI_SENHAUNICA_LAYOUT=layouts.app
```

O layout escolhido precisa expor a seção `content`. A configuração antiga `senhaunica.template` não controla as views fornecidas pelo Portal UI 0.2.

### Autorização e erro 403

O Portal UI apenas substitui a apresentação das telas; ele não concede acesso administrativo. O `uspdev/senhaunica-socialite` chama `$this->authorize('admin')` em `/senhaunica-users` e `/loginas`, portanto o usuário autenticado precisa ser reconhecido pelo gate `admin` no app consumidor.

Se a autenticação vem de outro cliente SSO, valide especialmente:

- se o model autenticado possui as permissões/roles esperadas;
- se o `guard_name` usado pelo Spatie Permission coincide com o guard da sessão;
- se a lista `SENHAUNICA_ADMINS` foi convertida em permissão no fluxo de login;
- se `Gate::allows('admin')` retorna `true` para o usuário que deveria administrar.

Quando for necessário adaptar uma fonte externa de administradores ao gate exigido pela SenhaÚnica, essa ponte deve ficar no `AppServiceProvider` (ou em um provider de autorização) da aplicação. Ela não deve ficar no Portal UI: uma biblioteca visual não conhece a política de segurança nem pode tornar usuários administradores por conta própria.

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
- `icon`
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

<x-portal::icon name="fa-user" />
<x-portal::icon name="fa-brands fa-github" label="GitHub" />

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
    <x-slot name="head">
        <tr>
            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nome</th>
        </tr>
    </x-slot>

    <x-slot name="body">
        <tr>
            <td class="px-4 py-3 text-sm text-gray-600">Exemplo</td>
        </tr>
    </x-slot>
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
- fontes locais do Font Awesome (`public/fa-*.woff2`)

O app consumidor deve publicar esses assets para `public/vendor/portal-ui`:

```bash
php artisan vendor:publish --tag=portal-ui-assets
```

O CSS da versão 0.2 já inclui o Font Awesome e referencia as fontes publicadas no mesmo diretório. Assim, os ícones funcionam sem CDN. O carregamento externo permanece disponível apenas como fallback explícito:

```env
PORTAL_UI_FONTAWESOME_CDN=true
```

Se os ícones não aparecerem depois de uma atualização, execute a publicação com `--force` e rode `php artisan portal-ui:doctor`. O diagnóstico também detecta arquivos de fonte ausentes, layout SenhaÚnica inexistente e CSS/JS defasados.

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

    <x-slot name="footer">
        <x-portal::button
            variant="secondary"
            click="$set('showFormModal', false)"
        >
            Cancelar
        </x-portal::button>

        <x-portal::button click="save" icon="fa-save">
            Salvar
        </x-portal::button>
    </x-slot>
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

### Como usar os stubs de exemplos e rotas no app consumidor

Após publicar os stubs com `php artisan vendor:publish --tag=portal-ui-stubs`, você pode carregar as rotas de demonstração diretamente no arquivo `routes/web.php` do seu projeto:

```php
if (file_exists(base_path('stubs/portal-ui/routes/demo.php'))) {
    require base_path('stubs/portal-ui/routes/demo.php');
}
```

Isso registrará as rotas automaticamente (como `/portal-ui-demo`).

Para configurar a barra lateral (sidebar) com um dos modelos de menu incluídos em `stubs/portal-ui/navigation/`:
1. Escolha um dos arquivos (ex: `simple.php` ou `admin.php`).
2. Copie a estrutura de grupos contida no `return` dele.
3. Cole no array `'groups'` dentro do arquivo `config/portal-ui.php` publicado no seu projeto.

Como alternativa manual, você pode registrar as rotas sugeridas diretamente:

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
