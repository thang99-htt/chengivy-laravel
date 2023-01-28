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
                    <h5 class="mb-4">UPDATE PASSWORD</h5>
                    
                    <div class="w-30">
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />
                    </div>
                </div>
                
                <form method="POST" action="{{ url('update-password') }}">
                    @csrf
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="profile_current_password" class="form-label">Current Password</label>
                                <input type="password" class="form-control" id="profile_current_password"
                                    name="profile_current_password" placeholder="Enter Current Password">
                                <span id="profile_check_password"></span>
                            </div>
                            <div class="mb-3">
                                <label for="profile_new_password" class="form-label">New Password
                                    <span class="required">*</span>
                                </label>
                                <input type="password" class="form-control" id="profile_new_password" name="profile_new_password" 
                                    placeholder="Enter New Password" value="">
                            </div>
                            <div class="mb-3">
                                <label for="profile_confirm_password" class="form-label">Confirm Password
                                    <span class="required">*</span>
                                </label>
                                <input type="password" class="form-control" id="profile_confirm_password" name="profile_confirm_password" 
                                    placeholder="Enter Confirm Password" value="">
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

