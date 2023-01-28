<x-app-layout>
    <!-- Form Start -->
    <div class="container-fluid pt-4 px-4">
        <div class="row g-4">
            <div class="col-sm-12 col-xl-6">
                <div class="bg-light rounded h-100 p-4">
                    <h4 class="mb-4">Update Admin Details</h4>

                    <!-- Validation Errors -->
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />

                    <form method="POST" action="{{ url('admin/update-admin-details') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="admin_email" class="form-label">Admin Username/Email</label>
                            <input type="email" class="form-control" id="admin_email"
                                aria-describedby="emailHelp" value="{{ Auth::guard('admin')->user()->email }}" readonly="">
                            <div id="emailHelp" class="form-text">We'll never share your email with anyone else.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="admin_fullname" class="form-label">Admin Full Name</label>
                            <input type="text" class="form-control" id="admin_fullname" name="admin_fullname" 
                                placeholder="Enter Name" value="{{ Auth::guard('admin')->user()->fullname }}">
                            <span id=""></span>
                        </div>
                        <div class="mb-3">
                            <label for="admin_phone" class="form-label">Admin Phone</label>
                            <input type="text" class="form-control" id="admin_phone" name="admin_phone" 
                                placeholder="Enter 10 Digit Mobile Number" value="{{ Auth::guard('admin')->user()->phone }}" >
                        </div>
                        <div class="mb-3">
                            <label class="form-label mb-2">Admin Gender</label>
                            <div class="d-flex">
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="radio" name="admin_gender" value="Male"
                                    @if(Auth::guard('admin')->user()->gender == 'Male') checked @endif>
                                    <label class="form-check-label" for="admin_gender">Male</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="admin_gender" value="Female"
                                    @if(Auth::guard('admin')->user()->gender == 'Female') checked @endif>
                                    <label class="form-check-label" for="admin_gender">FeMale</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="admin_birth_date" class="form-label">Staff Birth Date</label>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control datepicker" value="{{ date("Y-m-d") }}" id="admin_birth_date" name="admin_birth_date" aria-describedby="basic-addon1">
                                <span class="input-group-text" id="basic-addon1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar" viewBox="0 0 16 16">
                                        <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                                    </svg>
                                </span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="admin_address" class="form-label">Admin Address</label>
                            <input type="text" class="form-control" id="admin_address" name="admin_address" 
                                placeholder="Enter Address" value="{{ Auth::guard('admin')->user()->address }}" >
                        </div>
                        <div class="mb-3">
                            <label for="admin_image" class="form-label">Admin Photo</label>
                            <input type="file" class="form-control bg-white" id="admin_image" name="admin_image" >
                            @if(!empty(Auth::guard('admin')->user()->image)) 
                                <a target="_blank" href="{{ url('storage/images/admin/photos/'.
                                Auth::guard('admin')->user()->image) }}">View image</a>
                                <input type="text" hidden name="current_admin_image" value="{{ Auth::guard('admin')->user()->image }}">
                            @endif
                        </div>
                        <x-button class="ml-3 float-end" >{{ __('Save') }}</x-button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Form End -->
</x-app-layout>