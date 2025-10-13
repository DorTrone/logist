@extends('admin.layouts.app')
@section('title')
    @lang('app.configs')
@endsection
@section('content')
    <div class="row justify-content-center">
        <div class="col-sm-10 col-md-8 col-lg-6 col-xl-4">
            <div class="h3 mb-3">@lang('app.configs')</div>

            <form action="{{ route('admin.configs.update', $obj->id) }}" method="post" enctype="multipart/form-data">
                {{ method_field('PUT') }}
                @csrf

                <div class="mb-3">
                    <label for="android_version" class="form-label fw-semibold">
                        @lang('app.androidVersion') <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control @error('android_version') is-invalid @enderror"
                           id="android_version" name="android_version" value="{{ $obj->android_version }}" required autofocus>
                    @error('android_version')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="ios_version" class="form-label fw-semibold">
                        @lang('app.iosVersion') <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control @error('ios_version') is-invalid @enderror"
                           id="ios_version" name="ios_version" value="{{ $obj->ios_version }}" required>
                    @error('ios_version')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="szd_customer" class="form-label fw-semibold">
                        SZD Customer
                        <span class="font-monospace text-secondary">.apk</span>
                    </label>
                    <input type="file" accept=".apk" class="form-control @error('szd_customer') is-invalid @enderror" id="szd_customer" name="szd_customer">
                    @error('szd_customer')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="szd_employee" class="form-label fw-semibold">
                        SZD Employee
                        <span class="font-monospace text-secondary">.apk</span>
                    </label>
                    <input type="file" accept=".apk" class="form-control @error('szd_employee') is-invalid @enderror" id="szd_employee" name="szd_employee">
                    @error('szd_employee')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary"><i class="bi-arrow-repeat"></i> @lang('app.update')</button>
            </form>
        </div>
    </div>
@endsection
