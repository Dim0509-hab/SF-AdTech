@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Хлебные крошки -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Админ-панель</a></li>
            <li class="breadcrumb-item active" aria-current="page">Пользователи</li>
        </ol>
    </nav>

    <!-- Заголовок и кнопки -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Все пользователи</h1>
        <div>
            <a href="{{ route('admin.revenue.index') }}" class="btn btn-outline-primary btn-sm me-2">
                <i class="fas fa-chart-line"></i> Статистика
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                ← Назад в админку
            </a>
        </div>
    </div>

    <!-- Таблица пользователей -->
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Имя</th>
                <th>Email</th>
                <th>Роль</th>
                <th>Статус</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    <form action="{{ route('admin.users.role', $user->id) }}" method="POST" class="d-inline">
                        @csrf
                        <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="advertiser" {{ $user->role === 'advertiser' ? 'selected' : '' }}>Рекламодатель</option>
                            <option value="webmaster" {{ $user->role === 'webmaster' ? 'selected' : '' }}>Веб-мастер</option>
                        </select>
                    </form>
                </td>
                <td>
                    <span class="badge bg-{{ $user->active ? 'success' : 'danger' }}">
                        {{ $user->active ? 'Активен' : 'Заблокирован' }}
                    </span>
                </td>
                <td class="text-nowrap">
                    <!-- Кнопка активации/блокировки -->
                    <form method="POST" action="{{ route('admin.users.toggle', $user->id) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-{{ $user->active ? 'danger' : 'success' }}">
                            {{ $user->active ? 'Заблокировать' : 'Активировать' }}
                        </button>
                    </form>

                    <!-- Кнопка удаления с подтверждением -->
                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal{{ $user->id }}">
                        Удалить
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Модальные окна удаления (вне таблицы, в конце) -->
@foreach($users as $user)
<div class="modal fade" id="deleteUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="deleteUserLabel{{ $user->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteUserLabel{{ $user->id }}">Удалить пользователя</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                Вы уверены, что хотите удалить пользователя <strong>{{ $user->email }}</strong>?<br>
                Это действие <strong>необратимо</strong>.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Удалить</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection
