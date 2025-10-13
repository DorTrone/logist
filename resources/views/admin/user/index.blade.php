@extends('admin.layouts.app')
@section('title')
    @lang('app.users')
@endsection
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="h3 mb-0">@lang('app.users')</div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm"><i class="bi-plus-lg"></i> @lang('app.add')</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover table-sm">
            <thead>
            <tr class="small">
                <th>@lang('app.id')</th>
                <th>@lang('app.platform')</th>
                <th>@lang('app.language')</th>
                <th>@lang('app.name')</th>
                <th>@lang('app.username')</th>
                <th>@lang('app.lastSeen')</th>
                <th>@lang('app.guards')</th>
                <th style="width:15%">@lang('app.web'): @lang('app.permissions')</th>
                <th style="width:15%">@lang('app.api'): @lang('app.permissions')</th>
                <th>@lang('app.queries')</th>
                <th style="width:20%">@lang('app.tasks')</th>
                <th><i class="bi-gear-fill"></i></th>
            </tr>
            </thead>
            <tbody>
            @foreach($objs as $obj)
                <tr>
                    <td class="text-secondary">{{ $obj->id }}</td>
                    <td><i class="bi-{{ $obj->platformIcon() }} text-secondary"></i> {{ $obj->platform() }}</td>
                    <td>{{ $obj->language() }}</td>
                    <td>{{ $obj->name }}</td>
                    <td>{{ $obj->username }}</td>
                    <td><a href="{{ route('admin.tokens.index', ['tokenType' => 0, 'tokenId' => $obj->id]) }}" class="link-dark text-decoration-none">{{ $obj->last_seen }}</a></td>
                    <td>
                        @foreach($guards as $guard)
                            @if(in_array($guard['id'], $obj->guards ?: []))
                                <span class="badge bg-danger-subtle text-danger-emphasis">{{ $guard['name'] }}</span>
                            @endif
                        @endforeach
                    </td>
                    <td>
                        @foreach($permissions as $permission)
                            @if(in_array($permission['id'], $obj->permissions ?: []))
                                <span class="badge bg-primary-subtle text-primary-emphasis">{{ $permission['name'] }}</span>
                            @endif
                        @endforeach
                    </td>
                    <td>
                        @foreach($api_permissions as $api_permission)
                            @if(in_array($api_permission['id'], $obj->api_permissions ?: []))
                                <span class="badge bg-primary-subtle text-primary-emphasis">{{ $api_permission['name'] }}</span>
                            @endif
                        @endforeach
                    </td>
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
                        @foreach($obj->tasks as $task)
                            <span class="badge bg-primary-subtle text-primary-emphasis">{{ $task->getName() }}</span>
                        @endforeach
                    </td>
                    <td>
                        <a href="{{ route('admin.users.edit', $obj->id) }}" class="btn btn-success btn-sm mb-1">
                            <i class="bi-pencil"></i>
                        </a>
                        @if($obj->id != 1)
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
                                            <form action="{{ route('admin.users.destroy', $obj->id) }}" method="post">
                                                @method('DELETE')
                                                @csrf
                                                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal"><i class="bi-x-lg"></i> @lang('app.close')</button>
                                                <button type="submit" class="btn btn-dark btn-sm"><i class="bi-trash"></i> @lang('app.delete')</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
