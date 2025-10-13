@extends('admin.layouts.app')

@section('content')
<h1>Добавить склад</h1>

<form action="{{ route('admin.warehouses.store') }}" method="POST">
    @csrf
    <label>Название:</label>
    <input type="text" name="name" class="form-control">

    <label>Телефон:</label>
    <input type="text" name="phone" class="form-control">

    <label>Адрес:</label>
    <input type="text" name="address" class="form-control">

    <label>Почтовый индекс:</label>
    <input type="text" name="postal_code" class="form-control">

    <label>Meta (JSON):</label>
    <textarea name="meta" class="form-control"></textarea>

    <button type="submit" class="btn btn-success mt-3">Сохранить</button>
</form>
@endsection
