# Changelog

Todas as alterações relevantes deste pacote serão registradas neste arquivo.

O formato segue [Keep a Changelog](https://keepachangelog.com/pt-BR/1.1.0/) e o versionamento segue [Versionamento Semântico](https://semver.org/lang/pt-BR/).

## [Não lançado]

### Adicionado

- Suporte a parâmetros de rotas nos itens de breadcrumb do componente `x-portal::page-header`.

### Corrigido

- O auto-dismiss dos alertas agora pausa o temporizador e a barra de progresso enquanto o ponteiro permanece sobre a mensagem.

## [0.2.1] - 2026-09-02

### Corrigido

- Resultados do seletor de pessoas da SenhaÚnica que eram recortados pelo painel do modal.

## [0.2.0] - 2026-09-01

### Adicionado

- Diagnóstico de instalação com `php artisan portal-ui:doctor`.
- Instalador idempotente `portal-ui:install`, com scaffold opcional `--starter`.
- Configuração explícita do layout usado pela integração SenhaÚnica.
- Componente `x-portal::icon` com normalização de estilo.
- Publicação dos assets também pela tag Laravel `laravel-assets`.
- Documentação do limite entre apresentação e autorização nas rotas da SenhaÚnica.
- Lockfile do build frontend para instalações reproduzíveis e dependências auditadas.
- Tutoriais passo a passo para primeira tela, autenticação, integração SenhaÚnica e solução de problemas.
- Matriz de testes no GitHub Actions para PHP 7.4 a 8.4 e Laravel 8 a 13.

### Alterado

- A integração SenhaÚnica passa a usar o layout configurado pelo Portal UI.
- Font Awesome passa a ser fornecido localmente; o CDN permanece opcional.
- Interações da SenhaÚnica passam a usar o JavaScript nativo do pacote em vez de exigir Alpine.

### Corrigido

- Layout inexistente nas páginas LoginAs e de indisponibilidade.
- Modais da gestão de usuários que não abriam sem Alpine.
- Risco de assets publicados permanecerem desatualizados após atualizações Composer.
- Registro prematuro das views SenhaÚnica quando a dependência opcional não está instalada.
- Tipo de retorno exclusivo do PHP 8 em um fixture de autenticação da suíte de testes.

## [0.1.0] - 2026-06-18

### Adicionado

- Estrutura base do pacote `sistemas-eel/portal-ui` para layouts e componentes Blade reutilizáveis.
- Layouts `portal-ui::layouts.app` e `portal-ui::layouts.guest`.
- Componentes visuais base, exemplos de uso, stubs de navegação e assets compilados publicáveis.
- Integração opcional com `uspdev/senhaunica-socialite`, registrando views tematizadas no namespace `senhaunica`.
- Tag `portal-ui-senhaunica-views` para publicar apenas os overrides da SenhaUnica no app consumidor.
- Configuração `portal-ui.integrations.senhaunica.enabled` e variável `PORTAL_UI_SENHAUNICA_VIEWS`.
- Suíte de testes com Orchestra Testbench para renderização, configuração e publicação.

### Observações

- Esta é a linha inicial do changelog para a primeira disponibilização do pacote.
- Ajustes incrementais e mudanças futuras devem ser registrados a partir das próximas versões.
