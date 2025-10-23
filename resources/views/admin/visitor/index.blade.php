@extends('admin.layouts.app')
@section('title')
    @lang('app.visitors')
@endsection
@section('content')
    <div class="h3 mb-3">@lang('app.visitors')</div>

    <input type="text" class="form-control" id="dt-search" placeholder="@lang('app.search')" autofocus>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover table-sm" id="dt-table">
            <thead>
            <tr class="small">
                <th>@lang('app.id')</th>
                <th>@lang('app.ipAddress')</th>
                <th style="width:30%">@lang('app.userAgent')</th>
                <th>@lang('app.hits')</th>
                <th>@lang('app.suspectHits')</th>
                <th>@lang('app.visitor')</th>
                <th>@lang('app.visitor')</th>
                <th>@lang('app.status')</th>
                <th>@lang('app.createdAt')</th>
                <th>@lang('app.updatedAt')</th>
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
                    url: "{{ route('admin.visitors.api') }}",
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
                    {data: 'hits'},
                    {data: 'suspect_hits'},
                    {data: 'robot'},
                    {data: 'api'},
                    {data: 'disabled'},
                    {data: 'created_at'},
                    {data: 'updated_at'},
                ],
                order: [[0, 'desc']],
                mark: true,
            }).on('page.dt', function () {
                $("html, body").animate({scrollTop: 0}, 1500);
            }).on('draw.dt', function () {
                $('.check-disabled').click(function () {
                    let self = $(this);
                    self.attr("disabled", true);
                    $.ajax({
                        url: "{{ route("admin.visitors.disabled") }}",
                        dataType: "json",
                        type: "POST",
                        data: {"_token": "{{ csrf_token() }}", "id": self.val()},
                        success: function (result, status, xhr) {
                            self.attr("disabled", false);
                            self.prop('checked', result["checked"] === 1);
                        },
                    });
                });
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
