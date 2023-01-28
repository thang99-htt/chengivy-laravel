@php use App\Models\Product; @endphp

@extends('layouts.default')

<div class="section">
    <div class="cart-list list">
        <div class='container mt-30'>
            <div class="d-flex justify-content-between">
                <h5 class="mb-4">
                    <svg class="d-inline me-2" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bag" viewBox="0 0 16 16">
                        <path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1zm3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4h-3.5zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V5z"/>
                    </svg>
                    <span>YOUR SHOPPING CART</span>
                    <span class="text-secondary fs-6">({{ count($getCartItems) }} products)</span>
                </h5>
                
                <div class="w-30">
                    <!-- Validation Errors -->
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />
                </div>
            </div>
            
            @if(count($getCartItems) > 0)
            <div class="table-responsive overflow-hidden">
                <div class="row">
                    <div class="col-8">
                        <table class="table table-hover mt-2">
                            <thead>
                                <tr>
                                    <th scope="col">Product</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Quantity</th>
                                    <th scope="col">Total Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php 
                                    $totalOrders = 0; 
                                    $totalPriceOrigin = 0;
                                @endphp
                                @foreach($getCartItems as $item)
                                @php 
                                    $getDiscountPrice = Product::getDiscountPrice($item['product_id']);
                                    $totalPrice = 0;
                                @endphp
                                <form method="POST" action="{{ url('cart/update/'.$item['id']) }}" enctype="multipart/form-data">
                                @csrf
                                    <tr>
                                        <td>
                                            <div class="d-flex">
                                                <div class="me-4">
                                                    <img width="100" src="{{ asset('storage/images/products/'.$item['product']['image']) }}" alt="" />    
                                                </div>
                                                <div>
                                                    <input type="hidden" name="cart_product_id" id="" value="{{ $item['product']['id'] }}">
                                                    <p>{{ $item['product']['name'] }}</p>
                                                    <input type="hidden" name="cart_size_id" id="" value="{{ $item['sizes']['id'] }}">
                                                    <p>Size: {{ $item['sizes']['name'] }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($getDiscountPrice > 0)
                                            <p>{{ $getDiscountPrice }} $</p>
                                            <p class="text-decoration-line-through">
                                                {{ $item['product']['price'] }} $
                                            </p>
                                            @else 
                                                <p>{{ $item['product']['price'] }} $</p> <br>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="number-input ms-0">
                                                <button onclick="this.parentNode.querySelector('input[type=number]').stepDown()" >
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-dash" viewBox="0 0 16 16">
                                                        <path d="M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8z"/>
                                                    </svg>
                                                </button>
                                                <input class="quantity" min="1" name="cart_quantity" value="{{ $item['quantity'] }}" type="number">
                                                <button onclick="this.parentNode.querySelector('input[type=number]').stepUp()" class="plus">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                                                        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="position-relative">
                                            @php
                                                if($getDiscountPrice > 0) {
                                                    $totalPrice += $item['quantity']*$getDiscountPrice;
                                                } else {
                                                    $totalPrice += $item['quantity']*$item['product']['price'];
                                                }
                                                
                                                $totalOrders += $totalPrice;
                                                $totalPriceOrigin += $item['quantity']*$item['product']['price'];
                                            @endphp      
                                            <p>{{ $totalPrice }} $</p>    
                                            <div class="position-absolute bottom-0 right-0">
                                                <a href="javascript:avoid(0)" class="confirmDeleteFront remove-item delete-cart"
                                                    module="item" moduleid="{{ $item['id'] }}" modulename="{{ $item['id'] }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                                        <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                                    </svg>
                                                </a>   
                                            </div> 
                                        </td>
                                        
                                    </tr>
                                    <tr>
                                    </tr>
                                </form>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-between">
                            <div>{{ $getCartItems->links() }}</div>
                            <a href="{{ url('checkout') }}"><x-button class="mt-4" >{{ __('COUNTINUTE') }}</x-button></a>
                        </div>
                    </div>
                    <div class="col-4 provisional">
                        <h6>PROVISIONAL</h6>
                        <div class="row total">
                            <div class="col-6">
                                <p>Coupon Discount</p>
                            </div>
                            <div class="col-6">
                                {{ $totalPriceOrigin - $totalOrders }} $
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <p>Total Orders</p>
                            </div>
                            <div class="col-6">
                                {{ $totalOrders }} $
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @else 
            <div>
                <div class="d-flex justify-content-center">
                    <img src="{{ asset('storage/images/cart/empty-cart.svg') }}" alt="" />
                </div>
                <h6 class="text-center mt-4">Cart is empty!</h6>
                <div class="d-flex justify-content-center">
                    <a href="/all"><x-button class="mt-4" >{{ __('COUNTINUTE SHOPPING') }}</x-button></a>
                </div>
            </div>
            @endif
            
        </div>
    </div>
</div>
