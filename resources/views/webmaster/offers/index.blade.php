
@extends('layouts.app')
@section('content')
<h2>Доступные офферы</h2><table class="table"><thead><tr><th>Имя</th><th>Цена</th><th>Действие</th></tr></thead><tbody>@foreach($offers as $offer)<tr><td>{{ $offer->name }}</td><td>{{ $offer->price }}</td><td>@if(in_array($offer->id,$subs))<form method="POST" action="{{ route('webmaster.offers.unsubscribe',$offer->id) }}">@csrf<button class="btn btn-warning btn-sm">Отписаться</button></form><a class="btn btn-secondary btn-sm" href="{{ route('webmaster.offers.link',$offer->id) }}">Ссылка</a>@else<form method="POST" action="{{ route('webmaster.offers.subscribe',$offer->id) }}">@csrf<button class="btn btn-success btn-sm">Подписаться</button></form>@endif</td></tr>@endforeach</tbody></table>@endsection
