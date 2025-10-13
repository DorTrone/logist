<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@lang('app.login')</title>
    <link rel="icon" href="{{ asset('img/favicon.ico') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-icons.min.css') }}" rel="stylesheet">
    <script type="text/javascript" src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</head>
<body class="bg-dark">
<div class="container-xl">
    <div class="row justify-content-center align-items-center vh-100">
        <div class="col-8 col-sm-4 col-md-3 col-xl-2">
            <div class="card shadow">
                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        @honeypot

                        <div class="form-floating mb-3">
                            <input type="text" class="form-control @error('username') is-invalid @enderror"
                                   id="username" name="username" value="{{ old('username') }}" placeholder="@lang('app.username')" required autofocus>
                            <label for="username">
                                @lang('app.username') <span class="text-danger">*</span>
                            </label>
                            @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   id="password" name="password" value="{{ old('password') }}" placeholder="@lang('app.password')" required>
                            <label for="password">
                                @lang('app.password') <span class="text-danger">*</span>
                            </label>
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100">@lang('app.login')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
