<?php use App\Models\Staff;?>

<x-app-layout>
    <div class="section">
        <div class="product-list list">
            <div class='container-fluid mt-10'>
                <div class="row mb-5">
                    <div class="d-flex justify-content-between algin-items-center mb-3">
                        <h3>UPDATE STAFF ROLE</h3>

                        <x-button>
                            <a href="{{ url('admin/staffs') }}" class="text-white">{{ __('List of Staffs') }}</a>
                        </x-button>
                    </div>

                    <!-- Validation Errors -->
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />
                    
                    <form method="POST" action="{{ url('admin/update-staff-role/'.$staff['id']) }}">
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
                                        value="{{ $staff['fullname'] }}" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="sta_role" class="form-label">Staff Role</label>
                                    <select class="form-select" id="sta_role" name="sta_role"
                                        aria-label="Floating label select example">
                                        <option selected disabled>--Select Role--</option>
                                        <option aria-placeholder="aaa" selected hidden value="{{ $staff['role']['id'] }}">
                                            {{ $staff['role']['name'] }}
                                        </option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role['id'] }}">
                                                {{ $role['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
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
