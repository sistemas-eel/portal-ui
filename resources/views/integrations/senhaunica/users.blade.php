@extends(config('senhaunica.template'))

@section('title', 'Usuários SenhaUnica')

@push('styles')
<style>
    /* Alpine.js cloak para evitar piscada de conteúdo antes de carregar o JS */
    [x-cloak] { display: none !important; }

    /* Força o ponteiro do mouse em botões clicáveis */
    button, [role="button"], .cursor-pointer {
        cursor: pointer !important;
    }
</style>
@endpush


@section('content')
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
    <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
        @include('senhaunica::partials.users-menu')
    </div>
    
    <div class="p-6">
        @include('senhaunica::partials.users-list')
    </div>

    {{-- Singleton Modals --}}
    @include('senhaunica::partials.local-users-edit')
    @include('senhaunica::users.partials.permissoes-modal')
    @include('senhaunica::partials.show-json-modal')

    {{-- <h4>Todas as permissões</h4>
    {!! \App\Models\User::listarTodasPermissoes() !!} --}}
</div>
@endsection
