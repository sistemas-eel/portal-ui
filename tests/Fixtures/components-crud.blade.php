<x-portal::confirm-modal
    title="Confirmar remoção"
    message="O item selecionado será removido."
    confirm-label="Excluir"
    confirm-variant="danger"
    confirm-icon="fa-trash"
    confirm-action="deleteItem(10)"
    cancel-action="closeDeleteModal"
    wire:model="showDeleteModal"
/>

<x-portal::table class="lista-crud">
    <x-slot:head>
        <tr>
            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nome</th>
            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Ações</th>
        </tr>
    </x-slot:head>

    <x-slot:body>
        <tr>
            <td class="px-4 py-3 text-sm text-gray-700">Registro Livewire</td>
            <td class="px-4 py-3 text-right">
                <x-portal::resource-actions
                    :view-click="'viewItem(10)'"
                    :edit-click="'editItem(10)'"
                    :delete-click="'confirmDelete(10)'"
                    view-title="Visualizar registro"
                    edit-title="Editar registro"
                    delete-title="Excluir registro"
                />
            </td>
        </tr>
        <tr>
            <td class="px-4 py-3 text-sm text-gray-700">Registro tradicional</td>
            <td class="px-4 py-3 text-right">
                <x-portal::resource-actions
                    view-href="/registros/11"
                    edit-href="/registros/11/editar"
                    delete-href="/registros/11/excluir"
                    mode="label"
                />
            </td>
        </tr>
        <tr>
            <td colspan="2" class="p-2">
                <x-portal::empty-state
                    class="m-4"
                    title="Nada por aqui"
                    message="Cadastre um item para começar."
                    icon="fa-inbox"
                />
            </td>
        </tr>
    </x-slot:body>
</x-portal::table>

<x-portal::section-footer align="center" bordered="false" muted="true">
    <span>Mostrando 1 de 1</span>
    <x-portal::button class="botao-nao-full" variant="secondary" full="false">Adicionar</x-portal::button>
</x-portal::section-footer>

<x-portal::table-actions align="center">
    <x-portal::resource-actions
        only="view,delete"
        :view-onclick="'window.alert(&quot;visualizar&quot;)'"
        :delete-onclick="'window.alert(&quot;excluir&quot;)'"
        mode="label"
        view-title="Visualizar somente"
        delete-title="Excluir somente"
    />
</x-portal::table-actions>
