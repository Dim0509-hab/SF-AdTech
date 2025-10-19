
@extends('layouts.app')
@section('content')
<div class="row justify-content-center"><div class="col-md-6"><h2>Регистрация</h2><form method="POST" action="{{ route('register') }}">@csrf<div class="mb-3"><label>Имя</label><input name="name" class="form-control" value="{{ old('name') }}"></div><div class="mb-3"><label>Email</label><input name="email" type="email" class="form-control" value="{{ old('email') }}"></div><div class="mb-3"><label>Пароль</label><input name="password" type="password" class="form-control"></div><button class="btn btn-primary">Зарегистрироваться</button></form></div></div>@endsection
