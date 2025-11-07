@extends('layouts.app')
@section('content')
<div class="container">
    <h2 class="mb-4">Статистика доходов</h2>

    <!-- Карточки статистики (как было) -->
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

    <!-- Детализация по офферам -->
    <h4 class="mb-3">Детализация по офферам</h4>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Оффер</th>
                    <th>Цена за клик</th>
                    <th class="text-center">Сегодня</th>
                    <th class="text-center">Месяц</th>
                    <th class="text-center">Год</th>
                </tr>
            </thead>
            <tbody>
                @foreach($subscriptions as $sub)
                    <tr>
                        <td>{{ $sub->name }}</td>
                        <td>{{ number_format($sub->pivot->cost_per_click, 2) }} ₽</td>

                                                    <!-- Сегодня -->
                            <td class="text-center">
                                <strong>{{ $sub->today_clicks ?? 0 }}</strong><br>
                                <small class="text-muted">{{ number_format($sub->today_revenue ?? 0, 2) }} ₽</small>
                            </td>

                            <!-- Месяц -->
                            <td class="text-center">
                                <strong>{{ $sub->month_clicks ?? 0 }}</strong><br>
                                <small class="text-muted">{{ number_format($sub->month_revenue ?? 0, 2) }} ₽</small>
                            </td>

                            <!-- Год -->
                            <td class="text-center">
                                <strong>{{ $sub->year_clicks ?? 0 }}</strong><br>
                                <small class="text-muted">{{ number_format($sub->year_revenue ?? 0, 2) }} ₽</small>
                            </td>

                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Ссылка назад -->
    <div class="mt-4">
        <a href="{{ route('webmaster.offers') }}" class="btn btn-secondary">
            ← К списку офферов
        </a>
    </div>
</div>
@endsection
