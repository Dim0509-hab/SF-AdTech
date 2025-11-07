
@extends('layouts.app')
@section('content')
<h2>Ваша ссылка</h2><pre>{{ $link }}</pre>
<!-- Ссылка назад -->
    <a href="{{ route('webmaster.offers') }}" class="btn btn-secondary">
        ← К списку офферов
    </a>
@endsection


