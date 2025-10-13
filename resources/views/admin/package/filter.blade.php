<button class="btn btn-secondary btn-sm mb-1" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFilter" aria-expanded="false" aria-controls="collapseFilter">
    <i class="bi-funnel"></i>
</button>
<div class="collapse" id="collapseFilter">
    <form action="{{ url()->current() }}" method="get">
        <div class="row justify-content-end text-start g-3">
            @foreach($queries as $qKey => $qValues)
                <div class="col-sm-auto">
                    <div class="h6">@lang('app.' . $qKey)</div>
                    @foreach($qValues as $qValue)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="{{ $qValue['id'] }}"
                                   name="{{ $qKey }}[]" id="{{ $qKey }}_{{ $qValue['id'] }}" {{ in_array($qValue['id'], ${'f_' . $qKey} ?: []) ? 'checked' : '' }}>
                            <label class="form-check-label" for="{{ $qKey }}_{{ $qValue['id'] }}">
                                @lang('const.' . $qValue['name'])
                            </label>
                        </div>
                    @endforeach
                </div>
            @endforeach
            <div class="col-sm-auto">
                <button type="submit" class="btn btn-primary btn-sm">@lang('app.filter')</button>
                <a href="{{ url()->current() }}" class="btn btn-danger btn-sm"><i class="bi-x-lg"></i> @lang('app.clear')</a>
            </div>
        </div>
    </form>
</div>
