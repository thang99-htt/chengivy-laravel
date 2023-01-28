<x-app-layout>
    <div class="section">
        <div class="product-list list">
            <div class='container-fluid mt-10'>
                <div class="row mb-5">
                    <div class="d-flex justify-content-between algin-items-center mb-3">
                        <h3>ADD NEW PRODUCT</h3>

                        <x-button>
                            <a href="{{ url('admin/products') }}" class="text-white">{{ __('List of Products') }}</a>
                        </x-button>
                    </div>

                    <!-- Validation Errors -->
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />
                    
                    <form method="POST" action="{{ url('admin/add-product') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="mb-3">
                                    <label for="product_category" class="form-label">Product Category</label>
                                    <select class="form-select" id="product_category" name="product_category"
                                        aria-label="Floating label select example">
                                        <option selected disabled>--Select Category--</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category['id'] }}">
                                                {{ $category['name'] }} 
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="product_name" class="form-label">Product Name</label>
                                    <input type="text" class="form-control" id="product_name" name="product_name" 
                                        placeholder="Enter Product Name" value="">
                                </div>
                                <div class="">
                                    <label for="product_des" class="form-label">Product Description</label>
                                    <textarea class="form-control" id="product_des" name="product_des" 
                                        placeholder="Enter Product Description" value="" rows="8" maxlength="255">
                                    </textarea>
                                    <span id=""></span>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="mb-3">
                                    <label for="product_purchase_price" class="form-label">Product Purchase Price</label>
                                    <input type="text" class="form-control" id="product_purchase_price" name="product_purchase_price" 
                                        placeholder="Enter Product Price" value="">
                                </div>
                                <div class="mb-3">
                                    <label for="product_price" class="form-label">Product Price</label>
                                    <input type="text" class="form-control" id="product_price" name="product_price" 
                                        placeholder="Enter Product Price" value="">
                                </div>
                                <div class="mb-3">
                                    <label for="product_image" class="form-label">Product Image</label>
                                    <input type="file" class="form-control bg-white" id="product_image" name="product_image" >
                                </div>
                                <div class="mb-3">
                                    <label for="product_type" class="form-label">Product Type</label>
                                    <select class="form-select" id="product_type" name="product_type"
                                        aria-label="Floating label select example">
                                        @foreach($types as $type)
                                            <option value="{{ $type['id'] }}">
                                                {{ $type['name'] }} 
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="product_dis" class="form-label">Product Discount (%)</label>
                                    <input type="text" class="form-control" id="product_dis" name="product_dis" value="0">
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
