@extends('layouts.app')

@section('body-class', 'app-shell')

@section('content')
<div class="app-layout">
    @include('partials.app-sidebar', ['active' => $activeNav ?? ''])
    <div class="app-main">
        @yield('app-content')
    </div>
</div>
@endsection