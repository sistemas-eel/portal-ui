# Primeira tela com Portal UI

Este tutorial parte de um projeto Laravel novo e termina com uma página funcionando no navegador. Não é necessário instalar Tailwind, Vite, Alpine ou Livewire no sistema consumidor.

Ao final, a aplicação terá:

- topbar com alternância de tema;
- menu lateral com um link para a página inicial;
- CSS, JavaScript e Font Awesome locais;
- uma view preparada para receber autenticação posteriormente.

> Esta primeira página usa o visual de área interna, mas ainda não exige login. O Portal UI fornece apresentação; a autenticação é instalada e configurada separadamente.

## 1. Criar ou abrir o projeto Laravel

Para começar do zero:

```bash
composer create-project laravel/laravel meu-sistema
cd meu-sistema
```

Se o projeto já existe, execute os próximos comandos na pasta que contém o arquivo `artisan`.

## 2. Instalar o Portal UI

```bash
composer require sistemas-eel/portal-ui:^0.2
```

O Laravel normalmente descobre o provider do pacote durante esse comando. Somente em projetos que desabilitaram o package discovery será necessário executar:

```bash
php artisan package:discover
```

## Caminho automático recomendado

Para gerar imediatamente uma tela segura de demonstração:

```bash
php artisan portal-ui:install --starter
php artisan serve
```

Abra:

```text
http://127.0.0.1:8000/portal-ui-starter
```

O instalador:

- publica `config/portal-ui.php` e todos os assets locais;
- cria `resources/views/layouts/app.blade.php`;
- cria `resources/views/portal-ui-starter.blade.php`;
- cria `routes/portal-ui-starter.php`;
- adiciona uma única inclusão marcada em `routes/web.php`;
- configura o item “Início” quando a configuração é nova;
- limpa os caches da aplicação.

Arquivos existentes são preservados. Se uma configuração anterior for encontrada, o comando mantém seu conteúdo e a página usa a navegação já configurada; quando não houver nenhum grupo, a própria view mostra um item inicial de fallback.

A opção abaixo sobrescreve a configuração e os arquivos gerenciados pelo starter:

```bash
php artisan portal-ui:install --starter --force
```

Use `--force` apenas depois de revisar customizações. O comando não publica views internas do pacote, não instala autenticação e não concede o gate `admin`.

Se você utilizou o caminho automático, a instalação já está funcional. Continue nas próximas seções para entender e substituir o starter pela página real do sistema. Para fazer tudo manualmente, siga diretamente a seção 3.

## 3. Publicar configuração e assets

```bash
php artisan vendor:publish --tag=portal-ui-config
php artisan vendor:publish --tag=portal-ui-assets
```

Esses dois comandos também podem ser executados de uma vez, sem criar o starter:

```bash
php artisan portal-ui:install
```

Esses comandos criam:

```text
config/portal-ui.php
public/vendor/portal-ui/portal-ui.css
public/vendor/portal-ui/portal-ui.js
public/vendor/portal-ui/fa-*.woff2
```

Não publique `portal-ui-views` neste tutorial. O layout deve continuar vindo do pacote para receber correções nas próximas atualizações.

## 4. Criar o layout local

Crie a pasta `resources/views/layouts`, caso ainda não exista, e depois crie `resources/views/layouts/app.blade.php` com exatamente este conteúdo:

```blade
@extends('portal-ui::layouts.app')
```

Esse pequeno arquivo é uma ponte entre as páginas do sistema e o pacote:

```text
resources/views/home.blade.php
└── resources/views/layouts/app.blade.php
    └── portal-ui::layouts.app
        ├── head e assets
        ├── topbar
        ├── sidebar
        └── conteúdo da página
```

Não copie o HTML de `portal-ui::layouts.app`. A extensão de uma linha é suficiente.

### Aplicações que usam Vite e Tailwind

O CSS publicado pelo Portal UI contém somente as classes utilizadas nas views do próprio pacote. Se a aplicação consumidora usar classes Tailwind diretamente em suas páginas, ela também deverá carregar seu CSS compilado.

Nesse caso, use o stack `styles` no layout intermediário:

