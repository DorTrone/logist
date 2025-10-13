@extends('admin.layouts.app')
@section('title')
    @lang('app.warehouses')
@endsection
@section('content')
    <div class="row align-items-center mb-3">
        <div class="col-auto">
            <div class="h3 mb-0">@lang('app.warehouses')</div>
        </div>
        <div class="col text-end">
            <a href="{{ route('admin.warehouses.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> @lang('app.create')
            </a>
        </div>
    </div>

    <input type="text" class="form-control" id="dt-search" placeholder="@lang('app.search')" autofocus>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover table-sm" id="dt-table">
            <thead>
            <tr class="small">
                <th>@lang('app.id')</th>
                <th>@lang('app.name')</th>
                <th>@lang('app.phone')</th>
                <th>@lang('app.address')</th>
                <th>@lang('app.postalCode')</th>
                <th style="width:15%">@lang('app.actions')</th>
            </tr>
            </thead>
        </table>
    </div>
    <script>
        $(document).ready(function () {
            let dtTable = $('#dt-table').DataTable({
                processing: true,
                serverSide: true,
                fixedHeader: true,
                pageLength: 50,
                ajax: {
                    url: "{{ route('admin.warehouses.api') }}",
                    dataType: "json",
                    type: "POST",
                    data: {
                        "_token": "{{ csrf_token() }}",
                    },
                },
                columns: [
                    {data: 'id'},
                    {data: 'name'},
                    {data: 'phone'},
                    {data: 'address'},
                    {data: 'postal_code'},
                    {data: 'actions', orderable: false},
                ],
                order: [[0, 'desc']],
                mark: true,
            }).on('page.dt', function () {
                $("html, body").animate({scrollTop: 0}, 1500);
            });
            let dtSearch = document.getElementById('dt-search');
            let dtSearchTimeout = null;
            dtSearch.addEventListener('keyup', function () {
                let self = this;
                clearTimeout(dtSearchTimeout);
                dtSearchTimeout = setTimeout(function () {
                    if (dtTable.search() !== self.value) {
                        dtTable.search(self.value).draw();
                    }
                }, 500);
            });
        });
    </script>
@endsection