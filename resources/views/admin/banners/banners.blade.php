<?php use App\Models\Category;?>

<x-app-layout>
    <div class="section">
        <div class="product-list list">
            <div class='container-fluid mt-10'>
                <h3 class="mb-4 text-center">LIST BANNERS</h3>
                
                <div class="float-end w-30">
                    <!-- Validation Errors -->
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />
                </div>

                <table class="table table-striped table-hover mt-3">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">ID</th>
                            <th scope="col">Name</th>
                            <th scope="col">Description</th>
                            <th scope="col">Image</th>
                            <th scope="col">Type</th>
                            <th scope="col">Link</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $start = 1; ?>
                        @foreach($banners as $banner)
                        <tr>
                            <th scope="row"><?= $start++; ?></th>
                            <td>{{ $banner['id'] }}</td>
                            <td>{{ $banner['name'] }}</td>
                            <td>{{ $banner['description'] }}</td>
                            <td>
                                <img width="200px" src="{{ asset('storage/images/banners/'.$banner['image']) }}" alt="" />
                            </td>
                            <td>{{ $banner['type'] }}</td>
                            <td>{{ $banner['link'] }}</td>
                            <td>
                                <a href="{{ url('admin/update-banner/'.$banner['id']) }}" class="edit-item eidt-banner">
                                    <i class="fa fa-pen"></i>
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
