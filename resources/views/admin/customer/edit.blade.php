@extends('admin.layouts.app')
@section('title')
    @lang('app.customers')
@endsection
@section('content')
    <div class="row justify-content-center">
        <div class="col-sm-10 col-md-8 col-lg-6 col-xl-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="h3 mb-0">
                    <a href="{{ route('admin.customers.index') }}"><i class="bi-arrow-left-circle"></i></a>
                    @lang('app.customers')
                </div>
                <div>
                    <button type="button" class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $obj->id }}">
                        <i class="bi-trash"></i> @lang('app.delete')
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
                                    <form action="{{ route('admin.customers.destroy', $obj->id) }}" method="post">
                                        @method('DELETE')
                                        @csrf
                                        <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal"><i class="bi-x-lg"></i> @lang('app.close')</button>
                                        <button type="submit" class="btn btn-dark btn-sm"><i class="bi-trash"></i> @lang('app.delete')</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.customers.update', $obj->id) }}" method="post">
                {{ method_field('PUT') }}
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">
                        @lang('app.name') <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                           id="name" name="name" value="{{ $obj->name }}" required autofocus>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="surname" class="form-label fw-semibold">
                        @lang('app.surname') <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control @error('surname') is-invalid @enderror"
                           id="surname" name="surname" value="{{ $obj->surname }}" required>
                    @error('surname')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                @if($obj->auth_method == 2)
                    <div class="mb-3">
                        <label for="username" class="form-label fw-semibold">
                            @lang('app.username') <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror"
                               id="username" name="username" value="{{ $obj->username }}" required>
                        @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @endif

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">
                        @lang('app.password')
                    </label>
                    <input type="text" class="form-control @error('password') is-invalid @enderror"
                           id="password" name="password" value="{{ old('password') }}">
                    @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="note" class="form-label fw-semibold">
                        @lang('app.note')
                    </label>
                    <input type="text" class="form-control @error('note') is-invalid @enderror"
                           id="note" name="note" value="{{ $obj->note }}">
                    @error('note')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary"><i class="bi-save"></i> @lang('app.update')</button>
            </form>
        </div>
    </div>
@endsection
