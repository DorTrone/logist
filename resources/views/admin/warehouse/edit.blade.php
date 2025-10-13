@extends('admin.layouts.app')

@section('content')
<h1>Редактировать склад</h1>

<form action="{{ route('admin.warehouses.update', $warehouse) }}" method="POST">
    @csrf
    @method('PUT')
    <label>Название:</label>
    <input type="text" name="name" class="form-control" value="{{ $warehouse->name }}">

    <label>Телефон:</label>
    <input type="text" name="phone" class="form-control" value="{{ $warehouse->phone }}">

    <label>Адрес:</label>
    <input type="text" name="address" class="form-control" value="{{ $warehouse->address }}">

    <label>Почтовый индекс:</label>
    <input type="text" name="postal_code" class="form-control" value="{{ $warehouse->postal_code }}">

    <label>Meta (JSON):</label>
    <textarea name="meta" class="form-control">{{ $warehouse->meta }}</textarea>

    <button type="submit" class="btn btn-success mt-3">Обновить</button>
</form>
@endsection
