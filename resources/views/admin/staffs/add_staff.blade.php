<x-app-layout>
    <div class="section">
        <div class="product-list list">
            <div class='container-fluid mt-10'>
                <div class="row mb-5">
                    <div class="d-flex justify-content-between algin-items-center mb-3">
                        <h3>ADD NEW STAFF</h3>

                        <x-button>
                            <a href="{{ url('admin/staffs') }}" class="text-white">{{ __('List of Staffs') }}</a>
                        </x-button>
                    </div>

                    <!-- Validation Errors -->
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />
                    
                    <form method="POST" action="{{ url('admin/add-staff') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="mb-3">
                                    <label for="staff_fullname" class="form-label">Staff Full Name</label>
                                    <input type="text" class="form-control" id="staff_fullname" name="staff_fullname" 
                                    placeholder="Enter Staff Full Name">
                                </div>
                                <div class="mb-3">
                                    <label for="staff_email" class="form-label">Staff Email</label>
                                    <input type="text" class="form-control" id="staff_email" name="staff_email" 
                                    placeholder="Enter Staff Email">
                                </div>
                                <div class="mb-3">
                                    <label for="staff_pass" class="form-label">Staff Password</label>
                                    <input type="password" class="form-control" id="staff_pass" name="staff_pass" 
                                    placeholder="Enter Staff Password">
                                </div>
                                <div class="mb-3">
                                    <label for="staff_phone" class="form-label">Staff Phone</label>
                                    <input type="text" class="form-control" id="staff_phone" name="staff_phone" 
                                    placeholder="Enter Staff Phone">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label mb-2">Staff Gender</label>
                                    <div class="d-flex">
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="staff_gender" value="Male">
                                            <label class="form-check-label" for="staff_gender">Male</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="staff_gender" value="Female">
                                            <label class="form-check-label" for="staff_gender">FeMale</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="staff_birth_date" class="form-label">Staff Birth Date</label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control datepicker" value="{{ date("Y-m-d") }}" id="staff_birth_date" name="staff_birth_date" aria-describedby="basic-addon1">
                                        <span class="input-group-text" id="basic-addon1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar" viewBox="0 0 16 16">
                                                <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="staff_address" class="form-label">Staff Address</label>
                                    <input type="text" class="form-control" id="staff_address" name="staff_address" 
                                    placeholder="Enter Staff Address">
                                </div>
                                <x-button class="ml-3 float-end" >{{ __('Save') }}</x-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
