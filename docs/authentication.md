# Autenticação

O Portal UI não cria usuários, sessões, login ou políticas de acesso. Ele apresenta o usuário retornado por `auth()->user()` e usa nomes de rotas configurados pelo sistema consumidor.

Antes de proteger a primeira tela, confirme que sua solução de autenticação fornece:

- uma rota de login;
- uma rota `POST` de logout;
- o middleware Laravel `auth` funcionando;
- um usuário com pelo menos `name` ou `email`.

## 1. Informar os nomes das rotas

Em `config/portal-ui.php`:

```php
'routes' => [
    'login' => 'login',
    'logout' => 'logout',
    'home' => 'home',
],
```

Os valores são nomes de rotas. Para conferi-los:

```bash
php artisan route:list
```

Se seu pacote de autenticação usar outros nomes, altere apenas os valores:

```php
'routes' => [
    'login' => 'sso.login',
    'logout' => 'sso.logout',
    'home' => 'home',
],
```

## 2. Proteger a página

Depois que o login estiver funcionando, altere `routes/web.php`:

```php
<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'home')
    ->middleware('auth')
    ->name('home');
```

Agora visitantes devem ser enviados ao login, e usuários autenticados verão nome, avatar e ação de logout na topbar.

## 3. Usar o usuário na view

Como a rota está protegida, `resources/views/home.blade.php` pode acessar o usuário:

```blade
@extends('layouts.app')

@section('title', 'Página inicial')

@section('content')
    <x-portal::page-header
        title="Olá, {{ auth()->user()->name }}"
        subtitle="Você está em uma área autenticada."
    />

    <x-portal::card>
        Login realizado como {{ auth()->user()->email }}.
    </x-portal::card>
@endsection
```

## Autenticação não é autorização

O middleware `auth` responde “quem está conectado?”. Gates, policies e permissões respondem “o que essa pessoa pode fazer?”. Uma página pode estar autenticada e ainda retornar 403 caso exija uma habilidade que o usuário não possui.

Para a autorização administrativa do `uspdev/senhaunica-socialite`, continue em [Integração SenhaÚnica](senhaunica.md).
