<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Статистика оффера #{{ $offer->id }}</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Иконки Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <!-- Шапка страницы и селектор периода -->
        <div class="row mb-4 align-items-center">
            <div class="col-auto">
                <h1 class="display-6 text-primary">
                    <i class="bi bi-graph-up"></i>
                    Статистика оффера "{{ $offer->name }}"
                </h1>
            </div>
            <div class="col">
                <!-- Селектор периода -->
                <div class="btn-group" role="group" aria-label="Выбор периода">
                    <a href="{{ route('advertiser.stats', [$offer->id, 'day']) }}"
                       class="btn btn-sm {{ $period === 'day' ? 'btn-primary' : 'btn-outline-secondary' }}">
                        <i class="bi bi-calendar-day"></i> За день
                    </a>
                    <a href="{{ route('advertiser.stats', [$offer->id, 'month']) }}"
                       class="btn btn-sm {{ $period === 'month' ? 'btn-primary' : 'btn-outline-secondary' }}">
                        <i class="bi bi-calendar-month"></i> За месяц
                    </a>
                    <a href="{{ route('advertiser.stats', [$offer->id, 'year']) }}"
                       class="btn btn-sm {{ $period === 'year' ? 'btn-primary' : 'btn-outline-secondary' }}">
                        <i class="bi bi-calendar-year"></i> За год
                    </a>
                </div>
            </div>
        </div>

        <p class="text-muted mb-4">Оффер №{{ $offer->id }} | Период:
            <strong>
                @switch($period)
                    @case('day') Сегодня @break
                    @case('month') Текущий месяц @break
                    @case('year') Текущий год @break
                @endswitch
            </strong>
        </p>
        <!-- Карточки метрик -->
        @if($stats)
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-4 mb-4">
                <!-- Просмотры -->
                <div class="col">
                    <div class="card border-primary shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <i class="bi bi-eye fs-4 text-primary"></i>
                                <span class="badge bg-primary rounded-pill px-3">
                                    {{ number_format($stats['views'], 0, ' ', ' ') }}
                                </span>
                            </div>
                            <h5 class="card-title text-muted">Просмотры</h5>
                        </div>
                    </div>
                </div>
                <!-- Клики -->
                <div class="col">
                    <div class="card border-success shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <i class="bi bi-mouse fs-4 text-success"></i>
                                <span class="badge bg-success rounded-pill px-3">
                                    {{ number_format($stats['clicks'], 0, ' ', ' ') }}
                                </span>
                            </div>
                            <h5 class="card-title text-muted">Клики</h5>
                        </div>
                    </div>
                </div>
                <!-- Конверсии -->
                <div class="col">
                    <div class="card border-warning shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <i class="bi bi-check-circle fs-4 text-warning"></i>
                                <span class="badge bg-warning text-dark rounded-pill px-3">
                                    {{ number_format($stats['conversions'], 0, ' ', ' ') }}
                                </span>
                            </div>
                            <h5 class="card-title text-muted">Конверсии</h5>
                        </div>
                    </div>
                </div>
                <!-- Доход -->
                <div class="col">
                    <div class="card border-danger shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <i class="bi bi-currency-ruble fs-4 text-danger"></i>
                                <span class="badge bg-danger rounded-pill px-3">
                                    {{ number_format($stats['revenue'], 2, ',', ' ') }} ₽
                                </span>
                            </div>
                            <h5 class="card-title text-muted">Доход</h5>
                        </div>
                    </div>
                </div>
            </div>
                    <!-- Детализация показателей -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0">Детализация показателей</h5>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <!-- CTR -->
                            <dt class="col-sm-3">CTR (кликабельность):</dt>
                            <dd class="col-sm-9">
                                @if($stats['views'] > 0)
                                    {{ number_format(($stats['clicks'] / $stats['views']) * 100, 2) }}%
                                @else
                                    —
                                @endif
                            </dd>

                            <!-- Конверсия -->
                            <dt class="col-sm-3">Конверсия:</dt>
                            <dd class="col-sm-9">
                                @if($stats['clicks'] > 0)
                                    {{ number_format(($stats['conversions'] / $stats['clicks']) * 100, 2) }}%
                                @else
                                    —
                                @endif
                            </dd>

                            <!-- Средний чек -->
                            <dt class="col-sm-3">Средний чек:</dt>
                            <dd class="col-sm-9">
                                @if($stats['conversions'] > 0)
                                    {{ number_format($stats['revenue'] / $stats['conversions'], 2, ',', ' ') }} ₽
                                @else
                                    —
                                @endif
                            </dd>

                            <!-- Стоимость клика -->
                            <dt class="col-sm-3">Стоимость клика:</dt>
                            <dd class="col-sm-9">
                                @if($stats['clicks'] > 0)
                                    {{ number_format($stats['revenue'] / $stats['clicks'], 2, ',', ' ') }} ₽
                                @else
                                    —
                                @endif
                            </dd>

                            <!-- ROI (возврат инвестиций) -->
                            <dt class="col-sm-3">ROI:</dt>
                            <dd class="col-sm-9">
                                @if($stats['cost'] > 0)
                                    {{ number_format((($stats['revenue'] - $stats['cost']) / $stats['cost']) * 100, 2) }}%
                                @else
                                    —
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        @endif
        <!-- Кнопка возврата -->
        <div class="row mt-4">
            <div class="col-12 text-center">
                <a href="{{ route('advertiser.index') }}" class="btn btn-outline-primary px-4">
                    <i class="bi bi-arrow-left"></i> Назад к списку офферов
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
