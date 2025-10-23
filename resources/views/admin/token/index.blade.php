@extends('admin.layouts.app')
@section('title')
    @lang('app.tokens')
@endsection
@section('content')
    <div class="h3 mb-3">@lang('app.tokens')</div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover table-sm" id="dt-table">
            <thead>
            <tr class="small">
                <th>@lang('app.id')</th>
                <th>Tokenable type</th>
                <th>Tokenable id</th>
                <th>Name</th>
                <th>Last used at</th>
                <th>Expires at</th>
                <th>@lang('app.createdAt')</th>
                <th>@lang('app.updatedAt')</th>
            </tr>
            </thead>
        </table>
    </div>
    <script>
        $(document).ready(function () {
            $('#dt-table').DataTable({
                processing: true,
                serverSide: true,
                fixedHeader: true,
                pageLength: 50,
                ajax: {
                    url: "{{ route('admin.tokens.api') }}",
                    dataType: "json",
                    type: "POST",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "tokenType": "{{ $f_tokenType }}",
                        "tokenId": "{{ $f_tokenId }}",
                    },
                },
                columns: [
                    {data: 'id'},
                    {data: 'tokenable_type', orderable: false},
                    {data: 'tokenable_id', orderable: false},
                    {data: 'name'},
                    {data: 'last_used_at'},
                    {data: 'expires_at'},
                    {data: 'created_at'},
                    {data: 'updated_at'},
                ],
                order: [[0, 'desc']],
                mark: true,
            }).on('page.dt', function () {
                $("html, body").animate({scrollTop: 0}, 1500);
            });
        });
    </script>
@endsection
