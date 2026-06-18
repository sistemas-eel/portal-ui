<?php

return [
    'groups' => [
        'main' => [
            // Progressão 2/3: navegação simples para apps com poucas seções principais.
            'label' => 'Menu Principal',
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
                [
                    'label' => 'Ajuda',
                    'url' => 'https://example.org/ajuda',
                    'icon' => 'fa-circle-question',
                    'external' => true,
                ],
            ],
        ],
    ],
];
