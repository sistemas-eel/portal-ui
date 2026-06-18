<x-portal::input label="Nome" name="nome" value="Maria" required />

<x-portal::select
    label="Setor"
    name="setor"
    :options="['ti' => 'Tecnologia', 'rh' => 'Recursos Humanos']"
    selected="rh"
/>

<x-portal::textarea label="Observação" name="observacao" value="Texto inicial" />

<x-portal::input label="Nome Livewire" wire:model.live="form.nome" />

<x-portal::select
    label="Setor Livewire"
    wire:model.defer="form.setor"
    :options="['ti' => 'Tecnologia', 'rh' => 'Recursos Humanos']"
    selected="ti"
/>

<x-portal::textarea label="Observação Livewire" wire:model.blur="form.observacao" value="Texto do wire" />

<x-portal::switch label="Ativo Livewire" wire:model="form.ativo" />

<x-portal::button type="submit">Salvar</x-portal::button>

<x-portal::button click="save" wire:target="save" icon="fa-save">Salvar Livewire</x-portal::button>

<x-portal::modal title="Detalhes" id="detalhes">
    Abrir detalhes
</x-portal::modal>

<x-portal::modal title="Detalhes Livewire" wire:model="showDetails">
    Conteúdo do modal com binding Livewire.
</x-portal::modal>
