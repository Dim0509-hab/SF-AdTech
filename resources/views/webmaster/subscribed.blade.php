@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Мои подписанные офферы</h2>

    <a href="{{ route('webmaster.index') }}" class="btn btn-secondary mb-3">
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
                        <td>{{ $offer->name }}</td>
                        <td>{{ $offer->webmasters_count }}</td>
                        <td>
                            <a href="{{ route('webmaster.stats', $offer->id) }}"
                               class="btn btn-info btn-sm">
                                Посмотреть
                            </a>
                        </td>
                        <td>
                            <!-- Отписка -->
                            <form method="POST" action="{{ route('webmaster.offers.unsubscribe', $offer->id) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm">Отписаться</button>
                            </form>

                            <!-- Партнёрская ссылка -->
                            <a href="{{ route('webmaster.offers.link', $offer->id) }}"
                               class="btn btn-secondary btn-sm ms-1">
                                Ссылка
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
