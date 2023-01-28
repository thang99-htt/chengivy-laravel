<?php use App\Models\Category;?>

<x-app-layout>
    <div class="section">
        <div class="product-list list">
            <div class='container-fluid mt-10 mb=100'>
                <div class="d-flex justify-content-between">
                    <h3 class="mb-4">LIST CATEGORIES</h3>

                    <div class="w-30">
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />
                    </div>
                </div>
                <x-button>
                    <a href="{{ url('admin/add-category') }}" class="text-white">{{ __('Add New Category') }}</a>
                </x-button>
        

                <table class="table table-striped table-hover mt-3">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Parent</th>
                            <th scope="col">Name</th>
                            <th scope="col">Image</th>
                            <th scope="col">Description</th>
                            <th scope="col">URL</th>
                            <th scope="col">Status</th>
                            <th width='100' scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $start = 1; ?>
                        @foreach($categories as $category)
                        <tr>
                            <th scope="row"><?= $start++; ?></th>
                            <td>
                                @if(isset($category['parent']['name']) && !empty($category['parent']['name'])) 
                                    {{ $category['parent']['name'] }} 
                                @else 
                                    NULL
                                @endif
                            </td>
                            <td>{{ $category['name'] }}</td>
                            <td>
                                <img width="100" src="{{ asset('storage/images/categories/'.$category['image']) }}" alt="" />
                            </td>
                            <td>{{ $category['description'] }}</td>
                            <td>{{ $category['url'] }}</td>
                            <td>
                                @if($category['status'] == 1)
                                <a class="updateCategoryStatus" id="category-{{ $category['id'] }}" category_id="{{ $category['id'] }}"
                                    href="javascript:void(0)">
                                    <svg status="Active" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-bookmark-check-fill" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M2 15.5V2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.74.439L8 13.069l-5.26 2.87A.5.5 0 0 1 2 15.5zm8.854-9.646a.5.5 0 0 0-.708-.708L7.5 7.793 6.354 6.646a.5.5 0 1 0-.708.708l1.5 1.5a.5.5 0 0 0 .708 0l3-3z"/>
                                    </svg>
                                </a>
                                @else
                                <a class="updateCategoryStatus" id="category-{{ $category['id'] }}" category_id="{{ $category['id'] }}"
                                    href="javascript:void(0)">
                                    <svg status="Inactive" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-bookmark" viewBox="0 0 16 16">
                                        <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.777.416L8 13.101l-5.223 2.815A.5.5 0 0 1 2 15.5V2zm2-1a1 1 0 0 0-1 1v12.566l4.723-2.482a.5.5 0 0 1 .554 0L13 14.566V2a1 1 0 0 0-1-1H4z"/>
                                    </svg>
                                </a>
                                @endif
                            </td>
                            <td>
                                <a href="{{ url('admin/update-category/'.$category['id']) }}" class="edit-item eidt-category">
                                    <i class="fa fa-pen"></i>
                                </a>
                                <a href="javascript:avoid(0)" class="confirmDelete remove-item delete-category"
                                    module="category" moduleid="{{ $category['id'] }}" modulename="{{ $category['name'] }}">
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
</x-app-layout>
