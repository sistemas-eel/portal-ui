@extends('portal-ui::layouts.app')

@section('title', 'Página com User Menu')

@section('user-menu')
    <a href="#" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700">
        <i class="fa fa-star w-4"></i>
        <span>Item customizado do user menu</span>
    </a>
@endsection

@section('content')
    <p>Conteúdo</p>
@endsection
