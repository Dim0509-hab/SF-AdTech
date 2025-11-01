@extends('layouts.app')
@section('content')
<div class="container">
    <h2>Статистика доходов</h2>

    <!-- Карточки статистики -->
    <div class="row mb-4">
        <!-- Сегодня -->
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Сегодня</h5>
                    <p class="card-text">
                        Клики: <strong>{{ $stats['today']['clicks'] }}</strong><br>
                        Доход: <strong>{{ number_format($stats['today']['revenue'], 2) }} ₽</strong>
                    </p>
                </div>
            </div>
        </div>

        <!-- Месяц -->
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Месяц</h5>
                    <p class="card-text">
                        Клики: <strong>{{ $stats['month']['clicks'] }}</strong><br>
                        Доход: <strong>{{ number_format($stats['month']['revenue'], 2) }} ₽</strong>
                    </p>
                </div>
            </div>
        </div>

        <!-- Год -->
        <div class="col-md-4">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5 class="card-title">Год</h5>
                    <p class="card-text">
                        Клики: <strong>{{ $stats['year']['clicks'] }}</strong><br>
                        Доход: <strong>{{ number_format($stats['year']['revenue'], 2) }} ₽</strong>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Список офферов с детализацией -->
    <h4>Детализация по офферам</h4>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Оффер</th>
                <th>Цена за клик</th>
                <th>Клики сегодня</th>
                <th>Доход сегодня</th>
                <th>Клики месяц</th>
                <th>Доход месяц</th>
            </tr>
        </thead>
        <tbody>
            @foreach($subscriptions as $sub)
                <tr>
                    <td>{{ $sub->offer->name }}</td>
                    <td>{{ number_format($sub->cost_per_click, 2) }} ₽</td>

                    <!-- Сегодня -->
                    <td>
                        {{ $sub->clicks->where('created_at', '>=', now()->startOfDay())->count() }}
                    </td>
                    <td>
                        {{ number_format(
                            $sub->clicks->where('created_at', '>=', now()->startOfDay())->count() * $sub->cost_per_click,
                            2
                        ) }} ₽
                    </td>

                    <!-- Месяц -->
                    <td>
                        {{ $sub->clicks->where('created_at', '>=', now()->startOfMonth())->count() }}
                    </td>
                    <td>
                        {{ number_format(
                            $sub->clicks->where('created_at', '>=', now()->startOfMonth())->count() * $sub->cost_per_click,
                            2
                        ) }} ₽
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Ссылка назад -->
    <a href="{{ route('webmaster.offers') }}" class="btn btn-secondary">
        ← К списку офферов
    </a>
</div>
@endsection
