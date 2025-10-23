@extends('admin.layouts.app')
@section('title')
    @lang('app.banners')
@endsection
@section('content')
    <div class="h3 mb-3">
        <a href="{{ route('admin.banners.index') }}"><i class="bi-arrow-left-circle"></i></a>
        @lang('app.banners')
    </div>

    <form action="{{ route('admin.banners.store') }}" method="post" enctype="multipart/form-data">
        @csrf

        <div class="row row-cols-2 row-cols-md-4 g-3 mb-3">
            @foreach(['image' => null, 'image_tm' => 'tm', 'image_ru' => 'ru', 'image_cn' => 'cn'] as $name => $lang)
                <div class="col">
                    <label for="{{ $name }}" class="form-label fw-semibold">
                        @lang('app.image')
                        @if($loop->first)
                            <span class="text-danger">*</span>
                        @endif
                        @if(isset($lang))
                            <span class="text-primary">{{ str($lang)->upper() }}</span>
                        @endif
                        <span class="font-monospace text-secondary">400x400</span>
                    </label>
                    <div class="mb-2">
                        <img src="{{ asset('img/400x400.png') }}" alt="@lang('app.image')" class="img-fluid border">
                    </div>
                    <input type="file" accept="image/*" class="form-control @error($name) is-invalid @enderror" id="{{ $name }}" name="{{ $name }}" {{ $loop->first ? 'required autofocus' : '' }}>
                    @error($name)
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <script>
                        document.getElementById('{{ $name }}').addEventListener('change', function () {
                            let input = this;
                            if (input.files && input.files[0]) {
                                let reader = new FileReader();
                                reader.addEventListener('load', function (e) {
                                    input.previousElementSibling.firstElementChild.setAttribute('src', e.target.result);
                                });
                                reader.readAsDataURL(input.files[0]);
                            } else {
                                input.previousElementSibling.firstElementChild.setAttribute('src', "{{ asset('img/400x400.png') }}");
                            }
                        });
                    </script>
                </div>
            @endforeach
        </div>

        <div class="row row-cols-2 row-cols-md-4 g-3 mb-3">
            @foreach(['image_2' => null, 'image_2_tm' => 'tm', 'image_2_ru' => 'ru', 'image_2_cn' => 'cn'] as $name => $lang)
                <div class="col">
                    <label for="{{ $name }}" class="form-label fw-semibold">
                        @lang('app.image')
                        @if(isset($lang))
                            <span class="text-primary">{{ str($lang)->upper() }}</span>
                        @endif
                        <span class="font-monospace text-secondary">800xHEIGHT</span>
                    </label>
                    <div class="mb-2">
                        <img src="{{ asset('img/400x400.png') }}" alt="@lang('app.image')" class="img-fluid border">
                    </div>
                    <input type="file" accept="image/*" class="form-control @error($name) is-invalid @enderror" id="{{ $name }}" name="{{ $name }}">
                    @error($name)
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <script>
                        document.getElementById('{{ $name }}').addEventListener('change', function () {
                            let input = this;
                            if (input.files && input.files[0]) {
                                let reader = new FileReader();
                                reader.addEventListener('load', function (e) {
                                    input.previousElementSibling.firstElementChild.setAttribute('src', e.target.result);
                                });
                                reader.readAsDataURL(input.files[0]);
                            } else {
                                input.previousElementSibling.firstElementChild.setAttribute('src', "{{ asset('img/400x400.png') }}");
                            }
                        });
                    </script>
                </div>
            @endforeach
        </div>

        <div class="row row-cols-2 row-cols-md-4 g-3 mb-3">
            <div class="col">
                <label for="datetime_start" class="form-label fw-semibold">
                    @lang('app.dateTimeStart') <span class="text-danger">*</span>
                </label>
                <input type="datetime-local" class="form-control @error('datetime_start') is-invalid @enderror"
                       id="datetime_start" name="datetime_start" value="{{ today() }}" required>
                @error('datetime_start')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col">
                <label for="datetime_end" class="form-label fw-semibold">
                    @lang('app.dateTimeEnd') <span class="text-danger">*</span>
                </label>
                <input type="datetime-local" class="form-control @error('datetime_end') is-invalid @enderror"
                       id="datetime_end" name="datetime_end" value="{{ today()->addYear() }}" required>
                @error('datetime_end')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col">
                <label for="url" class="form-label fw-semibold">
                    @lang('app.url')
                </label>
                <input type="text" class="form-control @error('url') is-invalid @enderror"
                       id="url" name="url" value="{{ old('url') }}">
                @error('url')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col">
                <label for="sort_order" class="form-label fw-semibold">
                    @lang('app.sortOrder') <span class="text-danger">*</span>
                </label>
                <input type="number" min="1" class="form-control @error('sort_order') is-invalid @enderror"
                       id="sort_order" name="sort_order" value="1" required>
                @error('sort_order')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <button type="submit" class="btn btn-primary"><i class="bi-save"></i> @lang('app.save')</button>
    </form>
@endsection
