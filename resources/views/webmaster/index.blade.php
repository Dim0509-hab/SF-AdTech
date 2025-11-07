@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Доступные офферы</h2>

    <!-- Кнопка "Мои подписки" и "Статистика" -->
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
                    <th>Подписчиков</th>
                    <th>Действие</th>
                </tr>
            </thead>
            <tbody>
                @foreach($offers as $offer)
                    <tr>
                        <td>{{ $offer->name }}</td>
                        <td>{{ $offer->webmasters_count }}</td>
                        <td>
                            @if(in_array($offer->id, $subs))
                                <!-- Уже подписан: форма отписки -->
                                <form method="POST" action="{{ route('webmaster.offers.unsubscribe', $offer->id) }}" class="d-inline">
                                    @csrf
                                    @method('POST')
                                    <button type="submit" class="btn btn-warning btn-sm">Отписаться</button>
                                </form>

                                <!-- Ссылка на партнёрскую ссылку -->
                                <a href="{{ route('webmaster.offers.link', $offer->id) }}"
                                   class="btn btn-secondary btn-sm ms-1">
                    Ссылка
                </a>
                            @else
                                <!-- Форма подписки с указанием стоимости -->
                                <form method="POST"
                                        action="{{ route('webmaster.offers.subscribe', $offer->id) }}"
                                        class="d-inline">
                                    @csrf
                                    <div class="input-group input-group-sm mb-2">
                                        <span class="input-group-text">₽ за клик</span>
                                        <input type="number"
                                            name="cost_per_click"
                                            class="form-control"
                                            step="0.01"
                                            min="0.01"
                                            value="0.50"
                                            required>
                                    </div>
                                    <button class="btn btn-success btn-sm">Подписаться</button>
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
