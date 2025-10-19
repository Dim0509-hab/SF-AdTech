<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SF-AdTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="/">SF-AdTech</a>

        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
               @auth
    @php $role = auth()->user()->role; @endphp

    <li class="nav-item">
        <span class="nav-link fw-bold">
            {{ auth()->user()->name }} <small class="text-muted">({{ $role }})</small>
        </span>
    </li>

    @if($role === 'admin')
        <li class="nav-item"><a class="nav-link" href="{{ url('/admin/dashboard') }}">Админ-панель</a></li>
    @elseif($role === 'advertiser')
        <li class="nav-item"><a class="nav-link" href="{{ url('/advertiser/offers') }}">Мои офферы</a></li>
    @elseif($role === 'webmaster')
        <li class="nav-item"><a class="nav-link" href="{{ url('/webmaster/offers') }}">Мои подписки</a></li>
    @endif

    <li class="nav-item">
        <form method="POST" action="{{ route('logout') }}" class="d-inline">
            @csrf
            <button class="btn btn-link nav-link" style="display:inline; padding:0;">Выйти</button>
        </form>
    </li>
      @else
    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Войти</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Регистрация</a></li>
    @endauth
            </ul>
        </div>
    </div>
</nav>

<div class="container py-4">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</div>

</body>
</html>
