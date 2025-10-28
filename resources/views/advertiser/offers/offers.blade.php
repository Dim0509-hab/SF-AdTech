<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Мои офферы</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <h1 class="mb-4">Мои офферы</h1>

        <!-- Уведомления -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Таблица офферов -->
        <div class="card">
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Название</th>
                            <th>Описание</th>
                            <th>Веб-мастера</th>
                            <th>Статус</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($offers as $offer)
                            <tr>
                                <td>{{ $offer->name }}</td>
                                <td>{{ Str::limit($offer->description, 100) }}</td>
                                <td class="text-center">
                                    <span class="badge bg-primary">{{ $offer->subscriptions_count }}</span>
                                </td>
                                <td>
                                    @if($offer->active)
                                        <span class="badge bg-success">Активен</span>
                                    @else
                                        <span class="badge bg-secondary">Деактивирован</span>
                                    @endif
                                </td>
                                <td>
                                    <!-- Кнопка деактивации -->
                                    <form action="{{ route('advertiser.offers.deactivate', $offer->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Вы уверены, что хотите деактивировать этот оффер?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            Деактивировать
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    Нет созданных офферов
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
