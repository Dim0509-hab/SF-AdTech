<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') — Админ-панель</title>


<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<link
  href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
  rel="stylesheet">

</head>

<body class="d-flex flex-column min-vh-100">
    <!-- Навигационная панель -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                Админ-панель
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                           href="{{ route('admin.dashboard') }}">Главная</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.revenue') ? 'active' : '' }}"
                           href="{{ route('admin.revenue.index') }}">Доходы</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }}"
                           href="{{ route('admin.users') }}">Пользователи</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <span class="navbar-text text-white me-3">
                        Привет, {{ Auth::user()->email }}!
                    </span>
                    <a href="/logout" class="btn btn-outline-light btn-sm">Выход</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Основное содержимое -->
    <main class="container-fluid mt-4 px-4">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer mt-auto py-3 bg-light border-top">
        <div class="container text-center text-muted">
            &copy; {{ date('Y') }} Админ-панель SF-AdTech. Все права защищены.
        </div>
    </footer>

   <!-- Bootstrap JS + Popper (CDN) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Дополнительные скрипты (если нужны) -->
    @stack('scripts')
</body>
</html>
