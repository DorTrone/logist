<div class="btn-group mb-1">
    <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
        {{ $api ? trans('app.api') : trans('app.web') }}
    </button>
    <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['api' => 0, 'robot' => $robot]) }}">@lang('app.web')</a></li>
        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['api' => 1, 'robot' => $robot]) }}">@lang('app.api')</a></li>
    </ul>
</div>
<div class="btn-group mb-1">
    <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
        {{ $robot ? trans('app.robot') : trans('app.human') }}
    </button>
    <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['api' => $api, 'robot' => 0]) }}">@lang('app.human')</a></li>
        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['api' => $api, 'robot' => 1]) }}">@lang('app.robot')</a></li>
    </ul>
</div>
