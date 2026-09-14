# Solução de problemas

Comece sempre com:

```bash
php artisan optimize:clear
php artisan portal-ui:doctor
```

O diagnóstico verifica versão, layout configurado, CSS, JavaScript e fontes publicadas.

| Sintoma | Causa provável | Como corrigir |
|---|---|---|
| Ícones aparecem como quadrados ou não aparecem | Fontes não publicadas ou CSS antigo | Execute `php artisan vendor:publish --tag=portal-ui-assets --force` e rode o diagnóstico novamente. |
| CSS carrega, mas as fontes retornam 404 | Assets de uma versão anterior ou caminho de publicação incorreto | Confirme que CSS e `fa-*.woff2` estão juntos em `public/vendor/portal-ui`. |
| `View [layouts.app] not found` | O layout local não foi criado | Crie `resources/views/layouts/app.blade.php` estendendo `portal-ui::layouts.app`. |
| `View [layouts.portal-app] not found` | Uma view local antiga ainda usa o nome removido | Troque por `layouts.app` ou `portal-ui::layouts.app` e confira `resources/views/vendor/senhaunica`. |
| Menu lateral não aparece | Navegação vazia ou todas as rotas estão ausentes | Confira `portal-ui.navigation.groups`, `hide_missing_routes` e `php artisan route:list`. |
| Item do menu não fica selecionado | O padrão `active` não corresponde à rota atual | Use o nome exato ou um padrão como `usuarios.*`. |
| Botão ou modal não abre | `portal-ui.js` publicado está desatualizado | Republique `portal-ui-assets` com `--force` e limpe caches. |
| `portal-ui:install --starter` não alterou meu layout ou configuração | O arquivo já existia e foi preservado | Revise o arquivo existente. Use `--force` somente se realmente quiser substituí-lo pelo stub. |
| O starter abre, mas não aparece no menu configurado | Uma configuração existente foi preservada | Adicione a rota `portal-ui.starter` à sua navegação ou mantenha o item de fallback da view inicial. |
| O diagnóstico informa que a integração SenhaÚnica foi solicitada, mas está ausente | `PORTAL_UI_SENHAUNICA_VIEWS=true` foi configurado sem instalar a dependência | Instale `uspdev/senhaunica-socialite` ou remova a variável/defina-a como `false`. |
| `/senhaunica-users` ou `/loginas` retorna 403 | O usuário não passa pelo gate `admin` | Verifique permissões, roles, guards e a regra de integração descrita no guia SenhaÚnica. |
| Uma correção do pacote não aparece | Existe override local antigo | Confira `resources/views/vendor/portal-ui` e `resources/views/vendor/senhaunica`. Compare ou remova apenas os overrides que não são mais necessários. |
| `Target class [...CheckSSOSession] does not exist` | Middleware aponta para uma classe ausente ou para uma versão diferente do cliente SSO | Corrija o alias/FQCN conforme a versão instalada e execute `composer dump-autoload` e `php artisan optimize:clear`. Esse erro não é causado pelo layout. |
| Uma classe Tailwind usada pela aplicação não produz efeito | O CSS publicado contém apenas as classes utilizadas pelo Portal UI, ou o CSS compilado da aplicação não está carregado | No layout intermediário, adicione `@vite('resources/css/app.css')` ao stack `styles` e execute `npm run build`. |

## Verificações manuais

### Rotas

```bash
php artisan route:list
```

Os valores de `portal-ui.routes` e os campos `route` da navegação precisam aparecer na coluna de nomes.

O instalador adiciona a seguinte inclusão marcada ao final de `routes/web.php`:

```php
// Portal UI starter — arquivo gerenciado
require __DIR__.'/portal-ui-starter.php';
```

Executar o instalador novamente não duplica esse bloco.

### Assets

O diretório publicado deve conter pelo menos:

```text
public/vendor/portal-ui/portal-ui.css
public/vendor/portal-ui/portal-ui.js
public/vendor/portal-ui/fa-solid-900.woff2
public/vendor/portal-ui/fa-regular-400.woff2
public/vendor/portal-ui/fa-brands-400.woff2
```

### Views sobrescritas

Para localizar possíveis cópias antigas:

```bash
find resources/views/vendor/portal-ui -type f
find resources/views/vendor/senhaunica -type f
```

Não apague customizações sem antes comparar seu conteúdo. Views locais sempre têm prioridade e podem ter regras específicas do sistema.

### Navegador

Abra as ferramentas de desenvolvimento e confira a aba de rede. Requisições para CSS, JavaScript e fontes devem retornar HTTP 200. Depois de republicar assets, faça uma recarga sem cache.
