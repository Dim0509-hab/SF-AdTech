@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Все пользователи</h1>
    <table class="table">
    <thead>
        <tr>
            <th>Email</th>
            <th>Роль</th>
            <th>Статус</th>
            <th>Действия</th>
        </tr>
    </thead>
    <tbody>
        <a href="{{ route('admin.revenue.index') }}" class="btn btn-primary">
    <i class="fas fa-chart-line"></i> Вывод статистики
    </a>

        @foreach($users as $user)
            <tr>
                <td>{{ $user->email }}</td>
                <td>
                    <form action="{{ route('admin.users.role', $user->id) }}" method="POST">
                        @csrf
                        <select name="role" onchange="this.form.submit()">
                            <option value="advertiser" {{ $user->role === 'advertiser' ? 'selected' : '' }}>Рекламодатель</option>
                            <option value="webmaster" {{ $user->role === 'webmaster' ? 'selected' : '' }}>Веб‑мастер</option>
                        </select>
                    </form>
                </td>
                <td>{{ $user->active ? 'Активен' : 'Заблокирован' }}</td>
                    <td>
                    <form method="POST" action="{{ route('admin.users.toggle', $user->id) }}">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-{{ $user->is_active ? 'danger' : 'success' }}">
                            {{ $user->active ? 'Заблокировать' : 'Активировать' }}
                        </button>
                    </form>
                    <!-- Кнопка «Удалить» с модальным подтверждением -->
                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal{{ $user->id }}">
                            Удалить
                        </button>

                </td>
            </tr>
        @endforeach
    </tbody>
    </table>

    </div>
    @foreach($users as $user)
        <!-- Модальное окно для удаления пользователя -->
        <div class="modal fade" id="deleteUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="deleteUserLabel{{ $user->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteUserLabel{{ $user->id }}">Удалить пользователя</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Вы уверены, что хотите удалить пользователя {{ $user->email }}?
                        Это действие необратимо.
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
