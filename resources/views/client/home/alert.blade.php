@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-5" role="alert">
        {!! session('success') !!}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@elseif(isset($success))
    <div class="alert alert-success alert-dismissible fade show mb-5" role="alert">
        {!! $success !!}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-5" role="alert">
        {!! session('error') !!}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@elseif(isset($error))
    <div class="alert alert-danger alert-dismissible fade show mb-5" role="alert">
        {!! $error !!}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@elseif(isset($errors) && $errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-5" role="alert">
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
