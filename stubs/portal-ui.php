<?php

return [
    'brand' => [
        'name' => env('PORTAL_UI_BRAND_NAME', config('app.name', 'Sistema')),
        'subtitle' => env('PORTAL_UI_BRAND_SUBTITLE', ''),
        'logo' => env('PORTAL_UI_LOGO'),
        'favicon' => env('PORTAL_UI_FAVICON', 'favicon.ico'),
    ],

    'navigation' => [
        // Copie um dos arquivos em stubs/portal-ui/navigation/
        // para montar a estrutura inicial do menu da aplicação.
        'groups' => [],
    ],
];
