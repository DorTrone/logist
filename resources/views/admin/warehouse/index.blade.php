@extends('admin.layouts.app')

@section('content')
<h1>Склады</h1>

<a href="{{ route('admin.warehouses.create') }}" class="btn btn-primary">Добавить склад</a>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Название</th>
            <th>Телефон</th>
            <th>Адрес</th>
            <th>Индекс</th>
            <th>Действия</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($warehouses as $warehouse)
        <tr>
            <td>{{ $warehouse->id }}</td>
            <td>{{ $warehouse->name }}</td>
            <td>{{ $warehouse->phone }}</td>
            <td>{{ $warehouse->address }}</td>
            <td>{{ $warehouse->postal_code }}</td>
            <td>
                <a href="{{ route('admin.warehouses.edit', $warehouse) }}" class="btn btn-sm btn-warning">Редактировать</a>
                <form action="{{ route('admin.warehouses.destroy', $warehouse) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('Удалить склад?')">Удалить</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
