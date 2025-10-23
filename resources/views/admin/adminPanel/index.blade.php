@extends('admin.layouts.app')
@section('title')
    @lang('app.adminPanel')
@endsection
@section('content')
    <div class="row align-items-center mb-3">
        <div class="col-auto">
            <div class="h3 mb-0">@lang('app.adminPanel')</div>
        </div>
        <div class="col text-end">
            @include('admin.adminPanel.filter')
        </div>
    </div>

    <div class="row g-4 mb-3">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header p-3">
                    <div class="fs-5">
                        @lang('app.recentSuspiciousVisitors')
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive rounded-bottom">
                        <table class="table table-bordered table-striped table-hover table-sm mb-0">
                            <thead>
                            <tr class="small">
                                <th>@lang('app.ipAddress')</th>
                                <th>@lang('app.dateTime')</th>
                                <th>@lang('app.platform')</th>
                                <th>@lang('app.hits')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($suspectVisitors as $suspectVisitor)
                                <tr>
                                    <td class="{{ $suspectVisitor->ipAddress->disabled ? 'table-danger':'' }}">
                                        @if($suspectVisitor->ipAddress->country_code)
                                            <img src="{{ asset('flag/'.$suspectVisitor->ipAddress->country_code.'.png') }}" alt="{{ $suspectVisitor->ipAddress->country_name }}" class="align-text-top">
                                        @else
                                            <img src="{{ asset('flag/flag.png') }}" alt="{{ $suspectVisitor->ipAddress->ip_address }}" class="align-text-top">
                                        @endif
                                        {{ $suspectVisitor->ipAddress->ip_address }}
                                    </td>
                                    <td>{{ $suspectVisitor->updated_at->format('Y-m-d H:i:s') }}</td>
                                    <td class="{{ $suspectVisitor->userAgent->disabled ? 'table-danger':'' }}">
                                        {{ $suspectVisitor->userAgent->ua() }}
                                    </td>
                                    <td class="{{ $suspectVisitor->suspect_hits > 0 ? 'table-danger' : '' }}">
                                        {{ $suspectVisitor->hits }} / {{ $suspectVisitor->suspect_hits }}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow">
                <div class="card-header p-3">
                    <div class="fs-5">
                        @lang('app.countries')
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive rounded-bottom">
                        <table class="table table-bordered table-striped table-hover table-sm mb-0">
                            <thead>
                            <tr class="small">
                                <th>@lang('app.country')</th>
                                <th>@lang('app.hits')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($countries as $country)
                                <tr>
                                    <td>
                                        @if($country->country_code)
                                            <img src="{{ asset('flag/'.$country->country_code.'.png') }}" alt="{{ $country->country_code }}" class="align-text-top">
                                        @else
                                            <img src="{{ asset('flag/flag.png') }}" alt="{{ $country->country_name }}" class="align-text-top">
                                        @endif
                                        {{ $country->country_name }}
                                    </td>
                                    <td>{{ $country->count }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow">
                <div class="card-header p-3">
                    <div class="fs-5">
                        @lang('app.suspiciousCountries')
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive rounded-bottom">
                        <table class="table table-bordered table-striped table-hover table-sm mb-0">
                            <thead>
                            <tr class="small">
                                <th>@lang('app.country')</th>
                                <th>@lang('app.hits')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($suspectCountries as $suspectCountry)
                                <tr>
                                    <td>
                                        @if($suspectCountry->country_code)
                                            <img src="{{ asset('flag/'.$suspectCountry->country_code.'.png') }}" alt="{{ $suspectCountry->country_code }}" class="align-text-top">
                                        @else
                                            <img src="{{ asset('flag/flag.png') }}" alt="{{ $suspectCountry->country_name }}" class="align-text-top">
                                        @endif
                                        {{ $suspectCountry->country_name }}
                                    </td>
                                    <td>{{ $suspectCountry->count }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
