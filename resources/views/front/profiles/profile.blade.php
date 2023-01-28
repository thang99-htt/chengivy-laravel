@extends('layouts.default')

<!-- Load nội dung trang home/dashboard.php vào vị trí section('page') của layouts/default.php -->
@section('content')
<div class="container mt-100 profiles">
    <!-- Sidebar Start -->
    <div class="row">
        @include('front.profiles.navigation')
        <div class="col-lg-9">
            <div class="ms-3">
                <h5 class="mb-4">PROFILE</h5>
                <h5 class="name">{{ Auth::user()->name }}</h5>
                <p class="text-dark">General</p>
                <p class="mt-2 mb-2">Shop more to get a higher membership</p>
                
                <!-- Validation Errors -->
                <x-auth-validation-errors class="mb-4" :errors="$errors" />
                <div class="accordion mt-4" id="accordionPanelsStayOpenExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="panelsStayOpen-headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">
                                Account information
                            </button>
                          </h2>
                        <hr>
                        <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-headingOne">
                            <div class="accordion-body">
                                <p class="m-0">Contact Info</p>
                                <p class="m-0">{{ Auth::user()->name }}</p>
                                <p>{{ Auth::user()->email }}</p>
                                <p>
                                    <span class="pe-3 border-end"><a href="{{ url('update-profile') }}" class="text-danger">Edit</a></span> 
                                    <span class="ps-2"><a href="{{ url('update-password') }}" class="text-danger">Change Password</a></span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="panelsStayOpen-headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseTwo" aria-expanded="false" aria-controls="panelsStayOpen-collapseTwo">
                                Shipping Address
                            </button>
                        </h2>
                        <hr>
                        <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-headingTwo">
                            <div class="accordion-body">
                              <p>Default shipping address</p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="panelsStayOpen-headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseThree" aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">
                                Recent Orders
                            </button>
                        </h2>
                        <hr>
                        <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-headingThree">
                            <div class="accordion-body">
                                <table class="table table-striped table-hover mt-3">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Day</th>
                                            <th scope="col">Send to</th>
                                            <th scope="col">Total orders</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $start = 1; ?>
                                        <tr>
                                            <th scope="row"><?= $start++; ?></th>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>View orders</td>
                                        </tr>
                                    </tbody>
                                </table>
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

