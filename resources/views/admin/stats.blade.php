
@extends('layouts.app')
@section('content')
<h2>Статистика системы</h2>
<ul>
  <li>Общий доход: {{ $revenue }}</li>
  <li>Отказы: {{ $rejections }}</li>
</ul>
@endsection
