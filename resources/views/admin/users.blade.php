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
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

</div>
@endsection
