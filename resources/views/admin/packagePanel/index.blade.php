@extends('admin.layouts.app')
@section('title')
    @lang('app.packagesPanel')
@endsection
@section('content')
    <script type="text/javascript" src="{{ asset('js/Chart.bundle.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/amcharts/amcharts.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/amcharts/serial.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/amcharts/light.js') }}"></script>
    <style>.chart-serial{height:12.5rem;}@media(min-width:768px){.chart-serial{height:25rem;}}.amcharts-chart-div a{display:none !important;}</style>

    <div class="row align-items-center mb-3">
        <div class="col-auto">
            <div class="h3 mb-0">@lang('app.packagesPanel')</div>
        </div>
        <div class="col text-end">
            @include('admin.package.filter')
        </div>
    </div>
@endsection
