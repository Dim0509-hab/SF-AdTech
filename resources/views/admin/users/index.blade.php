
@extends('layouts.app')
@section('content')
<h2>Пользователи</h2>
<table class="table"><thead><tr><th>ID</th><th>Имя</th><th>Email</th><th>Роль</th><th>Active</th><th>Действие</th></tr></thead><tbody>@foreach($users as $u)<tr><td>{{ $u->id }}</td><td>{{ $u->name }}</td><td>{{ $u->email }}</td><td>{{ $u->role }}</td><td>{{ $u->active }}</td><td><form method="POST" action="{{ route('admin.users.toggle',$u->id) }}">@csrf<button class="btn btn-sm btn-warning">Toggle</button></form></td></tr>@endforeach</tbody></table>
@endsection
