@extends('admin.layouts.app')
@section('title')
    @lang('app.contacts')
@endsection
@section('content')
    <div class="h3 mb-3">@lang('app.contacts')</div>

    <input type="text" class="form-control" id="dt-search" placeholder="@lang('app.search')" autofocus>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover table-sm" id="dt-table">
            <thead>
            <tr class="small">
                <th>@lang('app.id')</th>
                <th>@lang('app.name')</th>
                <th>@lang('app.surname')</th>
                <th>@lang('app.phone')</th>
                <th>@lang('app.email')</th>
                <th style="width:40%">@lang('app.message')</th>
                <th>@lang('app.archive')</th>
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
                    url: "{{ route('admin.contacts.api') }}",
                    dataType: "json",
                    type: "POST",
                    data: {"_token": "{{ csrf_token() }}"},
                },
                columns: [
                    {data: 'id'},
                    {data: 'name'},
                    {data: 'surname'},
                    {data: 'phone'},
                    {data: 'email'},
                    {data: 'message'},
                    {data: 'archive'},
                    {data: 'created_at'},
                    {data: 'action', searchable: false, orderable: false},
                ],
                order: [[0, 'desc']],
                mark: true,
            }).on('page.dt', function () {
                $("html, body").animate({scrollTop: 0}, 1500);
            }).on('draw.dt', function () {
                $('.check-archive').click(function () {
                    let self = $(this);
                    self.attr("disabled", true);
                    $.ajax({
                        url: "{{ route("admin.contacts.archive") }}",
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
