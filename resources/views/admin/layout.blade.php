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
