<?php

return [
    'groups' => [
        'main' => [
            // Progressão 1/3: navegação mínima para apps com uma única área interna.
            'label' => 'Menu Principal',
            'items' => [
                [
                    'label' => 'Início',
                    'route' => 'dashboard',
                    'icon' => 'fa-home',
                    'active' => 'dashboard',
                ],
            ],
        ],
    ],
];
