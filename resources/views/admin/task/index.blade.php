@extends('admin.layouts.app')
@section('title')
    @lang('app.tasks')
@endsection
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="h3 mb-0">@lang('app.tasks')</div>
        <a href="{{ route('admin.tasks.create') }}" class="btn btn-primary btn-sm"><i class="bi-plus-lg"></i> @lang('app.add')</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover table-sm">
            <thead>
            <tr class="small">
                <th>@lang('app.id')</th>
                <th><span class="text-primary">EN</span> @lang('app.name')</th>
                <th><span class="text-primary">TM</span> @lang('app.name')</th>
                <th><span class="text-primary">RU</span> @lang('app.name')</th>
                <th><span class="text-primary">CN</span> @lang('app.name')</th>
                <th>@lang('app.queries')</th>
                <th style="width:20%">@lang('app.users')</th>
                <th><i class="bi-gear-fill"></i></th>
            </tr>
            </thead>
            <tbody>
            @foreach($objs as $obj)
                <tr>
                    <td class="text-secondary">{{ $obj->id }}</td>
                    <td>{{ $obj->name }}</td>
                    <td>{{ $obj->name_tm }}</td>
                    <td>{{ $obj->name_ru }}</td>
                    <td>{{ $obj->name_cn }}</td>
                    <td>
                        @foreach($queries as $qKey => $qValues)
                            @if(count($obj->queries[$qKey] ?? []) > 0)
                                <div class="small text-secondary">@lang('app.' . $qKey)</div>
                                @foreach($qValues as $qValue)
                                    @if(in_array($qValue['id'], $obj->queries[$qKey] ?? []))
                                        <span class="badge bg-success-subtle text-success-emphasis">
                                            @lang('const.' . $qValue['name'])
                                        </span>
                                    @endif
                                @endforeach
                            @endif
                        @endforeach
                    </td>
                    <td>
                        @foreach($obj->users as $user)
                            <span class="badge bg-primary-subtle text-primary-emphasis">{{ $user->getName() }}</span>
                        @endforeach
                    </td>
                    <td>
                        <a href="{{ route('admin.tasks.edit', $obj->id) }}" class="btn btn-success btn-sm mb-1">
                            <i class="bi-pencil"></i>
                        </a>
                        <button type="button" class="btn btn-dark btn-sm mb-1" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $obj->id }}">
                            <i class="bi-trash"></i>
                        </button>
                        <div class="modal fade" id="deleteModal{{ $obj->id }}" tabindex="-1" aria-labelledby="deleteModal{{ $obj->id }}Label" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <div class="modal-title fs-5" id="deleteModal{{ $obj->id }}Label">@lang('app.delete')</div>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        {{ $obj->getName() }}
                                    </div>
                                    <div class="modal-footer">
                                        <form action="{{ route('admin.tasks.destroy', $obj->id) }}" method="post">
                                            @method('DELETE')
                                            @csrf
                                            <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal"><i class="bi-x-lg"></i> @lang('app.close')</button>
                                            <button type="submit" class="btn btn-dark btn-sm"><i class="bi-trash"></i> @lang('app.delete')</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
