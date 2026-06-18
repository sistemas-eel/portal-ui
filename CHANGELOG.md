# Changelog

Todas as alterações relevantes deste pacote serão registradas neste arquivo.

O formato segue [Keep a Changelog](https://keepachangelog.com/pt-BR/1.1.0/) e o versionamento segue [Versionamento Semântico](https://semver.org/lang/pt-BR/).

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
