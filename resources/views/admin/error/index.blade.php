@extends('admin.layouts.app')
@section('title')
    @lang('app.errors')
@endsection
@section('content')
    <div class="h3 mb-3">@lang('app.errors')</div>

    <input type="text" class="form-control" id="dt-search" placeholder="@lang('app.search')" autofocus>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover table-sm" id="dt-table">
            <thead>
            <tr class="small">
                <th>@lang('app.id')</th>
                <th style="width:20%">@lang('app.title')</th>
                <th style="width:50%">@lang('app.body')</th>
                <th>@lang('app.attempts')</th>
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
                    url: "{{ route('admin.errors.api') }}",
                    dataType: "json",
                    type: "POST",
                    data: {"_token": "{{ csrf_token() }}"},
                },
                columns: [
                    {data: 'id'},
                    {data: 'title'},
                    {data: 'body'},
                    {data: 'attempts'},
                    {data: 'status'},
                    {data: 'created_at'},
                    {data: 'updated_at'},
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
