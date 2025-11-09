@extends('layouts.app')

@section('content')
    <h2>Ваша партнёрская ссылка</h2>
    <div class="alert alert-light">
        <pre style="margin: 0; white-space: pre-wrap; word-break: break-all;">{{ $link }}</pre>
    </div>

    <!-- Кнопка копирования -->
    <button class="btn btn-primary mb-3" onclick="copyLink()">
        📋 Скопировать ссылку
    </button>

    <!-- Ссылка назад -->
    <a href="{{ route('webmaster.offers') }}" class="btn btn-secondary">
        ← К списку офферов
    </a>

    <script>
        function copyLink() {
            navigator.clipboard.writeText("{{ $link }}")
                .then(() => alert('Ссылка скопирована'))
                .catch(err => console.error('Ошибка копирования: ', err));
        }
    </script>
@endsection
