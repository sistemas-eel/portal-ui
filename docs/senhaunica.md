# Integração SenhaÚnica

Este guia trata apenas da integração visual entre Portal UI e `uspdev/senhaunica-socialite`. Instalação, credenciais OAuth, migrations e model de usuário continuam seguindo a documentação do pacote de autenticação.

O Portal UI ativa essa integração automaticamente quando encontra `Uspdev\SenhaunicaSocialite\SenhaunicaServiceProvider`. Sem essa classe, nenhuma view é registrada no namespace `senhaunica`.

## 1. Usar o layout local

Considerando que existe `resources/views/layouts/app.blade.php`:

```blade
@extends('portal-ui::layouts.app')
```

adicione ao `.env`:

```env
PORTAL_UI_SENHAUNICA_VIEWS=true
PORTAL_UI_SENHAUNICA_LAYOUT=layouts.app
```

A primeira variável é opcional quando a biblioteca está instalada; ela aparece aqui para tornar a intenção explícita.

Depois limpe os caches:

```bash
php artisan optimize:clear
```

As páginas `senhaunica::users`, `senhaunica::loginas`, `senhaunica::unavailable` e `senhaunica::local.login` passarão pelo layout local.

A configuração `senhaunica.template` pertence às views originais da SenhaÚnica. As views fornecidas pelo Portal UI 0.2 usam `portal-ui.integrations.senhaunica.layout`.

## 2. Não publicar views sem necessidade

O Portal UI registra automaticamente suas views tematizadas. Não é necessário copiá-las para a aplicação.

Publique somente se houver uma customização que não possa ser feita por configuração:

```bash
php artisan vendor:publish --tag=portal-ui-senhaunica-views
```

Esse comando também pode ser executado antes da instalação da SenhaÚnica, pois apenas copia os arquivos. Eles só poderão ser renderizados depois que a dependência, suas rotas e o model de usuário estiverem configurados.

Arquivos em `resources/views/vendor/senhaunica` têm prioridade sobre o pacote. Depois de uma atualização, eles não recebem correções automaticamente.

## 3. Entender o acesso administrativo

As ações abaixo chamam `$this->authorize('admin')` no `uspdev/senhaunica-socialite`:

- lista de usuários em `/senhaunica-users`;
- formulário e execução de LoginAs;
- criação e edição de usuários locais;
- edição de permissões.

O Portal UI não define nem libera o gate `admin`. Antes de investigar a view, confirme que o usuário autenticado passa por essa autorização.

Verifique:

- se o model usa os traits de autorização esperados;
- se permissões e roles foram sincronizadas no login;
- se o `guard_name` do Spatie Permission coincide com o guard utilizado;
- se a configuração `SENHAUNICA_ADMINS` está sendo aplicada pelo fluxo de autenticação;
- se o gate `admin` existe e retorna `true` para o administrador.

## 4. Adaptar uma fonte externa de administradores

Quando outro cliente SSO autentica o usuário, mas não cria a permissão hierárquica esperada pela SenhaÚnica, a aplicação pode precisar de uma ponte explícita. Essa regra pertence ao sistema consumidor, normalmente em `app/Providers/AppServiceProvider.php`:

```php
<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::before(static function (User $user, string $ability): ?bool {
            if ($ability !== 'admin' || empty($user->codpes)) {
                return null;
            }

            $admins = array_map(
                static fn ($codpes): string => trim((string) $codpes),
                (array) config('senhaunica.admins', []),
            );

            return in_array((string) $user->codpes, $admins, true)
                ? true
                : null;
        });
    }
}
```

Retornar `null` quando a regra não se aplica é importante: isso permite que os gates normais continuem avaliando o usuário. Nunca use um `Gate::before` que conceda acesso administrativo indiscriminadamente.

Depois de alterar providers, configuração ou `.env`:

```bash
php artisan optimize:clear
php artisan route:list
php artisan portal-ui:doctor
```

Um 403 significa que a rota e o controller foram encontrados, mas a autorização foi negada. Consulte também [Solução de problemas](troubleshooting.md).
