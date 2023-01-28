@extends('layouts.default')

<!-- Load nội dung trang home/dashboard.php vào vị trí section('page') của layouts/default.php -->
@section('content')
<div class="container mt-100 profiles">
    <!-- Sidebar Start -->
    <div class="row">
        @include('front.profiles.navigation')
        <div class="col-lg-9">
            <div class="ms-3">
                <div class="d-flex justify-content-between">
                    <h5 class="mb-4">UPDATE PROFILE</h5>
                    
                    <div class="w-30">
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />
                    </div>
                </div>

                <form method="POST" action="{{ url('update-profile') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="profile_name" class="form-label">Name
                                    <span class="required">*</span>
                                </label>
                                <input type="text" class="form-control" id="profile_name" name="profile_name" 
                                    placeholder="Enter Name" value="{{ Auth::user()->name }}">
                            </div>
                            <div class="mb-3">
                                <label for="profile_phone" class="form-label">Phone
                                    <span class="required">*</span>
                                </label>
                                <input type="text" class="form-control" id="profile_phone" name="profile_phone" 
                                    placeholder="Enter Phone" value="{{ $profile['phone'] }}">
                            </div>
                            <div class="mb-3">
                                <label for="profile_birth_date" class="form-label">Birth Date
                                    <span class="required">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control datepicker" value="{{ $profile['birth_date'] }}" id="profile_birth_date" name="profile_birth_date" aria-describedby="basic-addon2">
                                    <span class="input-group-text" id="basic-addon2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar" viewBox="0 0 16 16">
                                            <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                                        </svg>
                                    </span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label mb-2">Gender</label>
                                <div class="d-flex">
                                    <div class="form-check me-3">
                                        <input class="form-check-input" type="radio" name="profile_gender"
                                            @if($profile['gender'] == 'Male') checked @endif value="Male">
                                        <label class="form-check-label" for="profile_gender">Male</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="profile_gender" 
                                            @if($profile['gender'] == 'Female') checked @endif value="Female">
                                        <label class="form-check-label" for="profile_gender">FeMale</label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <a href="{{ url('update-email') }}" class="me-2 text-danger">
                                    Change Email
                                </a>
                                <a href="{{ url('update-password')   }}" class="text-danger">
                                    Change Password
                                </a>
                            </div>
                            
                            <x-button>{{ __('Save') }}</x-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Sidebar End -->
</div>
@endsection
