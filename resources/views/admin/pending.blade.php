@extends('layouts.app')

@section('title', 'Модерация пользователей')

@section('content')
<div class="container-fluid py-4">
    <!-- Хлебные крошки -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Админ-панель</a></li>
            <li class="breadcrumb-item active" aria-current="page">На модерации</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5>🔐 Пользователи на модерации</h5>
                            <p class="text-sm mb-0">Одобрьте или отклоните новых рекламодателей и веб-мастеров</p>
                        </div>
                        <!-- Кнопка "Назад" -->
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm mt-2">
                            ← Назад в админку
                        </a>
                    </div>
                </div>

                <div class="card-body px-0 pt-0 pb-2">
                    @if(session('success'))
                        <div class="alert alert-success mx-4">{{ session('success') }}</div>
                    @endif
                    @if(session('info'))
                        <div class="alert alert-info mx-4">{{ session('info') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger mx-4">{{ session('error') }}</div>
                    @endif

                    @if($pendingUsers->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
                            <h6 class="mt-3 text-muted">Нет пользователей на модерации</h6>
                        </div>
                    @else
                        <div class="table-responsive p-4">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">ID</th>
                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Имя</th>
                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Email</th>
                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Роль</th>
                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Дата</th>
                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingUsers as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <span class="text-xs font-weight-bold">{{ $user->id }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $user->name }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs text-secondary mb-0">{{ $user->email }}</p>
                                        </td>
                                        <td>
                                            <span class="badge bg-gradient-info">{{ ucfirst($user->role) }}</span>
                                        </td>
                                        <td>
                                            <span class="text-xs text-secondary mb-0">
                                                {{ $user->created_at ? $user->created_at->format('d.m') : '—' }}
                                            </span>
                                        </td>
                                        <td class="align-middle">
                                            <form action="{{ route('admin.approve', $user->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Одобрить пользователя {{ $user->name }}?')">
                                                    <i class="fas fa-check"></i> Одобрить
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.reject', $user->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Отклонить пользователя {{ $user->name }}?')">
                                                    <i class="fas fa-times"></i> Отклонить
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
