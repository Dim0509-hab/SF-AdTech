@extends('layouts.app')

@section('content')
<h2>Мои офферы</h2>
<table class="table">
    <thead>
        <tr>
            <th>Название</th>
            <th>Подписок</th>
            <th>Статус</th>
            <th>Действия</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($offers as $offer)
        <tr>
            <td>{{ $offer->name }}</td>
            <td>{{ $offer->subscriptions_count }}</td>
            <td>{{ $offer->active ? 'Активен' : 'Деактивирован' }}</td>
            <td>
                <a href="{{ route('advertiser.offers.stats', $offer->id) }}" class="btn btn-sm btn-info">Статистика</a>
                @if ($offer->active)
                    <form action="{{ route('advertiser.offers.deactivate', $offer->id) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-danger">Деактивировать</button>
                    </form>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
