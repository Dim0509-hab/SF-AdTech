@extends('layouts.app')

@section('title', 'SF-AdTech — Платформа для рекламодателей и веб‑мастеров')

@section('meta')
    <meta name="description" content="SF-AdTech — платформа для взаимодействия рекламодателей и веб‑мастеров. Управляйте офферами, отслеживайте статистику и масштабируйте доходы.">
@endsection

@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <h1 class="display-4 mb-4">SF-AdTech</h1>
                <p class="lead mb-5">
                    Проект для рекламодателей и веб‑мастеров.
                    Управляйте офферами, отслеживайте статистику и масштабируйте доходы.
                </p>

                <!-- Кнопки входа и регистрации -->
                <div class="mt-4">
                    @guest
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg me-3">
                            Войти
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline-secondary btn-lg">
                            Зарегистрироваться
                        </a>
                    @else
                        <a href="{{ route('webmaster.offers') }}" class="btn btn-success btn-lg">
                            Перейти в панель
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </div>
@endsection
