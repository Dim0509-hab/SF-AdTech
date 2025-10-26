@extends('layouts.app')
@section('content')
<h2>Создать оффер</h2>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('advertiser.offers.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
        <label for="name" class="form-label">Название оффера</label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="target_url" class="form-label">Целевой URL</label>
        <input type="url" class="form-control @error('target_url') is-invalid @enderror" id="target_url" name="target_url" value="{{ old('target_url') }}">
        @error('target_url')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="price" class="form-label">Цена за переход</label>
        <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price') }}">
        @error('price')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="themes">Темы оффера</label>
        <select multiple class="form-select" id="themes" name="themes[]">
            <option value="theme1">Тема 1</option>
            <option value="theme2">Тема 2</option>
            <option value="theme3">Тема 3</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Создать оффер</button>
</form>
@endsection
