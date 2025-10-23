@extends('client.layouts.app')
@section('title')
    @lang('app.appName')
@endsection
@section('content')
    <div class="text-white" style="background-color: #AD0000;">
        <div class="container-xl text-center">
            <div class="row justify-content-center align-items-center vh-100">
                <div class="col-10 col-sm-8 col-md-6 col-lg-4">
                    @include('client.home.alert')
                    <div class="mb-5">
                        <img src="{{ asset('img/shazada.svg') }}" alt="" style="max-height:10rem;">
                    </div>
                    <div class="fs-2 mb-2">
                        @lang('app.appName')
                    </div>
                    <div class="fs-5 mb-5">
                        @lang('app.appDescription')
                    </div>
                    <div class="mb-5">
                        <a href="https://apps.apple.com/tm/app/shazada-logistics/id6745868224" class="d-inline-block" target="_blank">
                            <img src="{{ asset('img/App_Store.png') }}" alt="" style="max-height:2.5rem;">
                        </a>
                        <a href="{{ Storage::url('szd_customer.apk') }}" class="d-inline-block" target="_blank">
                            <img src="{{ asset('img/Google_Play.png') }}" alt="" style="max-height:2.5rem;">
                        </a>
                    </div>
                    @include('client.home.contact')
                </div>
            </div>
        </div>
    </div>
@endsection
