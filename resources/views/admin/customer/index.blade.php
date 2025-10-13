@extends('admin.layouts.app')
@section('title')
    @lang('app.customers')
@endsection
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="h3 mb-0">@lang('app.customers')</div>
        <a href="{{ route('admin.customers.create') }}" class="btn btn-primary btn-sm"><i class="bi-plus-lg"></i> @lang('app.add')</a>
    </div>

    <input type="text" class="form-control" id="dt-search" placeholder="@lang('app.search')" autofocus>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover table-sm" id="dt-table">
            <thead>
            <tr class="small">
                <th>@lang('app.id')</th>
                <th>@lang('app.code')</th>
                <th>@lang('app.authMethod')</th>
                <th>@lang('app.platform')</th>
                <th>@lang('app.language')</th>
                <th>@lang('app.name')</th>
                <th>@lang('app.surname')</th>
                <th>@lang('app.username')</th>
                <th style="width:20%">@lang('app.note')</th>
                <th>@lang('app.lastSeen')</th>
                <th>@lang('app.createdAt')</th>
                <th>@lang('app.packagesCount')</th>
                <th>@lang('app.notificationsCount')</th>
                <th><i class="bi-gear-wide-connected"></i></th>
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
                    url: "{{ route('admin.customers.api') }}",
                    dataType: "json",
                    type: "POST",
                    data: {"_token": "{{ csrf_token() }}"},
                },
                columns: [
                    {data: 'id'},
                    {data: 'code'},
                    {data: 'auth_method'},
                    {data: 'platform'},
                    {data: 'language'},
                    {data: 'name'},
                    {data: 'surname'},
                    {data: 'username'},
                    {data: 'note'},
                    {data: 'last_seen'},
                    {data: 'created_at'},
                    {data: 'packages_count'},
                    {data: 'notifications_count'},
                    {data: 'action', searchable: false, orderable: false},
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
