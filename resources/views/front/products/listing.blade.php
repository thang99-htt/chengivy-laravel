@php use App\Models\Product; @endphp

@extends('layouts.default')

@section('content')
    <section class="product-sort">
        <div class="container-fluid">
            <div class="mt-100">
                <div class="row">
                    <div class="offset-10">
                        <form action="" class="sortProducts" id="sortProducts">
                            <input type="hidden" id="url" name="url" value="{{ $url }}">
                            <div class="toolbar-sorter">
                                <div class="select-box-wrapper">
                                    <label for="sort" class="text-dark me-2">Sort By</label>
                                    <select name="sort" id="sortListing" class="select-box">
                                        <option selected>-- Select --</option>
                                        <option value="price_lowest" 
                                            @if(isset($_GET['sort']) && $_GET['sort']=='price_lowest') selected @endif
                                        >Low to High</option>
                                        <option value="price_highest"
                                            @if(isset($_GET['sort']) && $_GET['sort']=='price_highest') selected @endif
                                        >High to Low</option>
                                        <option value="name_a_z"
                                            @if(isset($_GET['sort']) && $_GET['sort']=='name_a_z') selected @endif
                                        >Name A - Z</option>
                                        <option value="name_z_a" 
                                            @if(isset($_GET['sort']) && $_GET['sort']=='name_z_a') selected @endif
                                        >Name Z - A</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="product-list section filter_products">
        @include('front.products.ajax_products_listing')
    </section>

    <section class="product-list section">
        <div class="container-fluid">
            @if(isset($_GET['sort']))
            <div>{{ $products->appends(['sort'=>$_GET['sort']])->links() }}</div>
            @else
            <div>{{ $products->links() }}</div>
            @endif
        </div>
    </section>
@endsection

