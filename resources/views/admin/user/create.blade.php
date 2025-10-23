@extends('admin.layouts.app')
@section('title')
    @lang('app.users')
@endsection
@section('content')
    <div class="row justify-content-center">
        <div class="col-sm-10 col-md-8 col-lg-6 col-xl-4">
            <div class="h3 mb-3">
                <a href="{{ route('admin.users.index') }}"><i class="bi-arrow-left-circle"></i></a>
                @lang('app.users')
            </div>

            <form action="{{ route('admin.users.store') }}" method="post">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">
                        @lang('app.name') <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                           id="name" name="name" value="{{ old('name') }}" required autofocus>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="username" class="form-label fw-semibold">
                        @lang('app.username') <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control @error('username') is-invalid @enderror"
                           id="username" name="username" value="{{ old('username') }}" required>
                    @error('username')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">
                        @lang('app.password') <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control @error('password') is-invalid @enderror"
                           id="password" name="password" value="{{ old('password') }}" required>
                    @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="guards" class="form-label fw-semibold">
                        @lang('app.guards')
                    </label>
                    <select class="form-select @error('guards') is-invalid @enderror select2" id="guards" name="guards[]" multiple>
                        @foreach($guards as $guard)
                            <option value="{{ $guard['id'] }}">
                                {{ $guard['name'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('guards')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="permissions" class="form-label fw-semibold">
                        @lang('app.web'): @lang('app.permissions')
                    </label>
                    <select class="form-select @error('permissions') is-invalid @enderror select2" id="permissions" name="permissions[]" multiple>
                        @foreach($permissions as $permission)
                            <option value="{{ $permission['id'] }}">
                                {{ $permission['name'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('permissions')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="api_permissions" class="form-label fw-semibold">
                        @lang('app.api'): @lang('app.permissions')
                    </label>
                    <select class="form-select @error('api_permissions') is-invalid @enderror select2" id="api_permissions" name="api_permissions[]" multiple>
                        @foreach($api_permissions as $api_permission)
                            <option value="{{ $api_permission['id'] }}">
                                {{ $api_permission['name'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('api_permissions')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="card mb-3">
                    <div class="card-body">
                        @foreach($queries as $qKey => $qValues)
                            <div class="{{ $loop->last ? '' : 'mb-3' }}">
                                <label for="{{ $qKey }}" class="form-label fw-semibold">
                                    @lang('app.' . $qKey)
                                </label>
                                <select class="form-select @error($qKey) is-invalid @enderror select2" id="{{ $qKey }}" name="{{ $qKey }}[]" multiple>
                                    @foreach($qValues as $qValue)
                                        <option value="{{ $qValue['id'] }}">
                                            @lang('const.' . $qValue['name'])
                                        </option>
                                    @endforeach
                                </select>
                                @error($qKey)
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mb-3">
                    <label for="tasks" class="form-label fw-semibold">
                        @lang('app.tasks')
                    </label>
                    <select class="form-select @error('tasks') is-invalid @enderror select2" id="tasks" name="tasks[]" multiple>
                        @foreach($tasks as $task)
                            <option value="{{ $task->id }}">
                                {{ $task->getName() }}
                            </option>
                        @endforeach
                    </select>
                    @error('tasks')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary"><i class="bi-save"></i> @lang('app.save')</button>
            </form>
        </div>
    </div>
@endsection
