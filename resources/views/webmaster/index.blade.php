@extends('layouts.app')
@section('content')
<div class="container">
    <h2 class="mb-4">Доступные офферы</h2>

    <a href="{{ route('webmaster.stats') }}" class="btn btn-info mb-3">
        Посмотреть статистику
    </a>

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
                               <form method="POST" action="{{ route('webmaster.offers.unsubscribe', $offer->id) }}">
                                    @csrf
                                    @method('POST') <!-- Явно указываем POST -->
                                    <button type="submit" class="btn btn-warning">Отписаться</button>
                                </form>

                                <a href="{{ route('webmaster.offers.link', $offer->id) }}" class="btn btn-secondary btn-sm">Ссылка</a>
                            @else
                                <form method="POST" action="{{ route('webmaster.offers.subscribe', $offer->id) }}">
                                    @csrf
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
