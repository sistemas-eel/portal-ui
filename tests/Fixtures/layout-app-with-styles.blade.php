@extends('portal-ui::layouts.app')

@push('styles')
    <link
        rel="stylesheet"
        href="/build/assets/app.css"
        data-consumer-styles
    >
@endpush

@section('content')
    <p>Conteúdo com estilos próprios</p>
@endsection