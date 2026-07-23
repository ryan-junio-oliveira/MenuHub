@extends('layouts.app')
@section('title', 'Perfil')
@section('content')

<div class="max-w-4xl mx-auto">
    <div class="page-header">
        <h1 class="page-title">Perfil</h1>
        <p class="page-subtitle">Gerenciar suas informações pessoais</p>
    </div>

    <div class="space-y-6">
        @include('profile.partials.update-profile-information-form')

        @include('profile.partials.update-password-form')

        @include('profile.partials.delete-user-form')
    </div>
</div>

@endsection
