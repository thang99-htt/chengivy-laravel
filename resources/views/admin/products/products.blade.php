<x-app-layout>
    <div class="section">
        <div class="product-list list">
            <div class='container-fluid mt-10'>
                <div class="d-flex justify-content-between">
                    <h3 class="mb-4">LIST PRODUCTS</h3>

                    <div class="w-30">
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />
                    </div>
                </div>

                <div class="d-flex justify-content-between algin-items-end">
                    <x-button>
                        <a href="{{ url('admin/add-product') }}" class="text-white">{{ __('Add New Product') }}</a>
                    </x-button>

                    <form action="" class="sortProducts" id="sortProducts">
                        {{-- <input type="hidden" id="url" name="url" value="{{ $url }}"> --}}
                        <div class="toolbar-sorter">
                            <div class="select-box-wrapper">
                                <label for="sort" class="text-dark me-2">Sort By</label>
                                <select name="sort" class="select-box" id="sort">
                                    <option selected>-- Select --</option>
                                    <option value="name_a_z"
                                        @if(isset($_GET['sort']) && $_GET['sort']=='name_a_z') selected @endif
                                    >Name A - Z</option>
                                    <option value="name_z_a" 
                                        @if(isset($_GET['sort']) && $_GET['sort']=='name_z_a') selected @endif
                                    >Name Z - A</option>
                                    <option value="newest" 
                                        @if(isset($_GET['sort']) && $_GET['sort']=='newest') selected @endif
                                    >Newest</option>
                                    <option value="lastest"
                                        @if(isset($_GET['sort']) && $_GET['sort']=='lastest') selected @endif
                                    >Lastest</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover mt-3">
                        <thead class="table-secondary">
                            <tr class="text-center align-top">
                                <th scope="col">#</th>
                                <th scope="col">Category</th>
                                <th scope="col">Name</th>
                                <th scope="col">Purchase Price ($)</th>
                                <th scope="col">Price ($)</th>
                                <th scope="col">Image</th>
                                <th scope="col">Type</th>
                                <th scope="col">Discount (%)</th>
                                <th scope="col">Discont Price ($)</th>
                                <th scope="col">Description</th>
                                <th scope="col">Status</th>
                                <th scope="col">Option</th>
                                <th width='100' scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $start = 1; ?>
                            @foreach($products as $product)
                            <tr class="align-middle">
                                <th scope="row"><?= $start++; ?></th>
                                <td>{{ $product['category']['name'] }}</td>
                                <td>{{ $product['name'] }}</td>
                                <td>{{ $product['purchase_price'] }}</td>
                                <td>{{ $product['price'] }}</td>
                                <td>
                                    <img width="100" src="{{ asset('storage/images/products/'.$product['image']) }}" alt="" />
                                </td>
                                <td>{{ $product['type']['name'] }}</td>
                                <td>{{ $product['discount_percent'] }}</td>
                                <td>{{ $product['price'] - ($product['price']*$product['discount_percent']/100) }}</td>
                                <td>{{ substr($product['description'] , 0, 70) }}..........</td>
                                <td>
                                    @if($product['status'] == 1)
                                    <a class="updateProductStatus" id="product-{{ $product['id'] }}" product_id="{{ $product['id'] }}"
                                        href="javascript:void(0)">
                                        <svg status="Active" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-bookmark-check-fill" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd" d="M2 15.5V2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.74.439L8 13.069l-5.26 2.87A.5.5 0 0 1 2 15.5zm8.854-9.646a.5.5 0 0 0-.708-.708L7.5 7.793 6.354 6.646a.5.5 0 1 0-.708.708l1.5 1.5a.5.5 0 0 0 .708 0l3-3z"/>
                                        </svg>
                                    </a>
                                    @else
                                    <a class="updateProductStatus" id="product-{{ $product['id'] }}" product_id="{{ $product['id'] }}"
                                        href="javascript:void(0)">
                                        <svg status="Inactive" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-bookmark" viewBox="0 0 16 16">
                                            <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.777.416L8 13.101l-5.223 2.815A.5.5 0 0 1 2 15.5V2zm2-1a1 1 0 0 0-1 1v12.566l4.723-2.482a.5.5 0 0 1 .554 0L13 14.566V2a1 1 0 0 0-1-1H4z"/>
                                        </svg>
                                    </a>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ url('admin/add-images/'.$product['id']) }}" class="add-item mb-2">Images</a>
                                    <a href="{{ url('admin/add-sizes/'.$product['id']) }}" class="add-item">Sizes</a>
                                </td>
                                <td>
                                    <a href="{{ url('admin/update-product/'.$product['id']) }}" class="edit-item edit-product">
                                        <i class="fa fa-pen"></i>
                                    </a>
                                    <a href="javascript:avoid(0)" class="confirmDelete remove-item delete-product"
                                        module="product" moduleid="{{ $product['id'] }}" modulename="{{ $product['name'] }}">
                                        <i class="fa fa-times"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div>{{ $products->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
