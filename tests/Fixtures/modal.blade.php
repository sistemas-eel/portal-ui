<x-portal::modal title="Detalhes do item" id="detalhes-modal" max-width="lg">
    Conteúdo do modal sem Livewire.

    <x-slot name="footer">
        <button type="button" data-portal-modal-close>Fechar</button>
    </x-slot>
</x-portal::modal>

<button type="button" data-portal-modal-open="detalhes-modal">Abrir modal</button>
