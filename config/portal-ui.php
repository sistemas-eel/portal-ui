<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Marca
    |--------------------------------------------------------------------------
    |
    | Valores genéricos usados pelos layouts do pacote. Cada sistema consumidor
    | pode publicar este arquivo e sobrescrever nome, subtítulo, logo e favicon.
    |
    */

    'brand' => [
        'name' => env('PORTAL_UI_BRAND_NAME', config('app.name', 'Sistema')),
        'subtitle' => env('PORTAL_UI_BRAND_SUBTITLE', ''),
        'logo' => env('PORTAL_UI_LOGO'),
        'logo_alt' => env('PORTAL_UI_LOGO_ALT', env('PORTAL_UI_BRAND_NAME', config('app.name', 'Sistema'))),
        'favicon' => env('PORTAL_UI_FAVICON', 'favicon.ico'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Tokens Visuais
    |--------------------------------------------------------------------------
    |
    | O CSS compilado do pacote deve consumir estes valores como referência de
    | customização. Tailwind pode ser usado no build do pacote, mas não deve ser
    | obrigatório para o app consumidor.
    |
    */

    'colors' => [
        'primary' => env('PORTAL_UI_PRIMARY', '#0b7c93'),
        'primary_dark' => env('PORTAL_UI_PRIMARY_DARK', '#095f71'),
        'primary_soft' => env('PORTAL_UI_PRIMARY_SOFT', '#0d94a8'),
    ],

    'layout' => [
        'sidebar_collapsible' => true,
        'body_class' => 'bg-gray-50',
    ],

    /*
    |--------------------------------------------------------------------------
    | Assets
    |--------------------------------------------------------------------------
    |
    | O modo padrão é carregar assets publicados em public/vendor/portal-ui.
    | Apps com Vite/Tailwind podem optar por integrar os fontes do pacote.
    |
    */

    'assets' => [
        'mode' => env('PORTAL_UI_ASSET_MODE', 'published'),
        'load_css' => true,
        'load_js' => true,
        'css_path' => 'vendor/portal-ui/portal-ui.css',
        'js_path' => 'vendor/portal-ui/portal-ui.js',
        'fontawesome_cdn' => true,
        'fontawesome_cdn_url' => 'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@7.2.0/css/all.min.css',
    ],

    /*
    |--------------------------------------------------------------------------
    | Navegação
    |--------------------------------------------------------------------------
    |
    | O pacote não fornece menus de domínio. O consumidor pode configurar itens
    | com route, url, icon, active e can, ou substituir a navegação por slots.
    |
    */

    'navigation' => [
        'groups' => [],
        'hide_missing_routes' => true,
    ],

    'routes' => [
        'login' => 'login',
        'logout' => 'logout',
        'home' => 'home',
    ],

    /*
    |--------------------------------------------------------------------------
    | Integrações
    |--------------------------------------------------------------------------
    |
    | Integrações opcionais com pacotes comuns nos sistemas consumidores. Quando
    | habilitada, a integração SenhaUnica registra views tematizadas no namespace
    | "senhaunica", mantendo prioridade para overrides locais do consumidor.
    |
    */

    'integrations' => [
        'senhaunica' => [
            'enabled' => env('PORTAL_UI_SENHAUNICA_VIEWS', true),
        ],
    ],

    'flash' => [
        'success_keys' => ['success', 'message'],
        'error_keys' => ['error', 'danger'],
        'warning_keys' => ['warning'],
        'info_keys' => ['info', 'status'],
        'persistent_keys' => [
            'success' => ['message_persistent'],
            'error' => ['error_persistent'],
            'warning' => ['warning_persistent'],
        ],
        'title_keys' => [
            'success' => ['success_title', 'message_title'],
            'error' => ['error_title'],
            'warning' => ['warning_title'],
            'info' => ['info_title'],
        ],
        'id_keys' => [
            'success' => [
                'success' => 'success_id',
                'message' => 'message_id',
            ],
            'error' => [
                'error' => 'error_id',
                'danger' => 'danger_id',
            ],
            'warning' => [
                'warning' => 'warning_id',
            ],
            'info' => [
                'info' => 'info_id',
                'status' => 'status_id',
            ],
        ],
    ],
];
