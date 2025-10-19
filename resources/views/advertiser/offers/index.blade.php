
@extends('layouts.app')
@section('content')
<h2>Мои офферы</h2><a class="btn btn-primary" href="{{ route('advertiser.offers.create') }}">Создать</a><table class="table mt-3"><thead><tr><th>Имя</th><th>Цена</th><th>Подписчики</th><th>Действия</th></tr></thead><tbody>@foreach($offers as $offer)<tr><td>{{ $offer->name }}</td><td>{{ $offer->price }}</td><td>{{ $offer->webmasters_count }}</td><td><a class="btn btn-sm btn-info" href="{{ route('advertiser.offers.stats',$offer->id) }}">Статистика</a></td></tr>@endforeach</tbody></table>@endsection
