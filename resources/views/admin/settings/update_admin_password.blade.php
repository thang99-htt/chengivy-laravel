<x-app-layout>
    <!-- Form Start -->
    <div class="container-fluid pt-4 px-4">
        <div class="row g-4">
            <div class="col-sm-12 col-xl-6">
                <div class="bg-light rounded h-100 p-4">
                    <h4 class="mb-4">Update Admin Password</h4>

                    <!-- Validation Errors -->
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />

                    <form method="POST" action="{{ url('admin/update-admin-password') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Admin Username/Email</label>
                            <input type="email" class="form-control" id="exampleInputEmail1"
                                aria-describedby="emailHelp" value="{{ $adminDetails['email'] }}" readonly="">
                            <div id="emailHelp" class="form-text">We'll never share your email with anyone else.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="current_password"
                                name="current_password" placeholder="Enter Current Password">
                            <span id="check_password"></span>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" 
                                name="new_password" placeholder="Enter New Password">
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" 
                                name="confirm_password" placeholder="Confirm Password">
                        </div>
                        <x-button class="ml-3 float-end" >{{ __('Save') }}</x-button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Form End -->
</x-app-layout>