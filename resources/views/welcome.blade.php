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
                    Платформа для рекламодателей и веб‑мастеров.
                    Управляйте офферами, отслеживайте статистику и масштабируйте доходы.
                </p>

                <!-- Приветствие для авторизованного пользователя -->
                @auth
                    <h4 class="text-primary mb-4">
                        Здравствуйте, <span class="text-dark">{{ Auth::user()->name }}!</span>
                    </h4>
                @endauth

                <!-- Кнопки входа и регистрации / перехода в панель -->
                <div class="mt-4">
                    @guest
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg me-3 px-4">
                            Войти
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline-secondary btn-lg px-4">
                            Зарегистрироваться
                        </a>
                    @else
                        @php
                            $role = strtolower(Auth::user()->role);
                            $route = match($role) {
                                'admin' => 'admin.dashboard',
                                'advertiser' => 'advertiser.index',
                                default => 'webmaster.offers',
                            };

                            $btnClass = match($role) {
                                'admin' => 'btn-primary',
                                'advertiser' => 'btn-warning text-dark',
                                'webmaster' => 'btn-success',
                                default => 'btn-success',
                            };

                            $icon = match($role) {
                                'admin' => 'bi-person-badge',
                                'advertiser' => 'bi-megaphone',
                                'webmaster' => 'bi-graph-up',
                                default => 'bi-speedometer',
                            };
                        @endphp

                        <a href="{{ route($route) }}"
                           class="btn {{ $btnClass }} btn-lg px-4 d-inline-flex align-items-center gap-2 animate__animated animate__pulse animate__faster"
                           style="transition: transform 0.2s;"
                           onmouseover="this.style.transform='scale(1.05)'"
                           onmouseout="this.style.transform='scale(1)'">
                            <i class="bi {{ $icon }} me-1"></i>
                            Перейти в панель
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </div>

    <!-- Подключение Bootstrap Icons (если ещё не подключено в layout) -->
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    @endpush
@endsection
