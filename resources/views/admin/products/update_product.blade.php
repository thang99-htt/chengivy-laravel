<?php use App\Models\Product;?>

<x-app-layout>
    <div class="section">
        <div class="product-list list">
            <div class='container-fluid mt-10'>
                <div class="row mb-5">
                    <div class="d-flex justify-content-between algin-items-center mb-3">
                        <h3>UPDATE PRODUCT</h3>

                        <x-button>
                            <a href="{{ url('admin/products') }}" class="text-white">{{ __('List of Products') }}</a>
                        </x-button>
                    </div>

                    <!-- Validation Errors -->
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />
                    
                    <form method="POST" action="{{ url('admin/update-product/'.$product['id']) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="mb-3">
                                    <label for="pro_category" class="form-label">Product Category</label>
                                    <select class="form-select" id="pro_category" name="pro_category"
                                        aria-label="Floating label select example">
                                        <option aria-placeholder="aaa" selected hidden value="{{ $product['category']['id'] }}">
                                            {{ $product['category']['name'] }}
                                        </option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category['id'] }}">
                                                {{ $category['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="pro_name" class="form-label">Product Name</label>
                                    <input type="text" class="form-control" id="pro_name" name="pro_name" 
                                        value="{{ $product['name'] }}">
                                </div>
                                <div class="">
                                    <label for="pro_des" class="form-label">Product Description</label>
                                    <textarea class="form-control" id="pro_des" name="pro_des"  value="" 
                                        rows="9" maxlength="255">{{ $product['description'] }}</textarea>
                                    <span id=""></span>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="mb-3">
                                    <label for="pro_purchase_price" class="form-label">Product Purchase Price</label>
                                    <input type="text" class="form-control" id="pro_purchase_price" name="pro_purchase_price" 
                                        value="{{ $product['purchase_price'] }}">
                                </div>
                                <div class="mb-3">
                                    <label for="pro_price" class="form-label">Product Price</label>
                                    <input type="text" class="form-control" id="pro_price" name="pro_price" 
                                        value="{{ $product['price'] }}">
                                </div>
                                <div class="mb-3">
                                    <label for="pro_image" class="form-label">Product Image</label>
                                    <input type="file" class="form-control bg-white" id="pro_image" name="pro_image" >
                                    @if(!empty($product['image'])) 
                                        <a target="_blank" href="{{ asset('storage/images/products/'.$product['image']) }}">
                                            View image
                                        </a> &nbsp;&nbsp;
                                        <input type="text" hidden name="current_product_image" value="{{ $product['image'] }}">
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <label for="pro_type" class="form-label">Product Status</label>
                                    <select class="form-select" id="pro_type" name="pro_type"
                                        aria-label="Floating label select example">
                                        <option aria-placeholder="aaa" selected hidden value="{{ $product['type']['id'] }}">
                                            {{ $product['type']['name'] }}
                                        </option>
                                        @foreach($types as $type)
                                            <option value="{{ $type['id'] }}">
                                                {{ $type['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="pro_dis" class="form-label">Product Discount (%)</label>
                                    <input type="text" class="form-control" id="pro_dis" name="pro_dis" 
                                        value="{{ $product['discount_percent'] }}">
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
