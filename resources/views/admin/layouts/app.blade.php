<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('img/favicon.ico') }}">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-icons.min.css') }}" rel="stylesheet">
    <script type="text/javascript" src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <link href="{{ asset('css/fancybox.css') }}" rel="stylesheet">
    <script type="text/javascript" src="{{ asset('js/fancybox.umd.js') }}"></script>
    <link href="{{ asset('css/datatables.min.css') }}" rel="stylesheet">
    <script type="text/javascript" src="{{ asset('js/datatables.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/jquery.mark.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/datatables.mark.min.js') }}"></script>
    <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" rel="stylesheet">
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script>$(document).on('select2:open', function (e) {document.querySelector(`[aria-controls="select2-${e.target.id}-results"]`).focus();});</script>
    <style>#dt-table_length,#dt-table_filter{display:none}</style>
</head>
<body class="bg-light">
@include('admin.layouts.nav')
<div class="container-fluid">
    @include('admin.layouts.alert')
    <div class="my-3">
        @yield('content')
    </div>
</div>
<script>Fancybox.bind("[data-fancybox]", {});</script>
<script>$(".select2").select2({theme: "bootstrap-5",});</script>
</body>
</html>
