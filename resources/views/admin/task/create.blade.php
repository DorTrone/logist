@extends('admin.layouts.app')
@section('title')
    @lang('app.tasks')
@endsection
@section('content')
    <div class="row justify-content-center">
        <div class="col-sm-10 col-md-8 col-lg-6 col-xl-4">
            <div class="h3 mb-3">
                <a href="{{ route('admin.tasks.index') }}"><i class="bi-arrow-left-circle"></i></a>
                @lang('app.tasks')
            </div>

            <form action="{{ route('admin.tasks.store') }}" method="post">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">
                        <span class="text-primary">EN</span>
                        @lang('app.name') <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                           id="name" name="name" value="{{ old('name') }}" required autofocus>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="name_tm" class="form-label fw-semibold">
                        <span class="text-primary">TM</span>
                        @lang('app.name')
                    </label>
                    <input type="text" class="form-control @error('name_tm') is-invalid @enderror"
                           id="name_tm" name="name_tm" value="{{ old('name_tm') }}">
                    @error('name_tm')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="name_ru" class="form-label fw-semibold">
                        <span class="text-primary">RU</span>
                        @lang('app.name')
                    </label>
                    <input type="text" class="form-control @error('name_ru') is-invalid @enderror"
                           id="name_ru" name="name_ru" value="{{ old('name_ru') }}">
                    @error('name_ru')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="name_cn" class="form-label fw-semibold">
                        <span class="text-primary">CN</span>
                        @lang('app.name')
                    </label>
                    <input type="text" class="form-control @error('name_cn') is-invalid @enderror"
                           id="name_cn" name="name_cn" value="{{ old('name_cn') }}">
                    @error('name_cn')
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

                <button type="submit" class="btn btn-primary"><i class="bi-save"></i> @lang('app.save')</button>
            </form>
        </div>
    </div>
@endsection
