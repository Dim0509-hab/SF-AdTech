@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Панель администратора</h1>
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card p-3">
                <h3>Пользователи</h3>
                <p>Всего пользователей: {{ $userCount }}</p>
                <a href="{{ route('admin.users') }}" class="btn btn-primary">Просмотр всех пользователей</a>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-3">
                <h3>Офферы</h3>
                <p>Всего офферов: {{ $offerCount }}</p>
                <a href="{{ route('admin.offers') }}" class="btn btn-primary">Просмотр всех офферов</a>
            </div>
        </div>
    </div>
</div>
@endsection
