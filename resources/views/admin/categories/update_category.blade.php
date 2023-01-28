<x-app-layout>
    <div class="section">
        <div class="product-list list">
            <div class='container-fluid mt-10'>
                <div class="row mb-5">
                    <div class="col-md-6 offset-md-3">
                        <h3 class="mb-3">UPDATE CATEGORY</h3>

                        <x-button>
                            <a href="{{ url('admin/categories') }}" class="text-white">{{ __('List of Categories') }}</a>
                        </x-button>

                        <form method="POST" action="{{ url('admin/update-category/'.$category['id']) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3 mt-3">
                                <label for="cat_parent" class="form-label">Category Parent</label>
                                <select class="form-select" id="cat_parent" name="cat_parent"
                                    aria-label="Floating label select example">
                                    <option aria-placeholder="aaa" selected hidden value="{{ $category['parent_id'] }}">
                                        @if($category['parent_id'] == 0)
                                            NULL
                                        @else
                                            {{ $category['parent']['name'] }}
                                        @endif
                                    </option>
                                    <option value="0">NULL</option>
                                    @foreach($categories as $cat)
                                        @if($cat['parent_id'] == 0)
                                            <option value="{{ $cat['id'] }}">
                                                {{ $cat['name'] }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="cat_name" class="form-label">Category Name</label>
                                <input type="text" class="form-control" id="cat_name" name="cat_name" 
                                    value="{{ $category['name'] }}">
                            </div>
                            <div class="mb-3">
                                <label for="cat_image" class="form-label">Category Image</label>
                                <input type="file" class="form-control bg-white" id="cat_image" name="cat_image" >
                                @if(!empty($category['image'])) 
                                    <a target="_blank" href="{{ asset('storage/images/categories/'.$category['image']) }}">
                                        View image
                                    </a> &nbsp;&nbsp;
                                    <input type="text" hidden name="current_category_image" value="{{ $category['image'] }}">
                                @endif
                            </div>
                            <div class="">
                                <label for="cat_des" class="form-label">Category Description</label>
                                <textarea class="form-control" id="cat_des" name="cat_des"  value="" 
                                    rows="5" maxlength="255">{{ $category['description'] }}</textarea>
                                <span id=""></span>
                            </div>
                            <div class="mb-3">
                                <label for="cat_url" class="form-label">Category URL</label>
                                <input type="text" class="form-control" id="cat_url" name="cat_url" 
                                    value="{{ $category['url'] }}">
                            </div>
                            <x-button class="ml-3 float-end" >{{ __('Save') }}</x-button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
