@extends('layouts.app')

@section('title', 'Portal UI Starter')

@section('sidebar')
    @if(empty(config('portal-ui.navigation.groups')))
        <x-portal::sidebar-item
            label="Início"
            route="portal-ui.starter"
            icon="fa-house"
            active="portal-ui.starter"
        />
    @endif
@endsection

@section('content')
    <x-portal::page-header
        title="Portal UI instalado"
        subtitle="A primeira tela da aplicação está funcionando."
    />

    <x-portal::card>
        <div class="space-y-3">
            <p>Layout, estilos e JavaScript foram carregados pelo pacote.</p>

            <div class="flex items-center gap-2 text-portal">
                <x-portal::icon name="fa-circle-check" />
                <span>Os ícones locais também estão disponíveis.</span>
            </div>
        </div>
    </x-portal::card>
@endsection
