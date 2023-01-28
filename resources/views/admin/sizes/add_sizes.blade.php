<x-app-layout>
    <div class="section">
        <div class="product-list list">
            <div class='container-fluid mt-10'>
                <div class="row mb-5">
                    <div class="col-md-6 offset-md-3">
                        <h3 class="mb-4 text-center">ADD SIZES</h3>

                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />
                        
                        <form method="POST" action="{{ url('admin/add-sizes/'.$product['id']) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="mb-3">
                                    <label for="product_name" class="form-label">Product Name:</label> &nbsp;
                                    <span>{{ $product['name'] }}</span>
                                </div>
                                <div class="">
                                    <label for="product_des" class="form-label">Product Price:</label> &nbsp;
                                    <span>{{ $product['price'] }}$</span>
                                </div>
                                <div class="mb-3">
                                    <label for="product_image" class="form-label">Product Image:</label>
                                    <img width="100" src="{{ asset('storage/images/products/'.$product['image']) }}" alt="" />
                                </div>
                                <div class="mb-3">
                                    <label for="add_product_size" class="form-label">Add Product Size</label>
                                    <select class="form-select" id="add_product_size" name="add_product_size"
                                        aria-label="Floating label select example">
                                        <option selected disabled>--Select Size--</option>
                                        @foreach($sizes as $size)
                                            <option value="{{ $size['id'] }}">
                                                {{ $size['name'] }} 
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="">
                                    <label for="add_quantity_size" class="form-label">Product Quantity:</label>
                                    <input type="text" class="form-control" id="add_quantity_size" name="add_quantity_size" 
                                        placeholder="Enter Product Quantity" value="">
                                </div>
                            </div>
                            <x-button class="mt-3 mb-5">{{ __('Save') }}</x-button>
                        </form>

                        <h5 class="mb-4 text-center">PRODUCT SIZES</h5>
                        <table class="table table-striped table-hover mt-3">
                            <thead>
                                <tr class="text-center">
                                    <th scope="col">#</th>
                                    <th scope="col">ID</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Quantity</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $start = 1; @endphp
                                @foreach($product->sizes as $size)
                                <tr class="align-middle text-center">
                                    <th scope="row"><?= $start++; ?></th>
                                    <td>{{ $size->pivot->id }}</td>
                                    <td>{{ $size['name'] }}</td>
                                    <td>{{ $size->pivot->quantity }}</td>
                                    <td>
                                        <a href="javascript:void(0)" class="edit-item edit-product"  data-bs-toggle="modal" data-bs-target="#updateSizeModal">
                                            <i class="fa fa-pen"></i>
                                        </a>
                                        {{-- Modal Edit Size --}}
                                        <div class="modal fade" id="updateSizeModal" tabindex="-1" aria-labelledby="updateSizeModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="updateSizeModalLabel">EDIT SIZE</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <!-- Validation Errors -->
                                                        <x-auth-validation-errors class="mb-4" :errors="$errors" />
                                                        
                                                        <form method="POST" action="{{ url('/update-size/'.$size->pivot->id) }}">
                                                            @csrf
                                                            <div class="mb-3">
                                                                <label for="update_quantity" class="form-label">Quantity</label>
                                                                <input type="text" class="form-control" id="update_quantity" name="update_quantity" placeholder="Enter Category Name"
                                                                    value="{{ $size->pivot->quantity }}">
                                                            </div>
                                                            <x-button class="ml-3 float-end" >{{ __('Save') }}</x-button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="javascript:avoid(0)" class="confirmDelete remove-item delete-size"
                                            module="size" moduleid="{{ $size->pivot->id }}" modulename="{{ $size['name'] }}">
                                            <i class="fa fa-times"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
