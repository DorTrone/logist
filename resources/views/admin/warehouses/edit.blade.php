@extends('admin.layouts.app')
@section('title')
    @lang('app.editWarehouse')
@endsection
@section('content')
    <div class="row align-items-center mb-3">
        <div class="col-auto">
            <div class="h3 mb-0">@lang('app.editWarehouse')</div>
        </div>
        <div class="col text-end">
            <a href="{{ route('admin.warehouses.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> @lang('app.back')
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.warehouses.update', $warehouse) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="name" class="form-label">@lang('app.name') <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name', $warehouse->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">@lang('app.phone')</label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                           id="phone" name="phone" value="{{ old('phone', $warehouse->phone) }}">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">@lang('app.address')</label>
                    <textarea class="form-control @error('address') is-invalid @enderror" 
                              id="address" name="address" rows="3">{{ old('address', $warehouse->address) }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="postal_code" class="form-label">@lang('app.postalCode')</label>
                    <input type="text" class="form-control @error('postal_code') is-invalid @enderror" 
                           id="postal_code" name="postal_code" value="{{ old('postal_code', $warehouse->postal_code) }}">
                    @error('postal_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="meta" class="form-label">@lang('app.meta') (JSON)</label>
                    <textarea class="form-control @error('meta') is-invalid @enderror" 
                              id="meta" name="meta" rows="3">{{ old('meta', $warehouse->meta) }}</textarea>
                    @error('meta')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> @lang('app.save')
                </button>
                <a href="{{ route('admin.warehouses.index') }}" class="btn btn-secondary">
                    @lang('app.cancel')
                </a>
            </form>
        </div>
    </div>
@endsection