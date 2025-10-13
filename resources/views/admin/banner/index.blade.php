@extends('admin.layouts.app')
@section('title')
    @lang('app.banners')
@endsection
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="h3 mb-0">@lang('app.banners')</div>
        <a href="{{ route('admin.banners.create') }}" class="btn btn-primary btn-sm"><i class="bi-plus-lg"></i> @lang('app.add')</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover table-sm">
            <thead>
            <tr class="small">
                <th>@lang('app.id')</th>
                <th>@lang('app.sortOrder')</th>
                <th style="width:25%">@lang('app.image') <span class="font-monospace text-secondary">400x400</span></th>
                <th style="width:25%">@lang('app.image') <span class="font-monospace text-secondary">800xHEIGHT</span></th>
                <th>@lang('app.url')</th>
                <th>@lang('app.dateTimeStart')</th>
                <th>@lang('app.dateTimeEnd')</th>
                <th><i class="bi-gear-wide-connected"></i></th>
            </tr>
            </thead>
            <tbody>
            @foreach($objs as $obj)
                <tr>
                    <td>{{ $obj->id }}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-light btn-sm btn-down" value="{{ $obj->id }}"><i class="bi-dash-lg"></i></button>
                            <button type="button" class="btn btn-light btn-sm text-primary fw-bold">{{ $obj->sort_order }}</button>
                            <button type="button" class="btn btn-light btn-sm btn-up" value="{{ $obj->id }}"><i class="bi-plus-lg"></i></button>
                        </div>
                    </td>
                    <td>
                        <div class="row row-cols-2 g-2">
                            <div class="col">
                                <img src="{{ $obj->getImage(null, false) ?: asset('img/400x400.png') }}" alt="{{ $obj->image }}" class="img-fluid border">
                            </div>
                            <div class="col">
                                <img src="{{ $obj->getImage('tm', false) ?: asset('img/400x400.png') }}" alt="{{ $obj->image }}" class="img-fluid border">
                            </div>
                            <div class="col">
                                <img src="{{ $obj->getImage('ru', false) ?: asset('img/400x400.png') }}" alt="{{ $obj->image }}" class="img-fluid border">
                            </div>
                            <div class="col">
                                <img src="{{ $obj->getImage('cn', false) ?: asset('img/400x400.png') }}" alt="{{ $obj->image }}" class="img-fluid border">
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="row row-cols-2 g-2">
                            <div class="col">
                                <img src="{{ $obj->getImage2(null, false) ?: asset('img/400x400.png') }}" alt="{{ $obj->image }}" class="img-fluid border">
                            </div>
                            <div class="col">
                                <img src="{{ $obj->getImage2('tm', false) ?: asset('img/400x400.png') }}" alt="{{ $obj->image }}" class="img-fluid border">
                            </div>
                            <div class="col">
                                <img src="{{ $obj->getImage2('ru', false) ?: asset('img/400x400.png') }}" alt="{{ $obj->image }}" class="img-fluid border">
                            </div>
                            <div class="col">
                                <img src="{{ $obj->getImage2('cn', false) ?: asset('img/400x400.png') }}" alt="{{ $obj->image }}" class="img-fluid border">
                            </div>
                        </div>
                    </td>
                    <td class="text-primary">{{ $obj->url }}</td>
                    <td>{{ $obj->datetime_start->format('Y-m-d H:i:s') }}</td>
                    <td>{{ $obj->datetime_end->format('Y-m-d H:i:s') }}</td>
                    <td>
                        <a href="{{ route('admin.banners.edit', $obj->id) }}" class="btn btn-success btn-sm mb-1">
                            <i class="bi-pencil"></i>
                        </a>
                        <button type="button" class="btn btn-dark btn-sm mb-1" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $obj->id }}">
                            <i class="bi-trash"></i>
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
                                        <form action="{{ route('admin.banners.destroy', $obj->id) }}" method="post">
                                            @method('DELETE')
                                            @csrf
                                            <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal"><i class="bi-x-lg"></i> @lang('app.close')</button>
                                            <button type="submit" class="btn btn-dark btn-sm"><i class="bi-trash"></i> @lang('app.delete')</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <script>
        $('.btn-up').click(function () {
            let self = $(this);
            self.attr("disabled", true);
            $.ajax({
                url: "{{ route("admin.banners.up") }}",
                dataType: "json",
                type: "POST",
                data: {"_token": "{{ csrf_token() }}", "id": self.val()},
                success: function (result, status, xhr) {
                    self.attr("disabled", false);
                    self.prev().text(result["sort_order"]);
                },
            });
        });
        $('.btn-down').click(function () {
            let self = $(this);
            self.attr("disabled", true);
            $.ajax({
                url: "{{ route("admin.banners.down") }}",
                dataType: "json",
                type: "POST",
                data: {"_token": "{{ csrf_token() }}", "id": self.val()},
                success: function (result, status, xhr) {
                    self.attr("disabled", false);
                    self.next().text(result["sort_order"]);
                },
            });
        });
    </script>
@endsection