```blade
@extends('portal-ui::layouts.app')

@push('styles')
    @vite('resources/css/app.css')
@endpush
```

O CSS publicado do Portal UI continuará fornecendo os estilos dos componentes, enquanto `resources/css/app.css` fornecerá as classes encontradas nas views da aplicação.

Depois de adicionar ou alterar classes Tailwind, gere novamente os assets da aplicação:

```bash
npm run build
```

Executar o build sem carregar `resources/css/app.css` no layout não é suficiente, pois o navegador não receberá o arquivo compilado.

## 5. Criar a página inicial

Crie `resources/views/home.blade.php`:

```blade
@extends('layouts.app')

@section('title', 'Página inicial')

@section('content')
    <x-portal::page-header
        title="Meu primeiro sistema"
        subtitle="O Portal UI está funcionando corretamente."
    />

    <x-portal::card>
        <div class="space-y-3">
            <p>Esta é a primeira página da aplicação usando o Portal UI.</p>

            <div class="flex items-center gap-2 text-portal">
                <x-portal::icon name="fa-circle-check" />
                <span>Layout, estilos, JavaScript e ícones carregados.</span>
            </div>
        </div>
    </x-portal::card>
@endsection
```

## 6. Disponibilizar a página em uma rota

Abra `routes/web.php`. Em um projeto Laravel novo, substitua a rota inicial existente por:

```php
<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');
```

`home` após `name()` é o nome da rota. A configuração do menu usará esse nome, não o endereço `/`.

## 7. Configurar marca, menu e nomes de rotas

Abra `config/portal-ui.php` e localize os blocos abaixo. Ajuste-os para que fiquem assim:

```php
'brand' => [
    'name' => 'Meu Sistema',
    'subtitle' => 'Sistema administrativo',
    'logo' => null,
    'logo_alt' => 'Meu Sistema',
    'favicon' => 'favicon.ico',
],

'navigation' => [
    'hide_missing_routes' => true,
    'groups' => [
        'main' => [
            'label' => 'Menu Principal',
            'items' => [
                [
                    'label' => 'Início',
                    'route' => 'home',
                    'icon' => 'fa-house',
                    'active' => 'home',
                ],
            ],
        ],
    ],
],

'routes' => [
    'login' => 'login',
    'logout' => 'logout',
    'home' => 'home',
],
```

Cada campo tem uma função:

| Campo | O que significa |
|---|---|
| `label` | Texto mostrado ao usuário. |
| `route` | Nome definido por `->name(...)` no arquivo de rotas. |
| `icon` | Classe de um ícone Font Awesome. |
| `active` | Padrão usado para destacar o item atual. Aceita curingas como `admin.*`. |
| `hide_missing_routes` | Esconde itens cujo nome de rota ainda não existe. |

As configurações `login` e `logout` apenas informam nomes de rotas. Elas não criam autenticação. Enquanto essas rotas não existirem, seus respectivos links não serão mostrados.

O Portal UI detecta automaticamente se `uspdev/senhaunica-socialite` está instalado. Em um projeto sem essa dependência, a integração fica inativa e não exige configuração adicional. Use `PORTAL_UI_SENHAUNICA_VIEWS=false` apenas quando quiser desativá-la explicitamente mesmo com a biblioteca instalada.

## 8. Limpar caches e verificar a instalação

```bash
php artisan optimize:clear
php artisan portal-ui:doctor
php artisan serve
```

Abra no navegador:

```text
http://127.0.0.1:8000
```

O resultado esperado é:

- título “Meu primeiro sistema”;
- menu lateral com o item “Início”;
- card com um ícone de confirmação;
- botão de modo escuro no canto superior;
- nenhuma requisição 404 para `portal-ui.css`, `portal-ui.js` ou arquivos `fa-*.woff2`.

## 9. Próximos passos

- Para exigir login, siga [Autenticação](authentication.md).
- Para usar as telas administrativas da SenhaÚnica, siga [Integração SenhaÚnica](senhaunica.md).
- Se algo não aparecer como descrito, consulte [Solução de problemas](troubleshooting.md).
