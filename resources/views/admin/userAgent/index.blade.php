@extends('admin.layouts.app')
@section('title')
    @lang('app.userAgents')
@endsection
@section('content')
    <div class="h3 mb-3">@lang('app.userAgents')</div>

    <input type="text" class="form-control" id="dt-search" placeholder="@lang('app.search')" autofocus>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover table-sm" id="dt-table">
            <thead>
            <tr class="small">
                <th>@lang('app.id')</th>
                <th style="width:30%">@lang('app.userAgent')</th>
                <th>@lang('app.device')</th>
                <th>@lang('app.platform')</th>
                <th>@lang('app.browser')</th>
                <th>@lang('app.robot')</th>
                <th>@lang('app.status')</th>
                <th>@lang('app.authAttemptsCount')</th>
                <th>@lang('app.visitorsCount')</th>
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
                    url: "{{ route('admin.userAgents.api') }}",
                    dataType: "json",
                    type: "POST",
                    data: {"_token": "{{ csrf_token() }}"},
                },
                columns: [
                    {data: 'id'},
                    {data: 'user_agent'},
                    {data: 'device'},
                    {data: 'platform'},
                    {data: 'browser'},
                    {data: 'robot'},
                    {data: 'disabled'},
                    {data: 'auth_attempts_count'},
                    {data: 'visitors_count'},
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
                        url: "{{ route("admin.userAgents.disabled") }}",
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
