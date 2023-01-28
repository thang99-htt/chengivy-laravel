<x-app-layout>
    <div class="section">
        <div class="product-list list">
            <div class='container-fluid mt-10'>
                <div class="row mb-5">
                    <div class="col-md-6 offset-md-3">
                        <h3 class="mb-4 text-center">ADD IMAGES</h3>

                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />

                        <x-button>
                            <a href="{{ url('admin/products') }}" class="text-white float-end">{{ __('List of Products') }}</a>
                        </x-button>

                        <form method="POST" action="{{ url('admin/add-images/'.$product['id']) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="mt-3">
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
                                <div class="form-group">
                                    <label for="product_image" class="form-label">Choose one or more images for the product:</label>
                                    <div class="field-wrapper">
                                        <input type="file" name="images[]" multiple="" id="images">
                                    </div>
                                </div>

                            </div>
                            <x-button class="mt-3 mb-5">{{ __('Save') }}</x-button>
                        </form>

                        <h5 class="mb-4 text-center">PRODUCT IMAGES</h5>
                        <table class="table table-striped table-hover mt-3">
                            <thead>
                                <tr class="text-center">
                                    <th scope="col">#</th>
                                    <th scope="col">ID</th>
                                    <th width="100" scope="col">Image</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $start = 1; @endphp
                                @foreach($product['images'] as $image)
                                <tr class="align-middle text-center">
                                    <th scope="row"><?= $start++; ?></th>
                                    <td>{{ $image['id'] }}</td>
                                    <td>
                                        <img width="" src="{{ asset('storage/images/products/'.$image['image']) }}" alt="" />
                                    </td>
                                    <td>
                                        <a href="javascript:avoid(0)" class="confirmDelete remove-item delete-image"
                                            module="image" moduleid="{{ $image['id'] }}" modulename="{{ $image['image'] }}">
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
