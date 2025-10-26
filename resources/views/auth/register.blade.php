@extends('layouts.app')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <h2>Регистрация</h2>
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Имя</label>
                <input id="name" name="name" type="text" class="form-control" value="{{ old('name') }}" required autofocus>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input id="email" name="email" type="email" class="form-control" value="{{ old('email') }}" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Пароль</label>
                <input id="password" name="password" type="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">Кто вы?</label>
                <select id="role" name="role" class="form-select" required>
                    <option value="">Выберите роль</option>
                    <option value="advertiser" {{ old('role') == 'advertiser' ? 'selected' : '' }}>Рекламодатель</option>
                    <option value="webmaster" {{ old('role') == 'webmaster' ? 'selected' : '' }}>Вебмастер</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
        </form>
    </div>
</div>
@endsection
