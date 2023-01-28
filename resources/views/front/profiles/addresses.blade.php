@php 
use App\Models\Profile;
use App\Models\City;
use App\Models\District;
use App\Models\Ward;
@endphp
@extends('layouts.default')

<!-- Load nội dung trang home/dashboard.php vào vị trí section('page') của layouts/default.php -->
@section('content')
<div class="container mt-100 profiles">
    <!-- Sidebar Start -->
    <div class="row">
        @include('front.profiles.navigation')
        <div class="col-lg-9">
            <div class="ms-3">
                <h5 class="mb-4">DELIVERY ADDRESS</h5>
                
                <!-- Validation Errors -->
                <x-auth-validation-errors class="mb-4" :errors="$errors" />
                <div class="accordion mt-4" id="accordionPanelsStayOpenExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="panelsStayOpen-headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">
                                Default address
                            </button>
                          </h2>
                        <hr>
                        <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-headingOne">
                            <div class="accordion-body">
                                @foreach($user->wards as $ward)
                                    @if($ward['id']==1) 
                                    <p>Phone: {{ $ward->pivot->phone }}</p>
                                    @endif
                                @endforeach
                                <div id="addressDefault"></div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="panelsStayOpen-headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseTwo" aria-expanded="false" aria-controls="panelsStayOpen-collapseTwo">
                                Address List
                            </button>
                        </h2>
                        <hr>
                        <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-headingThree">
                            <div class="accordion-body">
                                <table class="table table-striped table-hover mt-3">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Address</th>
                                            <th scope="col">Ward</th>
                                            <th scope="col">District</th>
                                            <th scope="col">City</th>
                                            <th scope="col">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $start = 1; ?>
                                        @foreach($user->wards as $ward)
                                        <tr>
                                            <th scope="row"><?= $start++; ?></th>
                                            <td>{{ $ward->pivot->address }}</td>
                                            <td>{{ $ward->name }}</td>
                                            <td>{{ $ward->district->name }}</td>
                                            <td>{{ $ward->district->city->name }}</td>
                                            <td>
                                                <a href="" class="edit-item edit-product" data-bs-toggle="modal" data-bs-target="#updateAdressModal_{{ $ward->pivot->id }}">
                                                    <i class="fa fa-pen"></i>
                                                </a>
                                                {{-- Modal Update Adress --}}
                                                <div class="modal fade" id="updateAdressModal_{{ $ward->pivot->id }}" tabindex="-1" aria-labelledby="updateAddressModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="updateAddressModalLabel">EDIT ADDRESS</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <!-- Validation Errors -->
                                                                <x-auth-validation-errors class="mb-4" :errors="$errors" />
                                                                
                                                                <form method="POST" action="{{ url('address/upadate/'.$ward->pivot->id) }}">
                                                                    @csrf
                                                                    <div class="mb-3">
                                                                        <label for="update_address_city" class="form-label">City</label>
                                                                        <select class="form-select" id="update_address_city" name="update_address_city">
                                                                            <option selected hidden value="{{ $ward->district->city->id }}">{{ $ward->district->city->name }}</option>
                                                                            @foreach($cities as $city)
                                                                                <option value="{{ $city['id'] }}">
                                                                                    {{ $city['name'] }} 
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="update_address_district" class="form-label">District</label>
                                                                        <select class="form-select" id="update_address_district" name="update_address_district">
                                                                            <option selected hidden value="{{ $ward->district->id }}">{{ $ward->district->name }}</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="update_address_ward" class="form-label">Ward</label>
                                                                        <select class="form-select" id="update_address_ward" name="update_address_ward">
                                                                            <option selected hidden value="{{ $ward->id }}">{{ $ward->name }}</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="update_address_detail" class="form-label">Address</label>
                                                                        <input type="text" class="form-control" id="update_address_detail" name="update_address_detail" placeholder="Enter Category Name"
                                                                            value="{{ $ward->pivot->address }}">
                                                                    </div>
                                                                    <x-button class="ml-3 float-end" >{{ __('Save') }}</x-button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <a href="javascript:avoid(0)" class="confirmDeleteAddress remove-item delete-product"
                                                    module="address" moduleid="{{ $ward->pivot->id }}" modulename="{{ $ward->pivot->address }}">
                                                    <i class="fa fa-times"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-headingTwo">
                            <div class="accordion-body">
                              <p>You have no other addresses in your address book.</p>
                            </div>
                        </div>
                        <x-button class="mt-4" data-bs-toggle="modal" data-bs-target="#addAdressModal">{{ __('Add New Address') }}</x-button>
                        
                        {{-- Modal Add Address --}}
                        <div class="modal fade" id="addAdressModal" tabindex="-1" aria-labelledby="addAddressModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                         <h5 class="modal-title" id="addAddressModalLabel">ADD ADDRESS</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Validation Errors -->
                                        <x-auth-validation-errors class="mb-4" :errors="$errors" />
                                        
                                        <form method="POST" action="{{ url('address/add') }}">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="address_city" class="form-label">City</label>
                                                <select class="form-select" id="address_city" name="address_city">
                                                    <option selected disabled>--Select City--</option>
                                                    @foreach($cities as $city)
                                                        <option value="{{ $city['id'] }}">
                                                            {{ $city['name'] }} 
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="address_district" class="form-label">District</label>
                                                <select class="form-select" id="address_district" name="address_district"></select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="address_ward" class="form-label">Ward</label>
                                                <select class="form-select" id="address_ward" name="address_ward"></select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="address_detail" class="form-label">Address</label>
                                                <input type="text" class="form-control" id="address_detail" name="address_detail" placeholder="Enter Category Name"
                                                    value="">
                                            </div>
                                            <x-button class="ml-3 float-end" >{{ __('Save') }}</x-button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Sidebar End -->
</div>
@endsection

