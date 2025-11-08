@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Панель администратора</h1>

    <!-- Статистика -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card p-3 text-center">
                <h5>Пользователи</h5>
                <p class="fs-4 mb-0">{{ $userCount }}</p>
                <a href="{{ route('admin.users') }}" class="btn btn-outline-primary btn-sm">Все пользователи</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 text-center">
                <h5>Офферы</h5>
                <p class="fs-4 mb-0">{{ $offerCount }}</p>
                <a href="{{ route('admin.offers') }}" class="btn btn-outline-primary btn-sm">Все офферы</a>
            </div>
        </div>
        <div class="col-md-4">
            <!-- Счётчик на модерации -->
            @php
                $pendingCount = \App\Models\User::where('status', 'pending')
                    ->whereIn('role', ['advertiser', 'webmaster'])
                    ->count();
            @endphp
            <div class="card p-3 text-center {{ $pendingCount > 0 ? 'border-warning' : 'border-success' }}">
                <h5>На модерации</h5>
                <p class="fs-4 mb-0">{{ $pendingCount }}</p>
                @if($pendingCount > 0)
                    <a href="{{ route('admin.pending') }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-user-clock"></i> Проверить
                    </a>
                @else
                    <span class="text-success small">Все одобрены</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Быстрые ссылки -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="list-group list-group-horizontal justify-content-center">
                <a href="{{ route('admin.users') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-users"></i> Управление пользователями
                </a>
                <a href="{{ route('admin.offers') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-ad"></i> Управление офферами
                </a>
                <a href="{{ route('admin.pending') }}" class="list-group-item list-group-item-action {{ $pendingCount > 0 ? 'list-group-item-warning' : '' }}">
                    <i class="fas fa-user-check"></i> Модерация
                    @if($pendingCount > 0)
                        <span class="badge bg-danger rounded-pill">{{ $pendingCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.stats') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-chart-line"></i> Статистика
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <!-- Подключаем Font Awesome для иконок -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush
