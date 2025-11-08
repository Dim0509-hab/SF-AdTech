@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Доступные офферы</h2>

    <!-- Кнопки навигации -->
    <div class="d-flex justify-content-between mb-3">
        <a href="{{ route('webmaster.offers.subscribed') }}" class="btn btn-primary">
            Мои подписки
        </a>
        <a href="{{ route('webmaster.stats') }}" class="btn btn-info">
            Посмотреть статистику
        </a>
    </div>

    @if($offers->isEmpty())
        <p class="text-muted">Офферов пока нет.</p>
    @else
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Название</th>
                    <th>Цена за действие</th>
                    <th>Подписчиков</th>
                    <th>Действие</th>
                </tr>
            </thead>
            <tbody>
                @foreach($offers as $offer)
                    <tr>
                        <td>{{ $offer->name }}</td>

                        <!-- ✅ Показываем price из таблицы offers -->
                        <td>
                            <strong>{{ number_format($offer->price, 2) }} ₽</strong>
                            <br>
                            <small class="text-muted">
                                Базовая ставка
                            </small>
                        </td>

                        <td>{{ $offer->webmasters_count }}</td>

                        <td>
                            @if(in_array($offer->id, $subs))
                                <!-- Уже подписан -->
                                <form method="POST" action="{{ route('webmaster.offers.unsubscribe', $offer->id) }}" class="d-inline">
                                    @csrf
                                    @method('POST')
                                    <button type="submit" class="btn btn-warning btn-sm">Отписаться</button>
                                </form>

                                <a href="{{ route('webmaster.offers.link', $offer->id) }}"
                                   class="btn btn-secondary btn-sm ms-1">
                                    Ссылка
                                </a>
                           @else
                                    <!-- Форма с регулировкой ставки -->
                                    <form method="POST" action="{{ route('webmaster.offers.subscribe', $offer->id) }}" class="d-inline">
                                        @csrf
                                        <div class="input-group input-group-sm">
                                            <!-- Скрытое поле — базовая цена -->
                                            <input type="hidden" name="base_price" value="{{ $offer->price }}">

                                            <!-- Поле с итоговой ставкой -->
                                            <input
                                                type="number"
                                                name="cost_per_click"
                                                id="cost_{{ $offer->id }}"
                                                class="form-control"
                                                value="{{ $offer->price }}"
                                                step="0.01"
                                                min="0.01"
                                                max="9999.99"
                                                required
                                                style="max-width: 100px;"
                                            >

                                            <!-- Кнопки + и - -->
                                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="adjustCost({{ $offer->id }}, -0.1)">−0.10</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="adjustCost({{ $offer->id }}, -0.05)">−0.05</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="adjustCost({{ $offer->id }}, 0.05)">+0.05</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="adjustCost({{ $offer->id }}, 0.1)">+0.10</button>

                                            <!-- Кнопка подписки -->
                                            <button type="submit" class="btn btn-success btn-sm ms-1">Подписаться</button>
                                        </div>

                                        <!-- Подсказка -->
                                        <small class="text-muted d-block mt-1" style="font-size: 0.8rem;">
                                            База: {{ number_format($offer->price, 2) }} ₽
                                        </small>

                                        <!-- Ошибка валидации -->
                                        @error('cost_per_click')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </form>
                                @endif

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
    @endsection

    @push('scripts')
    <script>
    function adjustCost(offerId, delta) {
        const input = document.getElementById('cost_' + offerId);
        let value = parseFloat(input.value) || 0;
        let newValue = (value + delta).toFixed(2);

        // Ограничиваем минимальное значение
        if (newValue < 0.01) newValue = 0.01;

        input.value = newValue;
}
</script>
@endpush

