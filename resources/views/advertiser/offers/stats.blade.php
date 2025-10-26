<!-- resources/views/advertiser/offers/stats.blade.php -->
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Статистика оффера #{{ $offer->id }}</title>
</head>
<body>
    <h1>Статистика оффера "{{ $offer->name }}"</h1>

    <div>
        <p><strong>Просмотры:</strong> {{ $stats['views'] }}</p>
        <p><strong>Клики:</strong> {{ $stats['clicks'] }}</p>
        <p><strong>Конверсии:</strong> {{ $stats['conversions'] }}</p>
        <p><strong>Доход:</strong> {{ $stats['revenue'] }} ₽</p>
    </div>

    <a href="{{ route('advertiser.offers.index') }}">Назад к списку офферов</a>
</body>
</html>
