@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Хлебные крошки -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Админ-панель</a></li>
            <li class="breadcrumb-item active" aria-current="page">Офферы</li>
        </ol>
    </nav>

    <!-- Заголовок и кнопка -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Все офферы</h1>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
            ← Назад в админку
        </a>
    </div>

    <!-- Таблица офферов -->
    @if($offers->isEmpty())
        <div class="text-center py-5">
            <i class="fas fa-box-open text-muted" style="font-size: 3rem;"></i>
            <h6 class="mt-3 text-muted">Нет доступных офферов</h6>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
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
                        <td class="text-center" style="width: 60px;"><strong>{{ $offer->id }}</strong></td>
                        <td>{{ $offer->name }}</td>
                        <td>{{ $offer->advertiser->name }}</td>
                        <td class="text-center"><strong>{{ number_format($offer->price, 2) }} ₽</strong></td>
                        <td class="text-center">
                            <span class="badge bg-{{ $offer->active ? 'success' : 'secondary' }}">
                                {{ $offer->active ? 'Активен' : 'Неактивен' }}
                            </span>
                        </td>
                        <td>
                            @if($offer->themes && count($offer->themes) > 0)
                                {{ implode(', ', $offer->themes) }}
                            @else
                                <em class="text-muted">— нет тем</em>
                            @endif
                        </td>
                        <td class="text-break" style="max-width: 200px;">
                            <a href="{{ $offer->target_url }}" target="_blank" class="text-decoration-none text-primary small">
                                {{ Str::limit($offer->target_url, 50) }}
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
