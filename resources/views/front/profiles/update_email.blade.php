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
                    <h5 class="mb-4">UPDATE EMAIL</h5>
                    
                    <div class="w-30">
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />
                    </div>
                </div>
                
                <form method="POST" action="{{ url('update-email') }}">
                    @csrf
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="profile_email" class="form-label">Email
                                    <span class="required">*</span>
                                </label>
                                <input type="email" class="form-control" id="profile_email" name="profile_email"
                                    aria-describedby="emailHelp" value="{{ Auth::user()->email }}">
                                <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
                            </div>
                            <div class="mb-3">
                                <label for="profile_current_password" class="form-label">Current Password</label>
                                <input type="password" class="form-control" id="profile_current_password"
                                    name="profile_current_password" placeholder="Enter Current Password">
                                <span id="profile_check_password"></span>
                            </div>
                        </div>
                    </div>
                    <x-button>{{ __('Save') }}</x-button>
                </form>
            </div>
        </div>
    </div>
    <!-- Sidebar End -->
</div>
@endsection

