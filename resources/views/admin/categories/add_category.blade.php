<x-app-layout>
    <div class="section">
        <div class="product-list list">
            <div class='container-fluid mt-10'>
                <div class="row mb-5">
                    <div class="col-md-6 offset-md-3">
                        <h3 class="mb-3">ADD NEW CATEGORY</h3>

                        <x-button>
                            <a href="{{ url('admin/categories') }}" class="text-white">{{ __('List of Categories') }}</a>
                        </x-button>


                        <form method="POST" action="{{ url('admin/add-category') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3 mt-3">
                                <label for="category_parent" class="form-label">Category Parent</label>
                                <select class="form-select" id="category_parent" name="category_parent"
                                    aria-label="Floating label select example">
                                    <option selected disabled>--Select Parent--</option>
                                    <option value="0">NULL</option>
                                    @foreach($categories as $category)
                                        @if($category['parent_id'] == 0) 
                                            <option value="{{ $category['id'] }}">
                                                {{ $category['name'] }} 
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="category_name" class="form-label">Category Name</label>
                                <input type="text" class="form-control" id="category_name" name="category_name" placeholder="Enter Category Name"
                                    value="">
                            </div>
                            <div class="mb-3">
                                <label for="category_image" class="form-label">Category Image</label>
                                <input type="file" class="form-control bg-white" id="category_image" name="category_image" >
                            </div>
                            <div class="">
                                <label for="category_des" class="form-label">Category Description</label>
                                <textarea class="form-control" id="category_des" name="category_des" 
                                    placeholder="Enter Category Description" value="" rows="5" maxlength="255">
                                </textarea>
                                <span id=""></span>
                            </div>
                            <div class="mb-3">
                                <label for="category_url" class="form-label">Category URL</label>
                                <input type="text" class="form-control" id="category_url" name="category_url" placeholder="Enter Category URL"
                                    value="">
                            </div>
                            <x-button class="ml-3 float-end" >{{ __('Save') }}</x-button>
                        </form>
                    </div>
                    <div class="col-3">
                        <div class="float-end w-30">
                            <!-- Validation Errors -->
                            <x-auth-validation-errors class="mb-4" :errors="$errors" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
