@extends('admin.layout')

@section('title', 'Контроль доходов системы')

@section('content')
<div class="container-fluid">

    <!-- Хлебные крошки -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Админ-панель</a></li>
            <li class="breadcrumb-item active" aria-current="page">Контроль доходов</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <!-- Заголовок -->
    <h1 class="mb-4">📊 Контроль доходов системы</h1>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
            ← Назад в админку
        </a>
    </div>



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
                <button type="submit" class="btn btn-primary me-2">Применить</button>
                <a href="{{ route('admin.revenue.export', request()->query()) }}"
                   class="btn btn-success btn-sm"
                   target="_blank">
                    <i class="fas fa-download me-1"></i> Экспорт CSV
                </a>
            </div>
        </div>
    </form>

    <!-- Основные показатели -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card text-white bg-primary shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-white">Общий доход</h6>
                    <p class="display-6 mb-0">{{ number_format($totalRevenue, 2, ',', ' ') }} ₽</p>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card text-white bg-success shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-white">Конверсии</h6>
                    <p class="display-6 mb-0">{{ $countConversions }}</p>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card text-white bg-info shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-white">Средний чек</h6>
                    <p class="display-6 mb-0">{{ number_format($avgRevenue, 2, ',', ' ') }} ₽</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Логическая секция: Доходы -->
    <div class="row">
        <!-- Топ офферов -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">🏆 Топ‑5 офферов по доходу</h5>
                </div>
                <div class="card-body">
                    @if($revenueByOffer->isEmpty())
                        <p class="text-muted">Нет данных за выбранный период.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Оффер</th>
                                        <th>Доход</th>
                                        <th>Доля</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($revenueByOffer as $item)
                                        <tr>
                                            <td>{{ $item->offer?->name ?? 'ID: '.$item->offer_id }}</td>
                                            <td>{{ number_format($item->total_revenue, 2, ',', ' ') }} ₽</td>
                                            <td>
                                                {{ $totalRevenue > 0 ? number_format(($item->total_revenue / $totalRevenue) * 100, 1) : '0.0' }}%
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

        <!-- Топ веб-мастеров -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">🏅 Топ‑5 веб-мастеров по доходу</h5>
                </div>
                <div class="card-body">
                    @if($revenueByUser->isEmpty())
                        <p class="text-muted">Нет данных за выбранный период.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Веб-мастер</th>
                                        <th>Доход</th>
                                        <th>Доля</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($revenueByUser as $item)
                                        <tr>
                                            <td>{{ $item->user?->name ?? $item->user?->email ?? 'ID: '.$item->user_id }}</td>
                                            <td>{{ number_format($item->total_revenue, 2, ',', ' ') }} ₽</td>
                                            <td>
                                                {{ $totalRevenue > 0 ? number_format(($item->total_revenue / $totalRevenue) * 100, 1) : '0.0' }}%
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
    </div>

    <!-- Логическая секция: Активность -->
    <div class="row">
        <!-- Переходы за 30 дней -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">🖱️ Переходы за последние 30 дней</h5>
                </div>
                <div class="card-body">
                    @if($clickStats->isEmpty())
                        <p class="text-muted">Нет данных о переходах.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
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
                    @endif
                </div>
            </div>
    </div>

        <!-- Уникальные ссылки -->
    <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">🔗 Выданные ссылки</h5>
                </div>
                <div class="card-body">
                    <p class="h5 text-primary">{{ $uniqueLinks }}</p>
                    <p class="text-muted mb-0">уникальных реферальных ссылок</p>
                </div>
            </div>
        </div>
    </div>

        <!-- Последние отказы -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0">🚫 Последние отказы</h5>
        </div>
        <div class="card-body">
            @if($rejections->isEmpty())
                <p class="text-muted">Нет отказов.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-sm">
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
                                    <td>
                                        @if($rejection->webmaster)
                                            {{ $rejection->webmaster->name ?? $rejection->webmaster->email }}
                                        @else
                                            <span class="text-muted">[ID: {{ $rejection->webmaster_id }}]</span>
                                        @endif
                                    </td>
                                    <td>{{ $rejection->offer?->name ?? 'ID: '.$rejection->offer_id }}</td>
                                    <td>{{ $rejection->reason }}</td>
                                    <td>{{ $rejection->created_at?->format('d.m.Y H:i') ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $rejections->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
