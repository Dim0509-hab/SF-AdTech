@extends('layouts.app')
@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Статистика оффера «{{ $subscription->offer->name }}»</h2>
        <a href="{{ route('webmaster.stats') }}" class="btn btn-secondary">
            ← К списку офферов
        </a>
    </div>

    <!-- Карточки с основными метриками -->
    <div class="row">
        <!-- Сегодня -->
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-primary">
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
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-success">
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
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-danger">
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

    <!-- Детализация по дням за последний месяц -->
    <div class="card">
        <div class="card-header">
            Детализация по дням (последний месяц)
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Дата</th>
                        <th>Клики</th>
                        <th>Доход</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dailyStats as $date => $data)
                        <tr>
                            <td>{{ $date }}</td>
                            <td>{{ $data['clicks'] }}</td>
                            <td>{{ number_format($data['revenue'], 2) }} ₽</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- График (опционально, требует подключения Chart.js) -->
    <div class="card mt-4">
        <div class="card-header">
            График кликов за последний месяц
        </div>
        <div class="card-body">
            <canvas id="clicksChart" width="400" height="200"></canvas>
        </div>
    </div>
</div>

@push('scripts')
    <!-- Подключение Chart.js (если ещё не подключено) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    <script>
        const ctx = document.getElementById('clicksChart').getContext('2d');
        const clicksChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json(array_keys($dailyStats)),
                datasets: [{
                    label: 'Клики',
                    data: @json(array_column($dailyStats, 'clicks')),
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
@endpush
@endsection
