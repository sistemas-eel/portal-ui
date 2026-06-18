@props([
    'only' => null,
    'mode' => 'icon',
    'viewClick' => null,
    'editClick' => null,
    'deleteClick' => null,
    'viewHref' => null,
    'editHref' => null,
    'deleteHref' => null,
    'viewOnclick' => null,
    'editOnclick' => null,
    'deleteOnclick' => null,
    'viewLabel' => 'Ver',
    'editLabel' => 'Editar',
    'deleteLabel' => 'Excluir',
    'viewTitle' => 'Ver',
    'editTitle' => 'Editar',
    'deleteTitle' => 'Excluir',
    'viewVariant' => 'ghost',
    'editVariant' => 'outline',
    'deleteVariant' => 'danger',
    'viewIcon' => 'fa-eye',
    'editIcon' => 'fa-pen',
    'deleteIcon' => 'fa-trash',
    'size' => 'sm',
    'align' => 'right',
])

@php
    $normalizedOnly = is_array($only)
        ? array_values(array_filter($only))
        : array_values(array_filter(array_map('trim', explode(',', (string) $only))));

    $showView = empty($normalizedOnly) || in_array('view', $normalizedOnly, true);
    $showEdit = empty($normalizedOnly) || in_array('edit', $normalizedOnly, true);
    $showDelete = empty($normalizedOnly) || in_array('delete', $normalizedOnly, true);
    $showLabels = in_array($mode, ['label', 'inline'], true);
@endphp

<x-portal::table-actions :align="$align" {{ $attributes }}>
    @if($showView && ($viewClick || $viewHref || $viewOnclick))
        <x-portal::button
            :size="$size"
            :variant="$viewVariant"
            :icon="$viewIcon"
            :href="$viewHref"
            :click="$viewClick"
            :onclick="$viewOnclick"
            title="{{ $viewTitle }}"
        >
            @if($showLabels)
                {{ $viewLabel }}
            @endif
        </x-portal::button>
    @endif

    @if($showEdit && ($editClick || $editHref || $editOnclick))
        <x-portal::button
            :size="$size"
            :variant="$editVariant"
            :icon="$editIcon"
            :href="$editHref"
            :click="$editClick"
            :onclick="$editOnclick"
            title="{{ $editTitle }}"
        >
            @if($showLabels)
                {{ $editLabel }}
            @endif
        </x-portal::button>
    @endif

    @if($showDelete && ($deleteClick || $deleteHref || $deleteOnclick))
        <x-portal::button
            :size="$size"
            :variant="$deleteVariant"
            :icon="$deleteIcon"
            :href="$deleteHref"
            :click="$deleteClick"
            :onclick="$deleteOnclick"
            title="{{ $deleteTitle }}"
        >
            @if($showLabels)
                {{ $deleteLabel }}
            @endif
        </x-portal::button>
    @endif
</x-portal::table-actions>
