@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Мои офферы</h2>
        <a class="btn btn-primary" href="{{ route('advertiser.create') }}">Создать оффер</a>
    </div>

    @if($offers->isEmpty())
        <div class="alert alert-info">
            Офферов пока нет. <a href="{{ route('advertiser.create') }}">Создайте первый</a>.
        </div>
    @else
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Название</th>
                    <th>Цена</th>
                    <th>Подписчики</th>
                    <th>Статус</th>
                    <th class="text-end">Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach($offers as $offer)
                    <tr>
                        <td>{{ $offer->name }}</td>
                        <td>{{ number_format($offer->price, 2) }} ₽</td>
                        <td>{{ $offer->webmasters_count }}</td>
                        <td>
                            @if($offer->active)
                                <span class="badge bg-success">Активен</span>
                            @else
                                <span class="badge bg-secondary">Деактивирован</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group" role="group">
                                <a class="btn btn-sm btn-outline-info"
                                   href="{{ route('advertiser.stats', $offer->id) }}"
                                   title="Статистика">
                                    <i class="bi bi-graph-up"></i>
                                </a>

                                @if($offer->active)
                                    <form action="{{ route('advertiser.offers.deactivate', $offer->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Вы уверены, что хотите деактивировать оффер?')">
                                        @csrf
                                        <button type="submit"
                                                class="btn btn-sm btn-outline-warning"
                                                title="Деактивировать">
                                            <i class="bi bi-power"></i>
                                        </button>
                                    </form>
                                @endif

                                @if(!$offer->active)
                                    <form action="{{ route('advertiser.offers.activate', $offer->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Вы уверены, что хотите активировать оффер?')">
                                        @csrf
                                        <button type="submit"
                                                class="btn btn-sm btn-outline-success"
                                                title="Активировать">
                                            <i class="bi bi-play-fill"></i> Активировать
                                        </button>
                                    </form>
                                 @endif


                                <form action="{{ route('advertiser.offers.destroy', $offer->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Вы уверены, что хотите удалить оффер? Это действие необратимо.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Удалить">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
