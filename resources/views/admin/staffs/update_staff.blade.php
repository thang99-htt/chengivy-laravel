<?php use App\Models\Staff;?>

<x-app-layout>
    <div class="section">
        <div class="product-list list">
            <div class='container-fluid mt-10'>
                <div class="row mb-5">
                    <div class="d-flex justify-content-between algin-items-center mb-3">
                        <h3>UPDATE STAFF</h3>

                        <x-button>
                            <a href="{{ url('admin/staffs') }}" class="text-white">{{ __('List of Staffs') }}</a>
                        </x-button>
                    </div>

                    <!-- Validation Errors -->
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />
                    
                    <form method="POST" action="{{ url('admin/update-staff/'.$staff['id']) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="mb-3">
                                    <label for="sta_id" class="form-label">Staff ID</label>
                                    <input type="text" class="form-control" id="sta_id" name="sta_id" 
                                        value="{{ $staff['id'] }}" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="sta_fullname" class="form-label">Staff Full Name</label>
                                    <input type="text" class="form-control" id="sta_fullname" name="sta_fullname" 
                                        value="{{ $staff['fullname'] }}">
                                </div>
                                <div class="mb-3">
                                    <label for="sta_email" class="form-label">Staff Email</label>
                                    <input type="text" class="form-control" id="sta_email" name="sta_email" 
                                        value="{{ $staff['email'] }}">
                                </div>
                                <div class="mb-3">
                                    <label for="sta_phone" class="form-label">Staff Phone</label>
                                    <input type="text" class="form-control" id="sta_phone" name="sta_phone" 
                                        value="{{ $staff['phone'] }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label mb-2">Gender</label>
                                    <div class="d-flex">
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="sta_gender"
                                                @if($staff['gender'] == 'Male') checked @endif value="Male">
                                            <label class="form-check-label" for="sta_gender">Male</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="sta_gender" 
                                                @if($staff['gender'] == 'Female') checked @endif value="Female">
                                            <label class="form-check-label" for="sta_gender">FeMale</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="sta_birth_date" class="form-label">Staff Birth Date</label>
                                    <input type="text" class="form-control" id="sta_birth_date" name="sta_birth_date" 
                                        value="{{ $staff['birth_date'] }}">
                                </div>
                                <div class="mb-3">
                                    <label for="sta_address" class="form-label">Staff Address</label>
                                    <input type="text" class="form-control" id="sta_address" name="sta_address" 
                                        value="{{ $staff['address'] }}">
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
