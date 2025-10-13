@extends('admin.layouts.app')
@section('title')
    @lang('app.customers')
@endsection
@section('content')
    <div class="row justify-content-center">
        <div class="col-sm-10 col-md-8 col-lg-6 col-xl-4">
            <div class="h3 mb-3">
                <a href="{{ route('admin.customers.index') }}"><i class="bi-arrow-left-circle"></i></a>
                @lang('app.customers')
            </div>

            <form action="{{ route('admin.customers.store') }}" method="post">
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
                    <label for="surname" class="form-label fw-semibold">
                        @lang('app.surname') <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control @error('surname') is-invalid @enderror"
                           id="surname" name="surname" value="{{ old('surname') }}" required>
                    @error('surname')
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
                    <label for="note" class="form-label fw-semibold">
                        @lang('app.note')
                    </label>
                    <input type="text" class="form-control @error('note') is-invalid @enderror"
                           id="note" name="note" value="{{ old('note') }}">
                    @error('note')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary"><i class="bi-save"></i> @lang('app.save')</button>
            </form>
        </div>
    </div>
@endsection
