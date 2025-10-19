@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Все офферы</h1>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Название</th>
                <th>Рекламодатель</th>
                <th>Цена</th>
                <th>Активность</th>
                <th>Темы</th>
                <th>URL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($offers as $offer)
            <tr>
                <td>{{ $offer->id }}</td>
                <td>{{ $offer->name }}</td>
                <td>{{ $offer->advertiser->name }}</td>
                <td>{{ $offer->price }}</td>
                <td>{{ $offer->active ? 'Да' : 'Нет' }}</td>
                <td>{{ implode(', ', $offer->themes ?? []) }}</td>
                <td><a href="{{ $offer->target_url }}" target="_blank">{{ $offer->target_url }}</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
