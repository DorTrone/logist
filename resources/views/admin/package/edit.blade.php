@extends('admin.layouts.app')
@section('title')
    @lang('app.packages')
@endsection
@section('content')
    <div class="row justify-content-center">
        <div class="col-sm-10 col-md-8 col-lg-6 col-xl-4">
            <div class="h3 mb-3">
                <a href="{{ route('admin.packages.index') }}"><i class="bi-arrow-left-circle"></i></a>
                @lang('app.packages')
            </div>

            <form action="{{ route('admin.packages.update', $obj->id) }}" method="post">
                {{ method_field('PUT') }}
                @csrf

                <div class="mb-3">
                    <label for="barcode" class="form-label fw-semibold">
                        @lang('app.barcode') <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control @error('barcode') is-invalid @enderror"
                           id="barcode" name="barcode" value="{{ $obj->barcode }}" required autofocus>
                    @error('barcode')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="customer" class="form-label fw-semibold">
                        @lang('app.customer') <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">SZD</span>
                        <input type="number" min="1001" class="form-control @error('customer') is-invalid @enderror"
                               id="customer" name="customer" value="{{ $obj->customer_id + 1000 }}" required>
                    </div>
                    @error('customer')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="location" class="form-label fw-semibold">
                        @lang('app.location') <span class="text-danger">*</span>
                    </label>
                    <select class="form-select @error('location') is-invalid @enderror select2" id="location" name="location" required>
                        @foreach($queries['locations'] as $qValue)
                            <option value="{{ $qValue['id'] }}" {{ $qValue['id'] == $obj->location ? 'selected':'' }}>
                                @lang('const.' . $qValue['name'])
                            </option>
                        @endforeach
                    </select>
                    @error('location')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="packageType" class="form-label fw-semibold">
                        @lang('app.packageType') <span class="text-danger">*</span>
                    </label>
                    <select class="form-select @error('packageType') is-invalid @enderror select2" id="packageType" name="packageType" required>
                        @foreach($queries['packageTypes'] as $qValue)
                            <option value="{{ $qValue['id'] }}" {{ $qValue['id'] == $obj->type ? 'selected':'' }}>
                                @lang('const.' . $qValue['name'])
                            </option>
                        @endforeach
                    </select>
                    @error('packageType')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="weight" class="form-label fw-semibold">
                        @lang('app.weight') <span class="text-danger">*</span>
                    </label>
                    <input type="number" min="0" step="0.01" class="form-control @error('weight') is-invalid @enderror"
                           id="weight" name="weight" value="{{ $obj->weight }}" required>
                    @error('weight')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary"><i class="bi-save"></i> @lang('app.update')</button>
            </form>
        </div>
    </div>
@endsection
