@props(['errors'])

@if ($errors->any())
    <div {{ $attributes }}>
        {{-- <div class="font-medium text-red-600">
            {{ __('Whoops! Something went wrong.') }}
        </div> --}}

        <div class="alert alert-danger alert-dismissible fade show border border-danger" role="alert">
            @foreach ($errors->all() as $error)
                <li class="list-unstyled">{{ $error }}</li>
            @endforeach
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                {{-- <span aria-hidden="true" class="fs-3">&times;</span> --}}
            </button>
        </div>
    </div>
@endif

@if(Session::has('error_message'))
<div class="alert alert-danger alert-dismissible fade show  border border-danger" role="alert">
    <strong>Error:</strong> {{ Session::get('error_message') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(Session::has('success_message'))
<div class="alert alert-success alert-dismissible fade show  border border-success" role="alert">
    <strong>Success:</strong> {{ Session::get('success_message') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
