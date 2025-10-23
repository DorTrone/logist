@extends('admin.layouts.app')
@section('title')
    @lang('app.notifications')
@endsection
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="h3 mb-0">
            <a href="{{ route('admin.notifications.index', ['customer' => 0]) }}" class="link-dark text-decoration-none">@lang('app.notifications')</a>
        </div>
        <a href="{{ route('admin.notifications.create') }}" class="btn btn-primary btn-sm"><i class="bi-plus-lg"></i> @lang('app.add')</a>
    </div>

    <input type="text" class="form-control" id="dt-search" placeholder="@lang('app.search')" autofocus>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover table-sm" id="dt-table">
            <thead>
            <tr class="small">
                <th>@lang('app.id')</th>
                <th>@lang('app.customer')</th>
                <th style="width:20%">@lang('app.title')</th>
                <th style="width:40%">@lang('app.body')</th>
                <th>@lang('app.dateTime')</th>
                <th>@lang('app.createdAt')</th>
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
                    url: "{{ route('admin.notifications.api') }}",
                    dataType: "json",
                    type: "POST",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "customer": "{{ $f_customer }}",
                    },
                },
                columns: [
                    {data: 'id'},
                    {data: 'customer_id'},
                    {data: 'title'},
                    {data: 'body'},
                    {data: 'datetime'},
                    {data: 'created_at'},
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
