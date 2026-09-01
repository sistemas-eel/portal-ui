@extends('layouts.app')

@section('title', 'Página inicial')

@section('content')
    <x-portal::page-header
        title="Meu primeiro sistema"
        subtitle="O Portal UI está funcionando corretamente."
    />

    <x-portal::card>
        <div class="space-y-3">
            <p>Esta é a primeira página da aplicação usando o Portal UI.</p>

            <div class="flex items-center gap-2 text-portal">
                <x-portal::icon name="fa-circle-check" />
                <span>Layout, estilos, JavaScript e ícones carregados.</span>
            </div>
        </div>
    </x-portal::card>
@endsection
