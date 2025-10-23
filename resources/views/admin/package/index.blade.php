@extends('admin.layouts.app')
@section('title')
    @lang('app.packages')
@endsection
@section('content')
    <div class="row align-items-center mb-3">
        <div class="col-auto">
            <div class="h3 mb-0">@lang('app.packages')</div>
        </div>
        <div class="col text-end">
            @include('admin.package.filter')
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
                <th style="width:10%">@lang('app.customer')</th>
                <th>@lang('app.transport')</th>
                <th>@lang('app.weight')</th>
                <th>@lang('app.totalPrice')</th>
                <th>@lang('app.status')</th>
                <th>@lang('app.payment')</th>
                <th style="width:15%">@lang('app.actions')</th>
                <th><i class="bi-gear-wide-connected"></i></th>
            </tr>
            </thead>
        </table>
    </div>
    <script>
        $(document).ready(function () {
            let f_transports = @if($f_transports) "{{ implode(',', $f_transports) }}".split(',').map(x => +x) @else [] @endif;
            let f_customers = @if($f_customers) "{{ implode(',', $f_customers) }}".split(',').map(x => +x) @else [] @endif;
            let f_transportTypes = @if($f_transportTypes) "{{ implode(',', $f_transportTypes) }}".split(',').map(x => +x) @else [] @endif;
            let f_packagePayments = @if($f_packagePayments) "{{ implode(',', $f_packagePayments) }}".split(',').map(x => +x) @else [] @endif;
            let f_locations = @if($f_locations) "{{ implode(',', $f_locations) }}".split(',').map(x => +x) @else [] @endif;
            let f_packageTypes = @if($f_packageTypes) "{{ implode(',', $f_packageTypes) }}".split(',').map(x => +x) @else [] @endif;
            let f_packageStatuses = @if($f_packageStatuses) "{{ implode(',', $f_packageStatuses) }}".split(',').map(x => +x) @else [] @endif;
            let f_paymentStatuses = @if($f_paymentStatuses) "{{ implode(',', $f_paymentStatuses) }}".split(',').map(x => +x) @else [] @endif;

            let dtTable = $('#dt-table').DataTable({
                processing: true,
                serverSide: true,
                fixedHeader: true,
                pageLength: 50,
                ajax: {
                    url: "{{ route('admin.packages.api') }}",
                    dataType: "json",
                    type: "POST",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "transports": f_transports,
                        "customers": f_customers,
                        "locations": f_locations,
                        "transportTypes": f_transportTypes,
                        "packagePayments": f_packagePayments,
                        "packageTypes": f_packageTypes,
                        "packageStatuses": f_packageStatuses,
                        "paymentStatuses": f_paymentStatuses,
                    },
                },
                columns: [
                    {data: 'id'},
                    {data: 'code'},
                    {data: 'images', orderable: false},
                    {data: 'note', orderable: false},
                    {data: 'customer', orderable: false},
                    {data: 'transport', orderable: false},
                    {data: 'weight'},
                    {data: 'total_price'},
                    {data: 'status'},
                    {data: 'payment_reports', orderable: false},
                    {data: 'actions', orderable: false},
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
