@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Мои подписанные офферы</h2>

    <a href="{{ route('webmaster.offers') }}" class="btn btn-secondary mb-3">
        Все офферы
    </a>
    <a href="{{ route('webmaster.stats') }}" class="btn btn-info mb-3">
        Статистика
    </a>

    @if($offers->isEmpty())
        <p class="text-muted">Вы не подписаны ни на один оффер.</p>
    @else
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Название</th>
                    <th>Подписчиков</th>
                    <th>Статистика</th>
                    <th>Действие</th>
                </tr>
            </thead>
            <tbody>
                @foreach($offers as $offer)
    <tr>
        <td>
            {{ $offer->name }}
            @if(! $offer->isActive())
                <br>
                <span class="text-danger small">
                    <strong>⚠️ Оффер отключён</strong>
                </span>
            @endif
        </td>
        <td>{{ $offer->webmasters_count }}</td>
        <td>
            @if($offer->isActive())
                <a href="{{ route('webmaster.stats', $offer->id) }}" class="btn btn-info btn-sm">Посмотреть</a>
            @else
                <button class="btn btn-secondary btn-sm" disabled>Недоступно</button>
            @endif
        </td>
        <td>
            @if($offer->isActive())
                <form method="POST" action="{{ route('webmaster.offers.unsubscribe', $offer->id) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm">Отписаться</button>
                </form>

                <a href="{{ route('webmaster.offers.link', $offer->id) }}" class="btn btn-secondary btn-sm ms-1">Ссылка</a>
            @else
                <button class="btn btn-secondary btn-sm" disabled>Ссылка недоступна</button>
            @endif
        </td>
    </tr>
@endforeach

            </tbody>
        </table>
    @endif
</div>
@endsection
