@extends('admin.layout')

@section('title', 'Контроль доходов системы')


@section('content')
    <div class="container-fluid">
        <h1 class="mb-4">Контроль доходов системы</h1>


        <!-- Фильтры -->
        <form method="GET" class="bg-white p-4 rounded shadow-sm mb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="from" class="form-label">Период с:</label>
                    <input type="date"
                           class="form-control"
                           id="from"
                           name="from"
                           value="{{ $from }}"
                           required>
                </div>
                <div class="col-md-3">
                    <label for="to" class="form-label">по:</label>
                    <input type="date"
                           class="form-control"
                           id="to"
                           name="to"
                           value="{{ $to }}"
                           required>
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Статус конверсий:</label>
                    <select class="form-select" id="status" name="status">
                        <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Оплачено</option>
                        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>В ожидании</option>
                        <option value="canceled" {{ $status === 'canceled' ? 'selected' : '' }}>Отменено</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Применить фильтры</button>
                    <a href="{{ route('admin.revenue.export', request()->query()) }}"
                       class="btn btn-success"
                       target="_blank">
                        Экспорт в CSV
                    </a>
                </div>
            </div>
        </form>

        <!-- Основные показатели -->
        <div class="row mb-4">
            <div class="col-xl-4 col-md-6 mb-3">
                <div class="card text-white bg-primary shadow">
                    <div class="card-body">
                        <h6 class="card-title text-white">Общий доход</h6>
                        <p class="display-6 mb-0">{{ number_format($totalRevenue, 2, ',', ' ') }} ₽</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-6 mb-3">
                <div class="card text-white bg-success shadow">
                    <div class="card-body">
                        <h6 class="card-title text-white">Количество конверсий</h6>
                        <p class="display-6 mb-0">{{ $countConversions }}</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-6 mb-3">
                <div class="card text-white bg-info shadow">
                    <div class="card-body">
                        <h6 class="card-title text-white">Средний чек</h6>
                        <p class="display-6 mb-0">{{ number_format($avgRevenue, 2, ',', ' ') }} ₽</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Топ офферов -->
        <div class="card shadow mb-4">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">Топ‑5 офферов по доходу</h5>
            </div>
            <div class="card-body">
                @if($revenueByOffer->isEmpty())
                    <p class="text-muted">Нет данных за выбранный период.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Оффер ID</th>
                                    <th>Доход</th>
                                    <th>Доля от общего</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($revenueByOffer as $item)
                                    <tr>
                                        <td>{{ $item->offer_id }}</td>
                                        <td>{{ number_format($item->total, 2, ',', ' ') }} ₽</td>
                                        <td>
                                            {{ number_format(($item->total / $totalRevenue) * 100, 1) }}%
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
                <!-- Общий доход -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Общий доход системы</h5>
            </div>
            <div class="card-body">
                <p class="h4 text-success">₽{{ number_format($totalRevenue, 2) }}</p>
            </div>
        </div>

        <!-- Выданные ссылки -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Выданные ссылки</h5>
            </div>
            <div class="card-body">
                <p class="h5">{{ $uniqueLinks }} уникальных ссылок</p>
            </div>
        </div>

        <!-- Переходы по дням -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Переходы за последние 30 дней</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Дата</th>
                            <th>Переходов</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clickStats as $stat)
                            <tr>
                                <td>{{ $stat->date }}</td>
                                <td>{{ $stat->total_clicks }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Отказы -->
        <div class="card">
            <div class="card-header">
                <h5>Последние отказы</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Веб-мастер</th>
                            <th>Оффер</th>
                            <th>Причина</th>
                            <th>Время</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rejections as $rejection)
                            <tr>
                                <td>{{ $rejection->webmaster->email }}</td>
                                <td>{{ $rejection->offer->name }}</td>
                                <td>{{ $rejection->reason }}</td>
                                <td>{{ $rejection->rejected_at->format('d.m.Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $rejections->links() }}
            </div>
        </div>


        <!-- Топ веб‑мастеров -->
        <div class="card shadow">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">Топ‑5 веб‑мастеров по доходу</h5>
            </div>
            <div class="card-body">
                @if($revenueByUser->isEmpty())
                    <p class="text-muted">Нет данных за выбранный период.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Веб‑мастер ID</th>
                                    <th>Доход</th>
                                    <th>Доля от общего</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($revenueByUser as $item)
                                    <tr>
                                        <td>{{ $item->user_id }}</td>
                                        <td>{{ number_format($item->total, 2, ',', ' ') }} ₽</td>
                                        <td>
                                            {{ number_format(($item->total / $totalRevenue) * 100, 1) }}%
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
