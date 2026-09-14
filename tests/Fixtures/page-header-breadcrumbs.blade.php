<x-portal::page-header
    title="Configuração da versão"
    :breadcrumbs="[
        [
            'label' => 'Tipo de chamado',
            'route' => 'tipos.show',
            'parameters' => ['tipo' => 42],
        ],
        [
            'label' => 'Versão 3',
            'route' => 'tipos.versoes.show',
            'parameters' => [
                'tipo' => 42,
                'versao' => 3,
            ],
        ],
        ['label' => 'Configuração'],
    ]"
/>
