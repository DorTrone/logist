@extends('admin.layouts.app')
@section('title')
    @lang('app.notifications')
@endsection
@section('content')
    <div class="row justify-content-center">
        <div class="col-sm-10 col-md-8 col-lg-6 col-xl-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="h3 mb-0">
                    <a href="{{ route('admin.notifications.index') }}"><i class="bi-arrow-left-circle"></i></a>
                    @lang('app.notifications')
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
                                    <form action="{{ route('admin.notifications.destroy', $obj->id) }}" method="post">
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

            <form action="{{ route('admin.notifications.update', $obj->id) }}" method="post">
                {{ method_field('PUT') }}
                @csrf

                <div class="mb-3">
                    <label for="title" class="form-label fw-semibold">
                        @lang('app.title') <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                           id="title" name="title" value="{{ $obj->title }}" required autofocus>
                    @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="body" class="form-label fw-semibold">
                        @lang('app.body') <span class="text-danger">*</span>
                    </label>
                    <textarea maxlength="255" rows="3" class="form-control @error('body') is-invalid @enderror"
                              id="body" name="body" required>{{ $obj->body }}</textarea>
                    @error('body')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="datetime" class="form-label fw-semibold">
                        @lang('app.dateTime') <span class="text-danger">*</span>
                    </label>
                    <input type="datetime-local" class="form-control @error('datetime') is-invalid @enderror"
                           id="datetime" name="datetime" value="{{ $obj->datetime }}" required>
                    @error('datetime')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary"><i class="bi-arrow-repeat"></i> @lang('app.update')</button>
            </form>
        </div>
    </div>
@endsection
