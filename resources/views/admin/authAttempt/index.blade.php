@extends('admin.layouts.app')
@section('title')
    @lang('app.authAttempts')
@endsection
@section('content')
    <div class="h3 mb-3">@lang('app.authAttempts')</div>

    <input type="text" class="form-control" id="dt-search" placeholder="@lang('app.search')" autofocus>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover table-sm" id="dt-table">
            <thead>
            <tr class="small">
                <th>@lang('app.id')</th>
                <th>@lang('app.ipAddress')</th>
                <th style="width:50%">@lang('app.userAgent')</th>
                <th>@lang('app.username')</th>
                <th>@lang('app.event')</th>
                <th>@lang('app.createdAt')</th>
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
                    url: "{{ route('admin.authAttempts.api') }}",
                    dataType: "json",
                    type: "POST",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "ipAddress": "{{ $f_ipAddress }}",
                        "userAgent": "{{ $f_userAgent }}",
                    },
                },
                columns: [
                    {data: 'id'},
                    {data: 'ip_address_id'},
                    {data: 'user_agent_id'},
                    {data: 'username'},
                    {data: 'event'},
                    {data: 'created_at'},
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
