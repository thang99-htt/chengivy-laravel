<x-app-layout>
    <div class="section">
        <div class="product-list list">
            <div class='container-fluid mt-10'>
                <div class="row mb-5">
                    <div class="col-md-6 offset-md-3">
                        <h3 class="mb-4 text-center">UPDATE BANNER</h3>
                        
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />
                        
                        <form method="POST" action="{{ url('admin/update-banner/'.$banner['id']) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="banner_name" class="form-label">Banner Name</label>
                                <input type="text" class="form-control" id="banner_name" name="banner_name" 
                                    value="{{ $banner['name'] }}">
                            </div>
                            <div class="">
                                <label for="banner_des" class="form-label">Banner Description</label>
                                <textarea class="form-control" id="banner_des" name="banner_des"  value="" 
                                    rows="5">{{ $banner['description'] }}</textarea>
                                <span id=""></span>
                            </div>
                            <div class="mb-3">
                                <label for="banner_image" class="form-label">Banner Image</label>
                                <input type="file" class="form-control bg-white" id="banner_image" name="banner_image" >
                                @if(!empty($banner['image'])) 
                                    <a target="_blank" href="{{ asset('storage/images/banners/'.$banner['image']) }}">
                                        View image
                                    </a> &nbsp;&nbsp;
                                    <input type="text" hidden name="current_banner_image" value="{{ $banner['image'] }}">
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="banner_type" class="form-label">Banner Type</label>
                                {{-- <select class="form-select" id="banner_type" name="banner_type"
                                    aria-label="Floating label select example">
                                    <option selected disabled>--Select Type--</option>
                                    <option value="Slider">Slider</option>
                                    <option value="Fix">Fix</option>
                                    <option value="Advertise">Advertise</option>
                                </select> --}}
                                <input type="text" class="form-control" id="banner_type" name="banner_type" 
                                    value="{{ $banner['type'] }}" readonly="">
                            </div>
                            <div class="mb-3">
                                <label for="banner_link" class="form-label">Banner Link</label>
                                <input type="text" class="form-control" id="banner_link" name="banner_link" 
                                    value="{{ $banner['link'] }}">
                            </div>
                            <x-button class="ml-3 float-end" >{{ __('Save') }}</x-button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
