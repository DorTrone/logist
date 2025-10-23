@extends('admin.layouts.app')
@section('title')
    @lang('app.notifications')
@endsection
@section('content')
    <div class="row justify-content-center">
        <div class="col-sm-10 col-md-8 col-lg-6 col-xl-4">
            <div class="h3 mb-3">
                <a href="{{ route('admin.notifications.index') }}"><i class="bi-arrow-left-circle"></i></a>
                @lang('app.notifications')
            </div>

            <form action="{{ route('admin.notifications.store') }}" method="post">
                @csrf

                <div class="mb-3">
                    <label for="title" class="form-label fw-semibold">
                        @lang('app.title') <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                           id="title" name="title" value="{{ old('title') }}" required autofocus>
                    @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="body" class="form-label fw-semibold">
                        @lang('app.body') <span class="text-danger">*</span>
                    </label>
                    <textarea maxlength="255" rows="3" class="form-control @error('body') is-invalid @enderror"
                              id="body" name="body" required>{{ old('body') }}</textarea>
                    @error('body')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="datetime" class="form-label fw-semibold">
                        @lang('app.dateTime') <span class="text-danger">*</span>
                    </label>
                    <input type="datetime-local" class="form-control @error('datetime') is-invalid @enderror"
                           id="datetime" name="datetime" value="{{ today() }}" required>
                    @error('datetime')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary"><i class="bi-save"></i> @lang('app.save')</button>
            </form>
        </div>
    </div>
@endsection
