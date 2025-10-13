@extends('admin.layouts.app')
@section('title')
    @lang('app.transports')
@endsection
@section('content')
    <div class="row align-items-center mb-3">
        <div class="col-auto">
            <div class="h3 mb-0">@lang('app.transports')</div>
        </div>
        <div class="col text-end">
            @include('admin.transport.filter')
        </div>
    </div>

    <input type="text" class="form-control" id="dt-search" placeholder="@lang('app.search')" autofocus>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover table-sm" id="dt-table">
            <thead>
            <tr class="small">
                <th>@lang('app.id')</th>
                <th>@lang('app.code')</th>
                <th style="width:10%">@lang('app.images')</th>
                <th style="width:10%">@lang('app.note')</th>
                <th>@lang('app.totalWeight')</th>
                <th>@lang('app.totalPrice')</th>
                <th>@lang('app.status')</th>
                <th>@lang('app.payment')</th>
                <th style="width:20%">@lang('app.actions')</th>
                <th>@lang('app.packagesCount')</th>
            </tr>
            </thead>
        </table>
    </div>
    <script>
        $(document).ready(function () {
            let f_transportStatuses = @if($f_transportStatuses) "{{ implode(',', $f_transportStatuses) }}".split(',').map(x => +x) @else [] @endif;

            let dtTable = $('#dt-table').DataTable({
                processing: true,
                serverSide: true,
                fixedHeader: true,
                pageLength: 50,
                ajax: {
                    url: "{{ route('admin.transports.api') }}",
                    dataType: "json",
                    type: "POST",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "transportStatuses": f_transportStatuses,
                    },
                },
                columns: [
                    {data: 'id'},
                    {data: 'code'},
                    {data: 'images', orderable: false},
                    {data: 'note', orderable: false},
                    {data: 'total_weight'},
                    {data: 'total_price'},
                    {data: 'status'},
                    {data: 'payment_reports', orderable: false},
                    {data: 'actions', orderable: false},
                    {data: 'packages_count'},
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
