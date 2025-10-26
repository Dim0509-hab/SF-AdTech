<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Статистика оффера #{{ $offer->id }}</title>
</head>
<body>
    <h1>Статистика оффера "{{ $offer->name }}"</h1>

    @if($stats)
        <div>
            <p><strong>Просмотры:</strong> {{ $stats['views'] }}</p>
            <p><strong>Клики:</strong> {{ $stats['clicks'] }}</p>
            <p><strong>Конверсии:</strong> {{ $stats['conversions'] }}</p>
            <p><strong>Доход:</strong> {{ number_format($stats['revenue'], 2, ',', ' ') }} ₽</p>
        </div>
    @else
        <p>Нет данных за выбранный период.</p>
    @endif

    <a href="{{ route('advertiser.offers.index') }}">Назад к списку офферов</a>
</body>
</html>
