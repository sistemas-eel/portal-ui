<?php

return [
    'groups' => [
        'main' => [
            // Progressão 3/3: navegação com área principal e seção administrativa protegida.
            'label' => 'Principal',
            'items' => [
                [
                    'label' => 'Início',
                    'route' => 'dashboard',
                    'icon' => 'fa-home',
                    'active' => 'dashboard',
                ],
                [
                    'label' => 'Documentos',
                    'icon' => 'fa-folder-open',
                    'active' => 'documentos.*',
                    'children' => [
                        [
                            'label' => 'Todos os documentos',
                            'route' => 'documentos.index',
                            'icon' => 'fa-list',
                            'active' => 'documentos.index',
                        ],
                        [
                            'label' => 'Novo documento',
                            'route' => 'documentos.create',
                            'icon' => 'fa-plus',
                            'active' => 'documentos.create',
                        ],
                    ],
                ],
                [
                    'label' => 'Relatórios',
                    'route' => 'relatorios.index',
                    'icon' => 'fa-chart-bar',
                    'active' => 'relatorios.*',
                ],
            ],
        ],
        'admin' => [
            'label' => 'Administração',
            'items' => [
                [
                    'label' => 'Usuários',
                    'route' => 'admin.users.index',
                    'icon' => 'fa-users',
                    'active' => 'admin.users.*',
                    'can' => 'admin',
                ],
                [
                    'label' => 'Setores',
                    'route' => 'admin.setores.index',
                    'icon' => 'fa-sitemap',
                    'active' => 'admin.setores.*',
                    'can' => 'admin',
                ],
                [
                    'label' => 'Sistemas',
                    'route' => 'admin.sistemas.index',
                    'icon' => 'fa-desktop',
                    'active' => 'admin.sistemas.*',
                    'can' => 'admin',
                ],
                [
                    'label' => 'Configurações',
                    'route' => 'admin.settings.index',
                    'icon' => 'fa-cogs',
                    'active' => 'admin.settings.*',
                    'can' => 'admin',
                ],
                [
                    'label' => 'Documentação',
                    'url' => 'https://example.org/docs',
                    'icon' => 'fa-book-open',
                    'external' => true,
                    'can' => 'admin',
                ],
            ],
        ],
    ],
];
