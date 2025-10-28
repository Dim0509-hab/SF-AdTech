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
        <!-- Шапка страницы -->
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="display-6 text-primary">
                    <i class="bi bi-graph-up"></i>
                    Статистика оффера "{{ $offer->name }}"
                </h1>
                <p class="text-muted">Оффер №{{ $offer->id }} | Период: текущий</p>
            </div>
        </div>


        <!-- Карточки с основными метриками -->
        @if($stats)
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-4 mb-4">
                <!-- Просмотры -->
                <div class="col">
                    <div class="card border-primary shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <i class="bi bi-eye fs-4 text-primary"></i>
                                <span class="badge bg-primary rounded-pill px-3">{{ number_format($stats['views'], 0, ' ', ' ') }}</span>
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
                                <span class="badge bg-success rounded-pill px-3">{{ number_format($stats['clicks'], 0, ' ', ' ') }}</span>
                            </div>
                            <h5 class="card-title text-muted">Клики</h5>
                        </div>
                    </div>
                </div>

                <!-- Конверсии -->
                <div class="col">
                    <div class="card border-warning shadow-sm h-1Desktop-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <i class="bi bi-check-circle fs-4 text-warning"></i>
                                <span class="badge bg-warning text-dark rounded-pill px-3">{{ number_format($stats['conversions'], 0, ' ', ' ') }}</span>
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
                                <span class="badge bg-danger rounded-pill px-3">{{ number_format($stats['revenue'], 2, ',', ' ') }} ₽</span>
                            </div>
                            <h5 class="card-title text-muted">Доход</h5>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Детализация -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0">Детализация показателей</h5>
                        </div>
                        <div class="card-body">
                            <dl class="row">
                                <dt class="col-sm-3">CTR (кликабельность):</dt>
                                <dd class="col-sm-9">
                                    @if($stats['views'] > 0)
                                        {{ number_format(($stats['clicks'] / $stats['views']) * 100, 2) }}%
                                    @else
                                        —
                                    @endif
                                </dd>


                                <dt class="col-sm-3">Конверсия:</dt>
                                <dd class="col-sm-9">
                                    @if($stats['clicks'] > 0)
                                        {{ number_format(($stats['conversions'] / $stats['clicks']) * 100, 2) }}%
                                    @else
                                        —
                                    @endif
                                </dd>

                                <dt class="col-sm-3">Средний чек:</dt>
                                <dd class="col-sm-9">
                                    @if($stats['conversions'] > 0)
                                        {{ number_format($stats['revenue'] / $stats['conversions'], 2, ',', ' ') }} ₽
                                    @else
                                        —
                                    @endif
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Сообщение об отсутствии данных -->
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle-fill fs-4"></i>
                <p class="mt-2 mb-0">Нет данных за выбранный период.</p>
            </div>
        @endif

        <!-- Кнопка возврата -->
        <div class="row mt-4">
            <div class="col-12 text-center">
                <a href="{{ route('advertiser.offers.index') }}" class="btn btn-outline-primary px-4">
                    <i class="bi bi-arrow-left"></i> Назад к списку офферов
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
